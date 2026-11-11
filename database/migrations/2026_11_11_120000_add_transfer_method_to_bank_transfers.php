<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bank_transfers', 'transfer_method')) {
            Schema::table('bank_transfers', function (Blueprint $table) {
                $table->string('transfer_method', 40)->nullable()->after('category');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bank_transfers', 'transfer_method')) {
            Schema::table('bank_transfers', function (Blueprint $table) {
                $table->dropColumn('transfer_method');
            });
        }
    }
};
