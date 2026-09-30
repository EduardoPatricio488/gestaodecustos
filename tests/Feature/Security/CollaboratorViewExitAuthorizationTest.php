<?php

use App\Models\User;

it('does not allow guests to change collaborator-view session state', function () {
    $this->withSession(['viewing_as_collaborator_id' => 123])
        ->post(route('hub.business.stop-viewing-collaborator'))
        ->assertRedirect(route('login'));
});

it('allows an authenticated user to leave collaborator view', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['viewing_as_collaborator_id' => 123])
        ->post(route('hub.business.stop-viewing-collaborator'))
        ->assertRedirect(route('hub.business.dashboard'))
        ->assertSessionMissing('viewing_as_collaborator_id');
});
