<?php

namespace App\Livewire\Business;

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

    public function runAnalysis(): void
    {
        $this->lastAudit = now()->format('H:i:s');
        $this->dispatch('toast', variant: 'success', text: 'Análise estratégica recalculada com os dados atuais.');
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

        $topClient = $workspace->invoices()->where('status', 'paga')
            ->select('client_name', DB::raw('SUM(amount_excl_vat) as total'))
            ->groupBy('client_name')->orderByDesc('total')->first();
        $riskConcentration = ($totalRevenue > 0 && $topClient) ? ($topClient->total / $totalRevenue) * 100 : 0;

        $projects = $workspace->projects()->get()->map(fn ($p) => [
            'name' => $p->name, 'hourly_profit' => (float) $p->hourly_profit,
            'hours' => round($p->total_time_seconds / 3600, 1), 'margin' => (float) $p->margin,
        ])->sortByDesc('hourly_profit');

        $products = $workspace->products()->get();
        $inventoryValue = (float) $products->sum(fn ($p) => $p->stock_quantity * $p->unit_cost);
        $lowStockCount = $products->filter(fn ($p) => $p->isLowStock())->count();
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
            'insights' => $this->generateStrategicInsights($cash, $totalPayroll, $riskConcentration, $lowStockCount, $projects),
            'analysisDisclaimer' => 'Esta análise é baseada nos dados registados na plataforma e em regras de gestão. Não substitui aconselhamento financeiro, contabilístico ou fiscal.',
        ]);
    }

    private function generateStrategicInsights($cash, $payroll, $risk, $lowStock, $projects): array
    {
        $insights = [];
        if ($payroll > 0 && $cash < $payroll) {
            $insights[] = ['type' => 'danger', 'title' => 'Pressão de tesouraria', 'text' => 'O saldo disponível está abaixo do custo salarial mensal registado.'];
        } elseif ($payroll > 0 && $cash >= $payroll * 6) {
            $insights[] = ['type' => 'success', 'title' => 'Boa cobertura de tesouraria', 'text' => 'O saldo disponível cobre pelo menos seis meses do custo salarial registado.'];
        }
        if ($risk > 45) $insights[] = ['type' => 'warning', 'title' => 'Concentração de receita', 'text' => 'Um cliente representa mais de 45% da receita recebida registada.'];
        $inefficient = $projects->filter(fn ($p) => $p['hourly_profit'] > 0 && $p['hourly_profit'] < $this->targetHourlyRate)->count();
        if ($inefficient > 0) $insights[] = ['type' => 'warning', 'title' => 'Projetos abaixo do objetivo', 'text' => "$inefficient projetos estão abaixo de {$this->targetHourlyRate}€/hora de resultado registado."];
        if ($lowStock > 0) $insights[] = ['type' => 'info', 'title' => 'Stock baixo', 'text' => "$lowStock artigos estão abaixo do nível mínimo configurado."];
        return $insights;
    }

    private function calculateBusinessHealth($cash, $payroll, $risk, $lowStock): int
    {
        $score = 100;
        if ($payroll > 0 && $cash < $payroll) $score -= 40;
        if ($risk > 50) $score -= 20;
        if ($lowStock > 3) $score -= 10;
        return max(5, min(100, $score));
    }
}
