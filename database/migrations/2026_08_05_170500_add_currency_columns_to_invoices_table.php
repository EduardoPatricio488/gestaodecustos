<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency', 3)->default('EUR')->after('total_amount');
            }

            if (! Schema::hasColumn('invoices', 'amount_excl_vat_converted')) {
                $table->decimal('amount_excl_vat_converted', 12, 2)->nullable()->after('currency');
            }

            if (! Schema::hasColumn('invoices', 'vat_amount_converted')) {
                $table->decimal('vat_amount_converted', 12, 2)->nullable()->after('amount_excl_vat_converted');
            }

            if (! Schema::hasColumn('invoices', 'total_amount_converted')) {
                $table->decimal('total_amount_converted', 12, 2)->nullable()->after('vat_amount_converted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            foreach (['total_amount_converted', 'vat_amount_converted', 'amount_excl_vat_converted', 'currency'] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
