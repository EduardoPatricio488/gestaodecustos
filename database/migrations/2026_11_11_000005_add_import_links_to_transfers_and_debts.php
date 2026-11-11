<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bank_transfers') && ! Schema::hasColumn('bank_transfers', 'bank_statement_import_id')) {
            Schema::table('bank_transfers', function (Blueprint $table): void {
                $table->foreignId('bank_statement_import_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('bank_statement_imports')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('debts') && ! Schema::hasColumn('debts', 'bank_statement_import_id')) {
            Schema::table('debts', function (Blueprint $table): void {
                $table->foreignId('bank_statement_import_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('bank_statement_imports')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('debts', 'bank_statement_import_id')) {
            Schema::table('debts', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('bank_statement_import_id');
            });
        }

        if (Schema::hasColumn('bank_transfers', 'bank_statement_import_id')) {
            Schema::table('bank_transfers', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('bank_statement_import_id');
            });
        }
    }
};
