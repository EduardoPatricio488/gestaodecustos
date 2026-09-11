<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_allocations')) {
            Schema::create('payment_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
                $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
                $table->decimal('amount', 15, 2);
                $table->string('currency', 3)->default('EUR');
                $table->date('paid_at');
                $table->string('reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['workspace_id', 'paid_at']);
            });
        }

        if (! Schema::hasTable('credit_notes')) {
            Schema::create('credit_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
                $table->string('number');
                $table->decimal('amount_excl_vat', 15, 2);
                $table->decimal('vat_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2);
                $table->string('currency', 3)->default('EUR');
                $table->string('reason');
                $table->date('issued_at');
                $table->string('status')->default('issued');
                $table->timestamps();
                $table->unique(['workspace_id', 'number']);
            });
        }

        if (! Schema::hasTable('bank_transactions')) {
            Schema::create('bank_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('transaction_date');
                $table->decimal('amount', 15, 2);
                $table->string('currency', 3)->default('EUR');
                $table->string('description');
                $table->string('external_reference')->nullable();
                $table->string('status')->default('unreconciled');
                $table->string('matched_type')->nullable();
                $table->unsignedBigInteger('matched_id')->nullable();
                $table->timestamp('reconciled_at')->nullable();
                $table->timestamps();

                // Explicit short names avoid MySQL's 64-character identifier limit.
                $table->index(
                    ['workspace_id', 'bank_account_id', 'transaction_date'],
                    'bt_ws_account_date_idx'
                );
                $table->index(['matched_type', 'matched_id'], 'bt_matched_idx');
            });
        }

        if (! Schema::hasTable('cost_centers')) {
            Schema::create('cost_centers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('code', 50);
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['workspace_id', 'code']);
            });
        }

        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'amount_paid')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('amount_paid', 15, 2)->default(0)->after('total_amount');
                $table->decimal('amount_credited', 15, 2)->default(0)->after('amount_paid');
            });
        }

        if (Schema::hasTable('expenses') && ! Schema::hasColumn('expenses', 'amount_paid')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->decimal('amount_paid', 15, 2)->default(0)->after('amount');
                $table->foreignId('cost_center_id')->nullable()->after('task_id')->constrained('cost_centers')->nullOnDelete();
            });
        } elseif (Schema::hasTable('expenses') && ! Schema::hasColumn('expenses', 'cost_center_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->foreignId('cost_center_id')->nullable()->after('task_id')->constrained('cost_centers')->nullOnDelete();
            });
        }

        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'cost_center_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('cost_center_id')->nullable()->after('client_id')->constrained('cost_centers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'cost_center_id')) {
            Schema::table('invoices', fn (Blueprint $t) => $t->dropConstrainedForeignId('cost_center_id'));
        }
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'cost_center_id')) {
            Schema::table('expenses', fn (Blueprint $t) => $t->dropConstrainedForeignId('cost_center_id'));
        }
        if (Schema::hasTable('expenses') && Schema::hasColumn('amount_paid')) {
            Schema::table('expenses', fn (Blueprint $t) => $t->dropColumn('amount_paid'));
        }
        if (Schema::hasTable('invoices') && Schema::hasColumn('amount_credited')) {
            Schema::table('invoices', fn (Blueprint $t) => $t->dropColumn(['amount_paid', 'amount_credited']));
        }
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('payment_allocations');
    }
};
