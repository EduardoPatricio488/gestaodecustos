<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Employee;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class TaxHub extends Component
{
    public function render()
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        $month = now()->month;
        $year = now()->year;

        // IVA DAS VENDAS
        $vatCollected = (float) Invoice::where('workspace_id', $workspace->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('vat_amount');

        // IVA DEDUTÍVEL
        $vatDeductible = (float) Expense::where('workspace_id', $workspace->id)
            ->where('is_company', true)
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('vat_amount');

        // SALDO DE IVA
        $vatNet = $vatCollected - $vatDeductible;

        // TSU — APENAS COLABORADORES ATIVOS
        $totalSalaries = (float) Employee::where('workspace_id', $workspace->id)
            ->where('active', true)
            ->sum('salary');

        $tsuEstimate = $totalSalaries * 0.2375;

        // IRC — LUCRO TRIBUTÁVEL REAL
        $revenue = (float) Invoice::where('workspace_id', $workspace->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount_excl_vat');

        $expenses = (float) Expense::where('workspace_id', $workspace->id)
            ->where('is_company', true)
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('amount');

        $estimatedProfit = max(0, $revenue - $expenses - $totalSalaries);

        // IRC + DERRAMA MUNICIPAL (1.5%)
        $ircProvision = $estimatedProfit * 0.21;
        $derrama = $estimatedProfit * 0.015;

        // IRS — RETENÇÕES NA FONTE (se existirem)
        $irsWithheld = (float) Expense::where('workspace_id', $workspace->id)
            ->where('type', 'irs_withheld')
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('amount');

        // TOTAL DE PROVISÃO
        $totalTaxDebt = max(0, $vatNet) + $tsuEstimate + $ircProvision + $derrama + $irsWithheld;

        return view('livewire.business.tax-hub', [
            'vatNet' => $vatNet,
            'vatCollected' => $vatCollected,
            'vatDeductible' => $vatDeductible,
            'tsuEstimate' => $tsuEstimate,
            'ircProvision' => $ircProvision,
            'derrama' => $derrama,
            'irsWithheld' => $irsWithheld,
            'totalTaxDebt' => $totalTaxDebt,
        ]);
    }
}
