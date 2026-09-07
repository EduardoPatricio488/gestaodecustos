<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove as assinaturas duplicadas do próprio plano Finance Pro, criadas por um bug do
     * fluxo de sugestão pós-checkout (agora substituído por um cartão automático e não duplicável).
     */
    public function up(): void
    {
        DB::table('subscriptions')
            ->where('name', 'like', 'Finance Pro %')
            ->where('payment_method', 'Stripe / Cartão')
            ->delete();
    }

    public function down(): void
    {
        // Dados de limpeza: não há como (nem motivo para) reverter.
    }
};
