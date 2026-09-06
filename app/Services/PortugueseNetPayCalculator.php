<?php

namespace App\Services;

/**
 * Estimativa de salário líquido (Continente, 2026).
 *
 * Retenção na fonte: Despacho n.º 233-A/2026.
 * TSU trabalhador: 11 %.
 * Subsídio de refeição: isenção 6,15 € (numerário) / 10,455 € (cartão).
 * IRS Jovem: art. 12.º-B CIRS (55 × IAS / 14 por mês).
 */
class PortugueseNetPayCalculator
{
    public const SOCIAL_SECURITY_RATE = 0.11;

    public const MEAL_EXEMPT_CASH = 6.15;

    public const MEAL_EXEMPT_CARD = 10.455;

    public const IAS_2026 = 537.13;

    public const IRS_JOVEM_ANNUAL_CAP = 55 * self::IAS_2026; // 29 542,15 €

    /**
     * @return array{
     *     gross: float,
     *     taxable_remuneration: float,
     *     social_security: float,
     *     irs: float,
     *     irs_before_jovem: float,
     *     meal_total: float,
     *     meal_exempt: float,
     *     meal_taxable: float,
     *     net: float,
     *     table: string,
     *     marginal_rate: float,
     *     effective_irs_rate: float,
     *     irs_jovem_rate: float,
     *     irs_jovem_exempt_income: float
     * }
     */
    public function calculate(
        float $gross,
        string $civilStatus = 'solteiro',
        int $dependents = 0,
        bool $isDisabled = false,
        float $mealPerDay = 0,
        int $workingDays = 22,
        string $mealPayment = 'cartao',
        bool $irsJovem = false,
        int $irsJovemYear = 1,
    ): array {
        $gross = max(0, $gross);
        $dependents = max(0, $dependents);
        $workingDays = max(0, min(31, $workingDays));
        $mealPerDay = max(0, $mealPerDay);
        $irsJovemYear = max(1, min(10, $irsJovemYear));

        $mealTotal = round($mealPerDay * $workingDays, 2);
        $dailyExempt = $mealPayment === 'numerario' ? self::MEAL_EXEMPT_CASH : self::MEAL_EXEMPT_CARD;
        $mealTaxable = round(max(0, $mealPerDay - $dailyExempt) * $workingDays, 2);
        $mealExempt = round($mealTotal - $mealTaxable, 2);

        $taxableRemuneration = round($gross + $mealTaxable, 2);

        $ss = round($taxableRemuneration * self::SOCIAL_SECURITY_RATE, 2);

        $withholding = $this->withholding($taxableRemuneration, $civilStatus, $dependents, $isDisabled);
        $irsNormal = $withholding['irs'];

        $jovemRate = $irsJovem ? $this->irsJovemExemptionRate($irsJovemYear) : 0.0;
        $monthlyCap = round(self::IRS_JOVEM_ANNUAL_CAP / 14, 2);
        $exemptIncome = 0.0;
        $irs = $irsNormal;

        if ($irsJovem && $taxableRemuneration > 0) {
            $exemptIncome = min($taxableRemuneration * $jovemRate, $monthlyCap);
            $taxableShare = max(0, $taxableRemuneration - $exemptIncome);

            if ($irsNormal > 0) {
                $effective = $irsNormal / $taxableRemuneration;
                $irs = round($taxableShare * $effective, 2);
            } else {
                $irs = 0.0;
            }
        }

        $net = round($gross - $ss - $irs + $mealTotal, 2);

        return [
            'gross' => round($gross, 2),
            'taxable_remuneration' => $taxableRemuneration,
            'social_security' => $ss,
            'irs' => $irs,
            'irs_before_jovem' => $irsNormal,
            'meal_total' => $mealTotal,
            'meal_exempt' => $mealExempt,
            'meal_taxable' => $mealTaxable,
            'net' => max(0, $net),
            'table' => $withholding['table'],
            'marginal_rate' => $withholding['marginal_rate'],
            'effective_irs_rate' => $taxableRemuneration > 0 ? round($irs / $taxableRemuneration, 4) : 0.0,
            'irs_jovem_rate' => $jovemRate,
            'irs_jovem_exempt_income' => round($exemptIncome, 2),
        ];
    }

