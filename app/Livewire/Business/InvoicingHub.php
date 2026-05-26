<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class InvoicingHub extends Component
{
    use WithPagination;

    public $client_name, $invoice_number, $amount_excl_vat, $vat_amount, $total_amount, $due_date;
    public $status = 'pendente';

    protected $rules = [
        'client_name' => 'required|string|max:255',
        'invoice_number' => 'required|string',
        'amount_excl_vat' => 'required|numeric|min:0.01',
        'due_date' => 'required|date',
    ];

    public function updatedAmountExclVat($value)
    {
        if (is_numeric($value)) {
            $this->vat_amount = round($value * 0.23, 2);
            $this->total_amount = $value + $this->vat_amount;
        }
    }

    public function save()
    {
        $this->validate();

        Invoice::create([
            'user_id' => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id, // LIGAÇÃO AO WORKSPACE
            'client_name' => $this->client_name,
            'invoice_number' => $this->invoice_number,
            'amount_excl_vat' => $this->amount_excl_vat,
            'vat_amount' => $this->vat_amount,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'due_date' => $this->due_date,
        ]);

        $this->reset(['client_name', 'invoice_number', 'amount_excl_vat', 'vat_amount', 'total_amount', 'due_date']);
        $this->dispatch('modal-close', name: 'add-invoice-modal');
        session()->flash('ok', 'Fatura registada!');
    }

    public function markAsPaid($id)
    {
        Invoice::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id)->update(['status' => 'paga']);
    }

    public function delete($id)
    {
        Invoice::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id)->delete();
    }

    public function render()
    {
        // FILTRAR SEMPRE POR WORKSPACE ATIVO
        $query = Invoice::where('workspace_id', auth()->user()->current_workspace_id);

        return view('livewire.business.invoicing-hub', [
            'invoices' => (clone $query)->latest()->paginate(10),
            'totalBilled' => (clone $query)->where('status', 'paga')->sum('total_amount'),
            'totalPending' => (clone $query)->where('status', 'pendente')->sum('total_amount'),
            'vatToPay' => (clone $query)->sum('vat_amount'),
        ]);
    }
}
