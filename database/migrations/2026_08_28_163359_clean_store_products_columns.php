<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            // Se a coluna title existir, vamos torná-la opcional (nullable)
            // para que o MySQL pare de dar erro no Seeder
            if (Schema::hasColumn('store_products', 'title')) {
                $table->string('title')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
