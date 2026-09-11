<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_transactions') || ! Schema::hasColumn('bank_transactions', 'bank_account_id')) {
            return;
        }

        Schema::table('bank_transactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('bank_account_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_transactions') || ! Schema::hasColumn('bank_transactions', 'bank_account_id')) {
            return;
        }

        Schema::table('bank_transactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('bank_account_id')->nullable(false)->change();
        });
    }
};
