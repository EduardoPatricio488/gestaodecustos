<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Adicionar à tabela de Despesas
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'workspace_id')) {
                    $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
                }
            });
        }

        // Adicionar à tabela de Receitas Recorrentes
        if (Schema::hasTable('recurring_incomes')) {
            Schema::table('recurring_incomes', function (Blueprint $table) {
                if (!Schema::hasColumn('recurring_incomes', 'workspace_id')) {
                    $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void {
        Schema::table('expenses', fn (Blueprint $table) => $table->dropColumn('workspace_id'));
        Schema::table('recurring_incomes', fn (Blueprint $table) => $table->dropColumn('workspace_id'));
    }
};
