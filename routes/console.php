<?php

use App\Models\User;
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
    User::query()
        ->whereNotNull('email_verified_at')
        ->each(function (User $user) {
            NotificationService::checkAll($user);
        });
})->dailyAt('08:30');
