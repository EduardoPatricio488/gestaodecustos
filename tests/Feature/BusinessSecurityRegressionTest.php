<?php

namespace Tests\Feature;

use App\Livewire\BancoHub;
use App\Livewire\IncomeHub;
use App\Livewire\ManageExpense;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\RecurringIncome;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BusinessSecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_cannot_write_through_bank_hub(): void
    {
        [$owner, $workspace] = $this->businessWorkspace();
        $viewer = User::factory()->create(['current_workspace_id' => $workspace->id]);
        $workspace->users()->attach($viewer->id, ['role' => 'viewer']);

        $this->actingAs($viewer);

        Livewire::test(BancoHub::class)
            ->set('modalType', 'account')
            ->set('acc_name', 'Conta não autorizada')
            ->set('acc_balance', 100)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseMissing('bank_accounts', ['workspace_id' => $workspace->id, 'name' => 'Conta não autorizada']);
    }

    public function test_bank_hub_rejects_transfer_using_an_account_from_another_workspace(): void
    {
        [$owner, $workspace] = $this->businessWorkspace();
        $foreignOwner = User::factory()->create();
        $foreignWorkspace = Workspace::create(['name' => 'Empresa B', 'type' => 'business', 'owner_id' => $foreignOwner->id]);
        $foreignWorkspace->users()->attach($foreignOwner->id, ['role' => 'admin']);

        $from = BankAccount::create(['workspace_id' => $workspace->id, 'user_id' => $owner->id, 'name' => 'A', 'type' => 'corrente', 'balance' => 100]);
        $foreign = BankAccount::create(['workspace_id' => $foreignWorkspace->id, 'user_id' => $foreignOwner->id, 'name' => 'B', 'type' => 'corrente', 'balance' => 100]);

        Livewire::actingAs($owner)
            ->test(BancoHub::class)
            ->set('modalType', 'transfer')
            ->set('tr_from_id', $from->id)
            ->set('tr_to_id', $foreign->id)
            ->set('tr_amount', 10)
            ->set('tr_date', now()->format('Y-m-d'))
            ->call('save')
            ->assertStatus(422);
    }

    public function test_save_fixed_income_rejects_a_bank_account_from_another_workspace(): void
    {
        [$owner, $workspace] = $this->businessWorkspace();
        $foreignOwner = User::factory()->create();
        $foreignWorkspace = Workspace::create(['name' => 'Empresa B', 'type' => 'business', 'owner_id' => $foreignOwner->id]);
        $foreignAccount = BankAccount::create(['workspace_id' => $foreignWorkspace->id, 'user_id' => $foreignOwner->id, 'name' => 'Conta B', 'type' => 'corrente', 'balance' => 100]);

        Livewire::actingAs($owner)
            ->test(IncomeHub::class)
            ->set('recBankAccountId', $foreignAccount->id)
            ->set('recDescription', 'Rendimento')
            ->set('recAmount', 1000)
            ->set('recDay', 1)
            ->set('recSource', 'emprego')
            ->set('recFrequency', 'mensal')
            ->call('saveFixed')
            ->assertStatus(422);

        $this->assertDatabaseMissing('recurring_incomes', ['workspace_id' => $workspace->id, 'bank_account_id' => $foreignAccount->id]);
    }

    public function test_income_workspace_selector_cannot_read_employee_from_an_unrelated_workspace(): void
    {
        [$owner, $workspace] = $this->businessWorkspace();
        $foreignOwner = User::factory()->create();
        $foreignWorkspace = Workspace::create(['name' => 'Empresa B', 'type' => 'business', 'owner_id' => $foreignOwner->id]);
        Employee::create(['workspace_id' => $foreignWorkspace->id, 'user_id' => $foreignOwner->id, 'name' => 'Privado', 'role' => 'Diretor', 'salary' => 99999, 'pay_day' => 25, 'active' => true]);

        Livewire::actingAs($owner)
            ->test(IncomeHub::class)
            ->set('recWorkspaceId', $foreignWorkspace->id)
            ->assertSet('recAmount', '');
    }

    public function test_manage_expense_rejects_a_bank_account_from_another_workspace(): void
    {
        [$owner, $workspace] = $this->businessWorkspace();
        $category = Category::create(['workspace_id' => $workspace->id, 'user_id' => $owner->id, 'name' => 'Teste', 'slug' => 'teste']);
        $foreignOwner = User::factory()->create();
        $foreignWorkspace = Workspace::create(['name' => 'Empresa B', 'type' => 'business', 'owner_id' => $foreignOwner->id]);
        $foreignAccount = BankAccount::create(['workspace_id' => $foreignWorkspace->id, 'user_id' => $foreignOwner->id, 'name' => 'Conta B', 'type' => 'corrente', 'balance' => 100]);

        Livewire::actingAs($owner)
            ->test(ManageExpense::class)
            ->set('amount', 10)
            ->set('currency', 'EUR')
            ->set('spent_at', now()->format('Y-m-d'))
            ->set('category_id', $category->id)
            ->set('bankAccountId', $foreignAccount->id)
            ->call('save')
            ->assertStatus(422);

        $this->assertDatabaseMissing('expenses', ['workspace_id' => $workspace->id, 'bank_account_id' => $foreignAccount->id]);
    }

    private function businessWorkspace(): array
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Empresa A',
            'type' => 'business',
            'owner_id' => $owner->id,
            'currency' => 'EUR',
        ]);
        $workspace->users()->attach($owner->id, ['role' => 'admin']);
        $owner->update(['current_workspace_id' => $workspace->id]);

        return [$owner, $workspace];
    }
}
