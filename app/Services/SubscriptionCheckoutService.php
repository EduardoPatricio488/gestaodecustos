<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;

class SubscriptionCheckoutService
{
    public function upgradePlan(User $user, string $plan): void
    {
        $user->update(['plan' => $plan]);

        if ($user->currentWorkspace) {
            $user->currentWorkspace->update(['plan' => $plan]);
        }

        if ($plan === 'free') {
            return;
        }

        $amount = config("plans.{$plan}.amount", 0);

        if ($amount <= 0) {
            return;
        }

        Payment::create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-'.strtoupper($plan).'-'.time(),
            'plan_type' => $plan,
            'amount' => $amount,
            'status' => 'paid',
            'method' => 'system',
            'paid_at' => now(),
        ]);
    }
}
