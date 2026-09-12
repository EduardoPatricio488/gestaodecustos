<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\BankCredit;
use App\Models\BankPatrimony;
use App\Models\BankReserve;
use App\Models\BankTransfer;
use App\Models\BankTransitItem;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\RecurringIncome;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BancoService
{
    private int $workspaceId;

    private ?Collection $accountsCache = null;

    private ?Collection $reservesCache = null;

    private ?Collection $investmentsCache = null;

    private ?Collection $patrimonyCache = null;

    public function __construct(int $workspaceId)
    {
        $this->workspaceId = $workspaceId;
    }

    public function getSummary(): array
    {
        $accounts = $this->getAccounts();
        $reserves = $this->getReserves();
        $transitItems = $this->getTransitItems();
        $investments = $this->getInvestments();
        $patrimony = $this->getPatrimony();

        $totalBankBalance = $accounts->where('status', '!=', 'archived')->sum(fn ($a) => $a->current_balance);
        $totalReserves = $reserves->where('status', 'active')->sum('amount');
        $totalTransitIn = $transitItems->where('direction', 'in')->where('status', 'pending')->sum('amount');
        $totalTransitOut = $transitItems->where('direction', 'out')->where('status', 'pending')->sum('amount');
        $totalInvestments = $investments->sum(fn ($i) => $i->quantity * $i->current_price);

        $totalRealEstate = $patrimony->where('type', 'real_estate')->sum('value');
        $totalVehicles = $patrimony->where('type', 'vehicle')->sum('value');
        $totalGold = $patrimony->where('type', 'gold')->sum('value');
        $totalCrypto = $patrimony->where('type', 'crypto')->sum('value');
        $totalOtherAssets = $patrimony->where('type', 'other_asset')->sum('value');
        $totalLiabilities = $patrimony->where('type', 'liability')->sum('value');

        $pendingExpenses = Expense::where('workspace_id', $this->workspaceId)
            ->where('status', 'pending')
            ->sum('amount');

        $taxReserves = $reserves
            ->filter(fn ($r) => str_contains(strtolower($r->name), 'irs') ||
                               str_contains(strtolower($r->name), 'imposto') ||
                               str_contains(strtolower($r->name), 'iva') ||
                               str_contains(strtolower($r->name), 'tax'))
            ->sum('amount');

        $availableCash = $totalBankBalance - $totalReserves - $pendingExpenses;
        $totalPatrimony = $totalBankBalance + $totalInvestments + $totalRealEstate
                        + $totalVehicles + $totalGold + $totalCrypto + $totalOtherAssets
                        - $totalLiabilities;

        $totalDebts = Debt::where('workspace_id', $this->workspaceId)
            ->where('type', 'owe')
            ->where('is_paid', false)
            ->sum('amount');

        $netWorth = $totalPatrimony - $totalDebts;
        $prevMonthIncome = $this->getPrevMonthIncome();
        $prevMonthExpense = $this->getPrevMonthExpense();
        $currMonthIncome = $this->getCurrentMonthIncome();
        $currMonthExpense = $this->getCurrentMonthExpense();

        return [
            'total_bank_balance' => $totalBankBalance,
            'total_reserves' => $totalReserves,
            'total_transit_in' => $totalTransitIn,
            'total_transit_out' => $totalTransitOut,
            'available_cash' => $availableCash,
            'total_investments' => $totalInvestments,
            'total_real_estate' => $totalRealEstate,
            'total_vehicles' => $totalVehicles,
            'total_gold' => $totalGold,
            'total_crypto' => $totalCrypto,
            'total_other_assets' => $totalOtherAssets,
            'total_liabilities' => $totalLiabilities,
            'total_debts' => $totalDebts,
            'total_patrimony' => $totalPatrimony,
            'net_worth' => $netWorth,
            'tax_reserves' => $taxReserves,
            'pending_expenses' => $pendingExpenses,
            'curr_month_income' => $currMonthIncome,
            'curr_month_expense' => $currMonthExpense,
            'curr_month_balance' => $currMonthIncome - $currMonthExpense,
            'prev_month_income' => $prevMonthIncome,
            'prev_month_expense' => $prevMonthExpense,
            'income_change_pct' => $prevMonthIncome > 0
                ? (($currMonthIncome - $prevMonthIncome) / $prevMonthIncome) * 100
                : 0,
            'expense_change_pct' => $prevMonthExpense > 0
                ? (($currMonthExpense - $prevMonthExpense) / $prevMonthExpense) * 100
                : 0,
            'is_available_negative' => $availableCash < 0,
        ];
    }

    public function getAccounts(): Collection
    {
        if ($this->accountsCache !== null) {
            return $this->accountsCache;
        }

        return $this->accountsCache = BankAccount::where('workspace_id', $this->workspaceId)
            ->withSum('incomes as current_balance_income_total', 'amount')
            ->withSum('expenses as current_balance_expense_total', 'amount')
            ->withSum([
                'recurringIncomes as current_balance_recurring_due' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('day_of_month', '<=', now()->day),
            ], 'amount')
            ->orderBy('is_business')
            ->orderBy('name')
            ->get();
    }

    public function getPersonalAccounts(): Collection
    {
        return $this->getAccounts()->where('is_business', false);
    }

    public function getBusinessAccounts(): Collection
    {
        return $this->getAccounts()->where('is_business', true);
    }

    public function getReserves(): Collection
    {
        if ($this->reservesCache !== null) {
            return $this->reservesCache;
        }

        return $this->reservesCache = BankReserve::where('workspace_id', $this->workspaceId)
            ->orderBy('name')
            ->get();
    }

    public function getTransitItems(): Collection
    {
        return BankTransitItem::where('workspace_id', $this->workspaceId)
            ->where('status', 'pending')
            ->orderBy('expected_date')
            ->get();
    }

    public function getCredits(): Collection
    {
        return BankCredit::where('workspace_id', $this->workspaceId)
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('due_date')
            ->get();
    }

    public function getTransfers(int $limit = 20): Collection
    {
        return BankTransfer::where('workspace_id', $this->workspaceId)
            ->with(['fromAccount', 'toAccount'])
            ->orderByDesc('transferred_at')
            ->limit($limit)
            ->get();
    }

    public function getInvestments(): Collection
    {
        if ($this->investmentsCache !== null) {
            return $this->investmentsCache;
        }

        return $this->investmentsCache = Investment::where('workspace_id', $this->workspaceId)->get();
    }

    public function getPatrimony(): Collection
    {
        if ($this->patrimonyCache !== null) {
            return $this->patrimonyCache;
        }

        return $this->patrimonyCache = BankPatrimony::where('workspace_id', $this->workspaceId)
            ->where('status', '!=', 'sold')
            ->orderBy('type')
            ->get();
    }

    public function getGoals(): Collection
    {
        return Goal::where('workspace_id', $this->workspaceId)
            ->orderBy('deadline')
            ->get()
            ->map(function ($goal) {
                $goal->progress = $goal->target_amount > 0
                    ? min(100, ($goal->current_amount / $goal->target_amount) * 100)
                    : 0;

                $goal->months_remaining = null;
                if ($goal->deadline && $goal->deadline->isFuture()) {
                    $goal->months_remaining = (int) now()->diffInMonths($goal->deadline);
                }

                return $goal;
            });
    }

    public function getDebts(): Collection
    {
        return Debt::where('workspace_id', $this->workspaceId)
            ->where('is_paid', false)
            ->orderBy('due_at')
            ->get();
    }

    public function getMonthlyFlow(int $months = 12): array
    {
        $months = max(1, min($months, 60));
        $start = now()->startOfMonth()->subMonths($months - 1);
        $end = now()->endOfMonth();
        $fixedIncome = $this->getFixedMonthlyIncome();

        $incomeMonthExpression = $this->monthKeyExpression('received_at');
        $expenseMonthExpression = $this->monthKeyExpression('spent_at');

        $incomes = Income::where('workspace_id', $this->workspaceId)
            ->whereBetween('received_at', [$start, $end])
            ->selectRaw("{$incomeMonthExpression} as month_key, SUM(amount) as total")
            ->groupByRaw($incomeMonthExpression)
            ->get()
            ->keyBy('month_key');

        $expenses = Expense::where('workspace_id', $this->workspaceId)
            ->whereBetween('spent_at', [$start, $end])
            ->selectRaw("{$expenseMonthExpression} as month_key, SUM(amount) as total")
            ->groupByRaw($expenseMonthExpression)
            ->get()
            ->keyBy('month_key');

        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $month = $date->format('Y-m');
            $income = (float) ($incomes->get($month)?->total ?? 0);
            $expense = (float) ($expenses->get($month)?->total ?? 0);

            $data[] = [
                'month' => $month,
                'label' => $date->locale('pt')->isoFormat('MMM YY'),
                'income' => $income + $fixedIncome,
                'expense' => $expense,
                'balance' => $income + $fixedIncome - $expense,
            ];
        }

        return $data;
    }

    public function getLiquidity(): array
    {
        $accounts = $this->getAccounts()->where('status', '!=', 'archived');
        $reserves = $this->getReserves()->where('status', 'active');
        $investments = $this->getInvestments();

        $immediateCash = $accounts->whereIn('type', ['corrente', 'cash', 'poupanca'])->sum(fn ($a) => $a->current_balance);
        $totalReserved = $reserves->sum('amount');
        $totalInvested = $investments->sum(fn ($i) => $i->quantity * $i->current_price);

        $avgMonthlyExpense = $this->getAvgMonthlyExpense(6);
        $availableForExpenses = max(0, $immediateCash - $totalReserved);

        $monthsCoverage = $avgMonthlyExpense > 0
            ? round($availableForExpenses / $avgMonthlyExpense, 1)
            : 0;

        return [
            'immediate_cash' => $immediateCash,
            'reserved' => $totalReserved,
            'available_today' => max(0, $immediateCash - $totalReserved),
            'invested' => $totalInvested,
            'total_liquidity' => $immediateCash + $totalInvested,
            'months_coverage' => $monthsCoverage,
            'avg_monthly_expense' => $avgMonthlyExpense,
            'low_liquidity_warning' => $monthsCoverage < 3,
        ];
    }

    public function getStats(): array
    {
        $accounts = $this->getAccounts();
        $transfers = BankTransfer::where('workspace_id', $this->workspaceId)->count();
        $reserves = $this->getReserves()->count();

        $incomeStats = Income::where('workspace_id', $this->workspaceId)
            ->selectRaw('COALESCE(MAX(amount), 0) as max_amount, COALESCE(SUM(amount), 0) as total_amount')
            ->first();
        $expenseStats = Expense::where('workspace_id', $this->workspaceId)
            ->selectRaw('COALESCE(MAX(amount), 0) as max_amount, COALESCE(SUM(amount), 0) as total_amount')
            ->first();

        $maxIncome = (float) ($incomeStats->max_amount ?? 0);
        $maxExpense = (float) ($expenseStats->max_amount ?? 0);
        $totalMoved = (float) ($incomeStats->total_amount ?? 0) + (float) ($expenseStats->total_amount ?? 0);

        $monthlyFlows = $this->getMonthlyFlow(12);
        $avgMonthly = count($monthlyFlows) > 0
            ? collect($monthlyFlows)->avg('balance')
            : 0;

        $investments = $this->getInvestments();
        $investmentCost = $investments->sum(fn ($i) => $i->quantity * $i->average_price);
        $investmentValue = $investments->sum(fn ($i) => $i->quantity * $i->current_price);
        $investmentReturn = $investmentCost > 0
            ? (($investmentValue - $investmentCost) / $investmentCost) * 100
            : 0;

        return [
            'total_accounts' => $accounts->count(),
            'total_transfers' => $transfers,
            'total_reserves' => $reserves,
            'max_income' => (float) $maxIncome,
            'max_expense' => (float) $maxExpense,
            'total_moved' => (float) $totalMoved,
            'avg_monthly_balance' => (float) $avgMonthly,
            'investment_return' => round($investmentReturn, 2),
        ];
    }

    public function getAlerts(): array
    {
        $alerts = [];
        $accounts = $this->getAccounts();

        foreach ($accounts as $account) {
            if ($account->alert_below !== null && $account->balance < $account->alert_below) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'title' => "Conta '{$account->name}' abaixo do limite",
                    'message' => 'Saldo: '.number_format($account->balance, 2).' € (limite: '.number_format($account->alert_below, 2).' €)',
                ];
            }

            if ($account->balance < 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'x-circle',
                    'title' => "Saldo negativo: {$account->name}",
                    'message' => number_format($account->balance, 2).' €',
                ];
            }
        }

        $overdueCredits = BankCredit::where('workspace_id', $this->workspaceId)
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCredits > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => 'Créditos em atraso',
                'message' => "{$overdueCredits} recebimento(s) pendente(s) com prazo ultrapassado.",
            ];
        }

        $liquidity = $this->getLiquidity();
        if ($liquidity['months_coverage'] < 3 && $liquidity['months_coverage'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'shield-exclamation',
                'title' => 'Liquidez reduzida',
                'message' => "Tem reservas para apenas {$liquidity['months_coverage']} meses de despesas. Recomenda-se um mínimo de 3 meses.",
            ];
        }

        $urgentDebts = Debt::where('workspace_id', $this->workspaceId)
            ->where('is_paid', false)
            ->where('type', 'owe')
            ->whereBetween('due_at', [now(), now()->addDays(7)])
            ->count();

        if ($urgentDebts > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'bell-alert',
                'title' => 'Dívida(s) urgente(s)',
                'message' => "{$urgentDebts} pagamento(s) vencem nos próximos 7 dias.",
            ];
        }

        return $alerts;
    }

    public function getPatrimonyDistribution(): array
    {
        $summary = $this->getSummary();

        $items = [
            ['label' => 'Dinheiro', 'value' => $summary['total_bank_balance'], 'color' => '#10b981'],
            ['label' => 'Investimentos', 'value' => $summary['total_investments'], 'color' => '#3b82f6'],
            ['label' => 'Imóveis', 'value' => $summary['total_real_estate'], 'color' => '#f59e0b'],
            ['label' => 'Veículos', 'value' => $summary['total_vehicles'], 'color' => '#8b5cf6'],
            ['label' => 'Ouro', 'value' => $summary['total_gold'], 'color' => '#eab308'],
            ['label' => 'Criptomoedas', 'value' => $summary['total_crypto'], 'color' => '#f97316'],
            ['label' => 'Outros Ativos', 'value' => $summary['total_other_assets'], 'color' => '#6b7280'],
        ];

        $total = collect($items)->sum('value');

        return collect($items)
            ->filter(fn ($i) => $i['value'] > 0)
            ->map(fn ($i) => array_merge($i, [
                'pct' => $total > 0 ? round(($i['value'] / $total) * 100, 1) : 0,
            ]))
            ->values()
            ->toArray();
    }

    private function getCurrentMonthIncome(): float
    {
        $variable = (float) Income::where('workspace_id', $this->workspaceId)
            ->whereYear('received_at', now()->year)
            ->whereMonth('received_at', now()->month)
            ->sum('amount');

        return $variable + $this->getFixedMonthlyIncome();
    }

    private function getCurrentMonthExpense(): float
    {
        return (float) Expense::where('workspace_id', $this->workspaceId)
            ->whereYear('spent_at', now()->year)
            ->whereMonth('spent_at', now()->month)
            ->sum('amount');
    }

    private function getPrevMonthIncome(): float
    {
        $variable = (float) Income::where('workspace_id', $this->workspaceId)
            ->whereYear('received_at', now()->subMonth()->year)
            ->whereMonth('received_at', now()->subMonth()->month)
            ->sum('amount');

        return $variable + $this->getFixedMonthlyIncome();
    }

    private function getPrevMonthExpense(): float
    {
        return (float) Expense::where('workspace_id', $this->workspaceId)
            ->whereYear('spent_at', now()->subMonth()->year)
            ->whereMonth('spent_at', now()->subMonth()->month)
            ->sum('amount');
    }

    private function getFixedMonthlyIncome(): float
    {
        return (float) RecurringIncome::where('workspace_id', $this->workspaceId)
            ->where('is_active', true)
            ->get()
            ->sum(fn ($r) => match ($r->frequency) {
                'semanal' => (float) $r->amount * 52 / 12,
                'anual' => (float) $r->amount / 12,
                default => (float) $r->amount,
            });
    }

    private function getAvgMonthlyExpense(int $months = 6): float
    {
        if ($months <= 0) {
            return 0;
        }

        $end = now()->startOfMonth()->subMonth()->endOfMonth();
        $start = $end->copy()->startOfMonth()->subMonths($months - 1);
        $total = (float) Expense::where('workspace_id', $this->workspaceId)
            ->whereBetween('spent_at', [$start, $end])
            ->sum('amount');

        return $total / $months;
    }

    private function monthKeyExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$column})",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
