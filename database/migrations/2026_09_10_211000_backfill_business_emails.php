<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('workspaces', 'business_email')) {
            return;
        }

        DB::table('workspaces')
            ->join('users', 'users.id', '=', 'workspaces.owner_id')
            ->whereIn('workspaces.type', ['business', 'company', 'bussiness'])
            ->where(function ($query) {
                $query->whereNull('workspaces.business_email')
                    ->orWhere('workspaces.business_email', '');
            })
            ->update(['workspaces.business_email' => DB::raw('users.email')]);
    }

    public function down(): void
    {
        // Intentionally left empty: restoring null would erase existing company emails.
    }
};
