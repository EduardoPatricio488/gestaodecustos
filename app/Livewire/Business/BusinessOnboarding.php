<?php

namespace App\Livewire\Business;

use App\Mail\WelcomeBusinessMail;
use App\Models\Workspace;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class BusinessOnboarding extends Component
{
    use WithFileUploads;

    public $step = 1;

    // Dados da Empresa
    public $name;

    public $industry;

    public $tax_number;

    public $customIndustry; // 🔥 Adiciona esta propriedade

    public $photo;

    // Valores Iniciais
    public $initial_capital = 0;

    public $currency = 'EUR';

    protected $rules = [
        2 => [
            'name' => 'required|min:3|max:50',
            'industry' => 'required',
            'tax_number' => 'nullable|numeric',
        ],
        3 => [
            'initial_capital' => 'required|numeric|min:0',
        ],
    ];

    public function nextStep()
    {
        if (isset($this->rules[$this->step])) {
            $this->validate($this->rules[$this->step]);
        }
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function createCompany()
    {
        $user = auth()->user();

        // 1. Lógica de Indústria Personalizada
        $finalIndustry = ($this->industry === 'Outro')
            ? $this->customIndustry
            : $this->industry;

        // 2. Criar o Workspace do tipo Business
        $workspace = Workspace::create([
            'name' => $this->name,
            'owner_id' => $user->id,
            'type' => 'business',
            'industry' => $finalIndustry,
            'tax_number' => $this->tax_number,
            'currency' => $this->currency ?? 'EUR',
            'initial_capital' => (float) ($this->initial_capital ?? 0),
            'invite_code' => strtoupper(Str::random(8)),
            'plan' => 'pro',
        ]);

        // 3. Processar a Foto se existir
        if ($this->photo) {
            $path = $this->photo->store('workspaces/logos', 'public');
            $workspace->update(['logo_path' => 'storage/'.$path]);
        }

        // 4. Associar o utilizador como Admin e atualizar contexto
        $user->workspaces()->attach($workspace->id, ['role' => 'admin']);
        $user->update(['current_workspace_id' => $workspace->id]);

        // 🔥 5. DISPARAR E-MAIL DE BOAS-VINDAS
        // Usamos um try-catch para o site não crashar se o Mailpit estiver desligado
        try {
            Mail::to($user->email)->send(new WelcomeBusinessMail($workspace));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar e-mail business: '.$e->getMessage());
        }

        $this->dispatch('toast', text: 'Empresa ativada! Enviamos um guia para o seu e-mail. 🏢');

        return redirect()->route('hub.business.dashboard');
    }

    public function render()
    {
        return view('livewire.business.business-onboarding')->layout('components.layouts.app');
    }
}
