<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Workspace;
use Illuminate\Support\Str;

class BusinessOnboarding extends Component
{
    use WithFileUploads;

    public $step = 1;

    // Dados da Empresa
    public $name;
    public $industry;
    public $tax_number;
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
        ]
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

        // 1. Criar o Workspace do tipo Business
        $workspace = Workspace::create([
            'name' => $this->name,
            'owner_id' => $user->id,
            'type' => 'business',
            'industry' => $this->industry,
            'tax_number' => $this->tax_number,
            'currency' => $this->currency,
            'initial_capital' => $this->initial_capital,
            'invite_code' => strtoupper(Str::random(8)),
        ]);

        // 2. Processar a Foto se existir
        if ($this->photo) {
            $path = $this->photo->store('workspaces/logos', 'public');
            $workspace->update(['logo_path' => '/storage/' . $path]);
        }

        // 3. Associar o utilizador como Admin
        $user->workspaces()->attach($workspace->id, ['role' => 'admin']);

        // 4. Mudar para este workspace e ir para o Dashboard
        $user->update(['current_workspace_id' => $workspace->id]);

        return redirect()->route('hub.business.dashboard');
    }

    public function render()
    {
        return view('livewire.business.business-onboarding')->layout('components.layouts.app');
    }
}

