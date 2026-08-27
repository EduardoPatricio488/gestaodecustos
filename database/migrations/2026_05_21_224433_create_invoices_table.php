<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // LIGAÇÕES (FOREIGN KEYS) - O MySQL exige que estas tabelas já existam
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // O client_id é opcional (nullable) para o caso de a fatura ser registada sem cliente
            // Usamos 'set null' para que se o cliente for apagado, a fatura não desapareça
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');

            // DADOS DA FATURA
            $table->string('client_name'); // Nome manual ou backup do nome do cliente
            $table->string('invoice_number');
            $table->decimal('amount_excl_vat', 15, 2); // Aumentado para 15 para suportar valores maiores
            $table->decimal('vat_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('paga'); // paga, pendente, atrasada, cancelada
            $table->date('due_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
