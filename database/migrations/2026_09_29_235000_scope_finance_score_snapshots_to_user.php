<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_score_snapshots', function (Blueprint $table) {
            $table->dropUnique('finance_score_snapshots_workspace_id_month_year_unique');
            $table->unique(['workspace_id', 'user_id', 'month', 'year'], 'finance_score_snapshots_ws_user_month_unique');
        });
    }

    public function down(): void
    {
        Schema::table('finance_score_snapshots', function (Blueprint $table) {
            $table->dropUnique('finance_score_snapshots_ws_user_month_unique');
            $table->unique(['workspace_id', 'month', 'year'], 'finance_score_snapshots_workspace_id_month_year_unique');
        });
    }
};