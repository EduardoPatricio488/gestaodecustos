<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── bank_reserves: adicionar colunas em falta ─────────────────
        Schema::table('bank_reserves', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_reserves', 'bank_account_id')) {
                $table->foreignId('bank_account_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('bank_reserves', 'target_date')) {
                $table->date('target_date')->nullable()->after('target_amount');
            }
            if (!Schema::hasColumn('bank_reserves', 'icon')) {
                $table->string('icon')->nullable()->after('color');
            }
            if (!Schema::hasColumn('bank_reserves', 'description')) {
                $table->text('description')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('bank_reserves', 'is_business')) {
                $table->boolean('is_business')->default(false)->after('description');
            }
        });

        // ─── bank_transit_items: adicionar colunas em falta ────────────
        Schema::table('bank_transit_items', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_transit_items', 'name')) {
                $table->string('name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('bank_transit_items', 'direction')) {
                $table->string('direction')->default('in')->after('type'); // in, out
            }
            if (!Schema::hasColumn('bank_transit_items', 'origin')) {
                $table->string('origin')->nullable()->after('direction');
            }
            if (!Schema::hasColumn('bank_transit_items', 'destination')) {
                $table->string('destination')->nullable()->after('origin');
            }
            if (!Schema::hasColumn('bank_transit_items', 'confirmed_date')) {
                $table->date('confirmed_date')->nullable()->after('expected_date');
            }
            if (!Schema::hasColumn('bank_transit_items', 'is_business')) {
                $table->boolean('is_business')->default(false);
            }
        });

        // ─── bank_transfers: adicionar colunas em falta ────────────────
        Schema::table('bank_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_transfers', 'currency')) {
                $table->string('currency')->default('EUR')->after('amount');
            }
            if (!Schema::hasColumn('bank_transfers', 'category')) {
                $table->string('category')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('bank_transfers', 'status')) {
                $table->string('status')->default('completed')->after('description');
            }
            if (!Schema::hasColumn('bank_transfers', 'receipt_path')) {
                $table->string('receipt_path')->nullable();
            }
            if (!Schema::hasColumn('bank_transfers', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        // ─── bank_accounts: garantir colunas extra ────────────────────
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_accounts', 'swift')) {
                $table->string('swift')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'holder_name')) {
                $table->string('holder_name')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'bank_name') && !Schema::hasColumn('bank_accounts', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'credit_limit')) {
                $table->decimal('credit_limit', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'forecast_balance')) {
                $table->decimal('forecast_balance', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'risk_score')) {
                $table->unsignedInteger('risk_score')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'tags')) {
                $table->json('tags')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'opened_at')) {
                $table->date('opened_at')->nullable();
            }
            if (!Schema::hasColumn('bank_accounts', 'include_in_total')) {
                $table->boolean('include_in_total')->default(true);
            }
            if (!Schema::hasColumn('bank_accounts', 'alert_below')) {
                $table->decimal('alert_below', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        // Reversão omitida intencionalmente
    }
};
