<?php

namespace App\Livewire\Public;

use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BankPortal extends Component
{
    public $company_nif = '';

    public $token = '';       // Código de Auditoria

    #[Layout('layouts.guest')]
    public function login()
    {
        $this->validate([
            'company_nif' => 'required',
            'token' => 'required|string|min:32|max:128',
        ]);

        $cleanNifInput = preg_replace('/[^0-9]/', '', $this->company_nif);
        $rateLimitKey = 'bank-portal:'.sha1($cleanNifInput.'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            session()->flash('error', 'CREDENCIAIS INVÁLIDAS OU TOKEN EXPIRADO.');

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        $cleanTokenInput = trim($this->token);

        $workspace = Workspace::whereRaw("REPLACE(REPLACE(REPLACE(tax_number, ' ', ''), '.', ''), '-', '') = ?", [$cleanNifInput])
            ->where('audit_token_purpose', 'bank_audit')
            ->whereNull('audit_token_revoked_at')
            ->where(function ($query) {
                $query->whereNull('audit_token_expires_at')
                    ->orWhere('audit_token_expires_at', '>', now());
            })
            ->first();

        if ($workspace && Hash::check($cleanTokenInput, $workspace->audit_token)) {
            RateLimiter::clear($rateLimitKey);
            session()->put('bank_portal_workspace_id', $workspace->id);

            return redirect()->route('bank.dashboard');
        }

        session()->flash('error', 'CREDENCIAIS INVÁLIDAS OU TOKEN EXPIRADO.');
    }

    public function render()
    {
        return view('livewire.public.bank-portal');
    }
}
