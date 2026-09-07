<?php

use App\Models\SubscriptionPlan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Garante que os planos Pro e Business existem sempre em produção, tal como o Free.
     */
    public function up(): void
    {
        SubscriptionPlan::updateOrCreate(['slug' => 'pro'], [
            'name' => 'Pro',
            'price' => 5.00,
            'description' => 'Acesso completo às ferramentas pessoais e IA Pilot.',
            'stripe_price_id' => 'price_1TosJDH35BygzIwGXxaIKBjZ',
            'features' => ['ia_access', 'advanced_reports', 'inventory', 'ads_free'],
            'is_active' => true,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'business'], [
            'name' => 'Business',
            'price' => 10.00,
            'description' => 'Gestão empresarial total, faturação e controlo de equipa.',
            'stripe_price_id' => 'price_1TosJuH35BygzIwGL7R3R2TH',
            'features' => ['ia_access', 'business_mode', 'inventory', 'advanced_reports', 'priority_support'],
            'is_active' => true,
        ]);
    }

    public function down(): void
    {
        // Não remove os planos core no rollback.
    }
};
