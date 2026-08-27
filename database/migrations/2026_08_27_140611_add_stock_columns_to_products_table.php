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
        Schema::table('products', function (Blueprint $table) {
            // Adiciona as colunas no final da tabela para evitar erros de dependência
            if (! Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0);
            }
            if (! Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(0);
            }

            // Aproveitando, se não tiver preço, vamos adicionar também para não dar erro no futuro
            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'min_stock', 'price']);
        });
    }
};
