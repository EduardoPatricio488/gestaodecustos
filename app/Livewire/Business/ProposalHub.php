<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Proposal;
use App\Models\Client;
use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ProposalHub extends Component
{
    use WithPagination;

    public $search = '';
    public $clientFilter = '';

    // Campos do Formulário
    public $title;
    public $proposal_number;
    public $client_id;
    public $amount;
    public $valid_until;
    public $notes;
    public $status = 'rascunho';
    public $editingId = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'proposal_number' => 'required|string',
        'client_id' => 'required|exists:clients,id',
        'amount' => 'required|numeric|min:0.01',
        'valid_until' => 'nullable|date',
    ];

    /**
     * Guardar ou Atualizar Proposta
     */
    public function save()
    {
        $this->validate();

        auth()->user()->currentWorkspace->proposals()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id'        => auth()->id(),
                'workspace_id'   => auth()->user()->current_workspace_id,
                'client_id'      => $this->client_id,
                'title'          => $this->title,
                'proposal_number'=> $this->proposal_number,
                'amount'         => $this->amount,
                'status'         => $this->status,
                'valid_until'    => $this->valid_until,
                'notes'          => $this->notes,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'proposal-modal');
        $this->dispatch('toast', text: 'Proposta comercial guardada!', variant: 'success');
    }

    /**
     * Converter Proposta em Fatura
     */
    public function convertToInvoice($id)
    {
        $proposal = Proposal::findOrFail($id);

        if ($proposal->status === 'convertida') {
            $this->dispatch('toast', text: 'Esta proposta já foi faturada.', variant: 'warning');
            return;
        }

        Invoice::create([
            'user_id'        => auth()->id(),
            'workspace_id'   => $proposal->workspace_id,
            'client_id'      => $proposal->client_id,
            'client_name'    => $proposal->client->name,
            'invoice_number' => 'FT-' . date('Y') . '/' . rand(100, 999),
            'amount_excl_vat'=> $proposal->amount,
            'vat_amount'     => $proposal->amount * 0.23,
            'total_amount'   => $proposal->amount * 1.23,
            'status'         => 'pendente',
            'due_date'       => now()->addDays(30),
        ]);

        $proposal->update(['status' => 'convertida']);

        $this->dispatch('toast', text: 'Proposta convertida em Fatura com sucesso!', variant: 'success');
    }

    public function updateStatus($id, $newStatus)
    {
        Proposal::findOrFail($id)->update(['status' => $newStatus]);
        $this->dispatch('toast', text: 'Estado da proposta atualizado.');
    }

    public function edit($id)
    {
        $proposal              = Proposal::findOrFail($id);
        $this->editingId       = $proposal->id;
        $this->title           = $proposal->title;
        $this->proposal_number = $proposal->proposal_number;
        $this->client_id       = $proposal->client_id;
        $this->amount          = $proposal->amount;
        $this->status          = $proposal->status;
        $this->valid_until     = $proposal->valid_until?->format('Y-m-d');
        $this->notes           = $proposal->notes;

        $this->dispatch('modal-show', name: 'proposal-modal');
    }

    public function delete($id)
    {
        Proposal::findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Proposta removida.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'proposal_number',
            'client_id',
            'amount',
            'valid_until',
            'notes',
            'status',
            'editingId',
        ]);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $query = $workspace->proposals()
            ->with('client')
            ->where('title', 'like', '%' . $this->search . '%')
            ->when($this->clientFilter, fn ($q) => $q->where('client_id', $this->clientFilter))
            ->orderByRaw("
                CASE
                    WHEN status = 'aceite' THEN 1
                    WHEN status = 'enviada' THEN 2
                    WHEN status = 'rascunho' THEN 3
                    WHEN status = 'recusada' THEN 4
                    WHEN status = 'convertida' THEN 5
                    ELSE 6
                END
            ")
            ->latest();

        $proposals = $query->get();

        return view('livewire.business.proposal-hub', [
            'proposals'      => $proposals,
            'clients'        => $workspace->clients()->orderBy('name')->get(),
            'totalValue'     => $proposals->where('status', '!=', 'recusada')->sum('amount'),
            'conversionRate' => $proposals->count() > 0
                ? ($proposals->where('status', 'convertida')->count() / $proposals->count()) * 100
                : 0,
        ]);
    }
}
