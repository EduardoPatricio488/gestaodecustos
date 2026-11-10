<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bank_accounts', 'account_number')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->string('account_number', 30)->nullable()->after('iban');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bank_accounts', 'account_number')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->dropColumn('account_number');
            });
        }
    }
};
