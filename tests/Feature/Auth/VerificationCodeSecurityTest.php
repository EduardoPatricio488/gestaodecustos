<?php

use App\Mail\VerifyAccountMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Volt;

it('stores registration verification codes only as hashes', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Teste Segurança',
        'date_of_birth' => '1990-01-15',
        'country_of_origin' => 'Portugal',
        'work_country' => 'PT',
        'email' => 'verification-security@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();

    $user = User::where('email', 'verification-security@example.com')->firstOrFail();

    expect($user->date_of_birth->toDateString())->toBe('1990-01-15')
        ->and($user->verification_code)->toBeNull()
        ->and($user->verification_code_hash)->toBeString()->toHaveLength(64)
        ->and($user->verification_code_expires_at)->not->toBeNull()
        ->and($user->verification_code_attempts)->toBe(0);

    Mail::assertQueued(VerifyAccountMail::class);
});

it('resends a verification code without keeping the legacy plaintext code', function () {
    Mail::fake();
    $user = User::factory()->unverified()->create([
        'verification_code_hash' => hash('sha256', '123456'),
        'verification_code_expires_at' => now()->addMinute(),
        'verification_code_attempts' => 4,
    ]);
    $user->forceFill(['verification_code' => '123456'])->save();

    $this->actingAs($user)
        ->from('/verify-email')
        ->post('/email/verification-notification')
        ->assertRedirect('/verify-email')
        ->assertSessionHas('status', 'verification-link-sent');

    $fresh = $user->fresh();
    expect($fresh->verification_code)->toBeNull()
        ->and($fresh->verification_code_attempts)->toBe(0)
        ->and($fresh->verification_code_expires_at->isFuture())->toBeTrue();
    Mail::assertQueued(VerifyAccountMail::class, fn (VerifyAccountMail $mail) => $mail->hasTo($user->email)
        && hash_equals($fresh->verification_code_hash, hash('sha256', $mail->code)));
});

it('sends verification emails immediately in local development without a queue worker', function () {
    app()->instance('env', 'local');
    config(['queue.default' => 'database', 'mail.default' => 'array']);
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->withSession(['_token' => 'test-token'])
        ->from('/verify-email')
        ->post('/email/verification-notification', ['_token' => 'test-token'])
        ->assertRedirect('/verify-email')
        ->assertSessionHas('status', 'verification-link-sent');

    $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
    expect($messages)->toHaveCount(1);
    $subject = $messages->first()->getOriginalMessage()->getSubject();
    expect(preg_match('/^(\d{6}) /', $subject, $matches))->toBe(1)
        ->and(hash('sha256', $matches[1]))->toBe($user->fresh()->verification_code_hash);
    $this->assertDatabaseCount('jobs', 0);
});

it('shows instructions to retrieve the verification code from email', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertOk()
        ->assertSee('Consulta o teu email e introduz abaixo o código de segurança de seis dígitos.')
        ->assertDontSee('Copia os dígitos acima');
});

it('reports a registration email failure and invalidates the unsent code', function () {
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Mail unavailable'));

    Volt::test('pages.auth.register')
        ->set('name', 'Teste')
        ->set('date_of_birth', '1990-01-15')
        ->set('email', 'mail-failure@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('verification.notice', absolute: false));

    $user = User::where('email', 'mail-failure@example.com')->firstOrFail();
    expect($user->verification_code_hash)->toBeNull()
        ->and($user->verification_code_expires_at)->toBeNull();
    $this->get('/verify-email')
        ->assertSee('Não foi possível enviar o código. Tenta reenviá-lo.');
});

it('clears the new verification code when resending fails', function () {
    $user = User::factory()->unverified()->create();
    $user->forceFill(['verification_code' => '123456'])->save();
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Mail unavailable'));

    $this->actingAs($user)
        ->from('/verify-email')
        ->post('/email/verification-notification')
        ->assertRedirect('/verify-email')
        ->assertSessionHasErrors('code');

    $fresh = $user->fresh();
    expect($fresh->verification_code)->toBeNull()
        ->and($fresh->verification_code_hash)->toBeNull()
        ->and($fresh->verification_code_expires_at)->toBeNull()
        ->and($fresh->verification_code_attempts)->toBe(0);
});

it('rejects expired verification codes', function () {
    $user = User::factory()->unverified()->create([
        'verification_code_hash' => hash('sha256', '123456'),
        'verification_code_expires_at' => now()->subMinute(),
        'verification_code_attempts' => 0,
    ]);

    Auth::login($user);

    $this->post('/verificar-codigo', ['code' => '123456'])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse()
        ->and($user->fresh()->verification_code_hash)->toBeNull();
});

it('blocks verification after five failed attempts', function () {
    $user = User::factory()->unverified()->create([
        'verification_code_hash' => hash('sha256', '123456'),
        'verification_code_expires_at' => now()->addMinutes(10),
        'verification_code_attempts' => 5,
    ]);

    Auth::login($user);

    $this->post('/verificar-codigo', ['code' => '123456'])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('accepts a valid unexpired verification code and clears it', function () {
    $user = User::factory()->unverified()->create([
        'verification_code_hash' => hash('sha256', '123456'),
        'verification_code_expires_at' => now()->addMinutes(10),
        'verification_code_attempts' => 0,
    ]);

    Auth::login($user);

    $this->post('/verificar-codigo', ['code' => '123456'])
        ->assertRedirect(route('dashboard', absolute: false));

    $fresh = $user->fresh();

    expect($fresh->hasVerifiedEmail())->toBeTrue()
        ->and($fresh->verification_code_hash)->toBeNull()
        ->and($fresh->verification_code_expires_at)->toBeNull()
        ->and($fresh->verification_code_attempts)->toBe(0);
});
