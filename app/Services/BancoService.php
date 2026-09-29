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
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BancoService
{
    private int $workspaceId;

    private int $userId;

    private bool $isBusinessWorkspace;

    private ?Collection $accountsCache = null;

    private ?Collection $reservesCache = null;

    private ?Collection $investmentsCache = null;

    private ?Collection $patrimonyCache = null;

    private ?Collection $creditsCache = null;

    private ?Collection $transfersCache = null;

    private ?Collection $debtsCache = null;

    private ?Collection $goalsCache = null;

    private ?Collection $incomesCache = null;

    private ?Collection $expensesCache = null;

    public function __construct(int $workspaceId, int $userId)
    {
        $this->workspaceId = $workspaceId;
        $this->userId = $userId;
        $this->isBusinessWorkspace = in_array(
            (string) Workspace::whereKey($workspaceId)->value('type'),
            ['business', 'company'],
            true
        );
    }

    /**
     * Personal workspaces are user-owned. Business workspaces are shared by members.
     */
    private function personalScope($query)
    {
        return $this->isBusinessWorkspace
            ? $query
            : $query->where('user_id', $this->userId);
    }

    private function getPrevMonthIncome(): float
    {
        $variable = (float) $this->personalScope(Income::where('workspace_id', $this->workspaceId))
            ->whereYear('received_at', now()->subMonth()->year)
            ->whereMonth('received_at', now()->subMonth()->month)
            ->sum('amount');

        return $variable + $this->getFixedMonthlyIncome();
    }

    private function getPrevMonthExpense(): float
    {
        return (float) $this->personalScope(Expense::where('workspace_id', $this->workspaceId))
            ->whereYear('spent_at', now()->subMonth()->year)
            ->whereMonth('spent_at', now()->subMonth()->month)
            ->sum('amount');
    }

    private function getFixedMonthlyIncome(): float
    {
        return (float) $this->personalScope(RecurringIncome::where('workspace_id', $this->workspaceId))
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
        $total = (float) $this->personalScope(Expense::where('workspace_id', $this->workspaceId))
            ->whereBetween('spent_at', [$start, $end])
            ->sum('amount');

        return $total / $months;
    }
}
