<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->foreignId('expense_id')->nullable()->after('is_paid')->constrained()->nullOnDelete();
            $table->foreignId('income_id')->nullable()->after('expense_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_id');
            $table->dropConstrainedForeignId('income_id');
        });
    }
};
