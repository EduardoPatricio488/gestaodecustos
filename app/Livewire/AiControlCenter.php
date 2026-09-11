<?php

namespace App\Livewire;

use App\Models\AiActionLog;
use App\Models\AiInsight;
use App\Models\AiMemory;
use App\Services\AI\ContextEngine;
use App\Services\AI\FinancialHealthScoreService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AiControlCenter extends Component
{
    public string $period = 'month';
    public string $feedback = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', AiInsight::class);
    }

    public function markRead(int $id): void
    {
        AiInsight::query()
            ->whereKey($id)
            ->where('user_id', auth()->id())
            ->where('workspace_id', app(ContextEngine::class)->resolveWorkspace(auth()->user())?->id)
            ->update(['read_at' => now()]);
    }

    public function dismiss(int $id): void
    {
        AiInsight::query()
            ->whereKey($id)
            ->where('user_id', auth()->id())
            ->where('workspace_id', app(ContextEngine::class)->resolveWorkspace(auth()->user())?->id)
            ->update(['dismissed_at' => now()]);
    }

    public function feedback(int $id, string $value): void
    {
        if (! in_array($value, ['useful', 'not_useful', 'more'], true)) {
            return;
        }

        $insight = AiInsight::query()
            ->whereKey($id)
            ->where('user_id', auth()->id())
            ->where('workspace_id', app(ContextEngine::class)->resolveWorkspace(auth()->user())?->id)
            ->firstOrFail();

        $data = $insight->data ?? [];
        $data['feedback'] = $value;
        $data['feedback_at'] = now()->toIso8601String();
        $insight->update(['data' => $data]);
        $this->feedback = $value;
    }

    public function forgetMemory(int $id): void
    {
        AiMemory::query()
            ->whereKey($id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function clearMemories(): void
    {
        AiMemory::query()->where('user_id', auth()->id())->delete();
    }

    public function render()
    {
        $workspace = app(ContextEngine::class)->resolveWorkspace(auth()->user());
        $health = $workspace
            ? ($workspace->type === 'business'
                ? app(FinancialHealthScoreService::class)->business($workspace)
                : app(FinancialHealthScoreService::class)->personal($workspace))
            : ['score' => 0, 'label' => 'Sem dados suficientes'];

        $insights = $workspace
            ? AiInsight::query()->visible()->where('user_id', auth()->id())->where('workspace_id', $workspace->id)->latest()->limit(30)->get()
            : collect();

        $actions = $workspace
            ? AiActionLog::query()->where('user_id', auth()->id())->where('workspace_id', $workspace->id)->latest()->limit(20)->get()
            : collect();

        $memories = AiMemory::query()->active()->where('user_id', auth()->id())->where(function ($q) use ($workspace) {
            $q->whereNull('workspace_id');
            if ($workspace) {
                $q->orWhere('workspace_id', $workspace->id);
            }
        })->latest()->limit(30)->get();

        return view('livewire.ai-control-center', compact('health', 'insights', 'actions', 'memories', 'workspace'));
    }
}
