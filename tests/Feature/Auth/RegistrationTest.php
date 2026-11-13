<?php

namespace Tests\Feature\Auth;

use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register')
        ->assertSee('País de trabalho')
        ->assertSee('🇵🇹 Portugal')
        ->assertSee('🇧🇷 Brasil');
});

test('new users can register', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('date_of_birth', '1990-01-15')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('verification.notice', absolute: false));

    $this->assertAuthenticated();
    expect(auth()->user()->date_of_birth->toDateString())->toBe('1990-01-15');
    expect(auth()->user()->country_of_origin)->toBe('Portugal');
    expect(auth()->user()->work_country)->toBe('PT');
});

test('registration rejects a future date of birth', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('date_of_birth', now()->addDay()->toDateString())
        ->set('email', 'future-birth-date@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register')
        ->assertHasErrors(['date_of_birth']);

    $this->assertDatabaseMissing('users', ['email' => 'future-birth-date@example.com']);
});
