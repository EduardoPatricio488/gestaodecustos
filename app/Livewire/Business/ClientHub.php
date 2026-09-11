<?php

namespace App\Livewire\Business;

use App\Mail\ClientPortalAccessMail;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
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

    /**
     * Abre o formulário de cliente através da API nativa do Flux.
     */
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
                $passcode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $exists = Client::where('portal_token', $passcode)->exists();
            } while ($exists);

            $client->update(['portal_token' => $passcode]);
        }

        $this->clientTaxNumber = $client->tax_number;
        $this->generatedPasscode = $client->portal_token;
        $this->generatedPortalUrl = route('client.portal', ['token' => $client->portal_token]);

        if ($client->email) {
            Mail::to($client->email)->send(new ClientPortalAccessMail(
                $client,
                auth()->user()->currentWorkspace,
                $client->portal_token,
                $this->generatedPortalUrl,
            ));

            $this->dispatch('toast', text: 'Código de acesso enviado para '.$client->email.'.', variant: 'success');
        } else {
            $this->dispatch('toast', text: 'Código gerado, mas este cliente não tem email registado.', variant: 'warning');
        }

        // O evento modal-show não é a API correta do Flux 2. Abrimos o modal
        // diretamente através da API Livewire do Flux para garantir que funciona
        // mesmo quando a página é renderizada através do wrapper.
        $this->modal('portal-link-modal')->show();
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
