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
    Schema::table('expenses', function (Blueprint $table) {
        // Adicionamos o campo de IVA às despesas para podermos deduzir
        $table->decimal('vat_amount', 10, 2)->default(0)->after('amount');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            //
        });
    }
};
