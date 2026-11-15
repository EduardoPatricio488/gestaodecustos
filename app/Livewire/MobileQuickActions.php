<?php

namespace App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class MobileQuickActions extends Component
{
    #[Locked]
    public array $loaded = [];

    #[On('mobile-load-action')]
    public function loadAction(string $action, ?array $rect = null): void
    {
        $event = match ($action) {
            'expense' => 'open-expense-category-picker',
            'income' => 'open-mobile-income-menu',
            'statement' => 'open-mobile-statement-import',
            'investment-upload' => 'open-mobile-investment-upload',
            'investment-create' => 'open-mobile-investment-create',
            'goal' => 'open-mobile-goal-create',
            'reminder' => 'open-mobile-reminder-create',
            'subscription' => 'open-mobile-subscription-create',
            default => abort(404),
        };

        $key = str_starts_with($action, 'investment-') ? 'investment' : $action;
        $this->loaded[$key] = true;

        if ($action === 'income') {
            abort_unless(
                is_array($rect)
                && isset($rect['left'], $rect['top'], $rect['bottom'])
                && is_numeric($rect['left'])
                && is_numeric($rect['top'])
                && is_numeric($rect['bottom']),
                422
            );
            $this->dispatch($event, rect: $rect);

            return;
        }

        $this->dispatch($event);
    }

    public function render()
    {
        return view('livewire.mobile-quick-actions');
    }
}
