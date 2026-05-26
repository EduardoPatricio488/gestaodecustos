<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    // Adicionar XP e Nível ao utilizador
    Schema::table('users', function (Blueprint $table) {
        $table->integer('xp')->default(0);
        $table->integer('level')->default(1);
    });

    // Tabela de Medalhas
    Schema::create('badges', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Ex: "Poupador Iniciante"
        $table->string('description');
        $table->string('icon'); // Ícone do Flux ou Emoji
        $table->string('color'); // Cor da medalha
        $table->timestamps();
    });

    // Tabela Pivot (Quem ganhou o quê)
    Schema::create('badge_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_tables');
    }
};
