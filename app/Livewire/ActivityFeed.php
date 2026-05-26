<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ActivityFeed extends Component
{
    public function render()
    {
        return view('livewire.activity-feed', [
            'logs' => ActivityLog::with('user')->latest()->paginate(20)
        ]);
    }
}