    public function irsJovemExemptionRate(int $year): float
    {
        return match (true) {
            $year <= 1 => 1.00,
            $year <= 4 => 0.75,
            $year <= 7 => 0.50,
            default => 0.25,
        };
    }

    /**
     * @return array{irs: float, table: string, marginal_rate: float}
     */
    public function withholding(float $remuneration, string $civilStatus, int $dependents, bool $isDisabled): array
    {
        $table = $this->resolveTable($civilStatus, $dependents, $isDisabled);
        $brackets = $this->tables()[$table];
        $bracket = $this->findBracket($brackets['rows'], $remuneration);

        $rate = $bracket['rate'];
        if ($dependents >= 3 && in_array($table, ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII'], true)) {
            $rate = max(0, $rate - 0.01);
        }

        $abater = $this->parcelaAAbater($bracket, $remuneration);
        $extra = ($dependents > 0) ? ($bracket['per_dependent'] * $dependents) : 0.0;

        $irs = round(($remuneration * $rate) - $abater - $extra, 2);

        return [
            'irs' => (float) max(0, $irs),
            'table' => $table,
            'marginal_rate' => round($rate, 4),
        ];
    }

    private function resolveTable(string $civilStatus, int $dependents, bool $isDisabled): string
    {
        $unicoTitular = $civilStatus === 'casado_1';
        $hasDependents = $dependents > 0;

        if ($isDisabled) {
            if ($unicoTitular) {
                return 'VII';
            }

            if ($hasDependents) {
                return $civilStatus === 'casado_2' ? 'VI' : 'V';
            }

            return 'IV';
        }

        if ($unicoTitular) {
            return 'III';
        }

        if ($hasDependents && $civilStatus !== 'casado_2') {
            return 'II';
        }

        return 'I';
    }

    /**
     * @param  list<array{max: float, rate: float, abater: float|null, formula: ?array{multiplier: float, ref: float}, per_dependent: float}>  $rows
     * @return array{max: float, rate: float, abater: float|null, formula: ?array{multiplier: float, ref: float}, per_dependent: float}
     */
    private function findBracket(array $rows, float $remuneration): array
    {
        foreach ($rows as $row) {
            if ($remuneration <= $row['max']) {
                return $row;
            }
        }

        return $rows[array_key_last($rows)];
    }

    /**
     * @param  array{abater: float|null, formula: ?array{multiplier: float, ref: float}}  $bracket
     */
    private function parcelaAAbater(array $bracket, float $remuneration): float
    {
        if ($bracket['formula'] !== null) {
            return $bracket['formula']['multiplier'] * ($bracket['formula']['ref'] - $remuneration);
        }

        return (float) $bracket['abater'];
    }

    /**
     * @return array<string, array{rows: list<array{max: float, rate: float, abater: float|null, formula: ?array{multiplier: float, ref: float}, per_dependent: float}>}>
     */
    private function tables(): array
    {
        $formula = fn (float $rate, float $factor, float $ref): array => [
            'multiplier' => $rate * $factor,
            'ref' => $ref,
        ];

        $row = fn (float $max, float $rate, ?float $abater, float $perDependent, ?array $formulaSpec = null): array => [
            'max' => $max,
            'rate' => $rate,
            'abater' => $abater,
            'formula' => $formulaSpec,
            'per_dependent' => $perDependent,
        ];

        $tableIRates = [
            $row(920.00, 0.0000, 0.00, 0.00),
            $row(1042.00, 0.1250, null, 21.43, $formula(0.1250, 2.60, 1273.85)),
            $row(1108.00, 0.1570, null, 21.43, $formula(0.1570, 1.35, 1554.83)),
            $row(1154.00, 0.1570, 94.71, 21.43),
            $row(1212.00, 0.2120, 158.18, 21.43),
            $row(1819.00, 0.2410, 193.33, 21.43),
            $row(2119.00, 0.3110, 320.66, 21.43),
            $row(2499.00, 0.3490, 401.19, 21.43),
            $row(3305.00, 0.3836, 487.66, 21.43),
            $row(5547.00, 0.3969, 531.62, 21.43),
            $row(20221.00, 0.4495, 823.40, 21.43),
            $row(PHP_FLOAT_MAX, 0.4717, 1272.31, 21.43),
        ];

        $tableII = array_map(function (array $r) {
            $r['per_dependent'] = $r['max'] <= 920 ? 0.00 : 34.29;

            return $r;
        }, $tableIRates);

        return [
            'I' => ['rows' => $tableIRates],
            'II' => ['rows' => $tableII],
            'III' => ['rows' => [
                $row(991.00, 0.0000, 0.00, 0.00),
                $row(1042.00, 0.1250, null, 42.86, $formula(0.1250, 2.6, 1372.15)),
                $row(1108.00, 0.1250, null, 42.86, $formula(0.1250, 1.35, 1677.85)),
                $row(1119.00, 0.1250, 96.17, 42.86),
                $row(1432.00, 0.1272, 98.64, 42.86),
                $row(1962.00, 0.1570, 141.32, 42.86),
                $row(2240.00, 0.1938, 213.53, 42.86),
                $row(2773.00, 0.2277, 289.47, 42.86),
                $row(3389.00, 0.2570, 370.72, 42.86),
                $row(5965.00, 0.2881, 476.12, 42.86),
                $row(20265.00, 0.3843, 1049.96, 42.86),
                $row(PHP_FLOAT_MAX, 0.4717, 2821.13, 42.86),
            ]],
            'IV' => ['rows' => [
                $row(1694.00, 0.0000, 0.00, 0.00),
                $row(2063.00, 0.2120, 359.13, 0.00),
                $row(2492.00, 0.3110, 563.37, 0.00),
                $row(4487.00, 0.3490, 658.07, 0.00),
                $row(4753.00, 0.3836, 813.33, 0.00),
                $row(6687.00, 0.3969, 876.55, 0.00),
                $row(20468.00, 0.4495, 1228.29, 0.00),
                $row(PHP_FLOAT_MAX, 0.4717, 1682.68, 0.00),
            ]],
            'V' => ['rows' => [
                $row(1938.00, 0.0000, 0.00, 0.00),
                $row(2063.00, 0.2132, 413.19, 42.86),
                $row(2854.00, 0.3110, 614.96, 42.86),
                $row(4504.00, 0.3490, 723.42, 42.86),
                $row(6826.00, 0.3836, 879.26, 42.86),
                $row(7048.00, 0.3969, 970.05, 42.86),
                $row(20468.00, 0.4495, 1340.78, 42.86),
                $row(PHP_FLOAT_MAX, 0.4717, 1795.17, 42.86),
            ]],
            'VI' => ['rows' => [
                $row(1668.00, 0.0000, 0.00, 0.00),
                $row(2068.00, 0.2049, 341.78, 21.43),
                $row(2497.00, 0.2410, 416.44, 21.43),
                $row(3107.00, 0.3110, 591.23, 21.43),
                $row(4504.00, 0.3490, 709.30, 21.43),
                $row(6826.00, 0.3836, 865.14, 21.43),
                $row(7048.00, 0.3969, 955.93, 21.43),
                $row(20468.00, 0.4495, 1326.66, 21.43),
                $row(PHP_FLOAT_MAX, 0.4717, 1781.05, 21.43),
            ]],
            'VII' => ['rows' => [
                $row(2325.00, 0.0000, 0.00, 0.00),
                $row(3494.00, 0.2277, 529.41, 42.86),
                $row(3761.00, 0.2570, 631.79, 42.86),
                $row(6687.00, 0.2881, 748.76, 42.86),
                $row(20468.00, 0.4244, 1660.20, 42.86),
                $row(PHP_FLOAT_MAX, 0.4717, 2628.34, 42.86),
            ]],
        ];
    }
}
