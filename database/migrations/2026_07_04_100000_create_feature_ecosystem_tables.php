<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_score_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->json('breakdown')->nullable();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->timestamps();
            $table->unique(['workspace_id', 'month', 'year']);
        });

        Schema::create('budget_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->decimal('target_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'failed'])->default('active');
            $table->boolean('xp_awarded')->default(false);
            $table->timestamps();
        });

        Schema::create('community_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('badge_name')->nullable();
            $table->string('type')->default('savings');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('community_challenge_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('progress_pct')->default(0);
            $table->timestamp('joined_at');
            $table->timestamps();
            $table->unique(['community_challenge_id', 'user_id']);
        });

        Schema::create('bank_statement_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('bank_detected')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->unsignedInteger('transactions_total')->default(0);
            $table->unsignedInteger('transactions_imported')->default(0);
            $table->json('errors')->nullable();
            $table->timestamps();
        });

        Schema::create('expense_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('spent_at');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });

        Schema::create('client_portal_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('family_budget_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('can_view_all')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->decimal('allowance_limit', 12, 2)->nullable();
            $table->timestamps();
            $table->unique(['workspace_id', 'user_id', 'category_id']);
        });

        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('unit_price', 12, 4);
            $table->string('unit_type')->nullable();
            $table->string('merchant')->nullable();
            $table->date('recorded_at');
            $table->timestamps();
            $table->index(['workspace_id', 'category_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('family_budget_permissions');
        Schema::dropIfExists('client_portal_tokens');
        Schema::dropIfExists('expense_approvals');
        Schema::dropIfExists('bank_statement_imports');
        Schema::dropIfExists('community_challenge_participants');
        Schema::dropIfExists('community_challenges');
        Schema::dropIfExists('budget_challenges');
        Schema::dropIfExists('finance_score_snapshots');
    }
};
