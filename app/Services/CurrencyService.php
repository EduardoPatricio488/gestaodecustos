<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    /**
     * Converte um valor de uma moeda para outra usando taxas em tempo real.
     */
    public static function convert($amount, $from = 'USD', $to = 'EUR')
    {
        if ($from === $to) return $amount;

        // Guardamos as taxas em cache por 24h para o site ser rápido e grátis
        $rates = Cache::remember('currency_rates_' . $to, 86400, function () use ($to) {
            $response = Http::get("https://open.er-api.com/v6/latest/{$to}");
            return $response->json()['rates'] ?? [];
        });

        if (isset($rates[$from])) {
            // Cálculo: Valor / Taxa da moeda de origem
            return round($amount / $rates[$from], 2);
        }

        return $amount;
    }

    public static function getSymbols()
    {
        return [
            'EUR' => '€ Euro',
            'USD' => '$ Dólar Americano',
            'BRL' => 'R$ Real Brasileiro',
            'GBP' => '£ Libra Esterlina',
            'CHF' => 'CHF Franco Suíço',
        ];
    }
}
