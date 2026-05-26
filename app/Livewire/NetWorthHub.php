<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Investment;
use App\Models\Goal;
use App\Models\Expense;
use App\Models\Income;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class NetWorthHub extends Component
{
    public function render()
    {
        $user = auth()->user();

        // 1. ATIVOS
        $investmentsValue = (float) $user->investments->sum(fn($i) => $i->quantity * $i->current_price);
        $goalsSaved = (float) $user->goals->sum('current_amount');

        $totalEarned = (float) $user->incomes->sum('amount') + (float) $user->recurringIncomes->sum('amount');
        $totalSpent = (float) $user->expenses->sum('amount');
        $cashOnHand = max(0, $totalEarned - $totalSpent);

        // 2. PASSIVOS (Empréstimos totais registados)
        $liabilities = (float) $user->expenses()->whereHas('category', fn($q) => $q->where('name', 'like', '%Emprestimo%'))->sum('amount');

        $totalAssets = $investmentsValue + $goalsSaved + $cashOnHand;
        $netWorth = $totalAssets - $liabilities;

        // 3. ANÁLISE DE RÁCIOS (O que a IA vai usar)
        $liquidityRatio = $totalAssets > 0 ? ($cashOnHand / $totalAssets) * 100 : 0;
        $investmentExposure = $totalAssets > 0 ? ($investmentsValue / $totalAssets) * 100 : 0;
        $savingsHealth = $totalAssets > 0 ? ($goalsSaved / $totalAssets) * 100 : 0;

        return view('livewire.net-worth-hub', [
            'investmentsValue' => $investmentsValue,
            'goalsSaved' => $goalsSaved,
            'cashOnHand' => $cashOnHand,
            'liabilities' => $liabilities,
            'totalAssets' => $totalAssets,
            'netWorth' => $netWorth,
            'liquidityRatio' => $liquidityRatio,
            'investmentExposure' => $investmentExposure,
            'savingsHealth' => $savingsHealth,
        ]);
    }
}
