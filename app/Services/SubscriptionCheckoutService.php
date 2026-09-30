<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Str;

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
        $paymentStatus = (string) ($session['payment_status'] ?? '');
        $sessionStatus = (string) ($session['status'] ?? '');
        $mode = (string) ($session['mode'] ?? '');

        // Nunca ativar um plano apenas porque metadata/client_reference_id parecem válidos.
        if (! $userId || ! $planSlug || $paymentStatus !== 'paid' || $sessionStatus !== 'complete' || $mode !== 'subscription') {
            return null;
        }

        $plan = SubscriptionPlan::where('slug', $planSlug)
            ->where('is_active', true)
            ->first();

        if (! $plan) {
            return null;
        }

        $user = User::find($userId);

        if (! $user) {
            return null;
        }

        // Se o Stripe já identificou o cliente, não aceitar uma sessão de outro cliente.
        $customerId = (string) ($session['customer'] ?? '');
        if ($customerId !== '' && $user->stripe_id && $customerId !== (string) $user->stripe_id) {
            return null;
        }

        if ($user->plan !== $planSlug) {
            $user->forceFill(['plan' => $planSlug])->save();

            if ($user->currentWorkspace) {
                $user->currentWorkspace->update(['plan' => $planSlug]);
            }
        }

        return $planSlug;
    }

    public function upgradePlan(User $user, string $plan): void
    {
        $user->forceFill(['plan' => $plan])->save();

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
            'invoice_id' => 'INV-'.strtoupper($plan).'-'.Str::upper((string) Str::ulid()),
            'plan_type' => $plan,
            'amount' => $amount,
            'status' => 'paid',
            'method' => 'system',
            'paid_at' => now(),
        ]);
    }
}
