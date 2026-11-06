<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\SalaryPayment;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class SalaryPaymentAutomationService
{
    public function __construct(private PortugueseNetPayCalculator $calculator)
    {
    }

    public function processDuePayments(?CarbonInterface $date = null): int
    {
        $date = $date?->copy() ?? now();
        $created = 0;

        RecurringIncome::withoutGlobalScopes()
            ->where('source', 'emprego')
            ->where('is_active', true)
            ->orderBy('id')
            ->chunkById(100, function ($salaries) use ($date, &$created): void {
                foreach ($salaries as $salary) {
                    if ($this->ensureMonthlyPayment($salary->id, $date)) {
                        $created++;
                    }
                }
            });

        return $created;
    }

    public function processSalaryPayment(RecurringIncome $salary, ?CarbonInterface $date = null): bool
    {
        return $this->ensureMonthlyPayment($salary->id, $date?->copy() ?? now());
    }

    private function ensureMonthlyPayment(int $salaryId, CarbonInterface $date): bool
    {
        return DB::transaction(function () use ($salaryId, $date): bool {
            $salary = RecurringIncome::withoutGlobalScopes()
                ->whereKey($salaryId)
                ->lockForUpdate()
                ->first();

            if (! $salary) {
                return false;
            }

            $paymentDate = $date->copy()
                ->startOfMonth()
                ->day(min((int) $salary->day_of_month, $date->daysInMonth));
            $due = $date->greaterThanOrEqualTo($paymentDate);
            $payment = SalaryPayment::query()
                ->where('recurring_income_id', $salary->id)
                ->where('payment_year', $date->year)
                ->where('payment_month', $date->month)
                ->lockForUpdate()
                ->first();

            if ($payment?->status === 'paid') {
                return false;
            }

            if ($payment && ! $due) {
                return false;
            }

            if (! $payment && ! $this->isEligible($salary, $paymentDate)) {
                return false;
            }

            $metadata = $salary->metadata ?? [];
            $serviceMonth = $date->copy()->subMonthNoOverflow();
            $salaryServiceAdditions = (float) Income::query()
                ->where('workspace_id', $salary->workspace_id)
                ->where('user_id', $salary->user_id)
                ->where('salary_recurring_income_id', $salary->id)
                ->where('salary_income_type', 'service')
                ->where('added_to_salary', true)
                ->whereMonth('received_at', $serviceMonth->month)
                ->whereYear('received_at', $serviceMonth->year)
                ->sum(DB::raw('COALESCE(amount_converted, amount)'));

            if ($payment) {
                $previousAccountId = $payment->bank_account_id;
                $previousBalanceAmount = (float) $payment->bank_balance_amount;
                $baseComplements = (float) ($metadata['other_complements'] ?? 0)
                    + (float) ($metadata['productivity_bonus'] ?? 0)
                    + (float) ($metadata['overtime'] ?? 0)
                    + (float) ($metadata['other_income'] ?? 0);
                $previousServiceAdditions = max(0, (float) $payment->other_complements - $baseComplements);
                $mealCard = ($metadata['meal_allowance_destination'] ?? null) === 'meal_card';
                $mealAllowance = (float) ($metadata['calculated_sa'] ?? $payment->meal_allowance);
                $payment->status = 'paid';
                $payment->is_automatic = true;
                $payment->paid_at = $paymentDate->toDateString();
                $payment->bank_account_id = $salary->bank_account_id;
                $payment->other_complements = round($baseComplements + $salaryServiceAdditions, 2);
                $payment->net_received = round((float) $payment->net_received + $salaryServiceAdditions - $previousServiceAdditions, 2);
                $payment->save();

                $targetBalanceAmount = $payment->bank_account_id
                    ? round(max(0, (float) $payment->net_received - ($mealCard ? (float) $payment->meal_allowance : 0)), 2)
                    : 0;

                if ($previousAccountId === $payment->bank_account_id && $payment->bank_account_id) {
                    $balanceDelta = round($targetBalanceAmount - $previousBalanceAmount, 2);
                    if ($balanceDelta > 0) {
                        BankAccount::withoutGlobalScopes()
                            ->where('workspace_id', $salary->workspace_id)
                            ->whereKey($payment->bank_account_id)
                            ->increment('balance', $balanceDelta);
                    } elseif ($balanceDelta < 0) {
                        BankAccount::withoutGlobalScopes()
                            ->where('workspace_id', $salary->workspace_id)
                            ->whereKey($payment->bank_account_id)
                            ->decrement('balance', abs($balanceDelta));
                    }
                } else {
                    if ($previousAccountId && $previousBalanceAmount > 0) {
                        BankAccount::withoutGlobalScopes()
                            ->where('workspace_id', $salary->workspace_id)
                            ->whereKey($previousAccountId)
                            ->decrement('balance', $previousBalanceAmount);
                    }
                    if ($payment->bank_account_id && $targetBalanceAmount > 0) {
                        BankAccount::withoutGlobalScopes()
                            ->where('workspace_id', $salary->workspace_id)
                            ->whereKey($payment->bank_account_id)
                            ->increment('balance', $targetBalanceAmount);
                    }
                }

                $payment->bank_balance_amount = $targetBalanceAmount;
                $payment->save();

                return true;
            }

            $duodecimos = (float) ($metadata['monthly_duodecimos'] ?? 0);
            $otherComplements = (float) ($metadata['other_complements'] ?? 0)
                + (float) ($metadata['productivity_bonus'] ?? 0)
                + (float) ($metadata['overtime'] ?? 0)
                + (float) ($metadata['other_income'] ?? 0)
                + $salaryServiceAdditions;
            $payroll = $this->calculator->calculate(
                gross: (float) ($metadata['salary_gross'] ?? 0) + $duodecimos + $otherComplements,
                civilStatus: $metadata['civil_status'] ?? 'solteiro',
                dependents: (int) ($metadata['dependents'] ?? 0),
                mealPerDay: (float) ($metadata['meal_allowance'] ?? 0),
                workingDays: (int) ($metadata['working_days'] ?? 0),
                mealPayment: ($metadata['meal_allowance_destination'] ?? null) === 'meal_card' ? 'cartao' : 'numerario',
                irsJovem: (bool) ($metadata['irs_jovem'] ?? false),
                irsJovemYear: (int) ($metadata['irs_jovem_year'] ?? 1),
            );
            $mealAllowance = (float) ($metadata['calculated_sa'] ?? $payroll['meal_total']);
            $irs = (float) ($metadata['calculated_irs'] ?? $payroll['irs']);
            if (($metadata['withholding_status'] ?? 'normal') === 'exempt') {
                $irs = 0;
            }
            $mealCard = ($metadata['meal_allowance_destination'] ?? null) === 'meal_card';
            $depositAmount = round((float) $salary->amount, 2);
            $attributes = [
                'recurring_income_id' => $salary->id,
                'user_id' => $salary->user_id,
                'workspace_id' => $salary->workspace_id,
                'bank_account_id' => $salary->bank_account_id,
                'payment_month' => $date->month,
                'payment_year' => $date->year,
                'salary_base_gross' => (float) ($metadata['salary_gross'] ?? 0),
                'allowances_duodecimos' => $duodecimos,
                'other_complements' => $otherComplements,
                'social_security' => (float) ($metadata['calculated_ss'] ?? $payroll['social_security']),
                'irs' => (float) $irs,
                'other_deductions' => 0,
                'meal_allowance' => $mealAllowance,
                'net_received' => round($depositAmount + $salaryServiceAdditions + ($mealCard ? $mealAllowance : 0), 2),
                'paid_at' => $due ? $paymentDate->toDateString() : null,
                'status' => $due ? 'paid' : 'pending',
                'notes' => $payment?->notes ?: 'Pagamento criado automaticamente pelo sistema.',
                'bank_balance_amount' => 0,
                'is_automatic' => true,
            ];
            $payment = new SalaryPayment($attributes);
            $payment->save();

            $balanceAmount = $due && $payment->bank_account_id
                ? ($payment->net_received - ($mealCard ? $mealAllowance : 0))
                : 0;
            if ($payment->bank_account_id && $balanceAmount > 0) {
                BankAccount::withoutGlobalScopes()
                    ->where('workspace_id', $salary->workspace_id)
                    ->whereKey($payment->bank_account_id)
                    ->increment('balance', $balanceAmount - (float) $payment->bank_balance_amount);
            }
            $payment->bank_balance_amount = round(max(0, $balanceAmount), 2);
            $payment->save();

            return true;
        });
    }

    private function isEligible(RecurringIncome $salary, CarbonInterface $date): bool
    {
        $metadata = $salary->metadata ?? [];
        if (($metadata['income_type'] ?? null) === 'meal_card_allowance'
            || ($metadata['contract_status'] ?? 'active') !== 'active') {
            return false;
        }

        if (! empty($metadata['start_date']) && $date->toDateString() < $metadata['start_date']) {
            return false;
        }

        return empty($metadata['end_date']) || $date->toDateString() <= $metadata['end_date'];
    }
}
