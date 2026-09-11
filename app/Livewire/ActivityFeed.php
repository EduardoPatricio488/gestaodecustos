<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Services\BusinessAccessService;
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

        $workspace = $user->workspaces()->whereKey($workspaceId)->first();
        if ($workspace && in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('view_audit', $user, $workspace);
        }

        return view('livewire.activity-feed', [
            'logs' => ActivityLog::with('user')
                ->where('workspace_id', $workspaceId)
                ->latest()
                ->paginate(20),
        ]);
    }
}
