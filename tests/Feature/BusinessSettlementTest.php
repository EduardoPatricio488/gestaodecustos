<?php

namespace Tests\Feature;

use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BusinessSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BusinessSettlementTest extends TestCase
{
    use RefreshDatabase;

    private function businessUser(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id, 'type' => 'business']);
        $workspace->users()->attach($user->id, ['role' => 'owner']);
        $user->current_workspace_id = $workspace->id;
        $user->save();
        $this->actingAs($user);

        return [$user, $workspace];
    }

    public function test_invoice_partial_payment_cannot_exceed_outstanding_balance(): void
    {
        [$user, $workspace] = $this->businessUser();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'client_name' => 'Cliente',
            'invoice_number' => 'FT-1',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'currency' => 'EUR',
            'status' => 'pendente',
            'due_date' => now()->toDateString(),
        ]);

        app(BusinessSettlementService::class)->receiveInvoice($invoice, 50);
        $this->assertSame('50.00', (string) $invoice->refresh()->amount_paid);

        $this->expectException(ValidationException::class);
        app(BusinessSettlementService::class)->receiveInvoice($invoice, 100);
    }

    public function test_credit_note_cannot_exceed_invoice_total(): void
    {
        [$user, $workspace] = $this->businessUser();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'client_name' => 'Cliente',
            'invoice_number' => 'FT-2',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'currency' => 'EUR',
            'status' => 'pendente',
            'due_date' => now()->toDateString(),
        ]);

        $this->expectException(ValidationException::class);
        app(BusinessSettlementService::class)->issueCreditNote($invoice, 100, 30, 'Correção', 'NC-1');
    }

    public function test_bank_reconciliation_rejects_mismatched_amount(): void
    {
        [$user, $workspace] = $this->businessUser();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'client_name' => 'Cliente',
            'invoice_number' => 'FT-3',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'currency' => 'EUR',
            'status' => 'pendente',
            'due_date' => now()->toDateString(),
        ]);

        $transaction = BankTransaction::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'transaction_date' => now()->toDateString(),
            'amount' => 50,
            'currency' => 'EUR',
            'description' => 'Movimento bancário',
            'status' => 'unreconciled',
        ]);

        $this->expectException(ValidationException::class);
        app(BusinessSettlementService::class)->reconcile($transaction, 'invoice', $invoice->id);
        $this->assertSame('unreconciled', $transaction->refresh()->status);
    }
}
