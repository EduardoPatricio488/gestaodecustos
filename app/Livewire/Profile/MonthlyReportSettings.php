<?php

namespace App\Livewire\Profile;

use App\Services\DailyReportService;
use App\Services\MonthlyReportService;
use Livewire\Component;

class MonthlyReportSettings extends Component
{
    private const DAILY_REPORT_SECTIONS = ['earned', 'spent', 'balance', 'categories'];

    public bool $enabled = true;

    public int $day = 1;

    public bool $dailyEnabled = false;

    public array $dailySections = ['earned', 'spent', 'balance', 'categories'];

    public function mount(): void
    {
        $user = auth()->user();
        $this->enabled = (bool) ($user->monthly_report_enabled ?? true);
        $this->day = (int) ($user->monthly_report_day ?? 1);
        $this->dailyEnabled = (bool) ($user->daily_report_enabled ?? false);
        $this->dailySections = $user->daily_report_sections ?: self::DAILY_REPORT_SECTIONS;
    }

    public function saveSettings(): void
    {
        $this->validate([
            'enabled' => 'boolean',
            'day' => 'required|integer|min:1|max:28',
            'dailyEnabled' => 'boolean',
            'dailySections' => 'required|array|min:1',
            'dailySections.*' => 'in:'.implode(',', self::DAILY_REPORT_SECTIONS),
        ]);

        $user = auth()->user();
        $user->forceFill([
            'monthly_report_enabled' => $this->enabled,
            'monthly_report_day' => $this->day,
            'daily_report_enabled' => $this->dailyEnabled,
            'daily_report_sections' => $this->dailySections,
        ])->save();

        $user->awardXp(15, 'configuração guardada');
        $this->dispatch('toast', variant: 'success', text: $user->xpToastText(15, 'configuração guardada'));
    }

    public function sendTest(): void
    {
        $user = auth()->user();
        app(MonthlyReportService::class)->sendMonthlyReport($user, now()->subMonth());
        $user->awardXp(10, 'relatório mensal enviado');
        $this->dispatch('toast', variant: 'success', text: $user->xpToastText(10, 'relatório mensal enviado'));
    }

    public function sendDailyTest(): void
    {
        $user = auth()->user();
        app(DailyReportService::class)->sendDailyReport($user, now()->subDay());
        $user->awardXp(8, 'relatório diário enviado');
        $this->dispatch('toast', variant: 'success', text: $user->xpToastText(8, 'relatório diário enviado'));
    }

    public function render()
    {
        return view('livewire.profile.monthly-report-settings');
    }
}
