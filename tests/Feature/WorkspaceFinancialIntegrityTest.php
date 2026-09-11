<?php

use App\Models\AutoSavingsRule;
use App\Models\BankAccount;
use App\Models\BankReserve;
use App\Models\BankTransaction;
use App\Models\BankTransfer;
use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\Income;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

function createTestWorkspaceFor(User $user, string $name): Workspace
{
    $workspace = Workspace::create([
        'name' => $name,
        'type' => 'personal',
        'owner_id' => $user->id,
        'currency' => 'EUR',
        'invite_code' => strtoupper(substr(md5($name), 0, 8)),
    ]);

    $user->workspaces()->attach($workspace->id, ['role' => 'admin']);

    return $workspace;
}

test('bank transfers cannot reference accounts from another workspace', function () {
    $user = User::factory()->create();
    $workspace = createTestWorkspaceFor($user, 'Workspace Principal');
    $foreignUser = User::factory()->create();
    $foreignWorkspace = createTestWorkspaceFor($foreignUser, 'Workspace Externo');

    Auth::logout();
    $foreignAccount = BankAccount::create([
        'workspace_id' => $foreignWorkspace->id,
        'user_id' => $foreignUser->id,
        'name' => 'Conta externa',
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
    ]);

    $this->actingAs($user);
    $user->update(['current_workspace_id' => $workspace->id]);
    $localAccount = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Conta local',
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
    ]);

    expect(fn () => BankTransfer::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'from_account_id' => $localAccount->id,
        'to_account_id' => $foreignAccount->id,
        'amount' => 25,
        'transferred_at' => now()->toDateString(),
        'status' => 'completed',
    ]))->toThrow(DomainException::class);
});

test('bank reserves cannot reference accounts from another workspace', function () {
    $user = User::factory()->create();
    $workspace = createTestWorkspaceFor($user, 'Workspace Principal');
    $foreignUser = User::factory()->create();
    $foreignWorkspace = createTestWorkspaceFor($foreignUser, 'Workspace Externo');

    Auth::logout();
    $foreignAccount = BankAccount::create([
        'workspace_id' => $foreignWorkspace->id,
        'user_id' => $foreignUser->id,
        'name' => 'Conta externa',
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
    ]);

    $this->actingAs($user);
    $user->update(['current_workspace_id' => $workspace->id]);

    expect(fn () => BankReserve::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'bank_account_id' => $foreignAccount->id,
        'name' => 'Reserva',
        'amount' => 100,
    ]))->toThrow(DomainException::class);
});

test('goal contributions cannot reference goals or incomes from another workspace', function () {
    $user = User::factory()->create();
    $workspace = createTestWorkspaceFor($user, 'Workspace Principal');
    $foreignUser = User::factory()->create();
    $foreignWorkspace = createTestWorkspaceFor($foreignUser, 'Workspace Externo');

    Auth::logout();
    $foreignGoal = Goal::create([
        'workspace_id' => $foreignWorkspace->id,
        'user_id' => $foreignUser->id,
        'name' => 'Objetivo externo',
        'target_amount' => 1000,
        'current_amount' => 0,
    ]);
    $foreignIncome = Income::create([
        'workspace_id' => $foreignWorkspace->id,
        'user_id' => $foreignUser->id,
        'description' => 'Receita externa',
        'amount' => 100,
        'received_at' => now()->toDateString(),
    ]);

    $this->actingAs($user);
    $user->update(['current_workspace_id' => $workspace->id]);

    expect(fn () => GoalContribution::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'goal_id' => $foreignGoal->id,
        'income_id' => $foreignIncome->id,
        'amount' => 50,
        'contributed_at' => now(),
    ]))->toThrow(DomainException::class);
});

test('auto savings rules reject invalid percentages and foreign goals', function () {
    $user = User::factory()->create();
    $workspace = createTestWorkspaceFor($user, 'Workspace Principal');
    $foreignUser = User::factory()->create();
    $foreignWorkspace = createTestWorkspaceFor($foreignUser, 'Workspace Externo');

    Auth::logout();
    $foreignGoal = Goal::create([
        'workspace_id' => $foreignWorkspace->id,
        'user_id' => $foreignUser->id,
        'name' => 'Objetivo externo',
        'target_amount' => 1000,
        'current_amount' => 0,
    ]);

    $this->actingAs($user);
    $user->update(['current_workspace_id' => $workspace->id]);

    expect(fn () => AutoSavingsRule::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'percent' => 150,
        'min_income_amount' => 0,
    ]))->toThrow(DomainException::class);

    expect(fn () => AutoSavingsRule::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'goal_id' => $foreignGoal->id,
        'percent' => 10,
        'min_income_amount' => 0,
    ]))->toThrow(DomainException::class);
});

test('bank transactions cannot reference accounts from another workspace', function () {
    $user = User::factory()->create();
    $workspace = createTestWorkspaceFor($user, 'Workspace Principal');
    $foreignUser = User::factory()->create();
    $foreignWorkspace = createTestWorkspaceFor($foreignUser, 'Workspace Externo');

    Auth::logout();
    $foreignAccount = BankAccount::create([
        'workspace_id' => $foreignWorkspace->id,
        'user_id' => $foreignUser->id,
        'name' => 'Conta externa',
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
    ]);

    $this->actingAs($user);
    $user->update(['current_workspace_id' => $workspace->id]);

    expect(fn () => BankTransaction::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'bank_account_id' => $foreignAccount->id,
        'transaction_date' => now()->toDateString(),
        'amount' => 10,
        'currency' => 'EUR',
        'description' => 'Transação inválida',
    ]))->toThrow(DomainException::class);
});
