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
        Schema::create('business_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Adicionado para rastreio

            $table->string('title'); // Alterado de 'name' para 'title' (padrão do projeto)
            $table->string('type');  // Alterado de 'category' para 'type' (padrão do projeto)
            $table->string('file_path');

            // 🔥 NOME CRÍTICO: Deve ser expires_at para o Dashboard Business funcionar
            $table->date('expires_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_documents');
    }
};
