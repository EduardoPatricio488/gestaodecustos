<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Repair databases where the previous migration was recorded as ran
        // without all expected columns being present.
        if (! Schema::hasColumn('bank_transfers', 'recipient')) {
            Schema::table('bank_transfers', function (Blueprint $table): void {
                $table->string('recipient')->nullable()->after('to_account_id');
            });
        }

        if (! Schema::hasColumn('bank_transfers', 'direction')) {
            Schema::table('bank_transfers', function (Blueprint $table): void {
                $table->string('direction')->default('out')->after('recipient');
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        if (Schema::hasColumn('bank_transfers', 'direction')) {
            $columns[] = 'direction';
        }

        if (Schema::hasColumn('bank_transfers', 'recipient')) {
            $columns[] = 'recipient';
        }

        if ($columns !== []) {
            Schema::table('bank_transfers', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
