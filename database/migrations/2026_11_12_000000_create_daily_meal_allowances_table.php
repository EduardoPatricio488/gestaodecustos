<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_meal_allowances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recurring_income_id')->constrained()->cascadeOnDelete();
            $table->date('allowance_date');
            $table->decimal('expected_amount', 12, 2);
            $table->decimal('received_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['recurring_income_id', 'allowance_date'],
                'daily_meal_allowances_contract_date_unique',
            );
            $table->index(
                ['workspace_id', 'user_id', 'allowance_date'],
                'daily_meal_allowances_workspace_date_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_meal_allowances');
    }
};
