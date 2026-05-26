<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criar a tabela de Fornecedores
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nome Comercial
            $table->string('legal_name')->nullable(); // Nome para Faturas
            $table->string('tax_number', 20)->nullable(); // NIF / CNPJ
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms')->nullable(); // Ex: 30 dias, Pronto Pagamento
            $table->timestamps();
        });

        // 2. Ligar as Despesas aos Fornecedores
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null')->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
        Schema::dropIfExists('suppliers');
    }
};
