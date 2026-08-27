<?php

namespace App\Livewire\Profile;

use App\Services\MonthlyReportService;
use Livewire\Component;

class MonthlyReportSettings extends Component
{
    public bool $enabled = true;

    public int $day = 1;

    public function mount(): void
    {
        $user = auth()->user();
        $this->enabled = (bool) ($user->monthly_report_enabled ?? true);
        $this->day = (int) ($user->monthly_report_day ?? 1);
    }

    public function saveSettings(): void
    {
        $this->validate([
            'enabled' => 'boolean',
            'day' => 'required|integer|min:1|max:28',
        ]);

        $user = auth()->user();
        $user->forceFill([
            'monthly_report_enabled' => $this->enabled,
            'monthly_report_day' => $this->day,
        ])->save();

        $this->dispatch('toast', text: 'Preferências do relatório mensal guardadas!');
    }

    public function sendTest(): void
    {
        app(MonthlyReportService::class)->sendMonthlyReport(auth()->user(), now()->subMonth());
        $this->dispatch('toast', text: 'Relatório de teste enviado para o teu email!');
    }

    public function render()
    {
        return view('livewire.profile.monthly-report-settings');
    }
}
