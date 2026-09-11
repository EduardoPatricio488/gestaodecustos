<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'workspace_id', 'last_activity_at']);
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 32);
            $table->longText('content');
            $table->string('tool_name')->nullable();
            $table->string('tool_call_id')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('tokens')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->boolean('is_error')->default(false);
            $table->timestamps();

            $table->index(['ai_conversation_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('ai_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 64);
            $table->string('key', 120);
            $table->text('value');
            $table->unsignedTinyInteger('importance')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'workspace_id', 'type', 'key'], 'ai_memories_scope_key_unique');
            $table->index(['user_id', 'workspace_id', 'is_active']);
        });

        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 64);
            $table->string('priority', 32)->default('info');
            $table->string('title');
            $table->text('message');
            $table->string('category', 64)->nullable();
            $table->string('source', 120)->nullable();
            $table->json('data')->nullable();
            $table->json('action')->nullable();
            $table->string('link')->nullable();
            $table->string('dedupe_key', 191)->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->unsignedSmallInteger('score')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'workspace_id', 'priority', 'read_at']);
            $table->index(['dedupe_key', 'created_at']);
        });

        Schema::create('ai_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tool_name', 120);
            $table->string('action_type', 32)->default('read');
            $table->string('status', 32)->default('pending');
            $table->json('request_payload')->nullable();
            $table->json('result_payload')->nullable();
            $table->string('confirmation_token', 128)->nullable()->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'workspace_id', 'created_at']);
            $table->index(['tool_name', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_action_logs');
        Schema::dropIfExists('ai_insights');
        Schema::dropIfExists('ai_memories');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
