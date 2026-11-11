<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_transfers', function (Blueprint $table): void {
            $table->foreignId('from_account_id')->nullable()->change();
            $table->foreignId('to_account_id')->nullable()->change();
            $table->string('recipient')->nullable()->after('to_account_id');
            $table->string('direction')->default('out')->after('recipient');
        });
    }

    public function down(): void
    {
        Schema::table('bank_transfers', function (Blueprint $table): void {
            $table->dropColumn(['recipient', 'direction']);
        });
    }
};
