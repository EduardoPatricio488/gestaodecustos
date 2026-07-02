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
    // Adiciona esta linha no topo: apaga a tabela se ela já existir por erro
    Schema::dropIfExists('business_documents');

    Schema::create('business_documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->string('file_path');
        $table->string('type'); // 'contrato', 'recibo', 'outro'
        $table->string('file_size')->nullable();
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
