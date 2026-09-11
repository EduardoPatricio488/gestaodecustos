<?php

namespace App\Livewire\Business;

use App\Models\Expense;
use App\Models\Invoice;
use App\Services\BusinessAccessService;
use App\Services\BusinessSettlementService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessSettlementHub extends Component
{
    public string $type = 'invoice';

    public ?int $recordId = null;

    public $amount = '';

    public ?int $bankAccountId = null;

    public string $reference = '';

    public string $paidAt = '';

    public $invoiceIdForCredit = null;

    public $creditExclVat = '';

    public $creditVat = '';

    public string $creditReason = '';

    public string $creditNumber = '';

    public function mount(): void
    {
        app(BusinessAccessService::class)->assert('manage_financials');
        $this->paidAt = now()->toDateString();
    }

    public function savePayment(): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        $this->validate(['type' => 'required|in:invoice,expense', 'recordId' => 'required|integer', 'amount' => 'required|numeric|min:0.01', 'bankAccountId' => 'nullable|integer', 'reference' => 'nullable|string|max:255', 'paidAt' => 'required|date']);
        if ($this->type === 'invoice') {
            app(BusinessSettlementService::class)->receiveInvoice(Invoice::where('workspace_id', $workspace->id)->findOrFail($this->recordId), (float) $this->amount, $this->bankAccountId, $this->reference, $this->paidAt);
        } else {
            app(BusinessSettlementService::class)->payExpense(Expense::where('workspace_id', $workspace->id)->findOrFail($this->recordId), (float) $this->amount, $this->bankAccountId, $this->reference, $this->paidAt);
        } $this->reset('recordId', 'amount', 'reference');
        $this->dispatch('toast', text: 'Pagamento registado.', variant: 'success');
    }

    public function saveCreditNote(): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        $this->validate(['invoiceIdForCredit' => 'required|integer', 'creditExclVat' => 'required|numeric|min:0.01', 'creditVat' => 'required|numeric|min:0', 'creditReason' => 'required|string|max:500', 'creditNumber' => 'required|string|max:100']);
        app(BusinessSettlementService::class)->issueCreditNote(Invoice::where('workspace_id', $workspace->id)->findOrFail($this->invoiceIdForCredit), (float) $this->creditExclVat, (float) $this->creditVat, $this->creditReason, $this->creditNumber);
        $this->reset('invoiceIdForCredit', 'creditExclVat', 'creditVat', 'creditReason', 'creditNumber');
        $this->dispatch('toast', text: 'Nota de crédito emitida.', variant: 'success');
    }

    public function render()
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();

        return view('livewire.business.business-settlement-hub', ['invoices' => $workspace->invoices()->whereRaw('(amount_paid + amount_credited) < total_amount')->latest()->limit(50)->get(), 'expenses' => $workspace->expenses()->where('is_company', true)->whereColumn('amount_paid', '<', 'amount')->latest('spent_at')->limit(50)->get(), 'bankAccounts' => $workspace->bankAccounts()->where('status', 'active')->where('type', '!=', 'credito')->get(), 'workspaceCurrency' => strtoupper($workspace->currency ?? 'EUR')]);
    }
}
