<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criar tabela de Contas/Carteiras
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name'); // Ex: Santander, Revolut, Carteira
            $table->string('type')->default('corrente'); // corrente, poupanca, cash, credito
            $table->decimal('balance', 15, 2)->default(0); // Saldo inicial/atual
            $table->string('currency')->default('EUR');
            $table->string('color')->nullable(); // Para distinguir visualmente

            $table->timestamps();
        });

        // 2. Ligar as Despesas e Receitas a uma Conta
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->after('category_id')->constrained()->onDelete('set null');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->after('workspace_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) { $table->dropColumn('bank_account_id'); });
        Schema::table('expenses', function (Blueprint $table) { $table->dropColumn('bank_account_id'); });
        Schema::dropIfExists('bank_accounts');
    }
};
