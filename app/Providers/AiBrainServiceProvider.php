<?php

namespace App\Providers;

use App\Jobs\AnalyzeWorkspaceForAiInsights;
use App\Livewire\AiCopilot;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AiBrainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // AI Brain services are resolved through Laravel's container.
    }

    public function boot(): void
    {
        Livewire::component('finance-bot', AiCopilot::class);

        foreach ([Expense::class, Income::class, Goal::class, Investment::class, Subscription::class, Invoice::class, BankAccount::class] as $model) {
            $model::created(function ($record): void {
                $this->queueAnalysis($record->workspace_id ?? null);
            });

            $model::updated(function ($record): void {
                $this->queueAnalysis($record->workspace_id ?? null);
            });
        }
    }

    private function queueAnalysis(?int $workspaceId): void
    {
        if (! $workspaceId) {
            return;
        }

        AnalyzeWorkspaceForAiInsights::dispatch($workspaceId)->afterCommit();
    }
}
