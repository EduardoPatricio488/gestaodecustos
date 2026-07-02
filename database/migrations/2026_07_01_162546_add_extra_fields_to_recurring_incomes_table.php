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
        Schema::table('recurring_incomes', function (Blueprint $table) {
            // Verifica a coluna 'source'
            if (!Schema::hasColumn('recurring_incomes', 'source')) {
                $table->string('source')->default('emprego')->after('is_active');
            }

            // Verifica a coluna 'frequency'
            if (!Schema::hasColumn('recurring_incomes', 'frequency')) {
                $table->string('frequency')->default('mensal')->after('source');
            }

            // Verifica a coluna 'tax_estimate'
            if (!Schema::hasColumn('recurring_incomes', 'tax_estimate')) {
                $table->decimal('tax_estimate', 5, 2)->nullable()->after('frequency');
            }

            // Verifica a coluna 'notes'
            if (!Schema::hasColumn('recurring_incomes', 'notes')) {
                $table->text('notes')->nullable()->after('tax_estimate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recurring_incomes', function (Blueprint $table) {
            $table->dropColumn(['source', 'frequency', 'tax_estimate', 'notes']);
        });
    }
};
