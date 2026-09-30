<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The suppliers table already creates workspace_id in its original
        // migration. Keep this legacy migration idempotent so a fresh test
        // database does not try to add the column a second time.
        if (! Schema::hasColumn('suppliers', 'workspace_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // workspace_id belongs to the suppliers table definition and must
        // not be removed when this legacy migration is rolled back.
    }
};
