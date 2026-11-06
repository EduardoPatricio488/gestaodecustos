<?php

use App\Models\BankAccount;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BancoService;

test('bank overview filters account balances by bank name', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);

    $user->forceFill(['current_workspace_id' => $workspace->id])->save();

    BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Conta à ordem',
        'bank_name' => 'Banco A',
        'type' => 'corrente',
        'balance' => 125,
        'currency' => 'EUR',
    ]);

    BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Conta poupança',
        'bank_name' => 'Banco B',
        'type' => 'poupanca',
        'balance' => 375,
        'currency' => 'EUR',
    ]);

    $allBanks = new BancoService($workspace->id, $user->id);
    $selectedBank = new BancoService($workspace->id, $user->id, 'Banco A');

    expect($allBanks->getSummary()['total_bank_balance'])->toBe(500.0)
        ->and($selectedBank->getSummary()['total_bank_balance'])->toBe(125.0)
        ->and($selectedBank->getStats()['total_accounts'])->toBe(1);
});
