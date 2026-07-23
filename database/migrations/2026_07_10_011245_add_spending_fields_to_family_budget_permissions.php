<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_budget_permissions', function (Blueprint $table) {
            $table->decimal('spending_limit', 15, 2)->default(0)->after('allowance_limit');
            $table->string('allowance_frequency')->default('monthly')->after('spending_limit');
        });
    }

    public function down(): void
    {
        Schema::table('family_budget_permissions', function (Blueprint $table) {
            $table->dropColumn(['spending_limit', 'allowance_frequency']);
        });
    }
};
