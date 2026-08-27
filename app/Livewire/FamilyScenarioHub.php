<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Income;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class FamilyScenarioHub extends Component
{
    public float $currentSalary = 0;

    public float $currentRent = 0;

    public float $otherExpenses = 0;

    public float $salaryChangePct = 0;

    public float $inflationRate = 3.0;

    public int $newChildren = 0;

    public float $costPerChild = 250;

    public float $loanAmount = 0;

    public float $loanAnnualRate = 6.5;

    public int $loanMonths = 60;

    public function mount(): void
    {
        $workspaceId = auth()->user()->current_workspace_id;

        $this->currentSalary = (float) Income::where('workspace_id', $workspaceId)
            ->where('received_at', '>=', now()->subMonths(3))
            ->avg('amount');

        $recentExpenses = Expense::where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', now()->subMonths(3))
            ->get();

        $this->currentRent = (float) $recentExpenses
            ->filter(fn ($e) => str_contains(mb_strtolower((string) $e->description), 'renda'))
            ->avg('amount');

        $this->otherExpenses = max(0, (float) $recentExpenses->avg('amount') - $this->currentRent);
    }

    public function getSimulationProperty(): array
    {
        $salaryBase = max(0, $this->currentSalary);
        $rentBase = max(0, $this->currentRent);
        $otherBase = max(0, $this->otherExpenses);

        $salaryProjected = $salaryBase * (1 + ($this->salaryChangePct / 100));
        $inflationFactor = 1 + ($this->inflationRate / 100);

        $rentProjected = $rentBase * $inflationFactor;
        $otherProjected = $otherBase * $inflationFactor;
        $childrenCost = max(0, $this->newChildren) * max(0, $this->costPerChild);
        $loanPayment = $this->estimateLoanPayment();

        $baselineExpense = $rentBase + $otherBase;
        $baselineNet = $salaryBase - $baselineExpense;

        $scenarioExpense = $rentProjected + $otherProjected + $childrenCost + $loanPayment;
        $scenarioNet = $salaryProjected - $scenarioExpense;

        $baselineRate = $salaryBase > 0 ? ($baselineNet / $salaryBase) * 100 : 0;
        $scenarioRate = $salaryProjected > 0 ? ($scenarioNet / $salaryProjected) * 100 : 0;

        $deltaNet = $scenarioNet - $baselineNet;

        $series = [];
        $reserve = 0.0;

        for ($m = 1; $m <= 12; $m++) {
            $reserve += $scenarioNet;
            $series[] = [
                'month' => now()->addMonths($m - 1)->translatedFormat('M'),
                'net' => $scenarioNet,
                'reserve' => $reserve,
            ];
        }

        return [
            'salary_base' => $salaryBase,
            'expense_base' => $baselineExpense,
            'net_base' => $baselineNet,
            'rate_base' => $baselineRate,
            'salary_scenario' => $salaryProjected,
            'expense_scenario' => $scenarioExpense,
            'net_scenario' => $scenarioNet,
            'rate_scenario' => $scenarioRate,
            'delta_net' => $deltaNet,
            'children_cost' => $childrenCost,
            'loan_payment' => $loanPayment,
            'series' => $series,
        ];
    }

    private function estimateLoanPayment(): float
    {
        if ($this->loanAmount <= 0 || $this->loanMonths <= 0) {
            return 0.0;
        }

        $monthlyRate = ($this->loanAnnualRate / 100) / 12;
        if ($monthlyRate <= 0) {
            return round($this->loanAmount / $this->loanMonths, 2);
        }

        $factor = pow(1 + $monthlyRate, $this->loanMonths);
        $payment = $this->loanAmount * (($monthlyRate * $factor) / ($factor - 1));

        return round($payment, 2);
    }

    public function render()
    {
        return view('livewire.family-scenario-hub', [
            'result' => $this->simulation,
            'workspaceCurrency' => strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR')),
        ]);
    }
}
