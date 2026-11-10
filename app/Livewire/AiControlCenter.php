<?php

namespace App\Livewire;

use App\Models\AiActionLog;
use App\Models\AiInsight;
use App\Models\AiMemory;
use App\Services\AI\ContextEngine;
use App\Services\AI\FinancialHealthScoreService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AiControlCenter extends Component
{
    public string $feedback = '';

    public function mount(): void
    {
        $workspace = app(ContextEngine::class)->resolveWorkspace(auth()->user());

        if (! $workspace) {
            return;
        }

        // Garante que os insights elegíveis também são criados ao abrir o Centro,
        // mesmo que o diagnóstico CFO tenha sido gerado antes desta atualização.
        try {
            app(\App\Services\AI\ProactiveAiService::class)->analyze($workspace);
        } catch (\Throwable $exception) {
            Log::error('AI Control Center observer failed', [
                'user_id' => auth()->id(),
                'workspace_id' => $workspace->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    #[On('cfo-report-generated')]
    public function refreshAfterCfoReport(): void
    {
        // O evento força a atualização do diagnóstico, dos insights e dos contadores.
    }

    public function markRead(int $id): void
    {
        $this->insightQuery()->whereKey($id)->update(['read_at' => now()]);
    }

    public function dismiss(int $id): void
    {
        $this->insightQuery()->whereKey($id)->update(['dismissed_at' => now()]);
    }

    public function feedback(int $id, string $value): void
    {
        if (! in_array($value, ['useful', 'not_useful', 'more'], true)) {
            return;
        }

        $insight = $this->insightQuery()->whereKey($id)->firstOrFail();
        $data = $insight->data ?? [];
        $data['feedback'] = $value;
        $data['feedback_at'] = now()->toIso8601String();
        $insight->update(['data' => $data]);
        $this->feedback = $value;
        $this->dispatch('toast', variant: 'success', text: 'Obrigado pelo teu feedback. A IA vai ter isso em conta.');
    }

    public function forgetMemory(int $id): void
    {
        AiMemory::query()->whereKey($id)->where('user_id', auth()->id())->delete();
        $this->dispatch('toast', variant: 'success', text: 'Memória apagada.');
    }

    public function clearMemories(): void
    {
        AiMemory::query()->where('user_id', auth()->id())->delete();
        $this->dispatch('toast', variant: 'success', text: 'Toda a memória da IA foi apagada.');
    }

    private function insightQuery()
    {
        $workspaceId = app(ContextEngine::class)->resolveWorkspace(auth()->user())?->id;

        return AiInsight::query()
            ->where('user_id', auth()->id())
            ->where('workspace_id', $workspaceId);
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
            ? $this->insightQuery()->visible()->latest()->limit(30)->get()
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

        $latestDiagnosis = $workspace
            ? Cache::get('ai-insights:'.auth()->id().':workspace:'.(auth()->user()?->current_workspace_id ?? 'none'))
            : null;

        $stats = [
            'unread' => $insights->whereNull('read_at')->count(),
            'critical' => $insights->whereIn('priority', ['critical', 'high'])->count(),
            'pending_actions' => $actions->whereIn('status', ['pending', 'awaiting_confirmation'])->count(),
            'completed_actions' => $actions->where('status', 'completed')->count(),
        ];

        return view('livewire.ai-control-center', compact('health', 'insights', 'actions', 'memories', 'workspace', 'stats', 'latestDiagnosis'));
    }
}
