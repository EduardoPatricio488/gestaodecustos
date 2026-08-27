<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringIncome;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class InflationHub extends Component
{
    public float $inflationRate = 2.5;
    public float $salaryGrowthRate = 1.5;
    public int $horizonYears = 10;

    public float $monthlySalary = 0;
    public float $monthlyExpenses = 0;
    public float $monthlySavings = 0;
    public float $cashReserve = 0;

    public function mount(): void
    {
        $workspaceId = auth()->user()->current_workspace_id;

        $monthlyIncomeAvg = (float) Income::where('workspace_id', $workspaceId)
            ->where('received_at', '>=', now()->subMonths(3))
            ->avg('amount');

        $monthlyExpensesAvg = (float) Expense::where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', now()->subMonths(3))
            ->avg('amount');

        $recurring = RecurringIncome::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get();

        $recurringMonthly = (float) $recurring->sum(function ($r) {
            return match ($r->frequency) {
                'semanal' => $r->amount * 52 / 12,
                'anual' => $r->amount / 12,
                default => $r->amount,
            };
        });

        $this->monthlySalary = $recurringMonthly > 0 ? round($recurringMonthly, 2) : round($monthlyIncomeAvg, 2);
        $this->monthlyExpenses = round($monthlyExpensesAvg, 2);
        $this->monthlySavings = max(0, round($this->monthlySalary - $this->monthlyExpenses, 2));
        $this->cashReserve = (float) BankAccount::where('workspace_id', $workspaceId)->sum('balance');
    }

    public function getDataProperty(): array
    {
        $years = max(1, $this->horizonYears);
        $inf = max(0, $this->inflationRate) / 100;
        $salaryGrowth = $this->salaryGrowthRate / 100;

        $salaryToday = max(0, $this->monthlySalary);
        $expensesToday = max(0, $this->monthlyExpenses);
        $savingsMonthly = max(0, $this->monthlySavings);
        $cashReserve = max(0, $this->cashReserve);

        $futureSalaryNominal = $salaryToday * pow(1 + $salaryGrowth, $years);
        $requiredSalaryNominal = $salaryToday * pow(1 + $inf, $years);
        $salaryGap = $futureSalaryNominal - $requiredSalaryNominal;

        $futureSalaryReal = $futureSalaryNominal / pow(1 + $inf, $years);
        $purchasingPowerLossPct = $salaryToday > 0
            ? (($salaryToday - $futureSalaryReal) / $salaryToday) * 100
            : 0;

        $futureMonthlyExpensesNominal = $expensesToday * pow(1 + $inf, $years);

        // Poupança mensal em caixa (sem retorno) perde valor real com inflação.
        $nominalSaved = $savingsMonthly * 12 * $years;
        $realSaved = $nominalSaved / pow(1 + $inf, $years / 2);

        $cashReserveReal = $cashReserve / pow(1 + $inf, $years);
        $cashReserveLoss = $cashReserve - $cashReserveReal;

        $series = [];
        for ($y = 0; $y <= $years; $y++) {
            $salaryN = $salaryToday * pow(1 + $salaryGrowth, $y);
            $salaryR = $salaryN / pow(1 + $inf, $y);
            $expenseN = $expensesToday * pow(1 + $inf, $y);

            $series[] = [
                'year' => now()->year + $y,
                'salary_nominal' => $salaryN,
                'salary_real' => $salaryR,
                'expense_nominal' => $expenseN,
            ];
        }

        return [
            'futureSalaryNominal' => $futureSalaryNominal,
            'requiredSalaryNominal' => $requiredSalaryNominal,
            'salaryGap' => $salaryGap,
            'futureSalaryReal' => $futureSalaryReal,
            'purchasingPowerLossPct' => $purchasingPowerLossPct,
            'futureMonthlyExpensesNominal' => $futureMonthlyExpensesNominal,
            'nominalSaved' => $nominalSaved,
            'realSaved' => $realSaved,
            'cashReserveReal' => $cashReserveReal,
            'cashReserveLoss' => $cashReserveLoss,
            'series' => $series,
        ];
    }

    public function render()
    {
        return view('livewire.inflation-hub', [
            'data' => $this->data,
        ]);
    }
}
