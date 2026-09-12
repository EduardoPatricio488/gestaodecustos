<?php

use App\Mail\VerifyAccountMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

it('stores registration verification codes only as hashes', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Teste Segurança',
        'email' => 'verification-security@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();

    $user = User::where('email', 'verification-security@example.com')->firstOrFail();

    expect($user->getAttributes())->not->toHaveKey('verification_code')
        ->and($user->verification_code_hash)->toBeString()->toHaveLength(64)
        ->and($user->verification_code_expires_at)->not->toBeNull()
        ->and($user->verification_code_attempts)->toBe(0);

    Mail::assertSent(VerifyAccountMail::class);
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
