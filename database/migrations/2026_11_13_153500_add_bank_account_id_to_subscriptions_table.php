<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('subscriptions', 'bank_account_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->foreignId('bank_account_id')
                    ->nullable()
                    ->after('payment_method')
                    ->constrained('bank_accounts')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subscriptions', 'bank_account_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('bank_account_id');
            });
        }
    }
};
