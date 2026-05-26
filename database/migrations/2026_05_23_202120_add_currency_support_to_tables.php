<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Verificar/Adicionar Moeda no Workspace
        Schema::table('workspaces', function (Blueprint $table) {
            if (!Schema::hasColumn('workspaces', 'currency')) {
                $table->string('currency', 3)->default('EUR')->after('name');
            }
        });

        // 2. Adicionar suporte a moedas nas Despesas
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'currency')) {
                $table->string('currency', 3)->default('EUR')->after('amount');
            }
            if (!Schema::hasColumn('expenses', 'amount_converted')) {
                $table->decimal('amount_converted', 12, 2)->nullable()->after('currency');
            }
        });

        // 3. Adicionar suporte a moedas nas Receitas
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'currency')) {
                $table->string('currency', 3)->default('EUR')->after('amount');
            }
            if (!Schema::hasColumn('incomes', 'amount_converted')) {
                $table->decimal('amount_converted', 12, 2)->nullable()->after('currency');
            }
        });
    }

    public function down(): void {
        // Lógica de reversão se necessário
    }
};
