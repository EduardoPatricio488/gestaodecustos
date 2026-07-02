<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Models\StoreProduct;
use App\Services\StoreCartService;
use App\Services\StoreCompareService;
use App\Services\StoreCouponService;
use App\Services\StorePurchaseService;
use App\Services\StoreRecommendationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Checkout extends Component
{
    use InteractsWithStore;

    public int $step = 1;

    public string $couponCode = '';

    public string $paymentMethod = 'simulated';

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
            if ($cart->isOwned($item['product']->id)) {
                $cart->remove($item['product']->id);

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
            $this->dispatch('toast', text: 'Todos os produtos já estavam no teu inventário.');

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
