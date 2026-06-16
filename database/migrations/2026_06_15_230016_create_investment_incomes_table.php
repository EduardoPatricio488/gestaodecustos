<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('investment_incomes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('investment_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
        $table->date('reference_date');       // data em que o juro foi creditado
        $table->decimal('gross_amount', 12, 4);  // juro bruto
        $table->decimal('tax_amount', 12, 4);    // retenção 28%
        $table->decimal('net_amount', 12, 4);    // juro líquido
        $table->decimal('base_rate', 5, 3);      // taxa base usada
        $table->decimal('loyalty_bonus', 5, 3)->default(0); // prémio permanência
        $table->decimal('capital_before', 12, 4); // saldo antes da capitalização
        $table->decimal('capital_after', 12, 4);  // saldo após (só CA)
        $table->string('type', 5);               // 'CA' ou 'CT'
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('investment_incomes');
}
};
