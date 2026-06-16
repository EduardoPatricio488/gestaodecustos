<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentIncome;
use Carbon\Carbon;

class DebtInstrumentCalculator
{
    const TAX_RATE = 0.28;

    /**
     * Tabela oficial de prémios de permanência — Série F
     * Chave = anos completos de permanência
     */
    const LOYALTY_TABLE = [
        0 => 0.00,   // 0-1 ano
        1 => 0.25,   // 1-2 anos
        2 => 0.50,   // 2-3 anos
        3 => 0.75,   // 3-4 anos
        4 => 1.00,   // 4-5 anos
        5 => 1.25,   // 5+ anos
    ];

    /**
     * Calcula o prémio de permanência com base nos anos completos
     * desde a data de subscrição.
     */
    public static function getLoyaltyBonus(Carbon $subscriptionDate): float
    {
        $yearsComplete = (int) $subscriptionDate->diffInYears(now());
        $maxKey = max(array_keys(self::LOYALTY_TABLE));
        $key    = min($yearsComplete, $maxKey);
        return self::LOYALTY_TABLE[$key];
    }

    /**
     * Processa todos os trimestres em falta para um Certificado de Aforro (CA).
     * Capitaliza o saldo e regista cada juro em investment_incomes.
     * Devolve o novo current_price (saldo por título).
     */
    public static function processCA(Investment $asset): float
    {
        $subscriptionDate = Carbon::parse($asset->operation_date);
        $baseRate         = (float) ($asset->interest_rate ?? 0); // taxa base atual (%)
        $capitalPerUnit   = (float) $asset->current_price;        // saldo atual por título (começa em 1€)
        $quantity         = (float) $asset->quantity;             // número de títulos

        // Última capitalização registada (ou data de subscrição se nenhuma ainda)
        $lastIncome = InvestmentIncome::where('investment_id', $asset->id)
            ->where('type', 'CA')
            ->orderByDesc('reference_date')
            ->first();

        $lastCapDate = $lastIncome
            ? Carbon::parse($lastIncome->reference_date)
            : $subscriptionDate->copy();

        $today = now();

        // Processar cada trimestre em falta
        $cursor = $lastCapDate->copy()->addMonths(3);

        while ($cursor <= $today) {
            $loyaltyBonus    = self::getLoyaltyBonus($subscriptionDate);
            $annualRateGross = $baseRate + $loyaltyBonus;          // % bruta anual
            $quarterlyNet    = ($annualRateGross / 4 / 100) * (1 - self::TAX_RATE); // taxa trimestral líquida

            $grossPerUnit = $capitalPerUnit * ($annualRateGross / 4 / 100);
            $taxPerUnit   = $grossPerUnit * self::TAX_RATE;
            $netPerUnit   = $grossPerUnit - $taxPerUnit;

            $newCapitalPerUnit = $capitalPerUnit + $netPerUnit; // capitaliza (juro composto)

            InvestmentIncome::create([
                'investment_id'  => $asset->id,
                'user_id'        => $asset->user_id,
                'workspace_id'   => $asset->workspace_id,
                'reference_date' => $cursor->toDateString(),
                'gross_amount'   => round($grossPerUnit * $quantity, 4),
                'tax_amount'     => round($taxPerUnit   * $quantity, 4),
                'net_amount'     => round($netPerUnit   * $quantity, 4),
                'base_rate'      => $baseRate,
                'loyalty_bonus'  => $loyaltyBonus,
                'capital_before' => round($capitalPerUnit    * $quantity, 4),
                'capital_after'  => round($newCapitalPerUnit * $quantity, 4),
                'type'           => 'CA',
            ]);

            $capitalPerUnit = $newCapitalPerUnit;
            $cursor->addMonths(3);
        }

        return round($capitalPerUnit, 6);
    }

    /**
     * Processa todos os anos em falta para um Certificado do Tesouro (CT).
     * NÃO capitaliza — regista o juro como rendimento e o saldo fica em 1€.
     */
    public static function processCT(Investment $asset): float
    {
        $subscriptionDate = Carbon::parse($asset->operation_date);
        $baseRate         = (float) ($asset->interest_rate ?? 0);
        $quantity         = (float) $asset->quantity;

        $lastIncome = InvestmentIncome::where('investment_id', $asset->id)
            ->where('type', 'CT')
            ->orderByDesc('reference_date')
            ->first();

        $lastCapDate = $lastIncome
            ? Carbon::parse($lastIncome->reference_date)
            : $subscriptionDate->copy();

        $today  = now();
        $cursor = $lastCapDate->copy()->addYear();

        while ($cursor <= $today) {
            $loyaltyBonus    = self::getLoyaltyBonus($subscriptionDate);
            $annualRateGross = $baseRate + $loyaltyBonus;

            $grossTotal = $quantity * ($annualRateGross / 100);
            $taxTotal   = $grossTotal * self::TAX_RATE;
            $netTotal   = $grossTotal - $taxTotal;

            InvestmentIncome::create([
                'investment_id'  => $asset->id,
                'user_id'        => $asset->user_id,
                'workspace_id'   => $asset->workspace_id,
                'reference_date' => $cursor->toDateString(),
                'gross_amount'   => round($grossTotal, 4),
                'tax_amount'     => round($taxTotal,   4),
                'net_amount'     => round($netTotal,   4),
                'base_rate'      => $baseRate,
                'loyalty_bonus'  => $loyaltyBonus,
                'capital_before' => $quantity, // CT: capital não muda
                'capital_after'  => $quantity,
                'type'           => 'CT',
            ]);

            $cursor->addYear();
        }

        return 1.00; // CT: current_price mantém-se sempre 1€
    }

    /**
     * Ponto de entrada — decide CA ou CT e devolve o novo current_price.
     */
    public static function process(Investment $asset): float
    {
        if (!$asset->operation_date) return (float) $asset->current_price;

        return match($asset->product_type) {
            'CA'    => self::processCA($asset),
            'CT'    => self::processCT($asset),
            default => (float) $asset->current_price,
        };
    }
}
