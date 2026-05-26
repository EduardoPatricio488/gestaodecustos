<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // Criar a tabela de Clientes (se não existir)
    if (!Schema::hasTable('clients')) {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tax_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('status')->default('ativo');
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    // Ligar as Faturas aos Clientes (Apenas se a coluna não existir)
    Schema::table('invoices', function (Blueprint $table) {
        if (!Schema::hasColumn('invoices', 'client_id')) {
            $table->foreignId('client_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
        }
    });
}

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
        Schema::dropIfExists('clients');
    }
};
