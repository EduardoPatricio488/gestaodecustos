<?php

namespace App\Livewire\Business;

use App\Mail\ClientPortalAccessMail;
use App\Models\Client;
use App\Models\PortalAccessRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ClientHub extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedClient = null;
    public $generatedPasscode = '';
    public $showModal = false;
    public $generatedPortalUrl = '';
    public $editingId = null;
    public $name;
    public $legal_name;
    public $tax_number;
    public $email;
    public $phone;
    public $status = 'ativo';
    public $address;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:100',
        'email' => 'nullable|email',
        'status' => 'required|in:ativo,lead,inativo',
    ];

    public function updatedTaxNumber($value): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        $digits = substr($digits, 0, 9);
        $this->tax_number = implode(' ', str_split($digits, 3));
    }

    public function openClientModal(): void
    {
        $this->resetForm();
        $this->status = 'ativo';
        $this->dispatch('modal-show', name: 'client-modal');
    }

    public function openHistory($id): void
    {
        $this->selectedClient = auth()->user()->clients()
            ->with(['projects', 'invoices' => fn ($q) => $q->latest()])
            ->findOrFail($id);

        $this->dispatch('modal-show', name: 'history-modal');
    }

    public $clientTaxNumber = '';

    public function save(): void
    {
        $this->validate();

        $taxNumber = preg_replace('/\D/', '', (string) $this->tax_number);
        $taxNumber = substr($taxNumber, 0, 9);

        auth()->user()->clients()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'legal_name' => $this->legal_name,
                'tax_number' => $taxNumber ?: null,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'address' => $this->address,
                'notes' => $this->notes,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'client-modal');
        $this->dispatch('toast', text: 'Cliente atualizado no sistema.');
    }

    public function edit($id): void
    {
        $client = auth()->user()->clients()->findOrFail($id);
        $this->editingId = $client->id;
        $this->name = $client->name;
        $this->legal_name = $client->legal_name;
        $this->tax_number = $client->tax_number;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->status = $client->status;
        $this->address = $client->address;
        $this->notes = $client->notes;
        $this->dispatch('modal-show', name: 'client-modal');
    }

    public function delete($id): void
    {
        auth()->user()->clients()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Cliente removido.', variant: 'warning');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'legal_name', 'tax_number', 'email', 'phone', 'status', 'address', 'notes', 'editingId']);
    }

    public function generatePortalLink($id): void
    {
        $client = auth()->user()->clients()->findOrFail($id);

        // Portal credentials must have enough entropy to be unguessable online.
        // Rotate legacy 6-digit tokens when the business user opens the credential modal.
        if (! $client->portal_token || strlen((string) $client->portal_token) < 64) {
            $client->update(['portal_token' => $this->generateUniquePortalToken()]);
            $client->refresh();
        }

        $this->clientTaxNumber = $client->tax_number;
        $this->generatedPasscode = $client->portal_token;
        $this->generatedPortalUrl = route('client.portal', ['token' => $client->portal_token]);
        $this->dispatch('modal-show', name: 'portal-link-modal');
    }

    public function sendPortalEmail(): void
    {
        $client = auth()->user()->clients()
            ->where('portal_token', $this->generatedPasscode)
            ->firstOrFail();

        if (! $client->email) {
            $this->dispatch('toast', text: 'Este cliente não tem email registado.', variant: 'warning');
            return;
        }

        Mail::to($client->email)->send(new ClientPortalAccessMail(
            $client,
            auth()->user()->currentWorkspace,
            $client->portal_token,
            $this->generatedPortalUrl,
        ));

        $this->dispatch('toast', text: 'Código de acesso enviado para '.$client->email.'.', variant: 'success');
    }

    public function getPendingAccessRequests(): array
    {
        return $this->pendingAccessRequestsQuery()
            ->latest('requested_at')
            ->limit(50)
            ->get()
            ->map(fn (PortalAccessRequest $request) => [
                'id' => $request->id,
                'name' => $request->requester_name,
                'email' => $request->requester_email,
                'tax_number' => $request->tax_number,
                'requested_at' => optional($request->requested_at)->format('d/m/Y H:i'),
            ])
            ->values()
            ->all();
    }

    public function approveAccessRequest($id): void
    {
        $request = $this->pendingAccessRequestsQuery()->findOrFail($id);
        $taxNumber = preg_replace('/\D/', '', (string) $request->tax_number);
        $taxNumber = substr($taxNumber, 0, 9);
        $clientQuery = auth()->user()->clients();
        $client = null;

        if ($request->requester_email) {
            $client = (clone $clientQuery)->where('email', $request->requester_email)->first();
        }
        if (! $client && $taxNumber) {
            $client = (clone $clientQuery)->where('tax_number', $taxNumber)->first();
        }

        if (! $client) {
            $client = Client::create([
                'user_id' => auth()->id(),
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $request->requester_name,
                'legal_name' => $request->requester_name,
                'tax_number' => $taxNumber ?: null,
                'email' => $request->requester_email,
                'status' => 'ativo',
                'portal_token' => $this->generateUniquePortalToken(),
            ]);
        } else {
            $client->update([
                'name' => $client->name ?: $request->requester_name,
                'email' => $client->email ?: $request->requester_email,
                'tax_number' => $client->tax_number ?: ($taxNumber ?: null),
                'status' => 'ativo',
                'portal_token' => $client->portal_token ?: $this->generateUniquePortalToken(),
            ]);
            $client->refresh();
        }

        $portalUrl = route('client.portal', ['token' => $client->portal_token]);
        Mail::to($client->email)->send(new ClientPortalAccessMail(
            $client,
            auth()->user()->currentWorkspace,
            $client->portal_token,
            $portalUrl,
        ));

        $request->update(['status' => 'approved', 'responded_at' => now()]);
        $this->dispatch('toast', text: 'Pedido aprovado. Cliente criado e credenciais enviadas por email.', variant: 'success');
        $this->dispatch('client-access-request-updated');
    }

    public function rejectAccessRequest($id): void
    {
        $request = $this->pendingAccessRequestsQuery()->findOrFail($id);
        $request->update(['status' => 'rejected', 'responded_at' => now()]);
        $this->dispatch('toast', text: 'Pedido de acesso rejeitado.', variant: 'warning');
        $this->dispatch('client-access-request-updated');
    }

    private function pendingAccessRequestsQuery()
    {
        return PortalAccessRequest::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('portal_type', 'client')
            ->where('status', 'pending');
    }

    private function generateUniquePortalToken(): string
    {
        do {
            $token = Str::random(64);
        } while (Client::where('portal_token', $token)->exists());

        return $token;
    }

    public function render()
    {
        $clients = auth()->user()->clients()
            ->where('name', 'like', '%'.$this->search.'%')
            ->get();

        return view('livewire.business.client-hub', [
            'clients' => $clients,
            'totalClients' => $clients->count(),
            'activeLeads' => $clients->where('status', 'lead')->count(),
        ]);
    }
}
