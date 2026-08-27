<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RetirementSimulator extends Component
{
    public int $currentAge      = 30;
    public int $retirementAge   = 65;
    public int $lifeExpectancy  = 85;
    public float $currentSavings    = 0;
    public float $monthlyContribution = 200;
    public float $annualReturn       = 6.0;
    public float $inflationRate      = 2.5;
    public float $targetMonthlyIncome = 1000;

    public function getResultsProperty(): array
    {
        $years      = max(0, $this->retirementAge - $this->currentAge);
        $postYears  = max(0, $this->lifeExpectancy - $this->retirementAge);
        $monthlyRate = $this->annualReturn / 100 / 12;

        // Future value of current savings
        $fvSavings = $this->currentSavings * pow(1 + $this->annualReturn / 100, $years);

        // Future value of monthly contributions (annuity)
        $fvContributions = $monthlyRate > 0
            ? $this->monthlyContribution * ((pow(1 + $monthlyRate, $years * 12) - 1) / $monthlyRate)
            : $this->monthlyContribution * $years * 12;

        $nominalCapital = $fvSavings + $fvContributions;

        // Real value adjusted for inflation
        $realCapital = $nominalCapital / pow(1 + $this->inflationRate / 100, $years);

        // Monthly income drawable (capital / months in retirement, simplified)
        $monthsInRetirement = $postYears * 12;
        $monthlyIncome = $monthsInRetirement > 0 ? $nominalCapital / $monthsInRetirement : 0;
        $realMonthlyIncome = $monthsInRetirement > 0 ? $realCapital / $monthsInRetirement : 0;

        // Required monthly contribution to reach target
        $targetCapital  = $this->targetMonthlyIncome * $monthsInRetirement;
        $remainingNeeded = max(0, $targetCapital - $fvSavings);
        $requiredMonthly = ($monthlyRate > 0 && $years * 12 > 0)
            ? $remainingNeeded / ((pow(1 + $monthlyRate, $years * 12) - 1) / $monthlyRate)
            : ($years * 12 > 0 ? $remainingNeeded / ($years * 12) : 0);

        // Is on track?
        $onTrack = $monthlyIncome >= $this->targetMonthlyIncome;

        // Gap per month
        $gap = $this->targetMonthlyIncome - $monthlyIncome;

        // Yearly chart data (every 5 years up to retirement)
        $chartLabels = [];
        $chartNominal = [];
        $chartReal = [];

        for ($y = 0; $y <= $years; $y++) {
            $fvS = $this->currentSavings * pow(1 + $this->annualReturn / 100, $y);
            $fvC = $monthlyRate > 0
                ? $this->monthlyContribution * ((pow(1 + $monthlyRate, $y * 12) - 1) / $monthlyRate)
                : $this->monthlyContribution * $y * 12;

            $chartLabels[]  = $this->currentAge + $y;
            $chartNominal[] = round($fvS + $fvC);
            $chartReal[]    = round(($fvS + $fvC) / pow(1 + $this->inflationRate / 100, max(1, $y)));
        }

        return [
            'years'               => $years,
            'postYears'           => $postYears,
            'nominalCapital'      => $nominalCapital,
            'realCapital'         => $realCapital,
            'monthlyIncome'       => $monthlyIncome,
            'realMonthlyIncome'   => $realMonthlyIncome,
            'targetCapital'       => $targetCapital,
            'requiredMonthly'     => $requiredMonthly,
            'onTrack'             => $onTrack,
            'gap'                 => $gap,
            'totalContributed'    => $this->monthlyContribution * $years * 12 + $this->currentSavings,
            'totalGrowth'         => $nominalCapital - ($this->monthlyContribution * $years * 12 + $this->currentSavings),
            'chartLabels'         => $chartLabels,
            'chartNominal'        => $chartNominal,
            'chartReal'           => $chartReal,
        ];
    }

    public function render()
    {
        return view('livewire.retirement-simulator', [
            'results' => $this->results,
        ]);
    }
}
