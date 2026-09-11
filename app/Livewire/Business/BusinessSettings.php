<?php

namespace App\Livewire\Business;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class BusinessSettings extends Component
{
    use WithFileUploads;

    public $workspace;

    public $name;

    public $legal_name;

    public $tax_number;

    public $industry;

    public $business_email;

    public $address;

    public $currency;

    public $initial_capital;

    public $logo;

    public function mount()
    {
        $this->workspace = auth()->user()->currentWorkspace;

        if (! $this->workspace) {
            return redirect()->route('hub.business.gateway');
        }

        $this->name = $this->workspace->name;
        $this->legal_name = $this->workspace->legal_name;
        $this->tax_number = $this->formatTaxNumber($this->workspace->tax_number);
        $this->industry = $this->workspace->industry;
        $this->business_email = $this->workspace->business_email;
        $this->address = $this->workspace->address;
        $this->currency = $this->workspace->currency ?? 'EUR';
        $this->initial_capital = (float) $this->workspace->initial_capital;
    }

    public function updatedTaxNumber($value)
    {
        $this->tax_number = $this->formatTaxNumber($value);
    }

    private function formatTaxNumber($value): string
    {
        $digits = substr(preg_replace('/\D/', '', (string) $value), 0, 9);

        return implode(' ', str_split($digits, 3));
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'legal_name' => 'nullable|string|max:200',
            'tax_number' => 'nullable|string|max:11',
            'business_email' => 'required|email:rfc|max:255',
            'logo' => 'nullable|image|max:2048',
            'initial_capital' => 'numeric|min:0',
        ], [
            'business_email.required' => 'O email da empresa é obrigatório.',
            'business_email.email' => 'Introduz um email empresarial válido.',
        ]);

        $data = [
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'tax_number' => preg_replace('/\D/', '', (string) $this->tax_number),
            'industry' => $this->industry,
            'business_email' => strtolower(trim($this->business_email)),
            'address' => $this->address,
            'currency' => $this->currency,
            'initial_capital' => $this->initial_capital,
        ];

        if ($this->logo) {
            if ($this->workspace->logo_path) {
                $oldLogo = preg_replace('#^/?storage/#', '', $this->workspace->logo_path);
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
            }

            $data['logo_path'] = $this->logo->store('logos', 'public');
            $this->logo = null;
        }

        $this->workspace->update($data);
        $this->tax_number = $this->formatTaxNumber($data['tax_number']);
        $this->dispatch('toast', text: 'Dados da empresa atualizados com sucesso!', variant: 'success');
    }

    public function getLogoUrlAttribute()
    {
        return $this->workspace->logo_url ?: asset('images/default-logo.png');
    }

    public function leaveCompany()
    {
        $user = auth()->user();
        $this->workspace->users()->detach($user->id);
        $user->update(['current_workspace_id' => null]);
        $this->dispatch('toast', variant: 'success', heading: 'Sessão Terminada', message: 'Saíste da equipa com sucesso.');

        return redirect()->route('hub.business.gateway');
    }

    public function deleteCompany()
    {
        if (! auth()->user()->isOwner()) {
            abort(403);
        }

        $user = auth()->user();
        $user->update(['current_workspace_id' => null]);
        $this->workspace->employees()->delete();
        $this->workspace->delete();
        $this->dispatch('toast', variant: 'success', heading: 'Empresa Eliminada', message: 'O teu plano Business continua ativo.');

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
