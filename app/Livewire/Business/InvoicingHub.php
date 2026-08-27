<?php

namespace App\Livewire\Business;

use App\Services\CurrencyService;
use Livewire\Component;
use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class InvoicingHub extends Component
{
    use WithPagination;

    public $client_name;
    public $invoice_number;
    public $amount_excl_vat;
    public $vat_amount;
    public $total_amount;
    public string $currency = 'EUR';
    public $due_date;
    public $status = 'pendente';

    public $statusFilter = '';
    public array $currencyOptions = [];

    public function mount(): void
    {
        $workspaceCurrency = strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR'));
        $this->currency = $workspaceCurrency;
        $this->currencyOptions = CurrencyService::getSymbols();
    }

    public function rules()
{
    return [
        'client_name' => 'required|string|max:255',
        'amount_excl_vat' => 'required|numeric|min:0.01',
        'due_date' => 'required|date',
        'currency' => 'required|string|size:3',

        'invoice_number' => [
            'required',
            'string',
            Rule::unique('invoices')->where(fn ($q) =>
                $q->where('workspace_id', auth()->user()->current_workspace_id)
            ),
        ],
    ];
}


    /**
     * Recalcular IVA e total quando o valor base muda
     */
    public function updatedAmountExclVat($value)
    {
        $vatRate = floatval(auth()->user()->currentWorkspace->vat_rate ?? 0.23);

        if (is_numeric($value)) {
            $this->vat_amount = round($value * $vatRate, 2);
            $this->total_amount = $value + $this->vat_amount;
        }
    }

    public function updated($field)
    {
        if ($field === 'amount_excl_vat') {
            $this->updatedAmountExclVat($this->amount_excl_vat);
        }
    }

    /**
     * Guardar fatura
     */
    public function save()
    {
        $this->validate();

        // Gerar número automático se vazio
        if (!$this->invoice_number) {
            $next = Invoice::where('workspace_id', auth()->user()->current_workspace_id)->max('id') + 1;
            $this->invoice_number = 'FT-' . now()->year . '/' . str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        if ($this->total_amount <= 0) {
            $this->dispatch('toast', text: 'Montante inválido.', variant: 'danger');
            return;
        }

        Invoice::create([
            'user_id'        => auth()->id(),
            'workspace_id'   => auth()->user()->current_workspace_id,
            'client_name'    => $this->client_name,
            'invoice_number' => $this->invoice_number,
            'amount_excl_vat'=> $this->amount_excl_vat,
            'vat_amount'     => $this->vat_amount,
            'total_amount'   => $this->total_amount,
            'currency'       => strtoupper($this->currency),
            'status'         => $this->status,
            'due_date'       => $this->due_date,
        ]);

        $this->resetExcept('statusFilter');
        $this->status = 'pendente';
        $this->currency = strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR'));

        $this->dispatch('modal-close', name: 'add-invoice-modal');
        $this->dispatch('toast', text: 'Fatura registada!', variant: 'success');
    }

    /**
     * Marcar como paga
     */
    public function markAsPaid($id)
    {
        Invoice::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)
            ->update(['status' => 'paga']);

        $this->dispatch('toast', text: 'Fatura marcada como paga.', variant: 'success');
    }

    /**
     * Eliminar fatura
     */
    public function delete($id)
    {
        Invoice::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)
            ->delete();

        $this->dispatch('toast', text: 'Registo de venda eliminado.', variant: 'danger');
    }

    /**
     * Renderização
     */

    public function openInvoiceModal()
{
    $this->dispatch('open-modal', name: 'add-invoice-modal');
}

    public function render()
{
    $query = Invoice::where('workspace_id', auth()->user()->current_workspace_id)
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->orderByRaw("
            CASE
                WHEN status = 'pendente' THEN 1
                WHEN status = 'paga' THEN 2
                ELSE 3
            END
        ")
        ->orderBy('created_at', 'desc');

    return view('livewire.business.invoicing-hub', [
        'invoices'     => $query->paginate(10),
        'workspaceCurrency' => strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR')),
        'currencyOptions' => $this->currencyOptions,
        'totalBilled'  => (clone $query)->where('status', 'paga')->sum(DB::raw('COALESCE(total_amount_converted, total_amount)')),
        'totalPending' => (clone $query)->where('status', 'pendente')->sum(DB::raw('COALESCE(total_amount_converted, total_amount)')),
        'vatToPay'     => (clone $query)->sum(DB::raw('COALESCE(vat_amount_converted, vat_amount)')),
    ]);
}

}
