<?php

namespace App\Providers;

use App\Livewire\AiCopilot;
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
        // Keep the existing finance-bot alias stable while replacing its implementation
        // with the new central Copilot. The old FinanceBot class remains in the codebase
        // for rollback/reference during the migration.
        Livewire::component('finance-bot', AiCopilot::class);
    }
}
