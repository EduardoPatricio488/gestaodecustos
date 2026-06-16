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
    Schema::table('investments', function (Blueprint $table) {
        // 'CA' = Certificados de Aforro, 'CT' = Certificados do Tesouro
        $table->string('product_type', 10)->nullable()->after('series');
    });
}

public function down(): void
{
    Schema::table('investments', function (Blueprint $table) {
        $table->dropColumn('product_type');
    });
}
};
