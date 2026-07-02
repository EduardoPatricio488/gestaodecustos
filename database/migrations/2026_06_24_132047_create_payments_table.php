<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('invoice_id')->unique(); // Ex: INV-2024-001
        $table->string('plan_type'); // star, diamond
        $table->decimal('amount', 10, 2);
        $table->string('currency')->default('EUR');
        $table->string('status')->default('paid'); // paid, pending, failed, refunded
        $table->string('method')->default('stripe'); // stripe, paypal, mbway
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
