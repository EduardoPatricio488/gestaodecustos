<?php

namespace App\Livewire\Business;

use App\Mail\PortalAccessRequestMail;
use App\Models\Client;
use App\Models\PortalAccessRequest;
use App\Models\Workspace;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClientLogin extends Component
{
    public $tax_number = '';

    public $token = '';

    public $requesterName = '';

    public $requesterEmail = '';

    public $requestTaxNumber = '';

    public $companySearch = '';

    public $selectedCompanyId = null;

    public $requestSent = false;

    #[Layout('layouts.guest')]
    public function login()
    {
        $this->validate([
            'tax_number' => 'required|string',
            'token' => ['required', 'string', 'min:64', 'max:64', 'regex:/^[A-Za-z0-9]+$/'],
        ]);

        $cleanNifInput = preg_replace('/\s+/', '', $this->tax_number);
        $cleanTokenInput = preg_replace('/\s+/', '', $this->token);
        $rateLimitKey = 'client-portal-login:'.sha1($cleanNifInput.'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            session()->flash('error', 'Demasiadas tentativas. Tenta novamente mais tarde.');

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        $client = Client::whereHas('workspace', function ($query) use ($cleanNifInput) {
            $query->whereIn('type', ['business', 'company', 'bussiness'])
                ->whereRaw("REPLACE(tax_number, ' ', '') = ?", [$cleanNifInput]);
        })
            ->where('portal_token_hash', hash('sha256', $cleanTokenInput))
            ->first();

        if ($client) {
            RateLimiter::clear($rateLimitKey);
            session()->regenerate();

            session()->put('client_portal_id', $client->id);

            return redirect()->route('client.portal');
        }

        session()->flash('error', 'CREDENCIAIS INVÁLIDAS. VERIFICA O NIF DA EMPRESA E O CÓDIGO.');
    }

    public function selectCompany(int $companyId): void
    {
        $exists = Workspace::whereKey($companyId)
            ->whereIn('type', ['business', 'company', 'bussiness'])
            ->exists();

        $this->selectedCompanyId = $exists ? $companyId : null;
        $this->requestSent = false;
    }

    public function sendAccessRequest(): void
    {
        $this->validate([
            'selectedCompanyId' => 'required|integer|exists:workspaces,id',
            'requesterName' => 'required|string|min:2|max:150',
            'requesterEmail' => 'required|email:rfc|max:255',
            'requestTaxNumber' => 'nullable|string|max:30',
        ], [
            'selectedCompanyId.required' => 'Seleciona a empresa.',
            'requesterName.required' => 'Indica o teu nome.',
            'requesterEmail.required' => 'Indica o teu email.',
        ]);

        $email = strtolower(trim($this->requesterEmail));
        $ipKey = 'client-portal-request-ip:'.sha1((string) request()->ip());
        $rateLimitKey = 'client-portal-request:'.sha1($email.'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $this->addError('requesterEmail', 'Demasiados pedidos. Tenta novamente mais tarde.');

            return;
        }

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->addError('requesterEmail', 'Demasiados pedidos. Tenta novamente mais tarde.');

            return;
        }

        RateLimiter::hit($ipKey, 300);
        RateLimiter::hit($rateLimitKey, 300);

        $workspace = Workspace::whereKey($this->selectedCompanyId)
            ->whereIn('type', ['business', 'company', 'bussiness'])
            ->first();

        if (! $workspace || ! filled($workspace->business_email)) {
            $this->addError('selectedCompanyId', 'Esta empresa ainda não tem um email empresarial configurado.');

            return;
        }

        $pending = PortalAccessRequest::where('workspace_id', $workspace->id)
            ->where('portal_type', 'client')
            ->where('requester_email', $email)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->addError('requesterEmail', 'Já existe um pedido pendente deste email para esta empresa.');

            return;
        }

        $request = PortalAccessRequest::create([
            'workspace_id' => $workspace->id,
            'portal_type' => 'client',
            'requester_name' => trim($this->requesterName),
            'requester_email' => $email,
            'tax_number' => trim($this->requestTaxNumber) ?: null,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        Mail::to($workspace->business_email)->send(new PortalAccessRequestMail($workspace, $request));

        $this->requestSent = true;
        $this->requesterName = '';
        $this->requesterEmail = '';
        $this->requestTaxNumber = '';
    }

    #[Computed]
    public function companies()
    {
        if (mb_strlen(trim((string) $this->companySearch)) < 2) {
            return collect();
        }

        return Workspace::query()
            ->whereIn('type', ['business', 'company', 'bussiness'])
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->companySearch.'%')
                    ->orWhere('legal_name', 'like', '%'.$this->companySearch.'%');
            })
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'legal_name']);
    }

    public function render()
    {
        return view('livewire.business.client-login');
    }
}
