<?php

namespace App\Livewire\Business;

use App\Services\BusinessAccessService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class BusinessSettings extends Component
{
    use WithFileUploads;

    public $workspace, $name, $legal_name, $tax_number, $industry, $business_email, $address, $currency, $initial_capital, $logo;
    public $country_code = 'PT', $vat_rate = 23, $vat_regime = 'normal', $fiscal_year_start = 1;

    public function mount()
    {
        $this->workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_settings', auth()->user(), $this->workspace);

        $this->name = $this->workspace->name;
        $this->legal_name = $this->workspace->legal_name;
        $this->tax_number = $this->formatTaxNumber($this->workspace->tax_number);
        $this->industry = $this->workspace->industry;
        $this->business_email = $this->workspace->business_email;
        $this->address = $this->workspace->address;
        $this->currency = $this->workspace->currency ?? 'EUR';
        $this->initial_capital = (float) $this->workspace->initial_capital;
        $this->country_code = strtoupper((string) ($this->workspace->country_code ?? 'PT'));
        $this->vat_rate = (float) ($this->workspace->vat_rate ?? 23);
        $this->vat_regime = (string) ($this->workspace->vat_regime ?? 'normal');
        $this->fiscal_year_start = (int) ($this->workspace->fiscal_year_start ?? 1);
    }

    public function updatedTaxNumber($value): void { $this->tax_number = $this->formatTaxNumber($value); }

    private function formatTaxNumber($value): string
    {
        return implode(' ', str_split(substr(preg_replace('/\D/', '', (string) $value), 0, 9), 3));
    }

    public function save(): void
    {
        app(BusinessAccessService::class)->assert('manage_settings', auth()->user(), $this->workspace);

        $this->validate([
            'name' => 'required|string|max:100',
            'legal_name' => 'nullable|string|max:200',
            'tax_number' => 'nullable|string|max:11',
            'business_email' => 'required|email:rfc|max:255',
            'logo' => 'nullable|image|max:2048',
            'initial_capital' => 'numeric|min:0',
            'currency' => 'required|string|size:3|in:EUR,USD,GBP,CHF,BRL,JPY',
            'country_code' => 'required|string|size:2|alpha',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'vat_regime' => 'required|string|in:normal,isento,caixa',
            'fiscal_year_start' => 'required|integer|between:1,12',
        ], [
            'business_email.required' => 'O email da empresa é obrigatório.',
            'business_email.email' => 'Introduz um email empresarial válido.',
            'country_code.size' => 'O país deve usar o código ISO de 2 letras.',
        ]);

        $data = [
            'name' => trim($this->name),
            'legal_name' => $this->legal_name,
            'tax_number' => preg_replace('/\D/', '', (string) $this->tax_number),
            'industry' => $this->industry,
            'business_email' => strtolower(trim($this->business_email)),
            'address' => $this->address,
            'currency' => strtoupper($this->currency),
            'initial_capital' => round((float) $this->initial_capital, 2),
            'country_code' => strtoupper($this->country_code),
            'vat_rate' => round((float) $this->vat_rate, 2),
            'vat_regime' => $this->vat_regime,
            'fiscal_year_start' => (int) $this->fiscal_year_start,
        ];

        if ($this->logo) {
            if ($this->workspace->logo_path) {
                $oldLogo = preg_replace('#^/?storage/#', '', $this->workspace->logo_path);
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) Storage::disk('public')->delete($oldLogo);
            }
            $data['logo_path'] = $this->logo->store('logos', 'public');
            $this->logo = null;
        }

        $this->workspace->update($data);
        $this->tax_number = $this->formatTaxNumber($data['tax_number']);
        $this->dispatch('toast', text: 'Dados da empresa atualizados com sucesso!', variant: 'success');
    }

    public function getLogoUrlAttribute() { return $this->workspace->logo_url ?: asset('images/default-logo.png'); }

    public function leaveCompany()
    {
        $user = auth()->user();
        if ((int) $this->workspace->owner_id === (int) $user->id) abort(403, 'O proprietário deve transferir a propriedade antes de sair.');
        $this->workspace->users()->detach($user->id);
        $user->update(['current_workspace_id' => null]);
        return redirect()->route('hub.business.gateway');
    }

    public function deleteCompany()
    {
        app(BusinessAccessService::class)->assert('manage_settings', auth()->user(), $this->workspace);
        abort_unless((int) $this->workspace->owner_id === (int) auth()->id(), 403);

        $user = auth()->user();
        $user->update(['current_workspace_id' => null]);
        $this->workspace->delete();
        return redirect()->route('hub.business.gateway');
    }

    public function render()
    {
        return view('livewire.business.business-settings', [
            'runway' => $this->workspace->getRunway(),
            'burnRate' => $this->workspace->getBurnRate(),
            'cash' => $this->workspace->getLiquidezAtual(),
        ]);
    }
}
