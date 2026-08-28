<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Adiciona a coluna 'type' para suportar filtros de impostos e categorias especiais
            if (! Schema::hasColumn('expenses', 'type')) {
                $table->string('type')->nullable()->after('subcategory');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
