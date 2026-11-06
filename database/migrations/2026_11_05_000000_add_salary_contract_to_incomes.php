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
            $table->foreignId('salary_recurring_income_id')
                ->nullable()
                ->after('workspace_id')
                ->constrained('recurring_incomes')
                ->nullOnDelete();
        });

        DB::table('recurring_incomes')
            ->where('source', 'emprego')
            ->whereNotNull('metadata')
            ->get(['id', 'user_id', 'workspace_id', 'metadata'])
            ->each(function (object $salary): void {
                $metadata = json_decode((string) $salary->metadata, true);
                $bonusIncomeId = $metadata['bonus']['income_id'] ?? null;

                if (! $bonusIncomeId) {
                    return;
                }

                DB::table('incomes')
                    ->where('id', $bonusIncomeId)
                    ->where('user_id', $salary->user_id)
                    ->where('workspace_id', $salary->workspace_id)
                    ->whereNull('salary_recurring_income_id')
                    ->update(['salary_recurring_income_id' => $salary->id]);
            });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salary_recurring_income_id');
        });
    }
};
