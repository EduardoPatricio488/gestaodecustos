<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Client;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ClientHub extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    // Campos do formulário
    public $name, $legal_name, $tax_number, $email, $phone, $status = 'ativo', $address, $notes;

    protected $rules = [
        'name' => 'required|string|max:100',
        'email' => 'nullable|email',
        'status' => 'required|in:ativo,lead,inativo',
    ];

    public function save()
    {
        $this->validate();

        auth()->user()->clients()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'legal_name' => $this->legal_name,
                'tax_number' => $this->tax_number,
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

    public function render()
    {
        $clients = auth()->user()->clients()
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();

        return view('livewire.business.client-hub', [
            'clients' => $clients,
            'totalClients' => $clients->count(),
            'activeLeads' => $clients->where('status', 'lead')->count(),
            // O modelo Client já tem o atributo total_revenue que vamos usar no blade
        ]);
    }
}
