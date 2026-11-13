<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->string('xtb_position_id', 64)->nullable()->after('subscription_number');
            $table->unique(['workspace_id', 'user_id', 'broker', 'xtb_position_id'], 'investments_xtb_position_unique');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropUnique('investments_xtb_position_unique');
            $table->dropColumn('xtb_position_id');
        });
    }
};
