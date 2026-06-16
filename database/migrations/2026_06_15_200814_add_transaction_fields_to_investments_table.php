<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->string('isin', 12)->nullable()->after('symbol');
            $table->string('broker', 100)->nullable()->after('provider');
            $table->date('operation_date')->nullable()->after('broker');
            $table->decimal('fees', 10, 4)->default(0)->after('average_price');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['isin', 'broker', 'operation_date', 'fees']);
        });
    }
};
