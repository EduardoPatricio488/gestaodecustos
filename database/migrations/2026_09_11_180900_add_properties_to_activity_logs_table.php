<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs') || Schema::hasColumn('activity_logs', 'properties')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->json('properties')->nullable()->after('model_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('activity_logs') || ! Schema::hasColumn('activity_logs', 'properties')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->dropColumn('properties');
        });
    }
};
