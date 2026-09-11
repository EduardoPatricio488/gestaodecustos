<?php

namespace App\Livewire\Business;

use App\Services\AI\AiBrainService;
use App\Services\BusinessFinancialMetrics;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessAiHub extends Component
{
    public $lastAudit = null;
    public $targetHourlyRate = 50.00;

    public function runAnalysis(AiBrainService $brain): void
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;
        abort_unless($workspace && in_array($workspace->type, ['business', 'company'], true), 404);

        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace);
        $insights = $this->buildInsights($workspace, $metrics);

        try {
            $conversation = $brain->conversation($user, $workspace);
            $result = $brain->chat(
                $user,
                'Faz uma análise executiva desta empresa. Usa exclusivamente os dados reais disponíveis no workspace. Resume a situação financeira, identifica os 3 principais riscos/oportunidades e sugere ações práticas. Distingue FACTOS, INFERÊNCIAS e RECOMENDAÇÕES. Não inventes impostos, números ou dados. Se não houver dados suficientes, diz isso claramente.',
                $conversation,
                ['module'=>'business-ai','route'=>'hub.business.ai','path'=>request()->path(),'entity_type'=>'business_workspace','period'=>$metrics['period']],
            );
            $this->lastAudit = $result['content'] ?? 'A análise terminou sem conteúdo.';
            $this->dispatch('toast', variant: 'success', text: 'Análise estratégica concluída pelo AI Brain.');
        } catch (\Throwable $exception) {
            report($exception);
            $this->lastAudit = 'A análise automática não ficou disponível. Os indicadores abaixo continuam a ser calculados deterministicamente pelo backend.';
            $this->dispatch('toast', variant: 'info', text: 'Análise recalculada com os dados atuais.');
        }
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
        $totalRevenue = (float) $workspace->invoices()->where('status', 'paga')->sum('amount_excl_vat_converted');
        $topClient = $workspace->invoices()->where('status', 'paga')->select('client_name', DB::raw('SUM(amount_excl_vat_converted) as total'))->groupBy('client_name')->orderByDesc('total')->first();
        $riskConcentration = ($totalRevenue > 0 && $topClient) ? ($topClient->total / $totalRevenue) * 100 : 0;
        $projects = $workspace->projects()->get()->map(fn ($p) => ['name'=>$p->name,'hourly_profit'=>(float)$p->hourly_profit,'hours'=>round($p->total_time_seconds/3600,1),'margin'=>(float)$p->margin])->sortByDesc('hourly_profit');
        $products = $workspace->products()->get();
        $inventoryValue = (float) $products->sum(fn ($p) => $p->stock_quantity * $p->unit_cost);
        $lowStockCount = $products->filter(fn ($p) => $p->isLowStock())->count();

        return view('livewire.business.business-ai-hub', [
            'healthScore'=>$this->calculateBusinessHealth($cash,$totalPayroll,$riskConcentration,$lowStockCount),'runway'=>$workspace->getRunway(),'totalPayroll'=>$totalPayroll,
            'payrollCoverage'=>$totalPayroll>0?round($cash/$totalPayroll,1):0,'riskConcentration'=>round($riskConcentration,1),'topClientName'=>$topClient->client_name??'N/A',
            'inventoryValue'=>$inventoryValue,'lowStockCount'=>$lowStockCount,'projects'=>$projects,'insights'=>$this->buildInsights($workspace,$metrics,$projects,$riskConcentration,$lowStockCount),
            'analysisDisclaimer'=>'A análise usa os dados financeiros registados. A resposta do AI Brain, quando disponível, é explicativa e não substitui contabilista, auditor ou consultor financeiro.',
        ]);
    }

    private function buildInsights($workspace, array $metrics, $projects = null, float $risk = 0, int $lowStock = 0): array
    {
        $projects ??= $workspace->projects()->get()->map(fn ($p) => ['hourly_profit'=>(float)$p->hourly_profit]);
        $insights=[];
        if ($metrics['payroll']>0 && $metrics['cash']<$metrics['payroll']) $insights[]=['type'=>'danger','title'=>'Pressão de tesouraria','text'=>'O saldo disponível está abaixo do custo salarial mensal registado.'];
        if ($risk>45) $insights[]=['type'=>'warning','title'=>'Concentração de receita','text'=>'Um cliente representa mais de 45% da receita recebida registada.'];
        $inefficient=$projects->filter(fn ($p) => $p['hourly_profit']>0 && $p['hourly_profit']<$this->targetHourlyRate)->count();
        if ($inefficient>0) $insights[]=['type'=>'warning','title'=>'Projetos abaixo do objetivo','text'=>"$inefficient projetos estão abaixo de {$this->targetHourlyRate}€/hora de resultado registado."];
        if ($lowStock>0) $insights[]=['type'=>'info','title'=>'Stock baixo','text'=>"$lowStock artigos estão abaixo do nível mínimo configurado."];
        return $insights;
    }

    private function calculateBusinessHealth($cash, $payroll, $risk, $lowStock): int
    {
        $score=100;
        if ($payroll>0 && $cash<$payroll) $score-=40;
        if ($risk>50) $score-=20;
        if ($lowStock>3) $score-=10;
        return max(5,min(100,$score));
    }
}
