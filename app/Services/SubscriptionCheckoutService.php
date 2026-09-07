<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionCheckoutService
{
    /**
     * Ativa o plano a partir de uma sessão de Checkout do Stripe (usado pelo webhook e, como
     * rede de segurança, quando o utilizador regressa ao site antes do webhook chegar).
     *
     * @param  array|\ArrayAccess  $session
     */
    public function activateFromStripeSession($session): ?string
    {
        $userId = $session['client_reference_id'] ?? null;
        $planSlug = $session['metadata']['plan_slug'] ?? null;

        if (! $userId || ! $planSlug) {
            return null;
        }

        $user = User::find($userId);

        if (! $user) {
            return null;
        }

        if ($user->plan !== $planSlug) {
            $user->update(['plan' => $planSlug]);

            if ($user->currentWorkspace) {
                $user->currentWorkspace->update(['plan' => $planSlug]);
            }
        }

        return $planSlug;
    }

    public function upgradePlan(User $user, string $plan): void
    {
        $user->update(['plan' => $plan]);

        if ($user->currentWorkspace) {
            $user->currentWorkspace->update(['plan' => $plan]);
        }

        if ($plan === 'free') {
            return;
        }

        $amount = SubscriptionPlan::where('slug', $plan)->value('price')
            ?? config("plans.{$plan}.amount", 0);

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
