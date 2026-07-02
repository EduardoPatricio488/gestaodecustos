<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Investment;
use App\Models\Goal;
use App\Models\Expense;
use App\Models\Income;
use App\Models\BankAccount;
use App\Models\Debt;
use App\Models\Subscription;
use App\Models\InvestmentIncome;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class NetWorthHub extends Component
{
    public bool $aiLoading = false;
    public string $aiInsight = '';

    public function getAiInsight(): void
    {
        $this->aiLoading = true;
        // AI insight is generated client-side via the Anthropic API in the blade view
        $this->aiLoading = false;
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        // ─── 1. INVESTIMENTOS ─────────────────────────────────────────────────
        $investments = Investment::where('workspace_id', $workspaceId)->get();
        $investmentsValue = (float) $investments->sum(fn($i) => $i->quantity * $i->current_price);

        // Breakdown por tipo
        $investmentsByType = $investments->groupBy('type')->map(fn($group) => [
            'count'  => $group->count(),
            'value'  => $group->sum(fn($i) => $i->quantity * $i->current_price),
            'cost'   => $group->sum(fn($i) => $i->quantity * $i->average_price),
        ])->sortByDesc('value');

        // Ganho/perda não realizado
        $investmentCost    = (float) $investments->sum(fn($i) => $i->quantity * $i->average_price);
        $unrealizedGain    = $investmentsValue - $investmentCost;
        $unrealizedGainPct = $investmentCost > 0 ? (($unrealizedGain / $investmentCost) * 100) : 0;

        // Top 5 investimentos por valor
        $topInvestments = $investments
            ->map(fn($i) => array_merge($i->toArray(), ['current_value' => $i->quantity * $i->current_price]))
            ->sortByDesc('current_value')
            ->take(5)
            ->values();

        // Rendimentos de investimentos (últimos 12 meses)
        $investmentIncomes = InvestmentIncome::where('workspace_id', $workspaceId)
            ->where('reference_date', '>=', now()->subMonths(12))
            ->selectRaw('strftime("%Y-%m", reference_date) as month, SUM(net_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $totalInvestmentIncome = InvestmentIncome::where('workspace_id', $workspaceId)->sum('net_amount');

        // ─── 2. CONTAS BANCÁRIAS ──────────────────────────────────────────────
        $bankAccounts    = BankAccount::where('workspace_id', $workspaceId)->get();
        $totalBankBalance = (float) $bankAccounts->sum('balance');

        // ─── 3. METAS DE POUPANÇA ─────────────────────────────────────────────
        $goals     = Goal::where('workspace_id', $workspaceId)->get();
        $goalsSaved = (float) $goals->sum('current_amount');
        $goalsTarget = (float) $goals->sum('target_amount');
        $goalsProgress = $goalsTarget > 0 ? ($goalsSaved / $goalsTarget) * 100 : 0;

        // ─── 4. FLUXO DE CAIXA ────────────────────────────────────────────────
        // Rendimentos mensais (últimos 12 meses)
        $monthlyIncomes = Income::where('workspace_id', $workspaceId)
            ->where('received_at', '>=', now()->subMonths(12))
            ->selectRaw('strftime("%Y-%m", received_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Despesas mensais (últimos 12 meses)
        $monthlyExpenses = Expense::where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', now()->subMonths(12))
            ->selectRaw('strftime("%Y-%m", spent_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Gerar array dos últimos 12 meses
        $last12Months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $last12Months->put($m, [
                'month'    => $m,
                'label'    => now()->subMonths($i)->format('M'),
                'income'   => (float) ($monthlyIncomes[$m] ?? 0),
                'expense'  => (float) ($monthlyExpenses[$m] ?? 0),
                'net'      => (float) ($monthlyIncomes[$m] ?? 0) - (float) ($monthlyExpenses[$m] ?? 0),
            ]);
        }

        $totalIncome  = (float) Income::where('workspace_id', $workspaceId)->sum('amount');
        $totalExpense = (float) Expense::where('workspace_id', $workspaceId)->sum('amount');
        $cashFlow     = $totalIncome - $totalExpense;
        $cashOnHand   = max(0, $cashFlow);

        // Média mensal dos últimos 3 meses
        $avg3Income  = $last12Months->slice(-3)->avg('income');
        $avg3Expense = $last12Months->slice(-3)->avg('expense');
        $avgSavingsRate = $avg3Income > 0 ? (($avg3Income - $avg3Expense) / $avg3Income) * 100 : 0;

        // ─── 5. PASSIVOS / DÍVIDAS ────────────────────────────────────────────
        $debts = Debt::where('workspace_id', $workspaceId)->where('is_paid', false)->get();
        $liabilities = (float) $debts->sum('amount');

        $debtsByType = $debts->groupBy('type')->map(fn($g) => [
            'count'  => $g->count(),
            'amount' => $g->sum('amount'),
        ]);

        // Dívidas a vencer nos próximos 30 dias
        $upcomingDebts = $debts->filter(fn($d) => $d->due_at && \Carbon\Carbon::parse($d->due_at)->isBetween(now(), now()->addDays(30)));

        // ─── 6. SUBSCRIÇÕES ATIVAS ───────────────────────────────────────────
        $activeSubscriptions = Subscription::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get();
        $monthlySubscriptionCost = (float) $activeSubscriptions
            ->where('cycle', 'monthly')->sum('amount');
        $yearlySubscriptionCost  = (float) $activeSubscriptions
            ->where('cycle', 'yearly')->sum('amount');
        $totalAnnualSubscriptions = $monthlySubscriptionCost * 12 + $yearlySubscriptionCost;

        // ─── 7. TOTAIS E RÁCIOS ───────────────────────────────────────────────
        $totalAssets = $investmentsValue + $goalsSaved + $totalBankBalance;
        $netWorth    = $totalAssets - $liabilities;

        $liquidityRatio     = $totalAssets > 0 ? ($totalBankBalance / $totalAssets) * 100 : 0;
        $investmentExposure = $totalAssets > 0 ? ($investmentsValue / $totalAssets) * 100 : 0;
        $savingsHealth      = $totalAssets > 0 ? ($goalsSaved / $totalAssets) * 100 : 0;
        $debtToAssetRatio   = $totalAssets > 0 ? ($liabilities / $totalAssets) * 100 : 0;
        $solvencyRatio      = $liabilities > 0 ? ($totalAssets / $liabilities) : 999;

        // Score de saúde financeira (0–100)
        $healthScore = min(100, max(0,
            ($avgSavingsRate * 0.3)
            + (min(40, $investmentExposure) * 0.5)
            + (max(0, 30 - $debtToAssetRatio) * 0.6667)
            + (min(100, $goalsProgress) * 0.1)
        ));

        return view('livewire.net-worth-hub', [
            // Totais
            'totalAssets'          => $totalAssets,
            'liabilities'          => $liabilities,
            'netWorth'             => $netWorth,

            // Rácios
            'liquidityRatio'       => $liquidityRatio,
            'investmentExposure'   => $investmentExposure,
            'savingsHealth'        => $savingsHealth,
            'debtToAssetRatio'     => $debtToAssetRatio,
            'solvencyRatio'        => $solvencyRatio,
            'avgSavingsRate'       => $avgSavingsRate,
            'healthScore'          => $healthScore,

            // Investimentos
            'investmentsValue'     => $investmentsValue,
            'investmentsByType'    => $investmentsByType,
            'topInvestments'       => $topInvestments,
            'unrealizedGain'       => $unrealizedGain,
            'unrealizedGainPct'    => $unrealizedGainPct,
            'totalInvestmentIncome'=> $totalInvestmentIncome,
            'investmentIncomes'    => $investmentIncomes,

            // Contas
            'bankAccounts'         => $bankAccounts,
            'totalBankBalance'     => $totalBankBalance,

            // Metas
            'goals'                => $goals,
            'goalsSaved'           => $goalsSaved,
            'goalsTarget'          => $goalsTarget,
            'goalsProgress'        => $goalsProgress,

            // Fluxo
            'last12Months'         => $last12Months,
            'totalIncome'          => $totalIncome,
            'totalExpense'         => $totalExpense,
            'cashOnHand'           => $cashOnHand,
            'avg3Income'           => $avg3Income,
            'avg3Expense'          => $avg3Expense,

            // Dívidas
            'debts'                => $debts,
            'debtsByType'          => $debtsByType,
            'upcomingDebts'        => $upcomingDebts,

            // Subscrições
            'activeSubscriptions'  => $activeSubscriptions,
            'monthlySubscriptionCost' => $monthlySubscriptionCost,
            'totalAnnualSubscriptions'=> $totalAnnualSubscriptions,
        ]);
    }
}
