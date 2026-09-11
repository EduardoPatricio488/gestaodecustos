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

    public $business_email;

    public $customIndustry;

    public $photo;

    // Valores Iniciais
    public $initial_capital = 0;

    public $currency = 'EUR';

    protected $messages = [
        'tax_number.required' => 'O NIF é obrigatório.',
        'tax_number.regex' => 'O NIF deve conter exatamente 9 dígitos, no formato 123 456 789.',
        'business_email.required' => 'O email da empresa é obrigatório.',
        'business_email.email' => 'Introduza um endereço de email válido.',
    ];

    protected $rules = [
        2 => [
            'name' => 'required|min:3|max:50',
            'industry' => 'required',
            'tax_number' => 'required|regex:/^\d{3} \d{3} \d{3}$/',
            'business_email' => 'required|email:rfc|max:255',
        ],
        3 => [
            'initial_capital' => 'required|numeric|min:0',
        ],
    ];

    public function updatedTaxNumber($value)
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        $digits = substr($digits, 0, 9);

        $this->tax_number = trim(implode(' ', str_split($digits, 3)));
    }

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
        $this->validate($this->rules[2]);
        $user = auth()->user();

        $finalIndustry = ($this->industry === 'Outro')
            ? $this->customIndustry
            : $this->industry;

        $taxNumber = preg_replace('/\D/', '', (string) $this->tax_number);

        $workspace = Workspace::create([
            'name' => $this->name,
            'owner_id' => $user->id,
            'type' => 'business',
            'industry' => $finalIndustry,
            'tax_number' => $taxNumber ?: null,
            'business_email' => strtolower(trim($this->business_email)),
            'currency' => $this->currency ?? 'EUR',
            'initial_capital' => (float) ($this->initial_capital ?? 0),
            'invite_code' => strtoupper(Str::random(8)),
            'plan' => 'business',
        ]);

        if ($this->photo) {
            $path = $this->photo->store('workspaces/logos', 'public');
            $workspace->update(['logo_path' => $path]);
        }

        $user->workspaces()->attach($workspace->id, ['role' => 'admin']);
        $user->update(['current_workspace_id' => $workspace->id]);

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
