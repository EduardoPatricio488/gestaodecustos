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
        Schema::table('expenses', function (Blueprint $table) {
            // Adiciona o campo de título logo após o ID
            $table->string('title')->nullable()->after('id');

            // Adiciona a marcação se é despesa de empresa após o user_id
            // Definimos default(false) se quiseres que as antigas sejam tratadas como pessoais
            $table->boolean('is_company')->default(false)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Se decidires reverter a migração, estes campos são removidos
            $table->dropColumn(['title', 'is_company']);
        });
    }
};
