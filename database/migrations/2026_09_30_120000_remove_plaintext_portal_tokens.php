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
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($table) {
                    foreach ($rows as $row) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([
                                'portal_token_hash' => hash('sha256', (string) $row->portal_token),
                                'portal_token' => null,
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        // Tokens are intentionally non-recoverable. A new token must be issued.
    }
};
