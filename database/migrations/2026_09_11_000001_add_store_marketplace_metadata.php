<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            if (! Schema::hasColumn('store_products', 'audience')) {
                $table->string('audience')->default('both')->after('type');
            }
            if (! Schema::hasColumn('store_products', 'objectives')) {
                $table->json('objectives')->nullable()->after('features');
            }
            if (! Schema::hasColumn('store_products', 'compatibility')) {
                $table->json('compatibility')->nullable()->after('objectives');
            }
            if (! Schema::hasColumn('store_products', 'integration_type')) {
                $table->string('integration_type')->default('none')->after('compatibility');
            }
            if (! Schema::hasColumn('store_products', 'integration_route')) {
                $table->string('integration_route')->nullable()->after('integration_type');
            }
            if (! Schema::hasColumn('store_products', 'integration_label')) {
                $table->string('integration_label')->nullable()->after('integration_route');
            }
            if (! Schema::hasColumn('store_products', 'integration_location')) {
                $table->string('integration_location')->nullable()->after('integration_label');
            }
            if (! Schema::hasColumn('store_products', 'entitlement_key')) {
                $table->string('entitlement_key')->nullable()->index()->after('integration_location');
            }
            if (! Schema::hasColumn('store_products', 'delivery_type')) {
                $table->string('delivery_type')->default('feature')->after('entitlement_key');
            }
            if (! Schema::hasColumn('store_products', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('delivery_type');
            }
        });

        if (! Schema::hasTable('store_product_entitlements')) {
            Schema::create('store_product_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('store_products')->cascadeOnDelete();
                $table->string('key');
                $table->string('type')->default('feature');
                $table->string('route')->nullable();
                $table->string('label')->nullable();
                $table->string('location')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['product_id', 'key']);
                $table->index(['key', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_product_entitlements');

        Schema::table('store_products', function (Blueprint $table) {
            foreach ([
                'audience', 'objectives', 'compatibility', 'integration_type', 'integration_route',
                'integration_label', 'integration_location', 'entitlement_key', 'delivery_type', 'sort_order',
            ] as $column) {
                if (Schema::hasColumn('store_products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
