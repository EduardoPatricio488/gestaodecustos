<?php

namespace App\Services;

use App\Mail\MonthlyReportMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MonthlyReportService
{
    public function buildMonthlyData(User $user, Carbon $period): array
    {
        $workspaceId = $user->current_workspace_id;
        $month = (int) $period->month;
        $year = (int) $period->year;

        $spent = (float) $user->expenses()
            ->where('workspace_id', $workspaceId)
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('amount');

        $earnedOneOff = (float) $user->incomes()
            ->where('workspace_id', $workspaceId)
            ->whereMonth('received_at', $month)
            ->whereYear('received_at', $year)
            ->sum('amount');

        $earnedRecurring = (float) $user->recurringIncomes()
            ->where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get()
            ->sum(function ($r) {
                return match ($r->frequency) {
                    'semanal' => (float) $r->amount * 52 / 12,
                    'anual' => (float) $r->amount / 12,
                    default => (float) $r->amount,
                };
            });

        $earned = $earnedOneOff + $earnedRecurring;

        $categoryStats = $user->expenses()
            ->where('expenses.workspace_id', $workspaceId)
            ->whereMonth('expenses.spent_at', $month)
            ->whereYear('expenses.spent_at', $year)
            ->leftJoin('categories', 'expenses.category_id', '=', 'categories.id')
            ->selectRaw('COALESCE(categories.name, "Sem categoria") as name, SUM(expenses.amount) as total')
            ->groupBy('name')
            ->orderByDesc('total')
            ->get();

        return [
            'spent' => $spent,
            'earned' => $earned,
            'balance' => $earned - $spent,
            'categoryStats' => $categoryStats,
            'monthName' => $period->translatedFormat('F'),
            'monthNumber' => $month,
            'year' => $year,
        ];
    }

    public function sendMonthlyReport(User $user, Carbon $period): void
    {
        $data = $this->buildMonthlyData($user, $period);
        Mail::to($user->email)->send(new MonthlyReportMail($user, $data));
    }
}
