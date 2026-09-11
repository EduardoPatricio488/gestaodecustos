<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BusinessAccessService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function businessWorkspace(User $owner, string $role = 'admin'): Workspace
    {
        $workspace = Workspace::create([
            'name' => 'Empresa Teste',
            'owner_id' => $owner->id,
            'type' => 'business',
            'currency' => 'EUR',
        ]);
        $workspace->users()->attach($owner->id, ['role' => $role]);
        $owner->update(['current_workspace_id' => $workspace->id]);

        return $workspace;
    }

    public function test_business_roles_are_normalized_and_owner_is_distinct(): void
    {
        $owner = User::factory()->create();
        $workspace = $this->businessWorkspace($owner, 'admin');
        $service = app(BusinessAccessService::class);

        $this->assertSame('owner', $service->role($owner, $workspace));

        $manager = User::factory()->create();
        $workspace->users()->attach($manager->id, ['role' => 'editor']);
        $this->assertSame('manager', $service->role($manager, $workspace));

        $employee = User::factory()->create();
        $workspace->users()->attach($employee->id, ['role' => 'member']);
        $this->assertSame('employee', $service->role($employee, $workspace));
    }

    public function test_employee_cannot_create_invoice_but_can_create_own_expense(): void
    {
        $owner = User::factory()->create();
        $workspace = $this->businessWorkspace($owner);
        $employee = User::factory()->create();
        $workspace->users()->attach($employee->id, ['role' => 'employee']);
        $employee->update(['current_workspace_id' => $workspace->id]);
        $this->actingAs($employee);

        $this->expectException(AuthorizationException::class);
        Invoice::create([
            'workspace_id' => $workspace->id,
            'user_id' => $employee->id,
            'client_name' => 'Cliente',
            'invoice_number' => 'EMP-1',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'currency' => 'EUR',
            'status' => 'pendente',
        ]);
    }

    public function test_employee_expense_is_scoped_to_the_employee(): void
    {
        $owner = User::factory()->create();
        $workspace = $this->businessWorkspace($owner);
        $employee = User::factory()->create();
        $workspace->users()->attach($employee->id, ['role' => 'employee']);
        $employee->update(['current_workspace_id' => $workspace->id]);
        $this->actingAs($employee);

        $expense = Expense::create([
            'workspace_id' => $workspace->id,
            'user_id' => $employee->id,
            'amount' => 25,
            'vat_amount' => 5.75,
            'description' => 'Despesa de teste',
            'spent_at' => now()->toDateString(),
            'is_company' => true,
        ]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'user_id' => $employee->id, 'workspace_id' => $workspace->id]);
    }

    public function test_cross_workspace_invoice_cannot_be_modified_even_when_global_scope_is_bypassed(): void
    {
        $user = User::factory()->create();
        $first = $this->businessWorkspace($user);
        $second = Workspace::create(['name' => 'Empresa B', 'owner_id' => $user->id, 'type' => 'business', 'currency' => 'EUR']);
        $second->users()->attach($user->id, ['role' => 'admin']);

        $this->actingAs($user);
        $invoice = Invoice::create([
            'workspace_id' => $first->id,
            'user_id' => $user->id,
            'client_name' => 'Cliente A',
            'invoice_number' => 'A-1',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'currency' => 'EUR',
            'status' => 'pendente',
        ]);

        $user->update(['current_workspace_id' => $second->id]);
        $foreignInvoice = Invoice::withoutGlobalScopes()->findOrFail($invoice->id);

        $this->expectException(\RuntimeException::class);
        $foreignInvoice->update(['client_name' => 'Ataque']);
    }
}
