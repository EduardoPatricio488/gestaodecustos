<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CompanySubscriptionPaymentService
{
    public function processDuePayments(): int
    {
        $created = 0;
        $today = Carbon::today();

        Subscription::query()
            ->where('paid_by_company', true)
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereNotNull('renewal_date')
            ->whereDate('renewal_date', '<=', $today)
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) use (&$created, $today) {
                foreach ($subscriptions as $subscription) {
                    $created += DB::transaction(function () use ($subscription, $today) {
                        $locked = Subscription::query()->lockForUpdate()->find($subscription->id);

                        if (! $locked || ! $locked->paid_by_company || $locked->status !== 'active' || ! $locked->is_active || ! $locked->renewal_date) {
                            return 0;
                        }

                        $dueDate = Carbon::parse($locked->renewal_date)->startOfDay();
                        $processed = 0;

                        while ($dueDate->lte($today)) {
                            SubscriptionPayment::firstOrCreate(
                                [
                                    'subscription_id' => $locked->id,
                                    'billing_date' => $dueDate->toDateString(),
                                ],
                                [
                                    'workspace_id' => $locked->workspace_id,
                                    'user_id' => $locked->user_id,
                                    'amount' => $locked->amount,
                                    'paid_at' => $dueDate->toDateString(),
                                    'payment_source' => 'company',
                                ]
                            );

                            $processed++;
                            $dueDate = match ($locked->cycle) {
                                'quarterly' => $dueDate->copy()->addMonthsNoOverflow(3),
                                'semiannual' => $dueDate->copy()->addMonthsNoOverflow(6),
                                'annual', 'yearly' => $dueDate->copy()->addYearNoOverflow(),
                                default => $dueDate->copy()->addMonthNoOverflow(),
                            };
                        }

                        $locked->renewal_date = $dueDate->toDateString();
                        $locked->save();

                        return $processed;
                    });
                }
            });

        return $created;
    }
}
