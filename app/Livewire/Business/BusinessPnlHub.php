<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Employee;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class BusinessPnlHub extends Component
{
    public $year;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        if (!$workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500">Nenhum workspace empresarial selecionado.</div>
            HTML;
        }

        // 1. CONSOLIDAÇÃO MENSAL (12 Meses)
        $monthlyData = collect(range(1, 12))->map(function ($month) use ($workspace) {
            // Receitas (Faturas Pagas)
            $revenue = (float) $workspace->invoices()
                ->whereYear('created_at', $this->year)
                ->whereMonth('created_at', $month)
                ->where('status', 'paga')
                ->sum('total_amount');

            // Custos Operacionais (is_company = true)
            $opEx = (float) $workspace->expenses()
                ->where('is_company', true)
                ->whereYear('spent_at', $this->year)
                ->whereMonth('spent_at', $month)
                ->sum('amount');

            // Custos de Pessoal (Payroll)
            // Assumimos que o custo salarial é recorrente mensalmente
            $payroll = (float) $workspace->employees()->sum('salary');

            // Impostos (IVA Estimado do Mês)
            $vatIn = (float) $workspace->invoices()
                ->whereYear('created_at', $this->year)
                ->whereMonth('created_at', $month)
                ->sum('vat_amount');

            $vatOut = (float) $workspace->expenses()
                ->where('is_company', true)
                ->whereYear('spent_at', $this->year)
                ->whereMonth('spent_at', $month)
                ->sum('vat_amount');

            $netVat = max(0, $vatIn - $vatOut);

            $grossProfit = $revenue - $opEx - $payroll;
            $netProfit = $grossProfit - ($grossProfit > 0 ? ($grossProfit * 0.21) : 0); // Estimativa de IRC

            return [
                'month_name' => mb_convert_case(now()->month($month)->translatedFormat('F'), MB_CASE_TITLE),
                'revenue' => $revenue,
                'costs' => $opEx + $payroll,
                'vat' => $netVat,
                'profit' => $netProfit,
                'margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0
            ];
        });

        // 2. TOTAIS ANUAIS
        $yearlyRevenue = $monthlyData->sum('revenue');
        $yearlyProfit = $monthlyData->sum('profit');
        $avgMargin = $monthlyData->where('revenue', '>', 0)->avg('margin') ?? 0;

        return view('livewire.business.business-pnl-hub', [
            'monthlyData' => $monthlyData,
            'yearlyRevenue' => $yearlyRevenue,
            'yearlyProfit' => $yearlyProfit,
            'avgMargin' => $avgMargin,
            'chartMax' => max($monthlyData->max('revenue'), $monthlyData->max('costs'), 1)
        ]);
    }
}
