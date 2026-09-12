<?php

namespace App\Http\Controllers;

use App\Models\StoreCheckoutSession;
use App\Services\StorePurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreCheckoutStripeController extends Controller
{
    public function success(Request $request, StoreCheckoutSession $pending, StorePurchaseService $purchases)
    {
        abort_unless((int) $pending->user_id === (int) Auth::id(), 403);

        if ($pending->status === 'completed') {
            session()->forget(['store_cart', 'store_coupon']);

            return redirect()->route('hub.inventory')->with('toast', 'Compra confirmada! Recibo enviado por e-mail.');
        }

        $sessionId = (string) $request->query('session_id', '');
        if ($sessionId === '') {
            return redirect()->route('store.checkout')->with('toast', 'Sessão de pagamento inválida.');
        }

        // The success URL is user-controlled. The Stripe session must be the exact
        // session created for this pending checkout; otherwise a valid paid session
        // could otherwise be reused to unlock a different pending cart.
        if (! hash_equals((string) $pending->stripe_session_id, $sessionId)) {
            Log::warning('Stripe checkout session mismatch.', [
                'pending_id' => $pending->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('store.checkout')->with('toast', 'Sessão de pagamento inválida.');
        }

        try {
            $stripeSession = Auth::user()->stripe()->checkout->sessions->retrieve($sessionId);
        } catch (\Throwable $e) {
            Log::error('Erro ao verificar sessão Stripe da loja: '.$e->getMessage());

            return redirect()->route('store.checkout')->with('toast', 'Não foi possível confirmar o pagamento. Contacta o suporte se o valor foi debitado.');
        }

        if ($stripeSession->payment_status !== 'paid') {
            return redirect()->route('store.checkout')->with('toast', 'Pagamento não confirmado.');
        }

        $metadataPendingId = (string) ($stripeSession->metadata['pending_id'] ?? '');
        if ($metadataPendingId !== (string) $pending->id || (string) ($stripeSession->metadata['type'] ?? '') !== 'store_purchase') {
            Log::warning('Stripe checkout metadata mismatch.', [
                'pending_id' => $pending->id,
                'stripe_session_id' => $stripeSession->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('store.checkout')->with('toast', 'Sessão de pagamento inválida.');
        }

        $expectedAmount = (int) round(collect($pending->items ?? [])->sum(
            fn ($item) => ((float) ($item['amount_paid'] ?? 0)) * ((int) ($item['quantity'] ?? 1))
        ) * 100);
        $actualAmount = (int) ($stripeSession->amount_total ?? 0);
        $expectedCurrency = strtolower((string) Auth::user()->preferredCurrency());
        $actualCurrency = strtolower((string) ($stripeSession->currency ?? ''));

        if ($actualAmount !== $expectedAmount || $actualCurrency !== $expectedCurrency) {
            Log::warning('Stripe checkout amount/currency mismatch.', [
                'pending_id' => $pending->id,
                'stripe_session_id' => $stripeSession->id,
                'expected_amount' => $expectedAmount,
                'actual_amount' => $actualAmount,
                'expected_currency' => $expectedCurrency,
                'actual_currency' => $actualCurrency,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('store.checkout')->with('toast', 'O valor do pagamento não corresponde à compra. Contacta o suporte.');
        }

        $purchases->completeStoreCheckout($pending, $stripeSession->id);
        session()->forget(['store_cart', 'store_coupon']);

        return redirect()->route('hub.inventory')->with('toast', 'Pagamento confirmado! Recibo enviado por e-mail.');
    }

    public function cancel(StoreCheckoutSession $pending)
    {
        abort_unless((int) $pending->user_id === (int) Auth::id(), 403);

        if ($pending->status === 'pending') {
            $pending->update(['status' => 'cancelled']);
        }

        return redirect()->route('store.checkout')->with('toast', 'Pagamento cancelado.');
    }
}
