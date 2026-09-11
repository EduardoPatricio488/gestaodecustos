<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BusinessSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessSettlementTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_partial_payment_cannot_exceed_outstanding_balance(): void
    {
        $user=User::factory()->create(); $workspace=Workspace::factory()->create(['owner_id'=>$user->id,'type'=>'business']);
        $workspace->users()->attach($user->id,['role'=>'owner']); $user->current_workspace_id=$workspace->id; $user->save();
        $invoice=Invoice::create(['user_id'=>$user->id,'workspace_id'=>$workspace->id,'client_name'=>'Cliente','invoice_number'=>'FT-1','amount_excl_vat'=>100,'vat_amount'=>23,'currency'=>'EUR','status'=>'pendente','due_date'=>now()->toDateString()]);
        $this->actingAs($user); app(BusinessSettlementService::class)->receiveInvoice($invoice,50);
        $this->assertSame('50.00',(string)$invoice->refresh()->amount_paid);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(BusinessSettlementService::class)->receiveInvoice($invoice,100);
    }

    public function test_credit_note_cannot_exceed_invoice_total(): void
    {
        $user=User::factory()->create(); $workspace=Workspace::factory()->create(['owner_id'=>$user->id,'type'=>'business']);
        $workspace->users()->attach($user->id,['role'=>'owner']); $user->current_workspace_id=$workspace->id; $user->save(); $this->actingAs($user);
        $invoice=Invoice::create(['user_id'=>$user->id,'workspace_id'=>$workspace->id,'client_name'=>'Cliente','invoice_number'=>'FT-2','amount_excl_vat'=>100,'vat_amount'=>23,'currency'=>'EUR','status'=>'pendente','due_date'=>now()->toDateString()]);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(BusinessSettlementService::class)->issueCreditNote($invoice,100,30,'Correção','NC-1');
    }
}
