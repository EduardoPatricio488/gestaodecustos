<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use App\Models\Workspace;

#[Layout('components.layouts.app')]
class BusinessSettings extends Component
{
    use WithFileUploads;

    public $workspace;

    // Propriedades do Formulário
    public $name, $legal_name, $tax_number, $industry, $business_email, $address;
    public $currency, $initial_capital, $logo;

    public function mount()
    {
        // Carrega o workspace atual do utilizador
        $this->workspace = auth()->user()->currentWorkspace;

        $this->name = $this->workspace->name;
        $this->legal_name = $this->workspace->legal_name;
        $this->tax_number = $this->workspace->tax_number;
        $this->industry = $this->workspace->industry;
        $this->business_email = $this->workspace->business_email;
        $this->address = $this->workspace->address;
        $this->currency = $this->workspace->currency ?? 'EUR';
        $this->initial_capital = (float) $this->workspace->initial_capital;
    }

    /**
     * Lógica de Gravação Super Desenvolvida
     */
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'legal_name' => 'nullable|string|max:200',
            'tax_number' => 'nullable|string|max:20', // Pode adicionar regex de NIF aqui
            'business_email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048', // Aceita até 2MB
            'initial_capital' => 'numeric|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'tax_number' => $this->tax_number,
            'industry' => $this->industry,
            'business_email' => $this->business_email,
            'address' => $this->address,
            'currency' => $this->currency,
            'initial_capital' => $this->initial_capital,
        ];

        // GESTÃO DE LOGÓTIPO
        if ($this->logo) {
            // 1. Remover logo antigo do disco para não encher o servidor
            if ($this->workspace->logo_path) {
                Storage::disk('public')->delete($this->workspace->logo_path);
            }

            // 2. Guardar o novo
            $data['logo_path'] = $this->logo->store('logos', 'public');

            // 3. LIMPAR A VARIÁVEL (Resolve o erro 500 que tiveste)
            $this->logo = null;
        }

        $this->workspace->update($data);

        $this->dispatch('toast', text: 'Dados da empresa atualizados!', variant: 'success');
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
