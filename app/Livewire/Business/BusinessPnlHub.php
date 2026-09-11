<?php

namespace App\Livewire\Business;

use App\Services\BusinessAccessService;
use App\Services\BusinessFinancialMetrics;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessPnlHub extends Component
{
    public $year;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function setYear($year): void
    {
        $this->year = (int) $year;
    }

    public function render()
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();

        $monthlyData = collect(app(BusinessFinancialMetrics::class)->forYear($workspace, (int) $this->year))
            ->map(function (array $row, int $index) {
                $month = $index + 1;

                return [
                    'month_name' => mb_convert_case(Carbon::create((int) $this->year, $month, 1)->translatedFormat('F'), MB_CASE_TITLE),
                    'revenue' => $row['revenue_cash'],
                    'costs' => $row['total_costs'],
                    'vat' => 0,
                    'profit' => $row['net_result'],
                    'margin' => $row['margin'],
                    'payroll_is_current_run_rate' => $row['payroll_is_current_run_rate'],
                ];
            });

        return view('livewire.business.business-pnl-hub', [
            'monthlyData' => $monthlyData,
            'yearlyRevenue' => round($monthlyData->sum('revenue'), 2),
            'yearlyProfit' => round($monthlyData->sum('profit'), 2),
            'avgMargin' => round($monthlyData->where('revenue', '>', 0)->avg('margin') ?? 0, 2),
            'chartMax' => max($monthlyData->max('revenue'), $monthlyData->max('costs'), 1),
            'fiscalDisclaimer' => 'Resultado operacional em base de caixa. O histórico de salários não existe atualmente como série temporal; por isso, o salário apresentado só entra no mês corrente como custo mensal atual. Os meses anteriores não são preenchidos artificialmente. Não substitui a contabilidade oficial.',
            'workspaceCurrency' => strtoupper((string) ($workspace->currency ?? 'EUR')),
        ]);
    }
}
