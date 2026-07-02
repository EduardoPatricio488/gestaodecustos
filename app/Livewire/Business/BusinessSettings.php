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

    // Campos do formulário
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

        // Preencher campos
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
     * Guardar dados do perfil empresarial (Versão Diamante)
     */
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'legal_name' => 'nullable|string|max:200',
            'tax_number' => 'nullable|string|max:20',
            'business_email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
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

        // Gestão de logótipo
        if ($this->logo) {

            // Apagar logo antigo
            if ($this->workspace->logo_path) {
                Storage::disk('public')->delete($this->workspace->logo_path);
            }

            // Guardar novo logo
            $data['logo_path'] = $this->logo->store('logos', 'public');

            // Limpar variável para evitar erro 500
            $this->logo = null;
        }

        // Atualizar workspace
        $this->workspace->update($data);

        // Toast premium
        $this->dispatch('toast', text: 'Dados da empresa atualizados com sucesso!', variant: 'success');
    }

    /**
     * Getter de URL do logo (fallback incluído)
     */
    public function getLogoUrlAttribute()
    {
        return $this->workspace->logo_path
            ? asset('storage/' . $this->workspace->logo_path)
            : asset('images/default-logo.png');
    }

    /**
     * Renderização com métricas empresariais Diamante
     */ public function leaveCompany()
    {
        $user = auth()->user();

        // Remove a ligação na tabela pivot
        $this->workspace->users()->detach($user->id);

        // Limpa o workspace atual do utilizador
        $user->update(['current_workspace_id' => null]);

        $this->dispatch('toast', variant: 'success', heading: 'Sessão Terminada', message: 'Saíste da equipa com sucesso.');

        return redirect()->route('hub.business.gateway');
    }

    /**
     * Apagar Empresa (Apenas para o Dono)
     */
    public function deleteCompany()
{
    if (!auth()->user()->isOwner()) {
        abort(403);
    }

    $user = auth()->user();

    // Limpar apenas a referência ao workspace; o plano do utilizador mantém-se intacto
    $user->update([
        'current_workspace_id' => null
    ]);

    // Apagar a empresa
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
