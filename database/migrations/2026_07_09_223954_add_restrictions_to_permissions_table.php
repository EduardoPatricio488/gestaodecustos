<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('family_budget_permissions', function (Blueprint $table) {
        // Módulos Premium
        $table->boolean('restrict_business')->default(false);
        $table->boolean('restrict_store')->default(false);
        $table->boolean('restrict_fitness')->default(false);

        // Finanças
        $table->boolean('restrict_budget')->default(false);
        $table->boolean('restrict_import')->default(false);
        $table->boolean('restrict_incomes')->default(false);
        $table->boolean('restrict_debts')->default(false);
        $table->boolean('restrict_investments')->default(false);
        $table->boolean('restrict_subs')->default(false);
        $table->boolean('restrict_networth')->default(false);
        $table->boolean('restrict_bank')->default(false);

        // Ferramentas
        $table->boolean('restrict_calendar')->default(false);
        $table->boolean('restrict_reminders')->default(false);
        $table->boolean('restrict_goals')->default(false);
        $table->boolean('restrict_wrapped')->default(false);
    });
}

public function down(): void
{
    Schema::table('family_budget_permissions', function (Blueprint $table) {
        $table->dropColumn([
            'restrict_business', 'restrict_store', 'restrict_fitness',
            'restrict_budget', 'restrict_import', 'restrict_incomes',
            'restrict_debts', 'restrict_investments', 'restrict_subs',
            'restrict_networth', 'restrict_bank',
            'restrict_calendar', 'restrict_reminders', 'restrict_goals', 'restrict_wrapped'
        ]);
    });


    /**
     * Reverse the migrations.
     */
}
};
