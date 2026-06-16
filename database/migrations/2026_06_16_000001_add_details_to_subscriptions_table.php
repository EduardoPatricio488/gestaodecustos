<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('cycle');
            }

            if (! Schema::hasColumn('subscriptions', 'status')) {
                $table->string('status')->default('active')->after('payment_method');
            }

            if (! Schema::hasColumn('subscriptions', 'started_at')) {
                $table->date('started_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('subscriptions', 'renewal_date')) {
                $table->date('renewal_date')->nullable()->after('started_at');
            }

            if (! Schema::hasColumn('subscriptions', 'notes')) {
                $table->text('notes')->nullable()->after('renewal_date');
            }

            if (! Schema::hasColumn('subscriptions', 'notify_before_billing')) {
                $table->boolean('notify_before_billing')->default(false)->after('notes');
            }

            if (! Schema::hasColumn('subscriptions', 'notify_days_before')) {
                $table->unsignedTinyInteger('notify_days_before')->nullable()->after('notify_before_billing');
            }
        });

        if (Schema::hasColumn('subscriptions', 'status')) {
            DB::table('subscriptions')
                ->where('is_active', false)
                ->where('status', 'active')
                ->update(['status' => 'paused']);
        }
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            foreach ([
                'payment_method',
                'status',
                'started_at',
                'renewal_date',
                'notes',
                'notify_before_billing',
                'notify_days_before',
            ] as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
