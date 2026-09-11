<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ActivityFeed extends Component
{
    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user?->current_workspace_id;

        abort_unless($workspaceId && $user->workspaces()->whereKey($workspaceId)->exists(), 403);

        return view('livewire.activity-feed', [
            'logs' => ActivityLog::with('user')
                ->where('workspace_id', $workspaceId)
                ->latest()
                ->paginate(20),
        ]);
    }
}
