<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('workspaces', 'business_email') || ! Schema::hasColumn('users', 'email')) {
            return;
        }

        // Do not use a joined UPDATE here. SQLite (used by the test suite)
        // cannot reference the joined table from the UPDATE SET expression.
        // Updating each workspace from its owner also keeps this migration
        // portable across SQLite, MySQL and PostgreSQL.
        DB::table('workspaces')
            ->whereIn('type', ['business', 'company', 'bussiness'])
            ->where(function ($query) {
                $query->whereNull('business_email')
                    ->orWhere('business_email', '');
            })
            ->orderBy('id')
            ->chunkById(250, function ($workspaces): void {
                foreach ($workspaces as $workspace) {
                    if (! $workspace->owner_id) {
                        continue;
                    }

                    $ownerEmail = DB::table('users')
                        ->where('id', $workspace->owner_id)
                        ->value('email');

                    if ($ownerEmail !== null && $ownerEmail !== '') {
                        DB::table('workspaces')
                            ->where('id', $workspace->id)
                            ->update(['business_email' => $ownerEmail]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Intentionally left empty: restoring null would erase existing company emails.
    }
};
