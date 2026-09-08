<?php

use App\Livewire\OnboardingWizard;
use App\Models\BankAccount;
use App\Models\RecurringIncome;
use App\Models\User;
use App\Models\Workspace;
use Livewire\Livewire;

test('onboarding estimates net pay from the 2026 withholding tables', function () {
    $user = User::factory()->create([
        'name' => 'Eduardo Silva',
        'onboarding_completed' => false,
    ]);

    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('salaryGross', 1500)
        ->assertSet('calculatedIRS', 168.17)
        ->assertSet('calculatedSS', 165.0)
        ->assertSet('salaryAmount', 1166.83);
});

test('onboarding creates the configured bank account before income setup', function () {
    $user = User::factory()->create([
        'onboarding_completed' => false,
    ]);
    $workspace = Workspace::create([
        'name' => 'As Minhas Finanças',
        'owner_id' => $user->id,
    ]);
    $user->update(['current_workspace_id' => $workspace->id]);

    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 2)
        ->set('bankAccounts.0.name', 'Conta principal')
        ->set('bankAccounts.0.bank_name', 'Banco Teste')
        ->set('bankAccounts.0.balance', 1250.50)
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 3);

    $this->assertDatabaseHas('bank_accounts', [
        'user_id' => $user->id,
        'name' => 'Conta principal',
        'bank_name' => 'Banco Teste',
        'balance' => 1250.50,
    ]);
});

test('onboarding links the selected bank account to the recurring income', function () {
    $user = User::factory()->create([
        'onboarding_completed' => false,
    ]);
    $workspace = Workspace::create([
        'name' => 'As Minhas Finanças',
        'owner_id' => $user->id,
    ]);
    $user->update(['current_workspace_id' => $workspace->id]);
    $account = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Conta principal',
        'type' => 'corrente',
        'balance' => 0,
        'currency' => 'EUR',
        'status' => 'active',
    ]);

    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('salaryGross', 1500)
        ->set('salaryBankAccountId', (string) $account->id)
        ->call('nextStep');

    expect(RecurringIncome::where('workspace_id', $workspace->id)->first()->bank_account_id)
        ->toBe($account->id);
});
