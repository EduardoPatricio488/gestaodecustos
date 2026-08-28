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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('portal_token', 255)->nullable()->change();
            $table->timestamp('invite_expires_at')->nullable()->after('portal_token');
            $table->timestamp('invite_used_at')->nullable()->after('invite_expires_at');
            $table->timestamp('invite_revoked_at')->nullable()->after('invite_used_at');
        });

        DB::table('employees')
            ->whereNotNull('portal_token')
            ->orderBy('id')
            ->get(['id', 'portal_token'])
            ->each(function (object $employee): void {
                DB::table('employees')->where('id', $employee->id)->update([
                    'portal_token' => Hash::make($employee->portal_token),
                    'invite_expires_at' => now()->addDays(7),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'invite_expires_at',
                'invite_used_at',
                'invite_revoked_at',
            ]);
        });
    }
};
