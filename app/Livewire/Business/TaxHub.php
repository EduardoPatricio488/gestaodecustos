<?php

namespace App\Livewire\Business;

use App\Models\Expense;
use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TaxHub extends Component
{
    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        if (! $workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500">Nenhum workspace empresarial selecionado.</div>
            HTML;
        }

        $month = now()->month;
        $year = now()->year;

        $vatCollected = (float) $workspace->invoices()
            ->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('vat_amount');

        $vatDeductible = (float) $workspace->expenses()
            ->where('is_company', true)->whereMonth('spent_at', $month)->whereYear('spent_at', $year)->sum('vat_amount');

        $vatNet = round($vatCollected - $vatDeductible, 2);

        // Não estimamos TSU, IRS, IRC ou derrama sem dados e regras fiscais suficientes.
        // Isto evita apresentar uma estimativa como se fosse uma obrigação legal real.
        return view('livewire.business.tax-hub', [
            'vatNet' => $vatNet,
            'vatCollected' => round($vatCollected, 2),
            'vatDeductible' => round($vatDeductible, 2),
            'tsuEstimate' => 0,
            'ircProvision' => 0,
            'derrama' => 0,
            'irsWithheld' => 0,
            'totalTaxDebt' => max(0, $vatNet),
            'taxDisclaimer' => 'Os valores de IVA apresentados resultam apenas dos montantes de IVA registados. TSU, IRS, IRC, derrama e obrigações fiscais oficiais não são calculados automaticamente nesta área. Confirma os valores com o contabilista.',
        ]);
    }
}
