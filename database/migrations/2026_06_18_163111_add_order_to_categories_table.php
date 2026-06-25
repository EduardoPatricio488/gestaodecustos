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
    Schema::table('categories', function (Blueprint $table) {
        // Adiciona a coluna order, começando em 0 por padrão
        $table->integer('order')->default(0)->after('is_fixed');
    });
}

public function down(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropColumn('order');
    });
}
};
