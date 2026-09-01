<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Models\StoreCheckoutSession;
use App\Services\StoreCartService;
use App\Services\StoreCouponService;
use App\Services\StorePurchaseService;
use App\Services\StoreRecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Checkout extends Component
{
    use InteractsWithStore;

    public int $step = 1;

    public string $couponCode = '';

    public string $paymentMethod = 'stripe';

    public bool $addExpenseToEducation = true;

    public ?float $discount = null;

    public function mount(): void
    {
        if (app(StoreCartService::class)->count() === 0) {
            $this->redirect(route('store.cart'), navigate: true);
        }

        $this->discount = app(StoreCouponService::class)->calculateDiscount(
            app(StoreCartService::class)->total()
        );
    }

    public function nextStep(): void
    {
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function applyCoupon(): void
    {
        $result = app(StoreCouponService::class)->apply($this->couponCode);

        if (! $result['success']) {
            $this->dispatch('toast', text: $result['message']);

            return;
        }

        $this->discount = app(StoreCouponService::class)->calculateDiscount(
            app(StoreCartService::class)->total()
        );

        $this->dispatch('toast', text: $result['message']);
    }

    public function removeCoupon(): void
    {
        app(StoreCouponService::class)->clear();
        $this->couponCode = '';
        $this->discount = 0;
        $this->dispatch('toast', text: 'Cupão removido.');
    }

    public function confirmPurchase()
    {
        if ($this->paymentMethod === 'stripe') {
            return $this->payWithStripe();
        }

        $cart = app(StoreCartService::class);
        $couponService = app(StoreCouponService::class);
        $purchaseService = app(StorePurchaseService::class);
        $items = $cart->items();

        if ($items->isEmpty()) {
            $this->dispatch('toast', text: 'O carrinho está vazio.');

            return;
        }

        $subtotal = $cart->total();
        $coupon = $couponService->getApplied();
        $discount = $couponService->calculateDiscount($subtotal, $coupon);
        $purchased = 0;
        $couponUsed = false;

        foreach ($items as $item) {
            if ($item['product']->requires_business_plan && ! (Auth::user()?->isBusinessPlan() ?? false)) {
                $cart->remove($item['product']->id);

                $this->dispatch('toast', text: "{$item['product']->title} requer o plano Business e foi removido do carrinho.");

                continue;
            }

            $itemDiscount = $subtotal > 0 ? round($discount * ($item['subtotal'] / $subtotal), 2) : 0;

            $amountPaid = $item['subtotal'] - $itemDiscount;

            $purchaseService->completePurchase(
                $item['product'],
                $amountPaid,
                $this->paymentMethod,
                coupon: $couponUsed ? null : $coupon,
                discount: $itemDiscount,
            );

            if ($this->addExpenseToEducation) {
                $purchaseService->recordEducationExpense($item['product'], $amountPaid);
            }

            $couponUsed = $coupon !== null;
            $purchased++;
        }

        $cart->clear();
        $couponService->clear();
        $this->dispatch('cart-updated');

        if ($purchased === 0) {
            $this->dispatch('toast', text: 'Não foi possível concluir a compra com os produtos do carrinho.');

            return redirect()->route('hub.inventory');
        }

        $toast = $purchased === 1
            ? 'Compra concluída! Recurso ativado no inventário.'
            : "{$purchased} recursos ativados no inventário.";

        if ($this->addExpenseToEducation) {
            $toast .= ' Despesa registada em Educação.';
        }

        $this->dispatch('toast', text: $toast);

        return redirect()->route('hub.inventory');
    }

    /**
     * Cria uma sessão de Stripe Checkout para o carrinho atual e redireciona o utilizador
     * para o pagamento por cartão. A compra só é ativada quando o pagamento é confirmado
     * (ver StoreCheckoutStripeController e StripeWebhookListener).
     */
    public function payWithStripe()
    {
        $cart = app(StoreCartService::class);
        $couponService = app(StoreCouponService::class);
        $items = $cart->items();

        if ($items->isEmpty()) {
            $this->dispatch('toast', text: 'O carrinho está vazio.');

            return;
        }

        $subtotal = $cart->total();
        $coupon = $couponService->getApplied();
        $discount = $couponService->calculateDiscount($subtotal, $coupon);

        $lineItems = [];
        $pendingItems = [];

        foreach ($items as $item) {
            $product = $item['product'];

            if ($product->requires_business_plan && ! (Auth::user()?->isBusinessPlan() ?? false)) {
                $cart->remove($product->id);

                $this->dispatch('toast', text: "{$product->title} requer o plano Business e foi removido do carrinho.");

                continue;
            }

            $itemDiscount = $subtotal > 0 ? round($discount * ($item['subtotal'] / $subtotal), 2) : 0;
            $amountPaid = max(0, $item['subtotal'] - $itemDiscount);

            $lineItems[] = [
                'price_data' => [
                    'currency' => Auth::user()->preferredCurrency(),
                    'product_data' => ['name' => $product->title],
                    'unit_amount' => (int) round($amountPaid * 100),
                ],
                'quantity' => 1,
            ];

            $pendingItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'amount_paid' => $amountPaid,
            ];
        }

        if (empty($lineItems)) {
            $this->dispatch('toast', text: 'Não há produtos elegíveis para pagamento.');

            return redirect()->route('hub.inventory');
        }

        $pending = StoreCheckoutSession::create([
            'user_id' => Auth::id(),
            'items' => $pendingItems,
            'discount_amount' => $discount,
            'coupon_code' => $coupon?->code,
            'add_expense_to_education' => $this->addExpenseToEducation,
            'status' => 'pending',
        ]);

        try {
            $checkout = Auth::user()->checkout($lineItems, [
                'success_url' => route('store.checkout.stripe.success', $pending->id).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('store.checkout.stripe.cancel', $pending->id),
                'metadata' => [
                    'type' => 'store_purchase',
                    'pending_id' => $pending->id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erro no Stripe Checkout da loja: '.$e->getMessage());
            $pending->delete();
            $this->dispatch('toast', text: 'Não foi possível contactar o Stripe. Tenta novamente.');

            return;
        }

        $pending->update(['stripe_session_id' => $checkout->asStripeCheckoutSession()->id]);

        return redirect($checkout->url);
    }

    public function render()
    {
        $cart = app(StoreCartService::class);
        $couponService = app(StoreCouponService::class);
        $subtotal = $cart->total();
        $discount = $couponService->calculateDiscount($subtotal);
        $cartProducts = $cart->items()->pluck('product');
        $recommendations = app(StoreRecommendationService::class);

        return view('livewire.store.checkout', [
            'items' => $cart->items(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'appliedCoupon' => $couponService->getApplied(),
            'crossSell' => $recommendations->crossSell($cartProducts),
            'upsell' => $recommendations->upsell($cartProducts),
        ]);
    }
}
