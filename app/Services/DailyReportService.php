<?php

namespace App\Services;

use App\Mail\DailyReportMail;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Mail;

class DailyReportService
{
    public function buildDailyData(User $user, CarbonInterface $date): array
    {
        $workspaceId = $user->current_workspace_id;
        $reportDate = $date->startOfDay();

        $spent = (float) $user->expenses()
            ->where('workspace_id', $workspaceId)
            ->whereDate('spent_at', $reportDate)
            ->sum('amount');

        $earned = (float) $user->incomes()
            ->where('workspace_id', $workspaceId)
            ->whereDate('received_at', $reportDate)
            ->sum('amount');

        $categoryStats = $user->expenses()
            ->where('expenses.workspace_id', $workspaceId)
            ->whereDate('expenses.spent_at', $reportDate)
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
            'date' => $reportDate,
            'sections' => $user->daily_report_sections ?: ['earned', 'spent', 'balance', 'categories'],
        ];
    }

    public function sendDailyReport(User $user, CarbonInterface $date): void
    {
        Mail::to($user->email)->send(new DailyReportMail($user, $this->buildDailyData($user, $date)));
    }
}
