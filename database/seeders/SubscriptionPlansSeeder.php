<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(['slug' => 'pro'], [
            'name' => 'Pro',
            'price' => 5.00,
            'description' => 'Acesso completo às ferramentas pessoais e IA Pilot.',
            'stripe_price_id' => env('STRIPE_PRICE_PRO'),
            'features' => ['ia_access', 'advanced_reports', 'inventory', 'ads_free'],
            'is_active' => true,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'business'], [
            'name' => 'Business',
            'price' => 10.00,
            'description' => 'Gestão empresarial total, faturação e controlo de equipa.',
            'stripe_price_id' => env('STRIPE_PRICE_BUSINESS'),
            'features' => ['ia_access', 'business_mode', 'inventory', 'advanced_reports', 'priority_support'],
            'is_active' => true,
        ]);
    }
}
