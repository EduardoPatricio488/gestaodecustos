<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response
        ->assertOk()
        ->assertSeeVolt('profile.update-profile-information-form')
        ->assertSeeVolt('profile.update-password-form')
        ->assertSeeVolt('profile.delete-user-form');
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-profile-information-form')
        ->set('name', 'Test User')
        ->set('date_of_birth', '1990-01-15')
        ->set('country_of_origin', 'Brasil')
        ->set('work_country', 'FR')
        ->call('updateProfileInformation');

    $component
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $originalEmail = $user->email;
    $user->refresh();

    // O email é bloqueado por design (não editável neste formulário)
    $this->assertSame('Test User', $user->name);
    $this->assertSame($originalEmail, $user->email);
    $this->assertSame('1990-01-15', $user->date_of_birth->toDateString());
    $this->assertSame('Brasil', $user->country_of_origin);
    $this->assertSame('FR', $user->work_country);
});

test('profile rejects a future date of birth', function () {
    $user = User::factory()->create([
        'date_of_birth' => '1990-01-15',
    ]);

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('date_of_birth', now()->addDay()->toDateString())
        ->call('updateProfileInformation')
        ->assertHasErrors(['date_of_birth']);

    $this->assertSame('1990-01-15', $user->fresh()->date_of_birth->toDateString());
});

test('profile rejects an invalid work country without modifying the stored country', function () {
    $user = User::factory()->create(['work_country' => 'PT']);
    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('work_country', 'XX')
        ->call('updateProfileInformation')
        ->assertHasErrors(['work_country']);

    $this->assertSame('PT', $user->fresh()->work_country);
});

test('an existing free-text country of origin is preserved when updating the profile', function () {
    $user = User::factory()->create(['country_of_origin' => 'Nome antigo']);
    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('name', 'Novo Nome')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $this->assertSame('Nome antigo', $user->fresh()->country_of_origin);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-profile-information-form')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $component
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $component
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $component
        ->assertHasErrors('password')
        ->assertNoRedirect();

    $this->assertNotNull($user->fresh());
});
