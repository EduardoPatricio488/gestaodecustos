<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->string('subscription_number', 32)->nullable()->after('isin');
            $table->date('valuation_date')->nullable()->after('operation_date');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['subscription_number', 'valuation_date']);
        });
    }
};
