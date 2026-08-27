<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Reminder;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class SmartAlertService
{
    public static function checkAll(User $user): void
    {
        self::checkBudgetThresholds($user);
        self::checkExpenseSpikes($user);
        self::checkBehaviorAnomalies($user);
        self::checkUnusedSubscriptions($user);
        self::checkUpcomingPayments($user);
    }

    public static function checkBudgetThresholds(User $user): void
    {
        $workspace = $user->currentWorkspace;
        if (! $workspace) {
            return;
        }

        $budgetService = app(BudgetService::class);
        foreach ($budgetService->getAlerts($workspace) as $alert) {
            $title = "Orçamento: {$alert['category']}";
            if (! self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'title' => $title,
                    'message' => $alert['message'],
                    'type' => $alert['type'],
                    'link' => route('hub.budget'),
                ]);
            }
        }
    }

    public static function checkExpenseSpikes(User $user): void
    {
        $workspace = $user->currentWorkspace;
        if (! $workspace) {
            return;
        }

        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $categories = Expense::where('workspace_id', $workspace->id)
            ->where('is_company', false)
            ->where('spent_at', '>=', $lastMonth)
            ->with('category')
            ->get()
            ->groupBy('category_id');

        foreach ($categories as $categoryId => $expenses) {
            $category = $expenses->first()->category;
            if (! $category) {
                continue;
            }

            $spentThis = $expenses->where('spent_at', '>=', $thisMonth)->sum('amount');
            $spentLast = $expenses->whereBetween('spent_at', [$lastMonth, $lastMonthEnd])->sum('amount');

            if ($spentLast > 0 && $spentThis > 0) {
                $increase = (($spentThis - $spentLast) / $spentLast) * 100;
                if ($increase >= 20) {
                    $title = "Subida de Gastos: {$category->name}";
                    if (! self::alreadyNotified($user, $title)) {
                        AppNotification::create([
                            'user_id' => $user->id,
                            'workspace_id' => $workspace->id,
                            'title' => $title,
                            'message' => "Os gastos em {$category->name} subiram ".round($increase).'% vs mês passado.',
                            'type' => 'warning',
                            'link' => route('hub.category', $category->resolveSlug()),
                        ]);
                    }
                }
            }
        }
    }

    public static function checkUnusedSubscriptions(User $user): void
    {
        $workspace = $user->currentWorkspace;
        if (! $workspace) {
            return;
        }

        $cutoff = now()->subDays(60);
        $inactiveCount = Subscription::where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->where('updated_at', '<', $cutoff)
            ->count();

        if ($inactiveCount >= 2) {
            $title = 'Assinaturas Inativas';
            if (! self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'title' => $title,
                    'message' => "Tens {$inactiveCount} assinaturas sem atividade há mais de 60 dias. Revê-as!",
                    'type' => 'info',
                    'link' => route('hub.subscriptions'),
                ]);
            }
        }
    }

    public static function checkBehaviorAnomalies(User $user): void
    {
        $workspace = $user->currentWorkspace;
        if (! $workspace) {
            return;
        }

        $thisMonthStart = now()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        $historyStart = now()->subMonths(3)->startOfMonth();

        $memberCount = max(1, $workspace->users()->count());

        $personalCurrent = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('is_company', false)
            ->whereBetween('spent_at', [$thisMonthStart, now()])
            ->selectRaw('category_id, SUM(COALESCE(amount_converted, amount)) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $personalHistoryRows = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('is_company', false)
            ->whereBetween('spent_at', [$historyStart, $lastMonthEnd])
            ->selectRaw('category_id, spent_at, COALESCE(amount_converted, amount) as value')
            ->get();

        $personalAvgByCategory = $personalHistoryRows
            ->groupBy('category_id')
            ->map(function ($rows) {
                $monthly = $rows->groupBy(fn ($row) => Carbon::parse($row->spent_at)->format('Y-m'))
                    ->map(fn ($group) => (float) $group->sum('value'));

                return $monthly->isNotEmpty() ? (float) $monthly->avg() : 0.0;
            });

        $groupCurrentByCategory = Expense::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_company', false)
            ->whereBetween('spent_at', [$thisMonthStart, now()])
            ->selectRaw('category_id, SUM(COALESCE(amount_converted, amount)) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        foreach ($personalCurrent as $categoryId => $spentCurrent) {
            $category = Category::find($categoryId);
            if (! $category) {
                continue;
            }

            $spentCurrent = (float) $spentCurrent;
            $personalAvg = (float) ($personalAvgByCategory[$categoryId] ?? 0);
            $groupPerMember = ((float) ($groupCurrentByCategory[$categoryId] ?? 0)) / $memberCount;

            if ($personalAvg > 0 && $spentCurrent > ($personalAvg * 1.45) && ($spentCurrent - $personalAvg) >= 35) {
                $title = "Padrão Pessoal: {$category->name}";
                if (! self::alreadyNotified($user, $title)) {
                    $ratio = round(($spentCurrent / max(1, $personalAvg)) * 100);
                    AppNotification::create([
                        'user_id' => $user->id,
                        'workspace_id' => $workspace->id,
                        'title' => $title,
                        'message' => "Estás a gastar {$ratio}% do teu padrão médio em {$category->name} este mês.",
                        'type' => 'warning',
                        'link' => route('hub.category', $category->resolveSlug()),
                    ]);
                }
            }

            if ($groupPerMember > 0 && $spentCurrent > ($groupPerMember * 1.7) && ($spentCurrent - $groupPerMember) >= 40) {
                $title = "Padrão Grupo: {$category->name}";
                if (! self::alreadyNotified($user, $title)) {
                    $ratio = round(($spentCurrent / max(1, $groupPerMember)) * 100);
                    AppNotification::create([
                        'user_id' => $user->id,
                        'workspace_id' => $workspace->id,
                        'title' => $title,
                        'message' => "Os teus gastos em {$category->name} estão em {$ratio}% da média por membro do grupo.",
                        'type' => 'info',
                        'link' => route('hub.category', $category->resolveSlug()),
                    ]);
                }
            }
        }
    }

    public static function checkUpcomingPayments(User $user): void
    {
        $workspace = $user->currentWorkspace;
        if (! $workspace) {
            return;
        }

        $debts = Debt::where('workspace_id', $workspace->id)
            ->where('is_paid', false)
            ->whereBetween('due_at', [now(), now()->addDays(7)])
            ->get();

        foreach ($debts as $debt) {
            $daysLeft = now()->diffInDays(Carbon::parse($debt->due_at));
            $title = "Pagamento Próximo: {$debt->description}";
            if (! self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'title' => $title,
                    'message' => "Faltam {$daysLeft} dias para pagar ".number_format($debt->amount, 2, ',', '.').'€.',
                    'type' => 'warning',
                    'link' => route('hub.debts'),
                ]);
            }
        }

        $reminders = Reminder::where('workspace_id', $workspace->id)
            ->where('is_completed', false)
            ->whereBetween('remind_at', [now(), now()->addDays(3)])
            ->get();

        foreach ($reminders as $reminder) {
            $title = "Lembrete: {$reminder->title}";
            if (! self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'title' => $title,
                    'message' => 'Tens um lembrete para '.Carbon::parse($reminder->remind_at)->format('d/m H:i'),
                    'type' => 'info',
                    'link' => route('hub.reminders'),
                ]);
            }
        }
    }

    private static function alreadyNotified(User $user, string $title): bool
    {
        return AppNotification::where('user_id', $user->id)
            ->where('title', $title)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }
}
