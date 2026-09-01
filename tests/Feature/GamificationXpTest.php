<?php

use App\Models\User;

it('awards xp and stores the action message for the user', function () {
    $user = User::factory()->create([
        'xp' => 0,
        'level' => 1,
    ]);

    $awarded = $user->awardXp(30, 'despesa registada');

    expect($awarded)->toBe(30)
        ->and($user->fresh()->xp)->toBe(30)
        ->and(session('xp_award'))->toBe('🏆 +30 XP · despesa registada');
});
