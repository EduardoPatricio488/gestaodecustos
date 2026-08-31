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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Adicionamos a coluna color. Se não for definida, o padrão é o verde da marca.
            if (! Schema::hasColumn('subscription_plans', 'color')) {
                $table->string('color', 7)->default('#10b981')->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
