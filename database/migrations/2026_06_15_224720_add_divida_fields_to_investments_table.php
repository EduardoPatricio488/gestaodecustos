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
        $table->string('issuer')->nullable()->after('broker');
        $table->string('series')->nullable()->after('issuer');
        $table->decimal('interest_rate', 5, 3)->nullable()->after('series');
        $table->decimal('loyalty_bonus', 5, 3)->nullable()->after('interest_rate');
        $table->date('capitalization_date')->nullable()->after('loyalty_bonus');
    });
}

public function down(): void
{
    Schema::table('investments', function (Blueprint $table) {
        $table->dropColumn(['issuer', 'series', 'interest_rate', 'loyalty_bonus', 'capitalization_date']);
    });
}
};
