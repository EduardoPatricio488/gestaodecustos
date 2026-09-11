<?php

namespace App\Livewire\Business;

use App\Services\BusinessFinancialMetrics;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessAiHub extends Component
{
    public $lastAudit = null;
    public $targetHourlyRate = 50.00;

    public function runAnalysis(): void
    {
        $workspace = Auth::user()->currentWorkspace;
        abort_unless($workspace, 404);
        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace);
        $insights = $this->buildInsights($workspace, $metrics);

        $apiKey = config('services.openrouter.api_key');
        if ($apiKey) {
            try {
                $response = Http::withToken($apiKey)->acceptJson()->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model', 'openai/gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'És um analista financeiro empresarial. Usa exclusivamente os dados fornecidos. Não inventes números, impostos ou factos. Distingue claramente análise de aconselhamento fiscal/contabilístico. Responde em português de Portugal, de forma objetiva e executiva.'],
                        ['role' => 'user', 'content' => json_encode([
                            'empresa' => $workspace->name,
                            'periodo' => $metrics['period'],
                            'metricas' => $metrics,
                            'alertas' => $insights,
                            'pedido' => 'Resume a situação financeira, identifica os 3 principais riscos/oportunidades e sugere ações práticas. Usa apenas os dados recebidos.',
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 700,
                ]);

                $text = data_get($response->json(), 'choices.0.message.content');
                if ($response->successful() && is_string($text) && trim($text) !== '') {
                    $this->lastAudit = trim($text);
                    $this->dispatch('toast', variant: 'success', text: 'Análise estratégica concluída com IA e dados reais.');
                    return;
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $this->lastAudit = 'Análise baseada em regras e dados reais concluída. A IA generativa não está disponível neste momento. '.($insights[0]['text'] ?? 'Não foram detetados alertas prioritários.');
        $this->dispatch('toast', variant: 'info', text: 'Análise recalculada com os dados atuais.');
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace;
        if (! $workspace) return <<<'HTML'
            <div class="p-10 text-center italic text-zinc-500">Nenhum workspace empresarial detetado.</div>
        HTML;

        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace);
        $cash = $metrics['cash'];
        $totalPayroll = $metrics['payroll'];
        $totalRevenue = (float) $workspace->invoices()->where('status', 'paga')->sum('amount_excl_vat');
        $topClient = $workspace->invoices()->where('status', 'paga')->select('client_name', DB::raw('SUM(amount_excl_vat) as total'))->groupBy('client_name')->orderByDesc('total')->first();
        $riskConcentration = ($totalRevenue > 0 && $topClient) ? ($topClient->total / $totalRevenue) * 100 : 0;
        $projects = $workspace->projects()->get()->map(fn ($p) => ['name'=>$p->name,'hourly_profit'=>(float)$p->hourly_profit,'hours'=>round($p->total_time_seconds/3600,1),'margin'=>(float)$p->margin])->sortByDesc('hourly_profit');
        $products = $workspace->products()->get();
        $inventoryValue = (float)$products->sum(fn($p)=>$p->stock_quantity*$p->unit_cost);
        $lowStockCount = $products->filter(fn($p)=>$p->isLowStock())->count();

        return view('livewire.business.business-ai-hub', [
            'healthScore'=>$this->calculateBusinessHealth($cash,$totalPayroll,$riskConcentration,$lowStockCount),
            'runway'=>$workspace->getRunway(), 'totalPayroll'=>$totalPayroll,
            'payrollCoverage'=>$totalPayroll>0?round($cash/$totalPayroll,1):0,
            'riskConcentration'=>round($riskConcentration,1), 'topClientName'=>$topClient->client_name??'N/A',
            'inventoryValue'=>$inventoryValue, 'lowStockCount'=>$lowStockCount, 'projects'=>$projects,
            'insights'=>$this->buildInsights($workspace,$metrics,$projects,$riskConcentration,$lowStockCount),
            'analysisDisclaimer'=>'A análise usa os dados financeiros registados. A resposta de IA, quando disponível, é explicativa e não substitui contabilista, auditor ou consultor financeiro.',
        ]);
    }

    private function buildInsights($workspace, array $metrics, $projects = null, float $risk = 0, int $lowStock = 0): array
    {
        $projects ??= $workspace->projects()->get()->map(fn($p)=>['hourly_profit'=>(float)$p->hourly_profit]);
        $insights=[];
        if ($metrics['payroll']>0 && $metrics['cash']<$metrics['payroll']) $insights[]=['type'=>'danger','title'=>'Pressão de tesouraria','text'=>'O saldo disponível está abaixo do custo salarial mensal registado.'];
        if ($risk>45) $insights[]=['type'=>'warning','title'=>'Concentração de receita','text'=>'Um cliente representa mais de 45% da receita recebida registada.'];
        $inefficient=$projects->filter(fn($p)=>$p['hourly_profit']>0&&$p['hourly_profit']<$this->targetHourlyRate)->count();
        if($inefficient>0)$insights[]=['type'=>'warning','title'=>'Projetos abaixo do objetivo','text'=>"$inefficient projetos estão abaixo de {$this->targetHourlyRate}€/hora de resultado registado."];
        if($lowStock>0)$insights[]=['type'=>'info','title'=>'Stock baixo','text'=>"$lowStock artigos estão abaixo do nível mínimo configurado."];
        return $insights;
    }

    private function calculateBusinessHealth($cash,$payroll,$risk,$lowStock): int
    {
        $score=100; if($payroll>0&&$cash<$payroll)$score-=40; if($risk>50)$score-=20; if($lowStock>3)$score-=10; return max(5,min(100,$score));
    }
}
