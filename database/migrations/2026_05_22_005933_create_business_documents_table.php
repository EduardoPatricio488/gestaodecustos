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
        $table->string('name'); // Ex: Contrato de Arrendamento
        $table->string('category'); // Legal, RH, Seguros, Impostos
        $table->string('file_path');
        $table->date('expiry_date')->nullable(); // Data de expiração
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
