<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('client_name');
        $table->string('invoice_number');
        $table->decimal('amount_excl_vat', 10, 2);
        $table->decimal('vat_amount', 10, 2);
        $table->decimal('total_amount', 10, 2);
        $table->string('status')->default('paga'); // paga, pendente, atrasada
        $table->date('due_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
