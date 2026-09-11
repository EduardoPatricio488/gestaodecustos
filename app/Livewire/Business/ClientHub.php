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

    // Campos do formulário
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

    /**
     * Formata o NIF automaticamente em grupos de 3 dígitos.
     * Exemplo: 123456789 -> 123 456 789
     */
    public function updatedTaxNumber($value): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        $digits = substr($digits, 0, 9);
        $this->tax_number = implode(' ', str_split($digits, 3));
    }

    public function openHistory($id)
    {
        // Carregamos o cliente com as relações de faturas e projetos
        $this->selectedClient = auth()->user()->clients()
            ->with(['projects', 'invoices' => fn ($q) => $q->latest()])
            ->findOrFail($id);

        $this->dispatch('modal-show', name: 'history-modal');
    }

    public $clientTaxNumber = '';

    public function save()
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

    public function edit($id)
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

    public function delete($id)
    {
        auth()->user()->clients()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Cliente removido.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['name', 'legal_name', 'tax_number', 'email', 'phone', 'status', 'address', 'notes', 'editingId']);
    }

    public function generatePortalLink($id)
    {
        $client = auth()->user()->clients()->findOrFail($id);

        // 1. Gerar ou recuperar o código de acesso.
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

        // Envio automático do código para o email do cliente, seguindo o mesmo princípio
        // das credenciais enviadas automaticamente no acesso bancário empresarial.
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

        $this->dispatch('modal-show', name: 'portal-link-modal');
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
            // O modelo Client já tem o atributo total_revenue que vamos usar no blade
        ]);
    }
}
