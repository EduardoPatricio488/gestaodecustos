<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Contas Bancárias
        if (!Schema::hasTable('bank_accounts')) Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // checking, savings, investment, cash, wallet
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('currency')->default('EUR');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_business')->default(false);
            $table->string('status')->default('active'); // active, archived
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        // 2. Transferências entre contas
        if (!Schema::hasTable('bank_transfers')) Schema::create('bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->timestamp('transferred_at');
            $table->timestamps();
        });

        // 3. Reservas (Dinheiro "congelado")
        if (!Schema::hasTable('bank_reserves')) Schema::create('bank_reserves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ex: IRS, Férias, Emergência
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('target_amount', 15, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 4. Itens em Trânsito
        if (!Schema::hasTable('bank_transit_items')) Schema::create('bank_transit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('type'); // incoming (a entrar), outgoing (a sair)
            $table->string('description');
            $table->date('expected_date');
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transit_items');
        Schema::dropIfExists('bank_reserves');
        Schema::dropIfExists('bank_transfers');
        Schema::dropIfExists('bank_accounts');
    }
};
