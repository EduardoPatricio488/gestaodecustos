<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (! Schema::hasColumn('invoices', 'client_id')) {
                    $table->foreignId('client_id')->nullable()->after('workspace_id')->constrained('clients')->nullOnDelete();
                }
                if (! Schema::hasColumn('invoices', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('due_date');
                }
                $table->index(['workspace_id', 'status', 'due_date']);
                $table->index(['workspace_id', 'paid_at']);
            });

            DB::table('invoices')
                ->where('status', 'paga')
                ->whereNull('paid_at')
                ->update(['paid_at' => DB::raw('updated_at')]);

            if (Schema::hasTable('clients')) {
                DB::table('invoices')
                    ->whereNull('client_id')
                    ->whereNotNull('client_name')
                    ->orderBy('id')
                    ->chunkById(250, function ($invoices) {
                        foreach ($invoices as $invoice) {
                            $clientId = DB::table('clients')
                                ->where('workspace_id', $invoice->workspace_id)
                                ->where(function ($query) use ($invoice) {
                                    $query->where('name', $invoice->client_name)
                                        ->orWhere('legal_name', $invoice->client_name);
                                })
                                ->value('id');

                            if ($clientId) {
                                DB::table('invoices')->where('id', $invoice->id)->update(['client_id' => $clientId]);
                            }
                        }
                    });
            }
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (! Schema::hasColumn('expenses', 'supplier_id')) {
                    $table->foreignId('supplier_id')->nullable()->after('workspace_id')->constrained('suppliers')->nullOnDelete();
                }
                $table->index(['workspace_id', 'supplier_id', 'spent_at']);
            });
        }

        if (Schema::hasTable('incomes') && ! Schema::hasColumn('incomes', 'workspace_id')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->foreignId('workspace_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
                $table->index(['workspace_id', 'received_at']);
            });

            DB::table('incomes as i')
                ->join('users as u', 'u.id', '=', 'i.user_id')
                ->whereNull('i.workspace_id')
                ->update(['i.workspace_id' => DB::raw('u.current_workspace_id')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'supplier_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropIndex(['workspace_id', 'supplier_id', 'spent_at']);
                $table->dropColumn('supplier_id');
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'client_id')) {
                    $table->dropForeign(['client_id']);
                    $table->dropColumn('client_id');
                }
                if (Schema::hasColumn('invoices', 'paid_at')) {
                    $table->dropIndex(['workspace_id', 'paid_at']);
                    $table->dropColumn('paid_at');
                }
            });
        }

        if (Schema::hasTable('incomes') && Schema::hasColumn('incomes', 'workspace_id')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->dropForeign(['workspace_id']);
                $table->dropIndex(['workspace_id', 'received_at']);
                $table->dropColumn('workspace_id');
            });
        }
    }
};
