<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['investments', 'bank_credits', 'bank_transit_items', 'bank_patrimony', 'debts'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'bank_account_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('bank_account_id')->nullable()->after('user_id')->constrained('bank_accounts')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['investments', 'bank_credits', 'bank_transit_items', 'bank_patrimony', 'debts'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'bank_account_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('bank_account_id');
                });
            }
        }
    }
};
