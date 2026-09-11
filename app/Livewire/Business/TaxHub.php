<?php

namespace App\Livewire\Business;

use App\Services\BusinessAccessService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TaxHub extends Component
{
    public function render()
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $vatCollected = (float) $workspace->invoices()
            ->where('status', 'paga')
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->sum('vat_amount');

        $vatRecordedOnExpenses = (float) $workspace->expenses()
            ->where('is_company', true)
            ->whereBetween('spent_at', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('vat_amount');

        $vatNet = round($vatCollected - $vatRecordedOnExpenses, 2);

        return view('livewire.business.tax-hub', [
            'vatNet' => $vatNet,
            'vatCollected' => round($vatCollected, 2),
            'vatDeductible' => round($vatRecordedOnExpenses, 2),
            'tsuEstimate' => 0,
            'ircProvision' => 0,
            'derrama' => 0,
            'irsWithheld' => 0,
            'vatRate' => (float) ($workspace->vat_rate ?? 23),
            'vatRegime' => (string) ($workspace->vat_regime ?? 'normal'),
            'countryCode' => strtoupper((string) ($workspace->country_code ?? 'PT')),
            'taxDisclaimer' => 'O IVA apresentado é uma métrica informativa baseada nos valores de IVA registados e nos pagamentos registados. O IVA efetivamente dedutível, o momento de exigibilidade e as restantes obrigações dependem do regime fiscal, da natureza das operações e da documentação válida. TSU, IRS, IRC, derrama e obrigações oficiais não são calculados automaticamente. Confirma os valores com o contabilista.',
        ]);
    }
}
