<?php

namespace App\Livewire\Public;

use App\Mail\BankAccessRequestMail;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BankPortal extends Component
{
    public $company_nif = '';
    public $token = '';
    public $companySearch = '';
    public $selectedCompanyId = null;
    public $requestEmail = '';
    public $requestSent = false;

    #[Layout('layouts.guest')]
    public function login()
    {
        $this->validate([
            'company_nif' => 'required',
            'token' => 'required|string|size:8',
        ]);

        $cleanNifInput = preg_replace('/[^0-9]/', '', $this->company_nif);
        $rateLimitKey = 'bank-portal:'.sha1($cleanNifInput.'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            session()->flash('error', 'CREDENCIAIS INVÁLIDAS.');
            return;
        }

        RateLimiter::hit($rateLimitKey, 60);
        $cleanTokenInput = strtoupper(trim($this->token));

        $workspace = Workspace::whereRaw("REPLACE(REPLACE(REPLACE(tax_number, ' ', ''), '.', ''), '-', '') = ?", [$cleanNifInput])
            ->where('audit_token_purpose', 'bank_audit')
            ->whereNull('audit_token_revoked_at')
            ->where('audit_access_code', $cleanTokenInput)
            ->first();

        if ($workspace && Hash::check($cleanTokenInput, $workspace->audit_token)) {
            RateLimiter::clear($rateLimitKey);
            session()->put('bank_portal_workspace_id', $workspace->id);
            return redirect()->route('bank.dashboard');
        }

        session()->flash('error', 'CREDENCIAIS INVÁLIDAS.');
    }

    public function selectCompany(int $companyId): void
    {
        $exists = Workspace::whereKey($companyId)->where('type', 'company')->exists();
        if (! $exists) {
            $this->selectedCompanyId = null;
            return;
        }
        $this->selectedCompanyId = $companyId;
        $this->requestSent = false;
    }

    public function clearSelectedCompany(): void
    {
        $this->selectedCompanyId = null;
        $this->requestSent = false;
    }

    public function sendAccessRequest(): void
    {
        $this->validate([
            'selectedCompanyId' => 'required|integer|exists:workspaces,id',
            'requestEmail' => 'required|email:rfc|max:255',
        ], [
            'selectedCompanyId.required' => 'Seleciona uma empresa.',
            'requestEmail.required' => 'Introduz o email de destino.',
            'requestEmail.email' => 'Introduz um email válido.',
        ]);

        $workspace = Workspace::whereKey($this->selectedCompanyId)->where('type', 'company')->first();
        if (! $workspace) {
            $this->addError('selectedCompanyId', 'A empresa selecionada não está disponível.');
            return;
        }

        Mail::to($this->requestEmail)->send(new BankAccessRequestMail($workspace));
        $this->requestSent = true;
        $this->requestEmail = '';
    }

    #[\Livewire\Attributes\Computed]
    public function companies()
    {
        return Workspace::query()
            ->where('type', 'company')
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->companySearch.'%')
                    ->orWhere('legal_name', 'like', '%'.$this->companySearch.'%');
            })
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name', 'legal_name']);
    }

    public function render()
    {
        return view('livewire.public.bank-portal');
    }
}
