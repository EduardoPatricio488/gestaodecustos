<?php

use App\Livewire\Public\BankDashboard;
use App\Livewire\Public\BankPortal;
use App\Models\BankAccount;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

function bankPortalFixture(array $overrides = []): array
{
    $owner = User::factory()->create();
    $token = $overrides['token'] ?? 'BankPortalTokenA123456789012345678901234567890123456789012345678901';
    $workspace = Workspace::create([
        'name' => $overrides['name'] ?? 'Banco Workspace A',
        'type' => 'business',
        'owner_id' => $owner->id,
        'tax_number' => $overrides['tax_number'] ?? '509882314',
        'audit_token' => Hash::make($token),
        'audit_token_expires_at' => $overrides['expires_at'] ?? now()->addDays(30),
        'audit_token_revoked_at' => $overrides['revoked_at'] ?? null,
        'audit_token_purpose' => 'bank_audit',
    ]);

    $account = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $owner->id,
        'name' => 'Conta Principal',
        'type' => 'corrente',
        'bank_name' => $overrides['bank_name'] ?? 'Banco A',
        'balance' => 1250,
        'currency' => 'EUR',
    ]);

    return compact('owner', 'token', 'workspace', 'account');
}

function authenticateBankPortal(array $fixture): void
{
    Livewire::test(BankPortal::class)
        ->set('company_nif', $fixture['workspace']->tax_number)
        ->set('token', $fixture['token'])
        ->call('login');
}

test('token válido permite acesso apenas às contas do workspace autenticado', function () {
    $workspaceA = bankPortalFixture();
    $workspaceB = bankPortalFixture([
        'name' => 'Banco Workspace B',
        'tax_number' => '501234567',
        'token' => 'BankPortalTokenB123456789012345678901234567890123456789012345678901',
        'bank_name' => 'Banco B',
    ]);

    authenticateBankPortal($workspaceA);
    $dashboard = Livewire::test(BankDashboard::class);

    expect($dashboard->get('workspace')->id)->toBe($workspaceA['workspace']->id)
        ->and($dashboard->viewData('accounts')->pluck('id')->all())->toBe([$workspaceA['account']->id])
        ->and($dashboard->viewData('accounts')->pluck('id')->all())->not->toContain($workspaceB['account']->id);
});

test('token inválido não permite acesso', function () {
    $fixture = bankPortalFixture();

    Livewire::test(BankPortal::class)
        ->set('company_nif', $fixture['workspace']->tax_number)
        ->set('token', 'token-invalido-que-nao-corresponde-ao-hash')
        ->call('login');

    expect(session()->has('bank_portal_workspace_id'))->toBeFalse();
});

test('token de um workspace não autentica outro workspace', function () {
    $workspaceA = bankPortalFixture();
    $workspaceB = bankPortalFixture([
        'name' => 'Banco Workspace B',
        'tax_number' => '501234567',
        'token' => 'BankPortalTokenB123456789012345678901234567890123456789012345678901',
    ]);

    Livewire::test(BankPortal::class)
        ->set('company_nif', $workspaceA['workspace']->tax_number)
        ->set('token', $workspaceB['token'])
        ->call('login');

    expect(session()->has('bank_portal_workspace_id'))->toBeFalse();
});

test('token expirado não permite acesso', function () {
    $fixture = bankPortalFixture(['expires_at' => now()->subMinute()]);

    Livewire::test(BankPortal::class)
        ->set('company_nif', $fixture['workspace']->tax_number)
        ->set('token', $fixture['token'])
        ->call('login');

    expect(session()->has('bank_portal_workspace_id'))->toBeFalse();
});

test('token revogado não permite acesso', function () {
    $fixture = bankPortalFixture(['revoked_at' => now()]);

    Livewire::test(BankPortal::class)
        ->set('company_nif', $fixture['workspace']->tax_number)
        ->set('token', $fixture['token'])
        ->call('login');

    expect(session()->has('bank_portal_workspace_id'))->toBeFalse();
});

test('token antigo deixa de funcionar depois da rotação', function () {
    $fixture = bankPortalFixture();
    authenticateBankPortal($fixture);

    $rotatedToken = 'BankPortalRotatedToken12345678901234567890123456789012345678901234';
    $fixture['workspace']->update([
        'audit_token' => Hash::make($rotatedToken),
        'audit_token_expires_at' => now()->addDays(30),
        'audit_token_revoked_at' => null,
    ]);
    session()->forget('bank_portal_workspace_id');

    Livewire::test(BankPortal::class)
        ->set('company_nif', $fixture['workspace']->tax_number)
        ->set('token', $fixture['token'])
        ->call('login');

    expect(session()->has('bank_portal_workspace_id'))->toBeFalse();
});

test('tentativas de brute force são limitadas', function () {
    $fixture = bankPortalFixture();
    $key = 'bank-portal:'.sha1($fixture['workspace']->tax_number.'|'.request()->ip());
    RateLimiter::clear($key);

    foreach (range(1, 6) as $attempt) {
        Livewire::test(BankPortal::class)
            ->set('company_nif', $fixture['workspace']->tax_number)
            ->set('token', 'token-invalido-que-nao-corresponde-ao-hash')
            ->call('login');
    }

    expect(RateLimiter::tooManyAttempts($key, 5))->toBeTrue();
});

test('dados bancários não são expostos sem autenticação', function () {
    $this->get(route('bank.dashboard'))->assertForbidden();
});

test('a revogação invalida a sessão existente no dashboard', function () {
    $fixture = bankPortalFixture();
    authenticateBankPortal($fixture);
    $fixture['workspace']->update(['audit_token_revoked_at' => now()]);

    $this->get(route('bank.dashboard'))->assertForbidden();
});
