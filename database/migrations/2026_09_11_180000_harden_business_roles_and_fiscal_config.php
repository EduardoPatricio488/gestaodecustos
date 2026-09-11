<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            if (! Schema::hasColumn('workspaces', 'country_code')) {
                $table->string('country_code', 2)->default('PT')->after('currency');
            }
            if (! Schema::hasColumn('workspaces', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(23)->after('country_code');
            }
            if (! Schema::hasColumn('workspaces', 'vat_regime')) {
                $table->string('vat_regime', 30)->default('normal')->after('vat_rate');
            }
        });

        if (Schema::hasTable('workspace_user')) {
            DB::table('workspace_user')
                ->where('role', 'editor')
                ->update(['role' => 'manager']);

            DB::table('workspace_user')
                ->where('role', 'member')
                ->update(['role' => 'employee']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('workspace_user')) {
            DB::table('workspace_user')
                ->where('role', 'manager')
                ->update(['role' => 'editor']);

            DB::table('workspace_user')
                ->where('role', 'employee')
                ->update(['role' => 'member']);
        }

        Schema::table('workspaces', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('workspaces', 'country_code') ? 'country_code' : null,
                Schema::hasColumn('workspaces', 'vat_rate') ? 'vat_rate' : null,
                Schema::hasColumn('workspaces', 'vat_regime') ? 'vat_regime' : null,
            ]));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
