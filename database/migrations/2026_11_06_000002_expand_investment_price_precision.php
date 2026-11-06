<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'average_price')) {
                $table->decimal('average_price', 15, 6)->change();
            }

            if (Schema::hasColumn('investments', 'current_price')) {
                $table->decimal('current_price', 15, 6)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'average_price')) {
                $table->decimal('average_price', 15, 2)->change();
            }

            if (Schema::hasColumn('investments', 'current_price')) {
                $table->decimal('current_price', 15, 2)->change();
            }
        });
    }
};
