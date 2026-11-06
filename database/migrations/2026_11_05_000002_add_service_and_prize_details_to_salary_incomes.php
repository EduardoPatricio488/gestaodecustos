<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('salary_income_type')->nullable()->after('salary_recurring_income_id');
            $table->decimal('service_unit_amount', 12, 2)->nullable()->after('salary_income_type');
            $table->unsignedInteger('service_quantity')->nullable()->after('service_unit_amount');
        });

        DB::table('incomes')
            ->whereNotNull('salary_recurring_income_id')
            ->update([
                'salary_income_type' => 'service',
                'service_unit_amount' => DB::raw('amount'),
                'service_quantity' => 1,
            ]);

        DB::table('incomes')
            ->whereNotNull('salary_recurring_income_id')
            ->where('description', 'like', 'Prémio —%')
            ->update([
                'salary_income_type' => 'prize',
                'service_unit_amount' => null,
                'service_quantity' => null,
            ]);
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn(['salary_income_type', 'service_unit_amount', 'service_quantity']);
        });
    }
};
