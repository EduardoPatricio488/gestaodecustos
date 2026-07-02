<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. REPARAR TABELA USERS (Colunas em falta)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_ip')) {
                $table->string('last_ip')->nullable();
            }
        });

        // 2. REPARAR TABELA EMPLOYEES
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'workspace_id')) {
                $table->foreignId('workspace_id')->nullable();
            }
            if (!Schema::hasColumn('employees', 'photo_path')) {
                $table->string('photo_path')->nullable();
            }
        });

        // 3. CRIAR TABELA SUPPORT_TICKETS (Se não existir)
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('workspace_id')->nullable();
                $table->string('subject');
                $table->text('message')->nullable();
                $table->string('status')->default('open');
                $table->string('priority')->default('medium');
                $table->timestamps();
            });
        }

        // 4. CRIAR TABELA CHAT_MESSAGES (Se não existir)
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('role');
                $table->text('content');
                $table->boolean('is_error')->default(false);
                $table->integer('tokens')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void { }
};
