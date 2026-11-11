<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['expenses', 'incomes'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'bank_statement_import_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('bank_statement_import_id')
                        ->nullable()
                        ->after('workspace_id')
                        ->constrained('bank_statement_imports')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['expenses', 'incomes'] as $tableName) {
            if (Schema::hasColumn($tableName, 'bank_statement_import_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('bank_statement_import_id');
                });
            }
        }
    }
};
