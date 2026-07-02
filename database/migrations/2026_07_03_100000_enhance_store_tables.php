<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('features');
            $table->json('screenshots')->nullable()->after('video_url');
            $table->json('roadmap')->nullable()->after('screenshots');
            $table->json('faq')->nullable()->after('roadmap');
            $table->json('learning_outcomes')->nullable()->after('faq');
            $table->json('related_products')->nullable()->after('learning_outcomes');
            $table->unsignedInteger('sales_count')->default(0)->after('related_products');
            $table->decimal('rating_avg', 3, 2)->default(0)->after('sales_count');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
            $table->boolean('is_featured')->default(false)->after('rating_count');
            $table->string('stripe_price_id')->nullable()->after('is_featured');
            $table->string('download_path')->nullable()->after('stripe_price_id');
            $table->unsignedInteger('points_reward')->default(0)->after('download_path');
        });

        Schema::table('store_purchases', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('payment_status');
            $table->string('coupon_code')->nullable()->after('stripe_session_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
            $table->string('payment_method')->default('simulated')->after('discount_amount');
        });

        Schema::create('store_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('store_products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('store_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('store_products')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('store_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('badge')->nullable();
            $table->unsignedTinyInteger('savings_percent')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('store_bundle_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('store_bundles')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('store_products')->cascadeOnDelete();
            $table->unique(['bundle_id', 'product_id']);
        });

        Schema::create('store_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('percent');
            $table->decimal('value', 10, 2);
            $table->decimal('min_purchase', 10, 2)->default(0);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('store_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('store_purchases')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('store_products')->cascadeOnDelete();
            $table->string('license_key')->unique();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('store_download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('license_id')->constrained('store_licenses')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('token_hash')->nullable();
            $table->timestamp('downloaded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_download_logs');
        Schema::dropIfExists('store_licenses');
        Schema::dropIfExists('store_coupons');
        Schema::dropIfExists('store_bundle_products');
        Schema::dropIfExists('store_bundles');
        Schema::dropIfExists('store_reviews');
        Schema::dropIfExists('store_wishlists');

        Schema::table('store_purchases', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'coupon_code', 'discount_amount', 'payment_method']);
        });

        Schema::table('store_products', function (Blueprint $table) {
            $table->dropColumn([
                'video_url', 'screenshots', 'roadmap', 'faq', 'learning_outcomes',
                'related_products', 'sales_count', 'rating_avg', 'rating_count',
                'is_featured', 'stripe_price_id', 'download_path', 'points_reward',
            ]);
        });
    }
};
