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
    Schema::table('category_fields', function (Blueprint $table) {
        // Adiciona as colunas que faltam para o sistema inteligente
        if (!Schema::hasColumn('category_fields', 'key')) {
            $table->string('key')->after('label');
        }
        if (!Schema::hasColumn('category_fields', 'placeholder')) {
            $table->string('placeholder')->nullable()->after('type');
        }
        if (!Schema::hasColumn('category_fields', 'order')) {
            $table->integer('order')->default(0)->after('options');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_fields', function (Blueprint $table) {
            //
        });
    }
};
