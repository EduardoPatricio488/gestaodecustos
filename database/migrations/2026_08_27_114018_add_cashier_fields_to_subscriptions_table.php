<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Campos que o Laravel Cashier/Stripe exige
            if (! Schema::hasColumn('subscriptions', 'type')) {
                $table->string('type')->default('default');
            }
            if (! Schema::hasColumn('subscriptions', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->index();
            }
            if (! Schema::hasColumn('subscriptions', 'stripe_status')) {
                $table->string('stripe_status')->nullable();
            }
            if (! Schema::hasColumn('subscriptions', 'stripe_price')) {
                $table->string('stripe_price')->nullable();
            }
            if (! Schema::hasColumn('subscriptions', 'quantity')) {
                $table->integer('quantity')->nullable();
            }
            if (! Schema::hasColumn('subscriptions', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
            if (! Schema::hasColumn('subscriptions', 'ends_at')) {
                $table->timestamp('ends_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'stripe_id', 'stripe_status', 'stripe_price',
                'quantity', 'trial_ends_at', 'ends_at',
            ]);
        });
    }
};
