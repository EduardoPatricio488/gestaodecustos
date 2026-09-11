<?php

namespace App\Livewire\Business;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ProposalHub extends Component
{
    use WithPagination;

    public $search = '';

    public $clientFilter = '';

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
        'client_id' => 'required|integer',
        'amount' => 'required|numeric|min:0.01',
        'valid_until' => 'nullable|date',
    ];

    private function workspace()
    {
        $workspace = auth()->user()->currentWorkspace;
        abort_unless($workspace && in_array($workspace->type, ['business', 'company'], true), 403);

        return $workspace;
    }

    public function save()
    {
        $this->validate();
        $workspace = $this->workspace();
        abort_unless($workspace->clients()->whereKey($this->client_id)->exists(), 422, 'Cliente inválido para este workspace.');

        $workspace->proposals()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => auth()->id(), 'workspace_id' => $workspace->id, 'client_id' => $this->client_id,
                'title' => $this->title, 'proposal_number' => $this->proposal_number, 'amount' => $this->amount,
                'status' => $this->status, 'valid_until' => $this->valid_until, 'notes' => $this->notes,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'proposal-modal');
        $this->dispatch('toast', text: 'Proposta comercial guardada!', variant: 'success');
    }

    public function convertToInvoice(int $id)
    {
        $workspace = $this->workspace();
        $proposal = $workspace->proposals()->with('client')->findOrFail($id);
        abort_unless($proposal->client_id && $proposal->client, 422, 'A proposta não tem um cliente válido.');

        if ($proposal->status === 'convertida') {
            $this->dispatch('toast', text: 'Esta proposta já foi faturada.', variant: 'warning');

            return;
        }

        $vatRate = max(0, min(1, (float) ($workspace->vat_rate ?? 0.23)));
        $vatAmount = round((float) $proposal->amount * $vatRate, 2);
        $total = round((float) $proposal->amount + $vatAmount, 2);

        Invoice::create([
            'user_id' => auth()->id(), 'workspace_id' => $workspace->id, 'client_id' => $proposal->client_id,
            'client_name' => $proposal->client->name,
            'invoice_number' => 'FT-'.date('Y').'/'.strtoupper(bin2hex(random_bytes(3))),
            'amount_excl_vat' => $proposal->amount, 'vat_amount' => $vatAmount, 'total_amount' => $total,
            'status' => 'pendente', 'due_date' => now()->addDays(30),
        ]);

        $proposal->update(['status' => 'convertida']);
        $this->dispatch('toast', text: 'Proposta convertida em Fatura com sucesso!', variant: 'success');
    }

    public function updateStatus(int $id, string $newStatus)
    {
        abort_unless(in_array($newStatus, ['rascunho', 'enviada', 'aceite', 'recusada', 'convertida'], true), 422);
        $this->workspace()->proposals()->whereKey($id)->firstOrFail()->update(['status' => $newStatus]);
        $this->dispatch('toast', text: 'Estado da proposta atualizado.');
    }

    public function edit(int $id)
    {
        $proposal = $this->workspace()->proposals()->findOrFail($id);
        $this->editingId = $proposal->id;
        $this->title = $proposal->title;
        $this->proposal_number = $proposal->proposal_number;
        $this->client_id = $proposal->client_id;
        $this->amount = $proposal->amount;
        $this->status = $proposal->status;
        $this->valid_until = $proposal->valid_until?->format('Y-m-d');
        $this->notes = $proposal->notes;
        $this->dispatch('modal-show', name: 'proposal-modal');
    }

    public function delete(int $id)
    {
        $this->workspace()->proposals()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Proposta removida.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['title', 'proposal_number', 'client_id', 'amount', 'valid_until', 'notes', 'status', 'editingId']);
        $this->status = 'rascunho';
    }

    public function render()
    {
        $workspace = $this->workspace();
        $query = $workspace->proposals()->with('client')->where('title', 'like', '%'.$this->search.'%')
            ->when($this->clientFilter, fn ($q) => $q->where('client_id', $this->clientFilter))
            ->orderByRaw("CASE WHEN status = 'aceite' THEN 1 WHEN status = 'enviada' THEN 2 WHEN status = 'rascunho' THEN 3 WHEN status = 'recusada' THEN 4 WHEN status = 'convertida' THEN 5 ELSE 6 END")
            ->latest();
        $proposals = $query->get();

        return view('livewire.business.proposal-hub', [
            'proposals' => $proposals,
            'clients' => $workspace->clients()->orderBy('name')->get(),
            'totalValue' => $proposals->where('status', '!=', 'recusada')->sum('amount'),
            'conversionRate' => $proposals->count() > 0 ? ($proposals->where('status', 'convertida')->count() / $proposals->count()) * 100 : 0,
        ]);
    }
}
