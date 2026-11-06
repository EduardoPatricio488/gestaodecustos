<?php

use App\Livewire\BancoHub;
use App\Models\BankAccount;
use App\Models\User;
use App\Models\Workspace;
use Livewire\Livewire;

test('bank hub creates an account without mass assigning a null id', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(BancoHub::class)
        ->set('modalType', 'account')
        ->set('acc_name', 'Santander')
        ->set('acc_type', 'corrente')
        ->set('acc_balance', 100)
        ->set('acc_currency', 'EUR')
        ->set('acc_color', '#6366f1')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('bank_accounts', [
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Santander',
        'balance' => 100,
    ]);
});

test('bank hub deletes an account from the current personal workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $account = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => "Conta d'uso diário",
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
    ]);

    Livewire::test(BancoHub::class)
        ->assertSee("Eliminar conta Conta d'uso diário")
        ->assertSee('pointer-events-none absolute inset-0')
        ->assertSee('aria-label="Editar conta', false)
        ->call('deleteAccount', $account->id)
        ->assertHasNoErrors();

    expect(BankAccount::find($account->id))->toBeNull();
});

test('bank hub cannot delete another users personal account', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $owner->id,
        'type' => 'personal',
    ]);
    $owner->forceFill(['current_workspace_id' => $workspace->id])->save();
    $otherUser->forceFill(['current_workspace_id' => $workspace->id])->save();
    $account = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $otherUser->id,
        'name' => 'Conta privada',
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
    ]);
    $this->actingAs($owner);

    expect(fn () => Livewire::test(BancoHub::class)->call('deleteAccount', $account->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    expect(BankAccount::withoutGlobalScopes()->find($account->id))->not->toBeNull();
});
