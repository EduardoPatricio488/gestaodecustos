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
        abort_unless($pending->user_id === Auth::id(), 403);

        if ($pending->status === 'completed') {
            session()->forget(['store_cart', 'store_coupon']);

            return redirect()->route('hub.inventory')->with('toast', 'Compra confirmada! Recibo enviado por e-mail.');
        }

        $sessionId = (string) $request->query('session_id', '');

        if ($sessionId === '') {
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

        $purchases->completeStoreCheckout($pending, $stripeSession->id);

        session()->forget(['store_cart', 'store_coupon']);

        return redirect()->route('hub.inventory')->with('toast', 'Pagamento confirmado! Recibo enviado por e-mail.');
    }

    public function cancel(StoreCheckoutSession $pending)
    {
        abort_unless($pending->user_id === Auth::id(), 403);

        if ($pending->status === 'pending') {
            $pending->update(['status' => 'cancelled']);
        }

        return redirect()->route('store.checkout')->with('toast', 'Pagamento cancelado.');
    }
}
