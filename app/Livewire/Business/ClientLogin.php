<?php

namespace App\Livewire\Business;

use App\Mail\PortalAccessRequestMail;
use App\Models\Client;
use App\Models\PortalAccessRequest;
use App\Models\Workspace;
use Illuminate\Support\Facades\Mail;
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
            'token' => 'required|string|size:6',
        ]);

        $cleanNifInput = preg_replace('/\s+/', '', $this->tax_number);
        $cleanTokenInput = preg_replace('/\s+/', '', $this->token);

        $client = Client::whereRaw("REPLACE(tax_number, ' ', '') = ?", [$cleanNifInput])
            ->where('portal_token', $cleanTokenInput)
            ->first();

        if ($client) {
            return redirect()->route('client.portal', ['token' => $client->portal_token]);
        }

        session()->flash('error', 'CREDENCIAIS INVÁLIDAS. VERIFICA O NIF E O CÓDIGO.');
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

        $workspace = Workspace::whereKey($this->selectedCompanyId)
            ->whereIn('type', ['business', 'company', 'bussiness'])
            ->first();

        if (! $workspace || ! filled($workspace->business_email)) {
            $this->addError('selectedCompanyId', 'Esta empresa ainda não tem um email empresarial configurado.');

            return;
        }

        $email = strtolower(trim($this->requesterEmail));
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
        return Workspace::query()
            ->whereIn('type', ['business', 'company', 'bussiness'])
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->companySearch.'%')
                    ->orWhere('legal_name', 'like', '%'.$this->companySearch.'%');
            })
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'legal_name', 'business_email']);
    }

    public function render()
    {
        return view('livewire.business.client-login');
    }
}
