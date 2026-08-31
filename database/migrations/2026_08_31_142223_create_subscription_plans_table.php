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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Ex: Pro, Business
            $table->string('slug')->unique();    // Ex: pro, business
            $table->decimal('price', 10, 2);     // Ex: 5.00, 10.00
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // Aqui guardamos as permissões (IA, Empresa, etc)
            $table->string('stripe_price_id')->nullable(); // O ID que vem do Stripe
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
