<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('daily_report_enabled')->default(false)->after('monthly_report_day');
            $table->json('daily_report_sections')->nullable()->after('daily_report_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_report_enabled', 'daily_report_sections']);
        });
    }
};
