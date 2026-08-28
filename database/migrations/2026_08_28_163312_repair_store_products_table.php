<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            if (! Schema::hasColumn('store_products', 'name')) {
                $table->string('name')->after('id');
            }
            if (! Schema::hasColumn('store_products', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (! Schema::hasColumn('store_products', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('store_products', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('description');
            }
            if (! Schema::hasColumn('store_products', 'category')) {
                $table->string('category')->nullable()->after('price');
            }
            if (! Schema::hasColumn('store_products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('category');
            }
        });
    }

    public function down(): void
    {
        // No down não fazemos nada por segurança
    }
};
