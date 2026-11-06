<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_portfolio_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->longText('analysis');
            $table->json('snapshot')->nullable();
            $table->string('model')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'workspace_id', 'created_at'], 'investment_portfolio_analysis_history_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_portfolio_analyses');
    }
};
