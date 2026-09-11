<?php

use App\Jobs\RunAiObserver;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AI\AiReviewService;
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

// AI reviews are deterministic backend summaries; they do not spend model tokens.
Schedule::call(function () {
    $service = app(AiReviewService::class);
    Workspace::query()->select('id')->chunkById(100, function ($workspaces) use ($service) {
        foreach ($workspaces as $workspace) {
            $service->weekly($workspace);
        }
    });
})->weeklyOn(1, '08:15');

Schedule::call(function () {
    $service = app(AiReviewService::class);
    Workspace::query()->select('id')->chunkById(100, function ($workspaces) use ($service) {
        foreach ($workspaces as $workspace) {
            $service->monthly($workspace);
        }
    });
})->monthlyOn(1, '08:20');
