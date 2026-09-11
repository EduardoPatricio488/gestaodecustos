<?php
namespace App\Services;
use App\Models\PaymentAllocation;
use App\Models\Workspace;
use Illuminate\Support\Carbon;
class BusinessFinancialMetrics
{
    public function forMonth(Workspace $workspace, ?Carbon $month = null): array
    {
        $month=($month?:now())->copy()->startOfMonth(); $end=$month->copy()->endOfMonth(); $invoices=$workspace->invoices(); $expenses=$workspace->expenses()->where('is_company',true);
        $paidRevenue=(float)PaymentAllocation::where('workspace_id',$workspace->id)->whereNotNull('invoice_id')->whereBetween('paid_at',[$month->toDateString(),$end->toDateString()])->get()->sum(function($payment){$invoice=$payment->invoice; if(!$invoice||(float)$invoice->total_amount<=0)return 0; return (float)$payment->amount*((float)$invoice->amount_excl_vat/(float)$invoice->total_amount);});
        $issuedRevenue=(float)(clone $invoices)->whereBetween('created_at',[$month,$end])->whereNotIn('status',['cancelada','anulada'])->sum('amount_excl_vat_converted');
        $creditNotes=(float)$workspace->creditNotes()->whereBetween('issued_at',[$month->toDateString(),$end->toDateString()])->where('status','issued')->sum('amount_excl_vat');
        $issuedRevenue=max(0,$issuedRevenue-$creditNotes);
        $operatingExpenses=(float)(clone $expenses)->whereBetween('spent_at',[$month->toDateString(),$end->toDateString()])->sum('amount_converted');
        $activePayroll=$month->isSameMonth(now())?(float)$workspace->employees()->where('active',true)->where('suspended',false)->whereNull('terminated_at')->sum('salary'):0.0;
        $receivables=(float)(clone $invoices)->get()->sum(fn($invoice)=>(float)$invoice->outstanding_amount*((float)$invoice->total_amount_converted/max((float)$invoice->total_amount,0.01));
        $overdueCutoff=$month->isSameMonth(now())?now()->toDateString():$end->toDateString();
        $overdueReceivables=(float)(clone $invoices)->whereDate('due_date','<',$overdueCutoff)->whereNotIn('status',['paga','cancelada','anulada'])->get()->sum(fn($invoice)=>(float)$invoice->outstanding_amount*((float)$invoice->total_amount_converted/max((float)$invoice->total_amount,0.01));
        $cash=(float)$workspace->bankAccounts()->where('type','!=','credito')->get()->sum(fn($account)=>(float)$account->current_balance);
        if(!$workspace->bankAccounts()->exists())$cash=(float)($workspace->initial_capital??0)+(float)PaymentAllocation::where('workspace_id',$workspace->id)->whereNotNull('invoice_id')->sum('amount')-(float)PaymentAllocation::where('workspace_id',$workspace->id)->whereNotNull('expense_id')->sum('amount');
        $totalCosts=$operatingExpenses+$activePayroll; $netOperatingResult=$paidRevenue-$totalCosts;
        return ['period'=>$month->format('Y-m'),'revenue_cash'=>round($paidRevenue,2),'revenue_issued'=>round($issuedRevenue,2),'operating_expenses'=>round($operatingExpenses,2),'payroll'=>round($activePayroll,2),'total_costs'=>round($totalCosts,2),'net_result'=>round($netOperatingResult,2),'margin'=>round($paidRevenue>0?($netOperatingResult/$paidRevenue)*100:0,2),'cash'=>round($cash,2),'receivables'=>round($receivables,2),'overdue_receivables'=>round(max(0,$overdueReceivables),2),'payroll_is_current_run_rate'=>$month->isSameMonth(now())];
    }
    public function forYear(Workspace $workspace,int $year): array { return collect(range(1,12))->map(fn(int $month)=>$this->forMonth($workspace,Carbon::create($year,$month,1)))->all(); }
}
