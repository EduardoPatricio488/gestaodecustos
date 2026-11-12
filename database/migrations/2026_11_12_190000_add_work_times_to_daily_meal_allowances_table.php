<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_meal_allowances', function (Blueprint $table): void {
            $table->time('work_started_at')->nullable()->after('received_amount');
            $table->time('work_ended_at')->nullable()->after('work_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('daily_meal_allowances', function (Blueprint $table): void {
            $table->dropColumn(['work_started_at', 'work_ended_at']);
        });
    }
};
