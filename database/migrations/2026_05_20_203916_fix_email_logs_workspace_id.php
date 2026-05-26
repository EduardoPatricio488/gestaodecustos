<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('email_logs')) {
            Schema::table('email_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('email_logs', 'workspace_id')) {
                    $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn('workspace_id');
        });
    }
};
