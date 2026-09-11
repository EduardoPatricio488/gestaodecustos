<?php
namespace App\Livewire\Business;
use App\Models\BankTransaction;
use App\Models\PaymentAllocation;
use App\Services\BusinessAccessService;
use App\Services\BusinessSettlementService;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('components.layouts.app')]
class BusinessReconciliationHub extends Component
{
    public ?int $transactionId=null; public string $matchedType='payment_allocation'; public ?int $matchedId=null;
    public function mount(): void { app(BusinessAccessService::class)->assert('manage_financials'); }
    public function reconcile(): void { $workspace=app(BusinessAccessService::class)->assertWorkspace(); $this->validate(['transactionId'=>'required|integer','matchedType'=>'required|in:payment_allocation,invoice,expense','matchedId'=>'required|integer']); app(BusinessSettlementService::class)->reconcile(BankTransaction::where('workspace_id',$workspace->id)->findOrFail($this->transactionId),$this->matchedType,$this->matchedId); $this->reset('transactionId','matchedId'); $this->dispatch('toast',text:'Movimento reconciliado.',variant:'success'); }
    public function render() { $workspace=app(BusinessAccessService::class)->assertWorkspace(); return view('livewire.business.business-reconciliation-hub',['transactions'=>$workspace->bankTransactions()->where('status','unreconciled')->latest('transaction_date')->limit(100)->get(),'payments'=>$workspace->paymentAllocations()->latest('paid_at')->limit(100)->get(),'invoices'=>$workspace->invoices()->latest()->limit(100)->get(),'expenses'=>$workspace->expenses()->where('is_company',true)->latest('spent_at')->limit(100)->get()]); }
}
