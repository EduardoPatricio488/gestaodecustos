<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('plan');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
        });

        Schema::create('at_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('at_uid')->nullable();
            $table->string('issuer_nif', 20)->nullable();
            $table->string('issuer_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->date('issued_at');
            $table->string('document_type')->default('FT');
            $table->string('status')->default('imported');
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('at_invoices');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_subscription_id']);
        });
    }
};
