<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.login');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password');

    $component->call('login');

    $component
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('quick records selection continues directly to mobile after login', function () {
    $user = User::factory()->create();

    $this->post(route('app.launch.choose'), ['mode' => 'mobile'])
        ->assertRedirect(route('login'));

    $this->get(route('login'))
        ->assertSee('id="fp-account-loading"', escape: false)
        ->assertSee('wire:loading.flex', escape: false)
        ->assertSee('wire:target="login"', escape: false);

    Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.home', absolute: false));

    $this->assertAuthenticated();
    $this->assertSame('mobile', session('mobile_access_mode'));
});

test('the mobile loading screen is not shown for a normal login', function () {
    $this->get(route('login'))->assertDontSee('id="fp-account-loading"', escape: false);
});

test('failed mobile login keeps the quick records selection for retry', function () {
    $user = User::factory()->create();
    $this->post(route('app.launch.choose'), ['mode' => 'mobile']);

    Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrong-password')
        ->call('login')
        ->assertHasErrors()
        ->assertNoRedirect()
        ->assertSee('A preparar a tua conta');

    $this->assertGuest();
    $this->assertTrue(session('pending_mobile_login'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrong-password');

    $component->call('login');

    $component
        ->assertHasErrors()
        ->assertNoRedirect();

    $this->assertGuest();
});

test('navigation menu can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertOk();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('layout.navigation');

    $component->call('logout');

    $component
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
});
