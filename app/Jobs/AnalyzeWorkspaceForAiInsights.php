<?php

namespace App\Jobs;

use App\Models\Workspace;
use App\Services\AI\ProactiveAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class AnalyzeWorkspaceForAiInsights implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public int $workspaceId) {}

    public function middleware(): array
    {
        return [new WithoutOverlapping('ai-insights:workspace:'.$this->workspaceId)];
    }

    public function handle(ProactiveAiService $service): void
    {
        $workspace = Workspace::find($this->workspaceId);
        if (! $workspace) {
            return;
        }

        $service->analyze($workspace);
    }
}
