<?php

use App\Livewire\OnboardingWizard;
use App\Models\User;
use Livewire\Livewire;

test('onboarding estimates net pay from the 2026 withholding tables', function () {
    $user = User::factory()->create([
        'name' => 'Eduardo Silva',
        'onboarding_completed' => false,
    ]);

    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->assertSet('firstName', 'Eduardo')
        ->set('step', 2)
        ->set('salaryGross', 1500)
        ->assertSet('irsAmount', 168.17)
        ->assertSet('ssAmount', 165.0)
        ->assertSet('salaryAmount', 1166.83)
        ->assertSee('Recibo estimado')
        ->assertSee('1 166,83');
});
