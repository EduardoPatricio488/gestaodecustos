<?php

namespace App\Jobs;

use App\Models\Workspace;
use App\Services\AI\ProactiveAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class RunAiObserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 90;

    public function __construct(public int $workspaceId) {}

    public function middleware(): array
    {
        return [new WithoutOverlapping('ai-observer:workspace:'.$this->workspaceId)];
    }

    public function handle(ProactiveAiService $service): void
    {
        $workspace = Workspace::query()->find($this->workspaceId);
        if (! $workspace) {
            return;
        }

        $service->analyze($workspace);
    }
}
