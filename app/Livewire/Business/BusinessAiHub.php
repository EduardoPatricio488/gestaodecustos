<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Project, Product, Invoice, Expense, Employee};
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class BusinessAiHub extends Component
{
    public $lastAudit = null;
    public $targetHourlyRate = 50.00;

    /**
     * Executa a animação de auditoria
     */
    public function runAnalysis()
    {
        sleep(2); // Simulação para efeito visual de "processamento inteligente"
        $this->lastAudit = now()->format('H:i:s');
        $this->dispatch('toast', text: 'Auditoria estratégica concluída!');
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace;

        if (!$workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500">Nenhum workspace empresarial detetado.</div>
            HTML;
        }

        $month = now()->month;

        // 1. DADOS FINANCEIROS & LIQUIDEZ
        $cash = (float) $workspace->getLiquidezAtual();
        $totalPayroll = (float) $workspace->employees()->sum('salary');
        $totalRevenue = (float) $workspace->invoices()->where('status', 'paga')->sum('total_amount');

        // 2. RISCO DE CONCENTRAÇÃO (Clientes)
        $topClient = $workspace->invoices()
            ->where('status', 'paga')
            ->select('client_name', DB::raw('SUM(total_amount) as total'))
            ->groupBy('client_name')
            ->orderByDesc('total')
            ->first();

        $riskConcentration = ($totalRevenue > 0 && $topClient) ? ($topClient->total / $totalRevenue) * 100 : 0;

        // 3. PERFORMANCE DE PROJETOS (Lucro por Hora)
        $projects = $workspace->projects()->get()->map(function($p) {
            return [
                'name' => $p->name,
                'hourly_profit' => (float) $p->hourly_profit,
                'hours' => round($p->total_time_seconds / 3600, 1),
                'margin' => (float) $p->margin,
            ];
        })->sortByDesc('hourly_profit');

        // 4. AUDITORIA DE STOCK (Património Imobilizado)
        $products = $workspace->products()->get();
        $inventoryValue = (float) $products->sum(fn($p) => $p->stock_quantity * $p->unit_cost);
        $lowStockCount = $products->filter(fn($p) => $p->isLowStock())->count();

        // 5. CÁLCULO DE SCORE DE RESILIÊNCIA
        $healthScore = $this->calculateBusinessHealth($cash, $totalPayroll, $riskConcentration, $lowStockCount);

        return view('livewire.business.business-ai-hub', [
            'healthScore' => $healthScore,
            'runway' => $workspace->getRunway(),
            'totalPayroll' => $totalPayroll,
            'payrollCoverage' => $totalPayroll > 0 ? round($cash / $totalPayroll, 1) : 0,
            'riskConcentration' => round($riskConcentration, 1),
            'topClientName' => $topClient->client_name ?? 'N/A',
            'inventoryValue' => $inventoryValue,
            'lowStockCount' => $lowStockCount,
            'projects' => $projects,
            'insights' => $this->generateStrategicInsights($cash, $totalPayroll, $riskConcentration, $lowStockCount, $projects)
        ]);
    }

    private function generateStrategicInsights($cash, $payroll, $risk, $lowStock, $projects)
    {
        $insights = [];

        // Insight: Liquidez
        if ($payroll > 0) {
            if ($cash < $payroll) {
                $insights[] = ['type' => 'danger', 'title' => 'Ruptura de Tesouraria', 'text' => 'O saldo atual não cobre os salários do mês. Antecipa recebimentos imediatamente.'];
            } elseif ($cash > ($payroll * 6)) {
                $insights[] = ['type' => 'success', 'title' => 'Capacidade de Investimento', 'text' => 'Tens reserva para +6 meses. É o momento ideal para expandir ou investir em novos ativos.'];
            }
        }

        // Insight: Clientes
        if ($risk > 45) {
            $insights[] = ['type' => 'warning', 'title' => 'Risco de Dependência', 'text' => 'Mais de 45% da faturação vem de um só cliente. Perder este contrato compromete a operação.'];
        }

        // Insight: Operações
        $inefficient = $projects->filter(fn($p) => $p['hourly_profit'] > 0 && $p['hourly_profit'] < $this->targetHourlyRate)->count();
        if ($inefficient > 0) {
            $insights[] = ['type' => 'warning', 'title' => 'Dreno de Produtividade', 'text' => "Tens $inefficient projetos a render menos de {$this->targetHourlyRate}€/hora. Revê processos."];
        }

        // Insight: Inventário
        if ($lowStock > 0) {
            $insights[] = ['type' => 'info', 'title' => 'Reposição de Stock', 'text' => "Tens $lowStock artigos em nível crítico. Evita perder vendas por falta de material."];
        }

        return $insights;
    }

    private function calculateBusinessHealth($cash, $payroll, $risk, $lowStock)
    {
        $score = 100;
        if ($payroll > 0 && $cash < $payroll) $score -= 40;
        if ($risk > 50) $score -= 20;
        if ($lowStock > 3) $score -= 10;
        return max(5, $score);
    }
}
