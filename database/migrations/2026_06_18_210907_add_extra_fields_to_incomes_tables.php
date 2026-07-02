<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('source')->nullable()->after('type');        // emprego, freelance, investimento, outro
            $table->string('frequency')->nullable()->after('source');   // mensal, semanal, anual, pontual
            $table->decimal('tax_estimate', 8, 2)->nullable()->after('frequency'); // imposto estimado (%)
            $table->text('notes')->nullable()->after('tax_estimate');
        });

        Schema::table('recurring_incomes', function (Blueprint $table) {
            $table->string('source')->nullable()->after('is_active');
            $table->string('frequency')->default('mensal')->after('source');
            $table->decimal('tax_estimate', 8, 2)->nullable()->after('frequency');
            $table->text('notes')->nullable()->after('tax_estimate');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn(['source', 'frequency', 'tax_estimate', 'notes']);
        });

        Schema::table('recurring_incomes', function (Blueprint $table) {
            $table->dropColumn(['source', 'frequency', 'tax_estimate', 'notes']);
        });
    }
};
