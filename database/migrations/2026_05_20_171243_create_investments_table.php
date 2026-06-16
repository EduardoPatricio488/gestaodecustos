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
    Schema::create('investments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');

        $table->string('name');
        $table->string('symbol')->nullable();
        $table->string('type'); // Acao, Cripto, etc.

        $table->decimal('quantity', 15, 8);
        $table->decimal('average_price', 15, 2);
        $table->decimal('current_price', 15, 2);

        // Colunas que estavam no outro ficheiro, agora ficam aqui:
        $table->string('exchange')->nullable();
        $table->string('network')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
