<?php

use App\Jobs\RunAiObserver;
use App\Models\User;
use App\Models\Workspace;
use App\Services\DailyReportService;
use App\Services\MonthlyReportService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $service = app(MonthlyReportService::class);
    $lastMonth = now()->subMonth();
    $todayDay = (int) now()->day;

    $users = User::query()
        ->whereNotNull('email_verified_at')
        ->where('monthly_report_enabled', true)
        ->where('monthly_report_day', $todayDay)
        ->get();

    foreach ($users as $user) {
        $service->sendMonthlyReport($user, $lastMonth);
    }
})->dailyAt('08:00');

Schedule::call(function () {
    $service = app(DailyReportService::class);
    $reportDate = now()->subDay();

    User::query()
        ->whereNotNull('email_verified_at')
        ->where('daily_report_enabled', true)
        ->each(fn (User $user) => $service->sendDailyReport($user, $reportDate));
})->dailyAt('00:00');

Schedule::call(function () {
    User::query()
        ->whereNotNull('email_verified_at')
        ->each(function (User $user) {
            NotificationService::checkAll($user);
        });
})->dailyAt('08:30');

// AI Observer: deterministic analysis runs asynchronously and is deduplicated by AiInsight.
// Uses the existing default queue worker instead of introducing a new worker requirement.
Schedule::call(function () {
    Workspace::query()
        ->select('id')
        ->orderBy('id')
        ->chunkById(100, function ($workspaces) {
            foreach ($workspaces as $workspace) {
                RunAiObserver::dispatch($workspace->id);
            }
        });
})->hourly();