<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->timestamp('audit_token_expires_at')->nullable()->after('audit_token');
            $table->timestamp('audit_token_revoked_at')->nullable()->after('audit_token_expires_at');
            $table->string('audit_token_purpose')->nullable()->after('audit_token_revoked_at');
        });

        DB::table('workspaces')
            ->whereNotNull('audit_token')
            ->orderBy('id')
            ->get(['id', 'audit_token'])
            ->each(function (object $workspace): void {
                DB::table('workspaces')->where('id', $workspace->id)->update([
                    'audit_token' => Hash::make($workspace->audit_token),
                    'audit_token_purpose' => 'bank_audit',
                    'audit_token_revoked_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'audit_token_expires_at',
                'audit_token_revoked_at',
                'audit_token_purpose',
            ]);
        });
    }
};
