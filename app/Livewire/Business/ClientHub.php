<?php

namespace App\Livewire\Business;

use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientPortalAccessMail;
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
        $this->modal('client-modal')->show();
    }

    public function openHistory($id): void
    {
        $this->selectedClient = auth()->user()->clients()
            ->with(['projects', 'invoices' => fn ($q) => $q->latest()])
            ->findOrFail($id);

        $this->modal('history-modal')->show();
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
        $this->modal('client-modal')->close();
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

        $this->modal('client-modal')->show();
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

        if (! $client->portal_token) {
            do {
                $passcode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $exists = Client::where('portal_token', $passcode)->exists();
            } while ($exists);

            $client->update(['portal_token' => $passcode]);
        }

        $this->clientTaxNumber = $client->tax_number;
        $this->generatedPasscode = $client->portal_token;
        $this->generatedPortalUrl = route('client.portal', ['token' => $client->portal_token]);

        // IMPORTANTE: não enviar o email nesta mesma request.
        // O envio síncrono pode bloquear o Livewire (SMTP/Resend) e impedir
        // que a resposta chegue ao browser, fazendo parecer que o modal não abre.
        // O modal deve abrir imediatamente; o envio de email será uma ação separada.
        $this->modal('portal-link-modal')->show();
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

    public function render()
    {
        $clients = auth()->user()->clients()
            ->where('name', 'like', '%'.$this->search.'%')
            ->get();

        return view('livewire.business.client-hub-wrapper', [
            'clients' => $clients,
            'totalClients' => $clients->count(),
            'activeLeads' => $clients->where('status', 'lead')->count(),
        ]);
    }
}
