<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('free users can access the main planning and analysis routes', function () {
    $user = User::factory()->create(['plan' => 'free']);
    $this->actingAs($user);

    collect([
        route('hub.split'),
        route('hub.anomalies'),
        route('hub.expense-forecast'),
        route('hub.investments'),
        route('hub.subscriptions'),
        route('hub.subscriptions.scanner'),
        route('hub.networth'),
        route('hub.retirement'),
        route('hub.inflation'),
    ])->each(function (string $url) {
        $this->get($url)->assertOk();
    });
});
