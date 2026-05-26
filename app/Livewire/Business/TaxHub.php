<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Employee;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB; // IMPORTAÇÃO QUE FALTAVA

#[Layout('components.layouts.app')]
class TaxHub extends Component
{
    public function render()
    {
        $user = auth()->user();
        $month = now()->month;
        $year = now()->year;

        // 1. IVA DAS VENDAS (Baseado nas Faturas emitidas)
        $vatCollected = (float) $user->invoices()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('vat_amount');

        // 2. IVA DAS COMPRAS (Baseado nas Despesas de Empresa)
        $vatDeductible = (float) $user->expenses()
            ->where('is_company', true)
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('vat_amount');

        // 3. SEGURANÇA SOCIAL (TSU - 23.75% sobre os salários brutos)
        $totalSalaries = (float) $user->employees()->sum('salary');
        $tsuEstimate = $totalSalaries * 0.2375;

        // 4. PROVISÃO DE IRC (21% sobre o lucro estimado)
        $revenue = (float) $user->invoices()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount_excl_vat');

        $expenses = (float) $user->expenses()
            ->where('is_company', true)
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('amount');

        $estimatedProfit = max(0, $revenue - $expenses - $totalSalaries);
        $ircProvision = $estimatedProfit * 0.21;

        $vatNet = $vatCollected - $vatDeductible;

        return view('livewire.business.tax-hub', [
            'vatNet' => $vatNet,
            'vatCollected' => $vatCollected,
            'vatDeductible' => $vatDeductible,
            'tsuEstimate' => $tsuEstimate,
            'ircProvision' => $ircProvision,
            'totalTaxDebt' => max(0, $vatNet) + $tsuEstimate + $ircProvision
        ]);
    }
}
