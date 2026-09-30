<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['clients', 'suppliers'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'portal_token')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('portal_token')
                ->update(['portal_token_hash' => DB::raw("SHA2(portal_token, 256)")]);

            DB::table($table)->update(['portal_token' => null]);
        }
    }

    public function down(): void
    {
        // Tokens are intentionally non-recoverable. A new token must be issued.
    }
};
