<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    foreach (['incomes', 'expenses', 'recurring_incomes'] as $table) {
        if (Schema::hasTable($table) && !Schema::hasColumn($table, 'workspace_id')) {
            Schema::table($table, function ($table) {
                $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_tables', function (Blueprint $table) {
            //
        });
    }
};
