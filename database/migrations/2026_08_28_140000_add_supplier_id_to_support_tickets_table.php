<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('support_tickets', 'supplier_id')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->after('client_id')
                    ->constrained('suppliers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('support_tickets', 'supplier_id')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->dropConstrainedForeignId('supplier_id');
            });
        }
    }
};
