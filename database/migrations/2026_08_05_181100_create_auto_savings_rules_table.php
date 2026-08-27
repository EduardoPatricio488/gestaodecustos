<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_savings_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
            $table->string('profile')->default('equilibrado');
            $table->decimal('percent', 5, 2);
            $table->decimal('min_income_amount', 12, 2)->nullable();
            $table->string('applies_to')->default('all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['workspace_id', 'user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_savings_rules');
    }
};
