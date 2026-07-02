<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'type')) {
                $table->string('type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('activity_logs', 'metadata')) {
                $table->json('metadata')->nullable()->after('type');
            }
        });
    }

    public function down(): void {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['type', 'metadata']);
        });
    }
};
