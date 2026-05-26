<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('expenses', function (Blueprint $table) {
            // Adiciona a subcategoria a seguir ao ID da categoria
            $table->string('subcategory')->nullable()->after('category_id');

            // Adiciona o campo JSON para os campos extras (km, local, médico, etc)
            $table->json('metadata')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('expenses', function (Blueprint $table) {
            // Remove as colunas caso precises de fazer rollback
            $table->dropColumn(['subcategory', 'metadata']);
        });
    }
};
