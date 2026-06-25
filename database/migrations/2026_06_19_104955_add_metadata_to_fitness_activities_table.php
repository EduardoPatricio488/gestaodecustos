<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adicionar a coluna (Executado com php artisan migrate)
     */
    public function up(): void
    {
        Schema::table('fitness_activities', function (Blueprint $table) {
            // A gaveta onde vamos guardar os dados extras detetados pela IA
            $table->json('metadata')->nullable()->after('notes');
        });
    }

    /**
     * Remover a coluna (Executado com php artisan migrate:rollback)
     */
    public function down(): void
    {
        Schema::table('fitness_activities', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
