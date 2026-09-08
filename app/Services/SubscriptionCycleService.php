<?php

namespace App\Services;

class SubscriptionCycleService
{
    /**
     * Converte o valor de uma subscrição para o equivalente mensal, de acordo com o ciclo de cobrança.
     */
    public static function toMonthly(float $amount, ?string $cycle): float
    {
        return match ($cycle) {
            'quarterly' => $amount / 3,
            'semiannual' => $amount / 6,
            'annual', 'yearly' => $amount / 12,
            default => $amount,
        };
    }
}
