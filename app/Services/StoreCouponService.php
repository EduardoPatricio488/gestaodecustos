<?php

namespace App\Services;

use App\Models\StoreCoupon;

class StoreCouponService
{
    private const SESSION_KEY = 'store_coupon';

    public function getApplied(): ?StoreCoupon
    {
        $code = session(self::SESSION_KEY);

        if (! $code) {
            return null;
        }

        return StoreCoupon::where('code', $code)->first();
    }

    public function apply(string $code): array
    {
        $coupon = StoreCoupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon || ! $coupon->isValid()) {
            return ['success' => false, 'message' => 'Cupão inválido ou expirado.'];
        }

        session([self::SESSION_KEY => $coupon->code]);

        return ['success' => true, 'message' => 'Cupão aplicado com sucesso!', 'coupon' => $coupon];
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function calculateDiscount(float $subtotal, ?StoreCoupon $coupon = null): float
    {
        $coupon ??= $this->getApplied();

        if (! $coupon || ! $coupon->isValid() || $subtotal < (float) $coupon->min_purchase) {
            return 0;
        }

        if ($coupon->type === 'fixed') {
            return min((float) $coupon->value, $subtotal);
        }

        return round($subtotal * ((float) $coupon->value / 100), 2);
    }
}
