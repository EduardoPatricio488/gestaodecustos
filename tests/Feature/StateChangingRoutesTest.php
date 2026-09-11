<?php

use Illuminate\Support\Facades\Route;

test('workspace context routes only accept POST', function () {
    expect(Route::getRoutes()->getByName('workspace.switch')->methods())->toContain('POST')->not->toContain('GET');
    expect(Route::getRoutes()->getByName('hub.business.exit')->methods())->toContain('POST')->not->toContain('GET');
    expect(Route::getRoutes()->getByName('workspace.switch.fast')->methods())->toContain('POST')->not->toContain('GET');
});

test('strava disconnect only accepts POST', function () {
    expect(Route::getRoutes()->getByName('strava.disconnect')->methods())->toContain('POST')->not->toContain('GET');
});
