<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Criar Tabela de Workspaces
        if (!Schema::hasTable('workspaces')) {
            Schema::create('workspaces', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 2. Tabela Pivot para Membros
        if (!Schema::hasTable('workspace_user')) {
            Schema::create('workspace_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('role')->default('member');
                $table->timestamps();
            });
        }

        // 3. Adicionar current_workspace_id ao Utilizador
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'current_workspace_id')) {
                $table->foreignId('current_workspace_id')->nullable()->constrained('workspaces')->nullOnDelete();
            }
        });

        // 4. Adicionar workspace_id às tabelas de dados
        $tables = ['expenses', 'categories', 'incomes', 'goals', 'subscriptions', 'investments', 'recurring_incomes'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'workspace_id')) {
                        $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
                    }
                });
            }
        }
    }

    public function down(): void {
        Schema::dropIfExists('workspace_user');
        Schema::dropIfExists('workspaces');
    }
};
