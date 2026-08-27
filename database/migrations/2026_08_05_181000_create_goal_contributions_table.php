<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('income_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('source')->default('manual');
            $table->text('note')->nullable();
            $table->timestamp('contributed_at')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'goal_id', 'contributed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_contributions');
    }
};
