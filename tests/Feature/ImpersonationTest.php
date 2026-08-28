<?php

use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Carbon;

function impersonationUsers(): array
{
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_admin' => true,
        'is_active' => true,
    ]);
    $target = User::factory()->create([
        'role' => 'user',
        'is_admin' => false,
        'is_active' => true,
    ]);

    return compact('admin', 'target');
}

test('administrador autorizado consegue impersonar utilizador ativo permitido', function () {
    $users = impersonationUsers();

    $response = $this->actingAs($users['admin'])
        ->post(route('admin.impersonate', $users['target']));

    $response->assertRedirect(route('dashboard', absolute: false));
    expect(auth()->id())->toBe($users['target']->id)
        ->and(session('admin_impersonation.actor_id'))->toBe($users['admin']->id)
        ->and(session('admin_impersonation.target_id'))->toBe($users['target']->id)
        ->and(session('admin_impersonation.started_at'))->not->toBeNull()
        ->and(session('admin_impersonation.expires_at'))->not->toBeNull();

    expect(ImpersonationLog::where('actor_user_id', $users['admin']->id)
        ->where('target_user_id', $users['target']->id)
        ->where('action', 'started')->exists())->toBeTrue();
});

test('utilizador normal não consegue iniciar impersonation', function () {
    $users = impersonationUsers();
    $normal = User::factory()->create(['role' => 'user', 'is_admin' => false]);

    $this->actingAs($normal)
        ->post(route('admin.impersonate', $users['target']))
        ->assertRedirect(route('dashboard', absolute: false));

    expect(auth()->id())->toBe($normal->id);
});

test('administrador não consegue impersonar outro administrador', function () {
    $users = impersonationUsers();
    $otherAdmin = User::factory()->create(['role' => 'moderator', 'is_admin' => true]);

    $this->actingAs($users['admin'])
        ->post(route('admin.impersonate', $otherAdmin))
        ->assertForbidden();
});

test('utilizador desativado não pode ser impersonado', function () {
    $users = impersonationUsers();
    $users['target']->update(['is_active' => false]);

    $this->actingAs($users['admin'])
        ->post(route('admin.impersonate', $users['target']))
        ->assertForbidden();
});

test('GET não altera autenticação nem inicia impersonation', function () {
    $users = impersonationUsers();

    $this->actingAs($users['admin'])
        ->get(route('admin.impersonate', $users['target']))
        ->assertMethodNotAllowed();

    expect(auth()->id())->toBe($users['admin']->id)
        ->and(session()->has('admin_impersonation'))->toBeFalse();
});

test('POST autorizado inicia impersonation com CSRF protegido', function () {
    $users = impersonationUsers();

    $this->actingAs($users['admin'])
        ->post(route('admin.impersonate', $users['target']))
        ->assertRedirect();

    expect(auth()->id())->toBe($users['target']->id);
});

test('POST de impersonation usa middleware CSRF', function () {
    $middleware = app('router')->getRoutes()->getByName('admin.impersonate')->gatherMiddleware();

    expect(app('router')->getMiddlewareGroups()['web'])
        ->toContain(PreventRequestForgery::class);
});

test('sair da impersonation devolve ao administrador original e regista fim', function () {
    $users = impersonationUsers();
    $this->actingAs($users['admin'])->post(route('admin.impersonate', $users['target']));

    $response = $this->delete(route('admin.stop-impersonating'));

    $response->assertRedirect(route('admin.users', absolute: false));
    expect(auth()->id())->toBe($users['admin']->id)
        ->and(session()->has('admin_impersonation'))->toBeFalse()
        ->and(ImpersonationLog::where('actor_user_id', $users['admin']->id)
            ->where('target_user_id', $users['target']->id)
            ->where('action', 'ended')
            ->whereNotNull('ended_at')->exists())->toBeTrue();
});

test('impersonation expirada deixa de ser válida', function () {
    $users = impersonationUsers();
    $this->actingAs($users['admin'])->post(route('admin.impersonate', $users['target']));
    $context = session('admin_impersonation');
    $context['expires_at'] = Carbon::now()->subMinute()->toIso8601String();
    session()->put('admin_impersonation', $context);

    $response = $this->actingAs($users['target'])->get(route('dashboard'));

    $response->assertRedirect(route('admin.users', absolute: false));
    expect(auth()->id())->toBe($users['admin']->id)
        ->and(ImpersonationLog::where('id', $context['log_id'])->where('action', 'expired')->exists())->toBeTrue();
});

test('sessão adulterada não transforma utilizador normal em administrador', function () {
    $users = impersonationUsers();
    $normal = User::factory()->create(['role' => 'user', 'is_admin' => false]);

    $this->withSession([
        'admin_impersonation' => [
            'actor_id' => $normal->id,
            'target_id' => $users['admin']->id,
            'started_at' => now()->subMinute()->toIso8601String(),
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
        ],
    ])->actingAs($users['admin'])->get(route('dashboard'));

    expect(auth()->user())->toBeNull()
        ->and(session()->has('admin_impersonation'))->toBeFalse();
});

test('sair da impersonation exige DELETE', function () {
    $users = impersonationUsers();
    $this->actingAs($users['admin'])->post(route('admin.impersonate', $users['target']));

    $this->get(route('admin.stop-impersonating'))->assertMethodNotAllowed();
    expect(auth()->id())->toBe($users['target']->id);
});
