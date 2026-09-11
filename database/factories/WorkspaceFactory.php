<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workspace>
 */
class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => 'business',
            'owner_id' => User::factory(),
            'currency' => 'EUR',
            'country_code' => 'PT',
            'vat_rate' => 23,
            'vat_regime' => 'normal',
            'plan' => 'pro',
            'fiscal_year_start' => 1,
        ];
    }
}
