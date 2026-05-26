<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('workspaces', function (Blueprint $table) {
            // Adiciona a coluna do plano e a data de expiração
            if (!Schema::hasColumn('workspaces', 'plan')) {
                $table->string('plan')->default('free')->after('type');
            }
            if (!Schema::hasColumn('workspaces', 'plan_expires_at')) {
                $table->timestamp('plan_expires_at')->nullable()->after('plan');
            }
        });
    }

    public function down(): void {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['plan', 'plan_expires_at']);
        });
    }
};
