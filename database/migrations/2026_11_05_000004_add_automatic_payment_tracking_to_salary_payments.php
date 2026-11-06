<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->after('workspace_id')->constrained()->nullOnDelete();
            $table->decimal('bank_balance_amount', 12, 2)->default(0)->after('net_received');
            $table->boolean('is_automatic')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_account_id');
            $table->dropColumn(['bank_balance_amount', 'is_automatic']);
        });
    }
};
