<?php

namespace App\Livewire\Business;

use App\Models\Invoice;
use App\Services\BusinessAccessService;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
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
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        $this->currency = strtoupper((string) ($workspace->currency ?? 'EUR'));
        $this->currencyOptions = CurrencyService::getSymbols();
    }

    public function rules()
    {
        return [
            'client_name' => 'required|string|max:255',
            'amount_excl_vat' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'currency' => 'required|string|size:3|in:EUR,USD,GBP,CHF,BRL,JPY',
            'status' => 'required|string|in:pendente,paga,vencida',
            'invoice_number' => [
                'required',
                'string',
                Rule::unique('invoices')->where(fn ($q) => $q->where('workspace_id', auth()->user()->current_workspace_id)),
            ],
        ];
    }

    public function updatedAmountExclVat($value): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        $vatRate = (float) ($workspace->vat_rate ?? 23);

        if (is_numeric($value)) {
            $this->vat_amount = $workspace->vat_regime === 'isento'
                ? 0
                : round((float) $value * ($vatRate / 100), 2);
            $this->total_amount = round((float) $value + (float) $this->vat_amount, 2);
        }
    }

    public function updated($field): void
    {
        if ($field === 'amount_excl_vat') {
            $this->updatedAmountExclVat($this->amount_excl_vat);
        }
    }

    public function save(): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_financials', auth()->user(), $workspace);

        if (! $this->invoice_number) {
            $next = Invoice::where('workspace_id', $workspace->id)->max('id') + 1;
            $this->invoice_number = 'FT-'.now()->year.'/'.str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        $this->validate();
        $this->updatedAmountExclVat($this->amount_excl_vat);

        Invoice::create([
            'user_id' => auth()->id(),
            'workspace_id' => $workspace->id,
            'client_name' => trim($this->client_name),
            'invoice_number' => $this->invoice_number,
            'amount_excl_vat' => round((float) $this->amount_excl_vat, 2),
            'vat_amount' => round((float) $this->vat_amount, 2),
            'total_amount' => round((float) $this->total_amount, 2),
            'currency' => strtoupper($this->currency),
            'status' => $this->status,
            'due_date' => $this->due_date,
        ]);

        $this->resetExcept('statusFilter');
        $this->status = 'pendente';
        $this->currency = strtoupper((string) ($workspace->currency ?? 'EUR'));

        $this->dispatch('modal-close', name: 'add-invoice-modal');
        $this->dispatch('toast', text: 'Fatura registada!', variant: 'success');
    }

    public function markAsPaid($id): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_financials', auth()->user(), $workspace);

        Invoice::where('workspace_id', $workspace->id)->findOrFail($id)->update(['status' => 'paga']);
        $this->dispatch('toast', text: 'Fatura marcada como paga.', variant: 'success');
    }

    public function delete($id): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('delete_financials', auth()->user(), $workspace);

        Invoice::where('workspace_id', $workspace->id)->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo de venda eliminado.', variant: 'danger');
    }

    public function openInvoiceModal(): void
    {
        app(BusinessAccessService::class)->assert('manage_financials');
        $this->dispatch('open-modal', name: 'add-invoice-modal');
    }

    public function render()
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        $query = Invoice::where('workspace_id', $workspace->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByRaw("CASE WHEN status = 'pendente' THEN 1 WHEN status = 'vencida' THEN 2 WHEN status = 'paga' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc');

        return view('livewire.business.invoicing-hub', [
            'invoices' => $query->paginate(10),
            'workspaceCurrency' => strtoupper((string) ($workspace->currency ?? 'EUR')),
            'currencyOptions' => $this->currencyOptions,
            'businessRole' => app(BusinessAccessService::class)->role(auth()->user(), $workspace),
            'totalBilled' => (clone $query)->where('status', 'paga')->sum(DB::raw('COALESCE(total_amount_converted, total_amount)')),
            'totalPending' => (clone $query)->whereIn('status', ['pendente', 'vencida'])->sum(DB::raw('COALESCE(total_amount_converted, total_amount)')),
            'vatToPay' => (clone $query)->whereIn('status', ['pendente', 'paga', 'vencida'])->sum(DB::raw('COALESCE(vat_amount_converted, vat_amount)')),
        ]);
    }
}
