<?php

namespace App\Livewire\Public;

use App\Mail\BankAccessRequestMail;
use App\Models\BankAccessRequest;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BankPortal extends Component
{
    public $company_nif = '';
    public $token = '';
    public $companySearch = '';
    public $selectedCompanyId = null;
    public $bankName = '';
    public $requestEmail = '';
    public $requestSent = false;

    #[Layout('layouts.guest')]
    public function login()
    {
        $cleanNifInput = preg_replace('/[^0-9]/', '', (string) $this->company_nif);
        $rateLimitKey = 'bank-portal:'.sha1($cleanNifInput.'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            session()->flash('error', 'CREDENCIAIS INVÁLIDAS.');
            return;
        }

        RateLimiter::hit($rateLimitKey, 60);
        $this->validate([
            'company_nif' => 'required',
            'token' => 'required|string|min:8|max:255',
        ]);

        // Tokens são case-sensitive: nunca alterar a capitalização antes do Hash::check().
        $cleanTokenInput = trim((string) $this->token);

        $workspace = Workspace::whereRaw("REPLACE(REPLACE(REPLACE(tax_number, ' ', ''), '.', ''), '-', '') = ?", [$cleanNifInput])
            ->where('audit_token_purpose', 'bank_audit')
            ->whereNull('audit_token_revoked_at')
            ->where(function ($query) {
                $query->whereNull('audit_token_expires_at')->orWhere('audit_token_expires_at', '>', now());
            })
            ->whereNotNull('audit_token')
            ->first();

        if ($workspace && Hash::check($cleanTokenInput, (string) $workspace->audit_token)) {
            RateLimiter::clear($rateLimitKey);
            session()->put('bank_portal_workspace_id', $workspace->id);
            return redirect()->route('bank.dashboard');
        }

        session()->flash('error', 'CREDENCIAIS INVÁLIDAS.');
    }

    public function selectCompany(int $companyId): void
    {
        $exists = Workspace::whereKey($companyId)->whereIn('type', ['business', 'company', 'bussiness'])->exists();
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

    private function isInstitutionalEmail(string $email): bool
    {
        $domain = strtolower((string) substr(strrchr($email, '@') ?: '', 1));
        $freeProviders = ['gmail.com', 'googlemail.com', 'hotmail.com', 'outlook.com', 'live.com', 'msn.com', 'yahoo.com', 'yahoo.pt', 'icloud.com', 'me.com', 'aol.com', 'proton.me', 'protonmail.com', 'gmx.com', 'mail.com', 'sapo.pt', 'iol.pt'];
        return $domain !== '' && ! in_array($domain, $freeProviders, true) && str_contains($domain, '.');
    }

    public function sendAccessRequest(): void
    {
        $this->validate([
            'selectedCompanyId' => 'required|integer|exists:workspaces,id',
            'bankName' => 'required|string|min:2|max:150',
            'requestEmail' => 'required|email:rfc|max:255',
        ], [
            'selectedCompanyId.required' => 'Seleciona uma empresa.',
            'bankName.required' => 'Indica o nome do banco.',
            'requestEmail.required' => 'Introduz o email institucional do banco.',
            'requestEmail.email' => 'Introduz um email válido.',
        ]);

        if (! $this->isInstitutionalEmail($this->requestEmail)) {
            $this->addError('requestEmail', 'É necessário utilizar um email institucional do banco.');
            return;
        }

        $workspace = Workspace::whereKey($this->selectedCompanyId)->whereIn('type', ['business', 'company', 'bussiness'])->first();
        if (! $workspace) {
            $this->addError('selectedCompanyId', 'A empresa selecionada não está disponível.');
            return;
        }
        if (! filled($workspace->business_email)) {
            $this->addError('selectedCompanyId', 'Esta empresa ainda não tem um email empresarial configurado.');
            return;
        }

        $pending = BankAccessRequest::where('workspace_id', $workspace->id)
            ->where('bank_email', strtolower(trim($this->requestEmail)))
            ->where('status', 'pending')->exists();
        if ($pending) {
            $this->addError('requestEmail', 'Já existe um pedido pendente deste banco para esta empresa.');
            return;
        }

        $request = BankAccessRequest::create([
            'workspace_id' => $workspace->id,
            'bank_name' => trim($this->bankName),
            'bank_email' => strtolower(trim($this->requestEmail)),
            'status' => 'pending',
            'requested_at' => now(),
        ]);
        Mail::to($workspace->business_email)->send(new BankAccessRequestMail($workspace, $request));
        $this->requestSent = true;
        $this->requestEmail = '';
        $this->bankName = '';
    }

    #[Computed]
    public function companies()
    {
        return Workspace::query()->whereIn('type', ['business', 'company', 'bussiness'])
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->companySearch.'%')->orWhere('legal_name', 'like', '%'.$this->companySearch.'%');
            })->orderBy('name')->limit(100)->get(['id', 'name', 'legal_name', 'business_email']);
    }

    public function render()
    {
        return view('livewire.public.bank-portal');
    }
}
