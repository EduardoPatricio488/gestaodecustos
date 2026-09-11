<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed dados de referência seguros para ambientes novos.
     *
     * Dados de demonstração e contas privilegiadas NÃO são criados aqui.
     * Para uma demonstração local, use DemoSeeder com DEMO_SEED_ENABLED=true.
     */
    public function run(): void
    {
        $this->call([
            SubscriptionPlansSeeder::class,
            BadgeSeeder::class,
            CommunityChallengeSeeder::class,
            StoreProductSeeder::class,
        ]);

        $this->command?->info('Dados base do Finance Pro AI preparados.');
        $this->command?->info('Para uma demonstração completa, configure DEMO_SEED_ENABLED=true e execute DemoSeeder.');
    }
}
