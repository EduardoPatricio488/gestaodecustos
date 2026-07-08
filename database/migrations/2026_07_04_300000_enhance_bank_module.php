<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── 1. MELHORIAS À TABELA BANK_ACCOUNTS ──────────────────────────────
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_accounts', 'icon')) {
                $table->string('icon')->nullable()->after('color');
            }
            if (!Schema::hasColumn('bank_accounts', 'status')) {
                $table->string('status')->default('active')->after('icon'); // active, archived, hidden
            }
            if (!Schema::hasColumn('bank_accounts', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bank_accounts', 'opened_at')) {
                $table->date('opened_at')->nullable()->after('description');
            }
            if (!Schema::hasColumn('bank_accounts', 'include_in_total')) {
                $table->boolean('include_in_total')->default(true)->after('opened_at');
            }
            if (!Schema::hasColumn('bank_accounts', 'alert_below')) {
                $table->decimal('alert_below', 12, 2)->nullable()->after('include_in_total');
            }
        });

        // ─── 2. TRANSFERÊNCIAS ENTRE CONTAS ──────────────────────────────────
        if (!Schema::hasTable('bank_transfers')) {
            Schema::create('bank_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('from_account_id')->constrained('bank_accounts')->onDelete('cascade');
                $table->foreignId('to_account_id')->constrained('bank_accounts')->onDelete('cascade');
                $table->decimal('amount', 15, 2);
                $table->string('currency')->default('EUR');
                $table->string('category')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('completed'); // pending, completed, failed, cancelled
                $table->date('transferred_at');
                $table->string('receipt_path')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // ─── 3. RESERVAS FINANCEIRAS ──────────────────────────────────────────
        if (!Schema::hasTable('bank_reserves')) {
            Schema::create('bank_reserves', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null');
                $table->string('name');
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('target_amount', 15, 2)->nullable();
                $table->date('target_date')->nullable();
                $table->string('status')->default('active'); // active, completed, paused
                $table->string('color')->nullable();
                $table->string('icon')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_business')->default(false);
                $table->timestamps();
            });
        }

        // ─── 4. DINHEIRO EM TRÂNSITO ──────────────────────────────────────────
        if (!Schema::hasTable('bank_transit_items')) {
            Schema::create('bank_transit_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // transfer_sent, stripe_payment, refund, pending_payment, pending_receipt
                $table->string('name');
                $table->decimal('amount', 15, 2);
                $table->string('direction'); // in, out
                $table->string('origin')->nullable();
                $table->string('destination')->nullable();
                $table->string('status')->default('pending'); // pending, confirmed, failed
                $table->date('expected_date')->nullable();
                $table->date('confirmed_date')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_business')->default(false);
                $table->timestamps();
            });
        }

        // ─── 5. CRÉDITOS (DINHEIRO A RECEBER) ────────────────────────────────
        if (!Schema::hasTable('bank_credits')) {
            Schema::create('bank_credits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->decimal('amount', 15, 2);
                $table->string('category')->nullable(); // client, employee, friend, refund
                $table->date('due_date')->nullable();
                $table->string('status')->default('pending'); // pending, partial, received, cancelled
                $table->decimal('received_amount', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->boolean('is_business')->default(false);
                $table->timestamps();
            });
        }

        // ─── 6. PATRIMÓNIO (ATIVOS NÃO-BANCÁRIOS) ────────────────────────────
        if (!Schema::hasTable('bank_patrimony')) {
            Schema::create('bank_patrimony', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // real_estate, vehicle, gold, crypto, other_asset, liability
                $table->string('name');
                $table->decimal('value', 15, 2)->default(0);
                $table->decimal('purchase_price', 15, 2)->nullable();
                $table->date('purchase_date')->nullable();
                $table->string('currency')->default('EUR');
                $table->text('description')->nullable();
                $table->string('status')->default('active'); // active, sold, depreciating
                $table->boolean('is_business')->default(false);
                $table->json('metadata')->nullable(); // address for RE, plate for vehicle, etc.
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_patrimony');
        Schema::dropIfExists('bank_credits');
        Schema::dropIfExists('bank_transit_items');
        Schema::dropIfExists('bank_reserves');
        Schema::dropIfExists('bank_transfers');

        Schema::table('bank_accounts', function (Blueprint $table) {
            $cols = ['icon', 'status', 'description', 'opened_at', 'include_in_total', 'alert_below'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bank_accounts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
