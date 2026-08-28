<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            // Tornamos estas colunas opcionais para compatibilidade total com MySQL
            if (Schema::hasColumn('store_products', 'type')) {
                $table->string('type')->nullable()->change();
            }
            if (Schema::hasColumn('store_products', 'title')) {
                $table->string('title')->nullable()->change();
            }
            if (Schema::hasColumn('store_products', 'image_path')) {
                $table->string('image_path')->nullable()->change();
            }
        });
    }

    public function down(): void {}
};
