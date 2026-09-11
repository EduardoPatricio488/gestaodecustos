<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessFinancialIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_payment_timestamp_is_created_when_marked_paid(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Empresa Teste', 'owner_id' => $user->id, 'type' => 'business', 'currency' => 'EUR']);
        $workspace->users()->attach($user->id, ['role' => 'admin']);
        $user->update(['current_workspace_id' => $workspace->id]);
        $this->actingAs($user);

        $invoice = Invoice::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'client_name' => 'Cliente Teste',
            'invoice_number' => 'FT TESTE 1',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'total_amount' => 123,
            'currency' => 'EUR',
            'status' => 'paga',
        ]);

        $this->assertNotNull($invoice->fresh()->paid_at);
    }

    public function test_workspace_scoped_models_cannot_be_loaded_from_another_current_workspace(): void
    {
        $user = User::factory()->create();
        $first = Workspace::create(['name' => 'Empresa A', 'owner_id' => $user->id, 'type' => 'business', 'currency' => 'EUR']);
        $second = Workspace::create(['name' => 'Empresa B', 'owner_id' => $user->id, 'type' => 'business', 'currency' => 'EUR']);
        $first->users()->attach($user->id, ['role' => 'admin']);
        $second->users()->attach($user->id, ['role' => 'admin']);
        $user->update(['current_workspace_id' => $first->id]);
        $this->actingAs($user);

        Invoice::create([
            'workspace_id' => $first->id, 'user_id' => $user->id, 'client_name' => 'A', 'invoice_number' => 'A-1',
            'amount_excl_vat' => 10, 'vat_amount' => 2.30, 'total_amount' => 12.30, 'currency' => 'EUR', 'status' => 'paga',
        ]);
        Invoice::withoutEvents(fn () => Invoice::withoutGlobalScopes()->create([
            'workspace_id' => $second->id, 'user_id' => $user->id, 'client_name' => 'B', 'invoice_number' => 'B-1',
            'amount_excl_vat' => 20, 'vat_amount' => 4.60, 'total_amount' => 24.60, 'currency' => 'EUR', 'status' => 'paga',
        ]));

        $this->assertSame(1, Invoice::count());
        $this->assertSame('A-1', Invoice::first()->invoice_number);
    }
}
