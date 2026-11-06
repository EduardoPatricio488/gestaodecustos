<?php

namespace App\Console\Commands;

use App\Models\RecurringIncome;
use App\Services\PortugueseNetPayCalculator;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyAnnualIncomeRaises extends Command
{
    protected $signature = 'incomes:apply-annual-raises';

    protected $description = 'Aplica os aumentos anuais automáticos configurados nos rendimentos fixos';

    public function handle(PortugueseNetPayCalculator $calculator): int
    {
        $applied = 0;

        RecurringIncome::query()
            ->where('is_active', true)
            ->whereNotNull('metadata')
            ->chunkById(100, function ($incomes) use ($calculator, &$applied): void {
                foreach ($incomes as $income) {
                    $raise = $income->metadata['annual_raise'] ?? null;
                    if (! is_array($raise)
                        || empty($raise['enabled'])
                        || empty($raise['next_date'])
                        || Carbon::parse($raise['next_date'])->isFuture()) {
                        continue;
                    }

                    DB::transaction(function () use ($income, $raise, $calculator, &$applied): void {
                        $lockedIncome = RecurringIncome::whereKey($income->id)->lockForUpdate()->first();
                        $metadata = $lockedIncome?->metadata ?? [];
                        $currentRaise = $metadata['annual_raise'] ?? null;

                        if (! $lockedIncome || empty($currentRaise['enabled'])
                            || ($currentRaise['next_date'] ?? null) !== $raise['next_date']) {
                            return;
                        }

                        $value = (float) $currentRaise['value'];
                        $isPercentage = ($currentRaise['type'] ?? null) === 'percentage';

                        if ($lockedIncome->source === 'emprego' && ($metadata['income_type'] ?? null) !== 'meal_card_allowance') {
                            $this->applyEmploymentRaise($lockedIncome, $metadata, $calculator, $value, $isPercentage);
                        } else {
                            $currentAmount = (float) $lockedIncome->amount;
                            $lockedIncome->amount = round($currentAmount + ($isPercentage ? $currentAmount * $value / 100 : $value), 2);
                        }

                        $currentRaise['next_date'] = Carbon::parse($currentRaise['next_date'])->addYear()->toDateString();
                        $metadata['annual_raise'] = $currentRaise;
                        $lockedIncome->metadata = $metadata;
                        $lockedIncome->save();
                        $applied++;
                    });
                }
            });

        $this->info("Aumentos anuais aplicados: {$applied}.");

        return self::SUCCESS;
    }

    private function applyEmploymentRaise(
        RecurringIncome $income,
        array &$metadata,
        PortugueseNetPayCalculator $calculator,
        float $value,
        bool $isPercentage,
    ): void {
        $currentGross = (float) ($metadata['salary_gross'] ?? 0);
        $metadata['salary_gross'] = round($currentGross + ($isPercentage ? $currentGross * $value / 100 : $value), 2);

        $holidayGross = ($metadata['holiday_allowance_same_as_salary'] ?? true)
            ? $metadata['salary_gross']
            : (float) ($metadata['holiday_allowance_gross'] ?? 0);
        $christmasGross = ($metadata['christmas_allowance_same_as_salary'] ?? true)
            ? $metadata['salary_gross']
            : (float) ($metadata['christmas_allowance_gross'] ?? 0);
        $duodecimosGross = ($metadata['allowances_in_duodecimos'] ?? false)
            ? ($holidayGross + $christmasGross) / 12
            : 0;

        $mealDestination = $metadata['meal_allowance_destination'] ?? 'same';
        $mealPayment = $mealDestination === 'meal_card' ? 'cartao' : 'numerario';
        $result = $calculator->calculate(
            gross: (float) $metadata['salary_gross'] + $duodecimosGross,
            mealPerDay: (float) ($metadata['meal_allowance'] ?? 0),
            workingDays: (int) ($metadata['working_days'] ?? 22),
            mealPayment: $mealPayment,
            irsJovem: (bool) ($metadata['irs_jovem'] ?? false),
            irsJovemYear: (int) ($metadata['irs_jovem_year'] ?? 1),
        );

        $metadata['calculated_ss'] = $result['social_security'];
        $metadata['calculated_irs'] = $result['irs'];
        $metadata['calculated_sa'] = $result['meal_total'];
        $income->amount = max(0, $result['net'] - (! empty($metadata['meal_card_income_id']) ? $result['meal_total'] : 0));

        if (! empty($metadata['meal_card_income_id'])) {
            RecurringIncome::where('workspace_id', $income->workspace_id)
                ->whereKey($metadata['meal_card_income_id'])
                ->update(['amount' => $result['meal_total']]);
        }
    }
}
