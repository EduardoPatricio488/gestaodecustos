<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('salary_payments')) {
            Schema::create('salary_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('recurring_income_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('payment_month');
                $table->unsignedSmallInteger('payment_year');
                $table->decimal('salary_base_gross', 12, 2)->default(0);
                $table->decimal('allowances_duodecimos', 12, 2)->default(0);
                $table->decimal('other_complements', 12, 2)->default(0);
                $table->decimal('social_security', 12, 2)->default(0);
                $table->decimal('irs', 12, 2)->default(0);
                $table->decimal('other_deductions', 12, 2)->default(0);
                $table->decimal('meal_allowance', 12, 2)->default(0);
                $table->decimal('net_received', 12, 2)->default(0);
                $table->date('paid_at')->nullable();
                $table->string('status', 20)->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        $paymentIndexes = collect(Schema::getIndexes('salary_payments'))->pluck('name')->all();
        if (! in_array('salary_payments_contract_month_unique', $paymentIndexes, true)) {
            Schema::table('salary_payments', function (Blueprint $table) {
                $table->unique(
                    ['recurring_income_id', 'payment_year', 'payment_month'],
                    'salary_payments_contract_month_unique',
                );
            });
        }
        if (! in_array('salary_payments_workspace_period_index', $paymentIndexes, true)) {
            Schema::table('salary_payments', function (Blueprint $table) {
                $table->index(
                    ['workspace_id', 'user_id', 'payment_year', 'payment_month'],
                    'salary_payments_workspace_period_index',
                );
            });
        }

        if (! Schema::hasTable('salary_documents')) {
            Schema::create('salary_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('recurring_income_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->string('document_type', 40);
                $table->string('original_name');
                $table->string('file_path');
                $table->string('mime_type', 100);
                $table->unsignedBigInteger('file_size');
                $table->timestamps();

                $table->index(
                    ['workspace_id', 'user_id', 'recurring_income_id'],
                    'salary_documents_workspace_contract_index',
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_documents');
        Schema::dropIfExists('salary_payments');
    }
};
