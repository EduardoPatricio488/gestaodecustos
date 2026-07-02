<?php

namespace App\Livewire\Business;

use App\Services\CashFlowForecastService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CashFlowHub extends Component
{
    public int $forecastDays = 90;

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $forecast = app(CashFlowForecastService::class)->getForecast($workspace, $this->forecastDays);

        return view('livewire.business.cash-flow-hub', compact('forecast'));
    }
}
