<?php

namespace App\Livewire;

use App\Models\Debt;
use App\Models\BudgetChallenge;
use App\Models\FamilyBudgetPermission;
use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\Reminder;
use App\Models\Subscription;
use App\Models\ActivityLog;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class FamilyRanking extends Component
{
    public function createEconomicWeekChallenge(): void
    {
        $workspace = auth()->user()->currentWorkspace;
        $now = now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $baselineStart = $now->copy()->subWeeks(8)->startOfWeek();

        $baseline = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('spent_at', [$baselineStart, $weekStart->copy()->subDay()])
            ->with('category:id,name')
            ->get(['id', 'category_id', 'amount', 'amount_converted', 'spent_at']);

        $candidate = $baseline
            ->groupBy('category_id')
            ->map(function ($group, $categoryId) {
                $weeklyValues = collect($group)
                    ->groupBy(fn (Expense $expense) => Carbon::parse($expense->spent_at)->startOfWeek()->format('Y-m-d'))
                    ->map(fn ($weekGroup) => collect($weekGroup)->sum(fn (Expense $expense) => $this->amountValue($expense)))
                    ->values()
                    ->all();

                $avg = count($weeklyValues) > 0 ? array_sum($weeklyValues) / count($weeklyValues) : 0.0;

                return [
                    'category_id' => $categoryId ? (int) $categoryId : null,
                    'category' => collect($group)->first()->category?->name ?? 'Geral',
                    'limit' => round(max(10, $avg * 0.9), 2),
                    'avg' => $avg,
                ];
            })
            ->sortByDesc('avg')
            ->first() ?? [
                'category_id' => null,
                'category' => 'Geral',
                'limit' => 50.0,
                'avg' => 0.0,
            ];

        BudgetChallenge::firstOrCreate(
            [
                'workspace_id' => $workspace->id,
                'user_id' => auth()->id(),
                'title' => 'Semana economica: '.$candidate['category'],
                'start_date' => $weekStart->toDateString(),
            ],
            [
                'category_id' => $candidate['category_id'],
                'target_amount' => $candidate['limit'],
                'end_date' => $weekEnd->toDateString(),
                'status' => 'active',
            ]
        );

        $this->dispatch('toast', variant: 'success', text: 'Desafio da semana economica criado!');
    }

    private function amountValue($model): float
    {
        return (float) ($model->amount_converted ?? $model->amount ?? 0);
    }

    private function stddev(array $values): float
    {
        $count = count($values);
        if ($count === 0) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(fn (float $value): float => ($value - $mean) ** 2, $values)) / $count;

        return sqrt($variance);
    }

    private function normalizeServiceName(string $name): string
    {
        $normalized = Str::lower(trim($name));
        $normalized = preg_replace('/\b(premium|family|familia|plano|plan|mensal|anual|app)\b/u', '', $normalized) ?? $normalized;

        return trim(preg_replace('/\s+/', ' ', $normalized) ?? $normalized);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $monthLabel = $now->translatedFormat('F Y');
        $workspaceCurrency = strtoupper((string) ($workspace->currency ?? 'EUR'));

        // 1. RANKING COMPLETO (Ganhos, Gastos e Saldo por utilizador)
        $memberStats = User::join('workspace_user', 'users.id', '=', 'workspace_user.user_id')
            ->where('workspace_user.workspace_id', $workspace->id)
            ->select('users.*')
            ->withSum(['expenses' => function($q) use ($monthStart, $workspace) {
                $q->where('spent_at', '>=', $monthStart)->where('workspace_id', $workspace->id);
            }], 'amount')
            ->withSum(['incomes' => function($q) use ($monthStart, $workspace) {
                $q->where('received_at', '>=', $monthStart)->where('workspace_id', $workspace->id);
            }], 'amount')
            ->get()
            ->map(function($user) {
                $user->total_expenses = $user->expenses_sum_amount ?: 0;
                $user->total_incomes = $user->incomes_sum_amount ?: 0;
                $user->net_balance = $user->total_incomes - $user->total_expenses;
                return $user;
            })->sortByDesc('net_balance');

        // 2. RANKING DE ATIVIDADE (Quem mais trabalha na conta)
        $topRecorders = User::join('workspace_user', 'users.id', '=', 'workspace_user.user_id')
            ->where('workspace_user.workspace_id', $workspace->id)
            ->select('users.*')
            ->withCount(['expenses' => function($q) use ($monthStart, $workspace) {
                $q->where('spent_at', '>=', $monthStart)->where('workspace_id', $workspace->id);
            }])
            ->orderByDesc('expenses_count')
            ->get();

        $incomeByUser = Income::query()
            ->where('workspace_id', $workspace->id)
            ->where('received_at', '>=', $monthStart)
            ->selectRaw('user_id, SUM(COALESCE(amount_converted, amount)) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $expenseByUser = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->where('spent_at', '>=', $monthStart)
            ->selectRaw('user_id, SUM(COALESCE(amount_converted, amount)) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $savingsLeague = $workspace->users()
            ->get(['users.id', 'users.name', 'users.level', 'users.xp'])
            ->map(function ($member) use ($incomeByUser, $expenseByUser) {
                $income = (float) ($incomeByUser[$member->id] ?? 0);
                $expense = (float) ($expenseByUser[$member->id] ?? 0);
                $saved = $income - $expense;
                $rate = $income > 0 ? ($saved / $income) * 100 : 0;

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'level' => $member->level,
                    'xp' => $member->xp,
                    'income' => $income,
                    'expense' => $expense,
                    'saved' => $saved,
                    'savings_rate' => round($rate, 1),
                ];
            })
            ->sortByDesc(fn (array $row) => [$row['savings_rate'], $row['saved']])
            ->values()
            ->map(function (array $row, int $index) {
                $row['position'] = $index + 1;

                return $row;
            });

        $monthsKeys = collect(range(5, 0))->map(fn ($i) => $now->copy()->subMonths($i)->format('Y-m'));
        $historyStart = $now->copy()->subMonths(5)->startOfMonth();

        $incomeRows = Income::query()
            ->where('workspace_id', $workspace->id)
            ->where('received_at', '>=', $historyStart)
            ->selectRaw('user_id, strftime("%Y-%m", received_at) as ym, SUM(COALESCE(amount_converted, amount)) as total')
            ->groupBy('user_id', 'ym')
            ->get();

        $expenseRows = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->where('spent_at', '>=', $historyStart)
            ->selectRaw('user_id, strftime("%Y-%m", spent_at) as ym, SUM(COALESCE(amount_converted, amount)) as total')
            ->groupBy('user_id', 'ym')
            ->get();

        $incomeMap = [];
        foreach ($incomeRows as $row) {
            $incomeMap[(int) $row->user_id][(string) $row->ym] = (float) $row->total;
        }

        $expenseMap = [];
        foreach ($expenseRows as $row) {
            $expenseMap[(int) $row->user_id][(string) $row->ym] = (float) $row->total;
        }

        $consistencyLeague = $workspace->users()
            ->get(['users.id', 'users.name', 'users.level'])
            ->map(function ($member) use ($monthsKeys, $incomeMap, $expenseMap) {
                $rates = [];

                foreach ($monthsKeys as $ym) {
                    $income = (float) ($incomeMap[$member->id][$ym] ?? 0);
                    $expense = (float) ($expenseMap[$member->id][$ym] ?? 0);
                    $saved = $income - $expense;
                    $rate = $income > 0 ? ($saved / $income) * 100 : 0;

                    $rates[] = [
                        'month' => $ym,
                        'income' => $income,
                        'expense' => $expense,
                        'saved' => $saved,
                        'rate' => round($rate, 1),
                        'positive' => $income > 0 && $rate > 0,
                    ];
                }

                $currentStreak = 0;
                foreach (array_reverse($rates) as $point) {
                    if (! $point['positive']) {
                        break;
                    }
                    $currentStreak++;
                }

                $maxStreak = 0;
                $run = 0;
                foreach ($rates as $point) {
                    if ($point['positive']) {
                        $run++;
                        $maxStreak = max($maxStreak, $run);
                    } else {
                        $run = 0;
                    }
                }

                $avgRate = collect($rates)->avg('rate') ?? 0;
                $previousRates = collect($rates)->slice(0, max(0, count($rates) - 1))->pluck('rate')->values()->all();
                $lastRate = (float) (collect($rates)->last()['rate'] ?? 0);
                $previousAvg = count($previousRates) > 0 ? (array_sum($previousRates) / count($previousRates)) : 0.0;
                $relativeImprovement = $lastRate - $previousAvg;

                $rateValues = array_map(fn (array $item): float => (float) $item['rate'], $rates);
                $volatility = $this->stddev($rateValues);
                $regularityScore = max(0, min(100, round(100 - ($volatility * 3), 1)));

                $hadNegativeBeforeLast = collect($rates)->slice(0, max(0, count($rates) - 1))->contains(fn (array $point): bool => (float) $point['rate'] < 0);
                $recoveryBonus = ($hadNegativeBeforeLast && $lastRate > 0) ? 12 : 0;

                $points = round(
                    ($currentStreak * 20) +
                    (max(0, $avgRate) * 0.6) +
                    (max(0, $maxStreak - 1) * 5) +
                    (max(0, $relativeImprovement) * 0.8) +
                    ($regularityScore * 0.2) +
                    $recoveryBonus,
                    1
                );

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'level' => $member->level,
                    'current_streak' => $currentStreak,
                    'max_streak' => $maxStreak,
                    'avg_rate' => round((float) $avgRate, 1),
                    'relative_improvement' => round($relativeImprovement, 1),
                    'regularity_score' => round($regularityScore, 1),
                    'recovery_bonus' => $recoveryBonus,
                    'consistency_points' => $points,
                ];
            })
            ->sortByDesc(fn (array $row) => [$row['consistency_points'], $row['current_streak'], $row['avg_rate']])
            ->values()
            ->map(function (array $row, int $index) {
                $row['position'] = $index + 1;

                return $row;
            });

        $budgetService = app(BudgetService::class);
        $budgetOverview = $budgetService->getMonthlyOverview($workspace);
        $categoryBreakdown = $budgetService->getCategoryBreakdown($workspace);

        $budgetCategories = $categoryBreakdown->filter(fn ($cat) => ($cat['budget'] ?? 0) > 0);
        $withinBudgetCount = $budgetCategories->filter(fn ($cat) => ($cat['percentage'] ?? 0) <= 100)->count();
        $budgetCategoryCount = $budgetCategories->count();

        $categoryAdherenceScore = $budgetCategoryCount > 0
            ? round(($withinBudgetCount / $budgetCategoryCount) * 100, 1)
            : 100.0;

        $groupIncomeCurrent = (float) Income::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('received_at', [$monthStart, $monthEnd])
            ->sum('amount_converted');

        if ($groupIncomeCurrent <= 0) {
            $groupIncomeCurrent = (float) Income::query()
                ->where('workspace_id', $workspace->id)
                ->whereBetween('received_at', [$monthStart, $monthEnd])
                ->sum('amount');
        }

        $groupExpenseCurrent = (float) Expense::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('spent_at', [$monthStart, $monthEnd])
            ->sum('amount_converted');

        if ($groupExpenseCurrent <= 0) {
            $groupExpenseCurrent = (float) Expense::query()
                ->where('workspace_id', $workspace->id)
                ->whereBetween('spent_at', [$monthStart, $monthEnd])
                ->sum('amount');
        }

        $groupSavingsRate = $groupIncomeCurrent > 0
            ? (($groupIncomeCurrent - $groupExpenseCurrent) / $groupIncomeCurrent) * 100
            : 0;

        $budgetDisciplineScore = round(max(0, 100 - max(0, ((float) $budgetOverview['percentage']) - 100) * 2), 1);
        $savingsScore = round(min(100, max(0, ($groupSavingsRate + 10) * 4)), 1);

        $familyHealthScore = round(
            ($budgetDisciplineScore * 0.35) +
            ($savingsScore * 0.40) +
            ($categoryAdherenceScore * 0.25),
            1
        );

        $familyHealth = [
            'score' => $familyHealthScore,
            'budget_discipline' => $budgetDisciplineScore,
            'group_savings_rate' => round($groupSavingsRate, 1),
            'savings_score' => $savingsScore,
            'category_adherence' => $categoryAdherenceScore,
            'within_budget' => $withinBudgetCount,
            'budget_categories' => $budgetCategoryCount,
            'group_income' => $groupIncomeCurrent,
            'group_expense' => $groupExpenseCurrent,
        ];

        $expensesHistory = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->where('spent_at', '>=', $historyStart)
            ->with(['category:id,name'])
            ->get(['id', 'user_id', 'category_id', 'description', 'amount', 'amount_converted', 'spent_at']);

        $expensesMonth = $expensesHistory->filter(fn (Expense $expense): bool => Carbon::parse($expense->spent_at)->betweenIncluded($monthStart, $monthEnd))->values();

        $incomesMonth = Income::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('received_at', [$monthStart, $monthEnd])
            ->get(['id', 'user_id', 'description', 'source', 'amount', 'amount_converted', 'received_at']);

        $goals = Goal::query()
            ->where('workspace_id', $workspace->id)
            ->with(['contributions.user'])
            ->get(['id', 'name', 'target_amount', 'current_amount', 'deadline']);

        $goalPockets = $goals->map(function (Goal $goal) use ($groupSavingsRate) {
            $target = (float) $goal->target_amount;
            $current = (float) $goal->current_amount;
            $remaining = max(0.0, $target - $current);
            $progress = $target > 0 ? min(100, ($current / $target) * 100) : 0;
            $daysLeft = $goal->deadline ? now()->diffInDays(Carbon::parse($goal->deadline), false) : null;

            $monthsToDeadline = $goal->deadline
                ? max(1, now()->startOfMonth()->diffInMonths(Carbon::parse($goal->deadline)->startOfMonth(), false) + 1)
                : null;

            $requiredMonthly = $monthsToDeadline ? ($remaining / $monthsToDeadline) : 0;
            $last90DaysTotal = (float) $goal->contributions
                ->filter(fn (GoalContribution $contribution): bool => $contribution->contributed_at && $contribution->contributed_at->gte(now()->subDays(90)))
                ->sum('amount');
            $monthlyPace = $last90DaysTotal > 0 ? $last90DaysTotal / 3 : 0.0;
            $predictedCompletionDate = null;

           if ($progress >= 100) {
    $predictedCompletionDate = now();
} elseif ($remaining > 0 && $monthlyPace > 0.1) { // Só calcula se o ritmo for > 0.10€
    $monthsNeeded = (int) ceil($remaining / $monthlyPace);
    // Limite de segurança: se demorar mais de 100 anos (1200 meses), ignoramos o cálculo
    $predictedCompletionDate = $monthsNeeded < 1200 ? now()->addMonths($monthsNeeded) : null;
} else {
    $predictedCompletionDate = null;
}

            $isLateByForecast = $goal->deadline
                && $predictedCompletionDate
                && $predictedCompletionDate->gt(Carbon::parse($goal->deadline))
                && $progress < 100;

            return [
                'name' => $goal->name,
                'target' => $target,
                'current' => $current,
                'remaining' => $remaining,
                'progress' => round($progress, 1),
                'deadline' => $goal->deadline,
                'required_monthly' => round($requiredMonthly, 2),
                'monthly_pace' => round($monthlyPace, 2),
                'predicted_completion' => $predictedCompletionDate,
                'is_late_by_forecast' => $isLateByForecast,
                'alert' => match (true) {
                    $progress >= 100 => 'Meta concluida',
                    $daysLeft !== null && $daysLeft < 0 => 'Prazo vencido',
                    $isLateByForecast => 'Ritmo abaixo do necessario',
                    $requiredMonthly > 0 && $monthlyPace > 0 && $monthlyPace < $requiredMonthly => 'Atraso provavel',
                    default => null,
                },
                'contributors' => $goal->contributions
                    ->groupBy('user_id')
                    ->map(fn ($rows) => [
                        'name' => $rows->first()->user?->name ?? 'Membro',
                        'amount' => round((float) $rows->sum('amount'), 2),
                    ])
                    ->sortByDesc('amount')
                    ->values(),
                'status' => $progress >= 100 ? 'concluido' : ($groupSavingsRate >= 0 && ! $isLateByForecast ? 'em-curso' : 'risco'),
            ];
        })->sortByDesc('progress')->values();

        $subscriptions = Subscription::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->with('user:id,name')
            ->get(['id', 'user_id', 'name', 'amount', 'billing_day', 'cycle', 'is_active']);

        $debts = Debt::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_paid', false)
            ->whereBetween('due_at', [$now->copy()->startOfDay(), $monthEnd])
            ->get(['id', 'person_name', 'amount', 'due_at']);

        $pendingReminders = Reminder::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_completed', false)
            ->whereBetween('remind_at', [$now->copy()->startOfDay(), $monthEnd])
            ->get(['id', 'title', 'remind_at']);

        $upcomingIncomes = Income::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('received_at', [$now->copy()->startOfDay(), $monthEnd])
            ->get(['id', 'description', 'source', 'amount', 'amount_converted', 'received_at']);

        $recurringIncomes = RecurringIncome::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->get(['id', 'description', 'source', 'amount', 'day_of_month']);

        $calendarEvents = collect();

        foreach ($upcomingIncomes as $income) {
            $calendarEvents->push([
                'date' => Carbon::parse($income->received_at),
                'label' => 'Renda: '.($income->source ?: $income->description ?: 'entrada'),
                'amount' => $this->amountValue($income),
                'type' => 'income',
            ]);
        }

        foreach ($recurringIncomes as $income) {
            $incomeDay = max(1, min((int) $monthEnd->day, (int) ($income->day_of_month ?: 1)));
            $eventDate = $monthStart->copy()->day($incomeDay);

            if ($eventDate->lt($now->copy()->startOfDay())) {
                continue;
            }

            $calendarEvents->push([
                'date' => $eventDate,
                'label' => 'Salario: '.($income->source ?: $income->description ?: 'renda recorrente'),
                'amount' => (float) $income->amount,
                'type' => 'salary',
            ]);
        }

        foreach ($subscriptions as $subscription) {
            $billingDay = max(1, min(28, (int) ($subscription->billing_day ?: 1)));
            $eventDate = $monthStart->copy()->day($billingDay);

            if ($eventDate->lt($now->copy()->startOfDay())) {
                continue;
            }

            $calendarEvents->push([
                'date' => $eventDate,
                'label' => 'Assinatura: '.$subscription->name,
                'amount' => (float) $subscription->amount,
                'type' => 'subscription',
            ]);
        }

        foreach ($debts as $debt) {
            $calendarEvents->push([
                'date' => Carbon::parse($debt->due_at),
                'label' => 'Dívida: '.($debt->person_name ?: 'pendente'),
                'amount' => (float) $debt->amount,
                'type' => 'debt',
            ]);
        }

        foreach ($pendingReminders as $reminder) {
            $calendarEvents->push([
                'date' => Carbon::parse($reminder->remind_at),
                'label' => 'Lembrete: '.$reminder->title,
                'amount' => 0.0,
                'type' => 'reminder',
            ]);
        }

        $calendarEvents = $calendarEvents
            ->sortBy(fn (array $event) => $event['date']->timestamp)
            ->values();

        $runningBalance = $groupIncomeCurrent - $groupExpenseCurrent;
        $riskDays = [];
        $cursor = $now->copy()->startOfDay();

        $runningBalance = $groupIncomeCurrent - $groupExpenseCurrent;
$riskDays = [];
$cursor = $now->copy()->startOfDay();

// Agrupamos os eventos por data uma única vez antes do loop (Otimização de Performance)
$eventsByDate = $calendarEvents->groupBy(fn($e) => $e['date']->format('Y-m-d'));

while ($cursor->lte($monthEnd)) {
    $dateKey = $cursor->format('Y-m-d');
    $dayEvents = $eventsByDate->get($dateKey, collect());

    $dayOutflow = $dayEvents
        ->filter(fn ($e) => in_array($e['type'], ['subscription', 'debt']))
        ->sum('amount');

    $dayInflow = $dayEvents
        ->filter(fn ($e) => in_array($e['type'], ['income', 'salary']))
        ->sum('amount');

    $runningBalance += (float) $dayInflow - (float) $dayOutflow;

    if ($runningBalance < 0) {
        $riskDays[] = [
            'date' => $cursor->copy(),
            'projected_balance' => round($runningBalance, 2),
        ];
    }

    $cursor->addDay();
}

        $currentWeekStart = $now->copy()->startOfWeek();
        $weekExpenses = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('spent_at', [$currentWeekStart, $now])
            ->with('category:id,name')
            ->get(['id', 'category_id', 'amount', 'amount_converted', 'spent_at']);

        $baselineStart = $now->copy()->subWeeks(8)->startOfWeek();
        $baselineExpenses = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('spent_at', [$baselineStart, $currentWeekStart->copy()->subDay()])
            ->with('category:id,name')
            ->get(['id', 'category_id', 'amount', 'amount_converted', 'spent_at']);

        $weeklyChallenges = $weekExpenses
            ->groupBy(fn (Expense $expense) => $expense->category?->name ?? 'Sem categoria')
            ->map(function ($group, $categoryName) use ($baselineExpenses) {
                $groupCollection = collect($group);
                $currentSpent = $groupCollection->sum(fn (Expense $expense) => $this->amountValue($expense));

                $baselineWeeklyValues = $baselineExpenses
                    ->filter(fn (Expense $expense): bool => ($expense->category?->name ?? 'Sem categoria') === $categoryName)
                    ->groupBy(fn (Expense $expense) => Carbon::parse($expense->spent_at)->startOfWeek()->format('Y-m-d'))
                    ->map(fn ($weekGroup) => $weekGroup->sum(fn (Expense $expense) => $this->amountValue($expense)))
                    ->values()
                    ->all();

                $baselineAvg = count($baselineWeeklyValues) > 0 ? (array_sum($baselineWeeklyValues) / count($baselineWeeklyValues)) : $currentSpent;
                $suggestedLimit = max(10, $baselineAvg * 0.9);

                return [
                    'category' => $categoryName,
                    'spent' => round($currentSpent, 2),
                    'limit' => round($suggestedLimit, 2),
                    'status' => $currentSpent <= $suggestedLimit ? 'ok' : 'risk',
                ];
            })
            ->sortByDesc('spent')
            ->values();

        $weekMedals = $weeklyChallenges->where('status', 'ok')->count();

        $subscriptionRenegotiationLeads = $subscriptions
            ->filter(fn (Subscription $subscription): bool => (float) $subscription->amount >= 8)
            ->map(function (Subscription $subscription) {
                $amount = (float) $subscription->amount;
                $savingPct = $amount >= 25 ? 0.25 : ($amount >= 15 ? 0.18 : 0.12);

                return [
                    'service' => $subscription->name,
                    'monthly' => $amount,
                    'annual_saving' => round(($amount * 12) * $savingPct, 2),
                    'checklist' => [
                        'Comparar preço em 2 concorrentes',
                        'Pedir desconto de retenção',
                        'Negociar plano familiar ou anual',
                    ],
                ];
            })
            ->values();

        $renegotiablePatterns = ['internet', 'fibra', 'telemovel', 'telefone', 'energia', 'eletricidade', 'luz', 'gas', 'agua', 'seguro'];
        $expenseRenegotiationLeads = $expensesHistory
            ->filter(function (Expense $expense) use ($renegotiablePatterns): bool {
                $description = Str::lower((string) $expense->description.' '.($expense->category?->name ?? ''));

                return collect($renegotiablePatterns)->contains(fn (string $pattern): bool => Str::contains($description, $pattern));
            })
            ->groupBy(fn (Expense $expense) => Str::title($expense->category?->name ?: Str::limit((string) $expense->description, 28, '')))
            ->map(function ($group, string $label) {
                $monthlyAverage = (float) collect($group)
                    ->groupBy(fn (Expense $expense) => Carbon::parse($expense->spent_at)->format('Y-m'))
                    ->map(fn ($monthGroup) => collect($monthGroup)->sum(fn (Expense $expense) => $this->amountValue($expense)))
                    ->avg();

                if ($monthlyAverage < 15) {
                    return null;
                }

                return [
                    'service' => $label,
                    'monthly' => round($monthlyAverage, 2),
                    'annual_saving' => round(($monthlyAverage * 12) * 0.18, 2),
                    'checklist' => [
                        'Rever consumo dos ultimos 3 meses',
                        'Pedir proposta a 2 fornecedores',
                        'Negociar fidelizacao, tarifa social ou pacote familiar',
                    ],
                ];
            })
            ->filter()
            ->values();

        $renegotiationLeads = $subscriptionRenegotiationLeads
            ->concat($expenseRenegotiationLeads)
            ->sortByDesc('annual_saving')
            ->take(6)
            ->values();

        $categoryVolatility = $expensesHistory
            ->groupBy(fn (Expense $expense) => $expense->category?->name ?? 'Sem categoria')
            ->map(function ($group, $categoryName) {
                $groupCollection = collect($group);
                $monthly = $groupCollection
                    ->groupBy(fn (Expense $expense) => Carbon::parse($expense->spent_at)->format('Y-m'))
                    ->map(fn ($monthGroup) => $monthGroup->sum(fn (Expense $expense) => $this->amountValue($expense)))
                    ->values()
                    ->all();

                $mean = count($monthly) > 0 ? (array_sum($monthly) / count($monthly)) : 0.0;
                $std = $this->stddev(array_map(fn ($value): float => (float) $value, $monthly));
                $cv = $mean > 0 ? ($std / $mean) : 0.0;
                $score = max(0, min(100, round(100 - ($cv * 100), 1)));

                return [
                    'category' => $categoryName,
                    'predictability_score' => $score,
                    'volatility_index' => round($cv, 2),
                    'risk_label' => $score >= 70 ? 'Estável' : ($score >= 45 ? 'Variável' : 'Volátil'),
                ];
            })
            ->sortBy('predictability_score')
            ->values();

        $predictabilityScore = round((float) ($categoryVolatility->avg('predictability_score') ?? 0), 1);

        $duplicateSubscriptions = $subscriptions
            ->groupBy(fn (Subscription $subscription) => $this->normalizeServiceName($subscription->name))
            ->map(function ($group, $normalized) {
                $groupCollection = collect($group);
                $userIds = $groupCollection->pluck('user_id')->unique()->values();
                if ($userIds->count() <= 1) {
                    return null;
                }

                $monthlyTotal = (float) $groupCollection->sum('amount');

                return [
                    'service' => Str::title($normalized),
                    'members' => $userIds->count(),
                    'monthly_total' => round($monthlyTotal, 2),
                    'possible_annual_saving' => round(($monthlyTotal * 12) * 0.25, 2),
                ];
            })
            ->filter()
            ->values();

        $monthlyIncomeTotal = (float) $incomesMonth->sum(fn (Income $income) => $this->amountValue($income));
        $autoSavingsProfiles = collect([
            ['profile' => 'Conservador', 'percent' => 10],
            ['profile' => 'Equilibrado', 'percent' => 20],
            ['profile' => 'Agressivo', 'percent' => 30],
        ])->map(function (array $profile) use ($monthlyIncomeTotal) {
            $monthly = ($monthlyIncomeTotal * $profile['percent']) / 100;

            return [
                'profile' => $profile['profile'],
                'percent' => $profile['percent'],
                'monthly' => round($monthly, 2),
                'annual' => round($monthly * 12, 2),
            ];
        });

        $invisiblePatterns = ['cafe', 'café', 'app', 'uber', 'bolt', 'ifood', 'entrega', 'snack', 'taxa'];
        $invisibleExpenses = $expensesMonth->filter(function (Expense $expense) use ($invisiblePatterns): bool {
            $amount = $this->amountValue($expense);
            $description = Str::lower((string) $expense->description);

            return $amount > 0 && $amount <= 20 && collect($invisiblePatterns)->contains(fn (string $pattern): bool => Str::contains($description, $pattern));
        })->values();

        $invisibleMonthly = (float) $invisibleExpenses->sum(fn (Expense $expense) => $this->amountValue($expense));
        $invisibleTop = $invisibleExpenses
            ->groupBy(fn (Expense $expense) => Str::title(Str::limit((string) $expense->description, 24, '')))
            ->map(fn ($group, $label) => [
                'label' => $label,
                'total' => round((float) collect($group)->sum(fn (Expense $expense) => $this->amountValue($expense)), 2),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $decisionBase = max(300.0, round($groupIncomeCurrent * 0.15, 2));
        $inflationRateAnnual = 3.5;
        $waitMonths = 3;
        $inflationFactor = (1 + ($inflationRateAnnual / 100)) ** ($waitMonths / 12);
        $waitPrice = $decisionBase * $inflationFactor;
        $installmentRateMonthly = 0.12 / 12;
        $installmentMonths = 12;
        $factor = (1 + $installmentRateMonthly) ** $installmentMonths;
        $installmentValue = $decisionBase * (($installmentRateMonthly * $factor) / ($factor - 1));

        $bigDecision = [
            'item_value' => round($decisionBase, 2),
            'buy_now' => [
                'impact_30d' => round(-$decisionBase, 2),
                'total_paid' => round($decisionBase, 2),
            ],
            'wait' => [
                'impact_30d' => 0.0,
                'future_price' => round($waitPrice, 2),
            ],
            'installment' => [
                'monthly' => round($installmentValue, 2),
                'total_paid' => round($installmentValue * $installmentMonths, 2),
            ],
        ];

        $incomeDependency = $incomesMonth
            ->groupBy(fn (Income $income) => filled($income->source) ? (string) $income->source : ((string) $income->description ?: 'Sem fonte'))
            ->map(function ($group, $source) use ($monthlyIncomeTotal) {
                $total = (float) collect($group)->sum(fn (Income $income) => $this->amountValue($income));
                $share = $monthlyIncomeTotal > 0 ? ($total / $monthlyIncomeTotal) * 100 : 0;

                return [
                    'source' => Str::title((string) $source),
                    'total' => round($total, 2),
                    'share' => round($share, 1),
                ];
            })
            ->sortByDesc('share')
            ->values();

        $topIncomeShare = (float) ($incomeDependency->first()['share'] ?? 0);
        $incomeDependencyRisk = $topIncomeShare >= 65 ? 'alto' : ($topIncomeShare >= 45 ? 'medio' : 'baixo');

        $dayOfMonth = max(1, (int) $now->day);
        $daysInMonth = max(1, (int) $monthEnd->day);
        $remainingDays = max(1, $daysInMonth - $dayOfMonth + 1);
        $dailyExpenseRunRate = $groupExpenseCurrent / $dayOfMonth;
        $dailyIncomeRunRate = $groupIncomeCurrent / $dayOfMonth;
        $projectedExpensesRemaining = $dailyExpenseRunRate * $remainingDays;
        $projectedIncomeRemaining = $dailyIncomeRunRate * $remainingDays;
        $upcomingFixedOutflows = (float) $calendarEvents
            ->filter(fn (array $event): bool => in_array($event['type'], ['subscription', 'debt'], true))
            ->sum('amount');

        $projectedEndBalance = ($groupIncomeCurrent - $groupExpenseCurrent) + $projectedIncomeRemaining - $projectedExpensesRemaining - $upcomingFixedOutflows;
        $stressBase = (($groupExpenseCurrent + $projectedExpensesRemaining + $upcomingFixedOutflows) / max(1, $groupIncomeCurrent + $projectedIncomeRemaining)) * 100;
        $stressProbability = max(0, min(100, round($stressBase + ($projectedEndBalance < 0 ? 20 : 0), 1)));

        $stressForecast = [
            'probability' => $stressProbability,
            'projected_end_balance' => round($projectedEndBalance, 2),
            'pressure_level' => $stressProbability >= 80 ? 'critico' : ($stressProbability >= 60 ? 'alto' : ($stressProbability >= 40 ? 'moderado' : 'baixo')),
        ];

        $teenAccounts = FamilyBudgetPermission::query()
            ->where('workspace_id', $workspace->id)
            ->whereNull('category_id')
            ->where(function ($query) {
                $query->where('allowance_limit', '>', 0)
                    ->orWhere('spending_limit', '>', 0);
            })
            ->with('user:id,name')
            ->get()
            ->map(function (FamilyBudgetPermission $permission) use ($expensesMonth) {
                $spent = (float) $expensesMonth
                    ->where('user_id', $permission->user_id)
                    ->sum(fn (Expense $expense) => $this->amountValue($expense));

                $allowance = (float) ($permission->allowance_limit ?? 0);
                $limit = (float) ($permission->spending_limit ?? 0);
                $remainingAllowance = max(0, $allowance - $spent);

                $needsApproval = $limit > 0 && $spent >= ($limit * 0.85);

                return [
                    'member' => $permission->user?->name ?? 'Membro',
                    'allowance' => $allowance,
                    'frequency' => $permission->allowance_frequency ?? 'monthly',
                    'spending_limit' => $limit,
                    'spent' => round($spent, 2),
                    'remaining_allowance' => round($remainingAllowance, 2),
                    'needs_approval' => $needsApproval,
                ];
            })
            ->values();

        if ($now->day <= 10) {
            $temporalAlerts = [
                'period' => 'inicio',
                'messages' => [
                    'Início do mês: define teto por categoria para evitar aceleração precoce.',
                    'Programa autopoupança antes da segunda semana para não depender do que sobra.',
                ],
            ];
        } elseif ($now->day <= 20) {
            $temporalAlerts = [
                'period' => 'meio',
                'messages' => [
                    'Metade do mês: compara gasto atual com a média e corta as duas maiores fugas.',
                    'Semana de ajuste: reduzir delivery e pequenas compras pode virar o saldo final.',
                ],
            ];
        } else {
            $temporalAlerts = [
                'period' => 'fim',
                'messages' => [
                    'Fim do mês: prioriza obrigações fixas e evita novas assinaturas.',
                    'Fecho positivo: congela gastos não essenciais para consolidar a taxa de poupança.',
                ],
            ];
        }

        $onePageReport = [
            'wins' => array_values(array_filter([
                $familyHealthScore >= 70 ? 'Saúde financeira familiar acima de 70 pontos.' : null,
                $groupSavingsRate > 0 ? 'Grupo fechando mês com poupança positiva.' : null,
                $predictabilityScore >= 60 ? 'Boa previsibilidade de gastos em categorias principais.' : null,
            ])),
            'deviations' => array_values(array_filter([
                count($riskDays) > 0 ? 'Existem dias com risco de caixa negativo no calendário.' : null,
                $duplicateSubscriptions->count() > 0 ? 'Assinaturas duplicadas elevando custo recorrente.' : null,
                $stressProbability >= 60 ? 'Probabilidade elevada de estresse financeiro em 30 dias.' : null,
            ])),
            'actions' => [
                'Aplicar perfil de autopoupança e transferir no dia de entrada de renda.',
                'Renegociar 2 maiores assinaturas e consolidar planos duplicados.',
                'Executar desafio da semana econômica com foco na categoria mais volátil.',
            ],
        ];

        // 3. ÚLTIMAS ATIVIDADES DO GRUPO (O que fizeram na conta)
        $recentActivities = ActivityLog::with('user')
            ->where('workspace_id', $workspace->id)
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.family-ranking', [
            'workspaceName' => $workspace->name,
            'workspaceCurrency' => $workspaceCurrency,
            'monthLabel' => $monthLabel,
            'goalPockets' => $goalPockets,
            'financialCalendar' => $calendarEvents,
            'riskDays' => $riskDays,
            'weeklyChallenges' => $weeklyChallenges,
            'weekMedals' => $weekMedals,
            'renegotiationLeads' => $renegotiationLeads,
            'predictabilityScore' => $predictabilityScore,
            'categoryVolatility' => $categoryVolatility,
            'duplicateSubscriptions' => $duplicateSubscriptions,
            'autoSavingsProfiles' => $autoSavingsProfiles,
            'invisibleMonthly' => round($invisibleMonthly, 2),
            'invisibleTop' => $invisibleTop,
            'bigDecision' => $bigDecision,
            'incomeDependency' => $incomeDependency,
            'incomeDependencyRisk' => $incomeDependencyRisk,
            'stressForecast' => $stressForecast,
            'teenAccounts' => $teenAccounts,
            'onePageReport' => $onePageReport,
            'temporalAlerts' => $temporalAlerts,
            'memberStats' => $memberStats,
            'savingsLeague' => $savingsLeague,
            'consistencyLeague' => $consistencyLeague,
            'familyHealth' => $familyHealth,
            'topRecorders' => $topRecorders,
            'recentActivities' => $recentActivities,
            'levelLeaders' => $workspace->users()->orderByDesc('level')->orderByDesc('xp')->get(),
        ]);
    }
}
