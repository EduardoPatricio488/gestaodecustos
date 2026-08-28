<?php

use App\Livewire\Business\BusinessGateway;
use App\Livewire\Business\TeamHub;
use App\Models\Employee;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

function employeeInviteFixture(array $overrides = []): array
{
    $owner = User::factory()->create();
    $workspace = Workspace::create([
        'name' => $overrides['workspace_name'] ?? 'Empresa A',
        'type' => 'business',
        'owner_id' => $owner->id,
        'plan' => 'pro',
    ]);
    $token = $overrides['token'] ?? 'InviteTokenA123456789012345678901234567890123456789012345678901234';

    $employee = Employee::create([
        'user_id' => $overrides['user_id'] ?? null,
        'workspace_id' => $workspace->id,
        'name' => $overrides['name'] ?? 'Colaborador A',
        'role' => 'Editor',
        'salary' => 1500,
        'active' => $overrides['active'] ?? true,
        'suspended' => $overrides['suspended'] ?? false,
        'terminated_at' => $overrides['terminated_at'] ?? null,
        'portal_token' => Hash::make($token),
        'invite_expires_at' => $overrides['expires_at'] ?? now()->addDays(7),
        'invite_used_at' => $overrides['used_at'] ?? null,
        'invite_revoked_at' => $overrides['revoked_at'] ?? null,
    ]);

    return compact('owner', 'workspace', 'employee', 'token');
}

function acceptEmployeeInvite(array $fixture, ?User $user = null): void
{
    Livewire::actingAs($user ?? User::factory()->create())
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator');
}

test('convite válido associa o utilizador ao workspace correto', function () {
    $fixture = employeeInviteFixture();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator');

    expect($fixture['employee']->fresh()->user_id)->toBe($user->id)
        ->and($fixture['employee']->fresh()->invite_used_at)->not->toBeNull()
        ->and($fixture['employee']->fresh()->portal_token)->toBeNull()
        ->and($user->fresh()->current_workspace_id)->toBe($fixture['workspace']->id)
        ->and($user->fresh()->workspaces()->whereKey($fixture['workspace']->id)->exists())->toBeTrue();
});

test('convite inválido falha sem associar utilizador', function () {
    $fixture = employeeInviteFixture();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BusinessGateway::class)
        ->set('accessCode', 'invalid-invite-token-1234567890123456789012345678901234567890')
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');

    expect($fixture['employee']->fresh()->user_id)->toBeNull();
});

test('convite expirado falha', function () {
    $fixture = employeeInviteFixture(['expires_at' => now()->subMinute()]);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');

    expect($fixture['employee']->fresh()->user_id)->toBeNull();
});

test('convite revogado falha', function () {
    $fixture = employeeInviteFixture(['revoked_at' => now()]);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');
});

test('convite já utilizado e funcionário associado não pode ser reutilizado', function () {
    $existingUser = User::factory()->create();
    $fixture = employeeInviteFixture([
        'user_id' => $existingUser->id,
        'used_at' => now(),
    ]);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');

    expect($fixture['employee']->fresh()->user_id)->toBe($existingUser->id);
});

test('funcionário inativo não pode aceitar convite', function () {
    $fixture = employeeInviteFixture(['active' => false]);

    Livewire::actingAs(User::factory()->create())
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');
});

test('funcionário suspenso não pode aceitar convite', function () {
    $fixture = employeeInviteFixture(['suspended' => true]);

    Livewire::actingAs(User::factory()->create())
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');
});

test('funcionário terminado não pode aceitar convite', function () {
    $fixture = employeeInviteFixture(['terminated_at' => now()]);

    Livewire::actingAs(User::factory()->create())
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');
});

test('convite de um workspace não dá acesso a outro workspace', function () {
    $fixtureA = employeeInviteFixture();
    $fixtureB = employeeInviteFixture([
        'workspace_name' => 'Empresa B',
        'token' => 'InviteTokenB123456789012345678901234567890123456789012345678901234',
    ]);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixtureA['token'])
        ->call('joinAsCollaborator');

    expect($user->fresh()->current_workspace_id)->toBe($fixtureA['workspace']->id)
        ->and($user->fresh()->current_workspace_id)->not->toBe($fixtureB['workspace']->id);
});

test('o mesmo convite não pode ser utilizado duas vezes', function () {
    $fixture = employeeInviteFixture();
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    acceptEmployeeInvite($fixture, $firstUser);
    Livewire::actingAs($secondUser)
        ->test(BusinessGateway::class)
        ->set('accessCode', $fixture['token'])
        ->call('joinAsCollaborator')
        ->assertHasErrors('accessCode');

    expect($fixture['employee']->fresh()->user_id)->toBe($firstUser->id);
});

test('tentativas de brute force são limitadas', function () {
    $fixture = employeeInviteFixture();
    $code = 'invalid-invite-token-1234567890123456789012345678901234567890';
    $key = 'employee-invite:'.request()->ip();
    RateLimiter::clear($key);

    foreach (range(1, 6) as $attempt) {
        Livewire::actingAs(User::factory()->create())
            ->test(BusinessGateway::class)
            ->set('accessCode', $code)
            ->call('joinAsCollaborator');
    }

    expect(RateLimiter::tooManyAttempts($key, 5))->toBeTrue();
});

test('administrador consegue revogar um convite', function () {
    $fixture = employeeInviteFixture();
    $fixture['owner']->update(['current_workspace_id' => $fixture['workspace']->id]);

    Livewire::actingAs($fixture['owner'])
        ->test(TeamHub::class)
        ->call('revokeEmployeeAccessCode', $fixture['employee']->id);

    expect($fixture['employee']->fresh()->portal_token)->toBeNull()
        ->and($fixture['employee']->fresh()->invite_revoked_at)->not->toBeNull();
});
