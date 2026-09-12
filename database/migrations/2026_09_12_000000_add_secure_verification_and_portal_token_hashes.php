<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'verification_code_hash')) {
                    $table->string('verification_code_hash', 64)->nullable()->after('verification_code');
                }
                if (! Schema::hasColumn('users', 'verification_code_expires_at')) {
                    $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code_hash');
                }
                if (! Schema::hasColumn('users', 'verification_code_attempts')) {
                    $table->unsignedTinyInteger('verification_code_attempts')->default(0)->after('verification_code_expires_at');
                }
            });

            DB::table('users')
                ->whereNotNull('verification_code')
                ->whereNull('verification_code_hash')
                ->orderBy('id')
                ->chunkById(500, function ($users) {
                    foreach ($users as $user) {
                        DB::table('users')->where('id', $user->id)->update([
                            'verification_code_hash' => hash('sha256', (string) $user->verification_code),
                            'verification_code_expires_at' => now()->addMinutes(10),
                            'verification_code_attempts' => 0,
                        ]);
                    }
                });
        }

        foreach (['clients', 'suppliers'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'portal_token')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'portal_token_hash')) {
                    $table->string('portal_token_hash', 64)->nullable()->after('portal_token')->index();
                }
            });

            DB::table($tableName)
                ->whereNotNull('portal_token')
                ->whereNull('portal_token_hash')
                ->orderBy('id')
                ->chunkById(500, function ($records) use ($tableName) {
                    foreach ($records as $record) {
                        DB::table($tableName)->where('id', $record->id)->update([
                            'portal_token_hash' => hash('sha256', (string) $record->portal_token),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach (['clients', 'suppliers'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'portal_token_hash')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('portal_token_hash');
                });
            }
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                foreach (['verification_code_hash', 'verification_code_expires_at', 'verification_code_attempts'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
