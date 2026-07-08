<?php

namespace App\Livewire\Public;

use App\Models\Workspace;
use Livewire\Component;
use Livewire\Attributes\Layout;

class BankPortal extends Component
{
    public $company_nif = '';
    public $token = '';       // Código de Auditoria

    #[Layout('layouts.guest')]
   public function login()
{
    $this->validate([
        'company_nif' => 'required',
        'token'       => 'required|size:6',
    ]);

    // 1. Limpar rigorosamente o input (deixar apenas números)
    $cleanNifInput = preg_replace('/[^0-9]/', '', $this->company_nif);
    $cleanTokenInput = trim($this->token);

    // 2. Procurar na tabela workspaces limpando também o que está na BD
    // Isto garante que '509882314' encontre '509 882 314' ou '509.882.314'
    $workspace = \App\Models\Workspace::whereRaw("REPLACE(REPLACE(REPLACE(tax_number, ' ', ''), '.', ''), '-', '') = ?", [$cleanNifInput])
                        ->where('audit_token', $cleanTokenInput)
                        ->first();

    if ($workspace) {
        return redirect()->route('bank.dashboard', ['token' => $workspace->audit_token]);
    }

    session()->flash('error', 'CREDENCIAIS INVÁLIDAS OU TOKEN EXPIRADO.');
}

    public function render()
    {
        return view('livewire.public.bank-portal');
    }
}
