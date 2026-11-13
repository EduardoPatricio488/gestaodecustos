<?php

namespace App\Livewire;

use App\Models\AiActionLog;
use App\Models\AiInsight;
use App\Services\AI\AiBrainService;
use App\Services\AI\ContextEngine;
use App\Services\AI\FinancialHealthScoreService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AiControlCenter extends Component
{
    public string $insightFilter = 'all';

    public string $insightCategory = 'all';

    public ?string $insightActionError = null;

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
        // O evento força a atualização dos insights e dos contadores.
    }

    public function markRead(int $id): void
    {
        $updated = $this->insightQuery()->visible()->whereNull('read_at')->whereKey($id)->update(['read_at' => now()]);
        $this->insightActionError = $updated ? null : 'O alerta já não está disponível ou já foi lido.';
        if ($updated) {
            $this->dispatch('toast', variant: 'success', text: 'Alerta marcado como lido.');
        }
    }

    public function resetInsightFilters(): void
    {
        $this->insightFilter = 'all';
        $this->insightCategory = 'all';
    }

    public function showUnreadInsights(): void
    {
        $this->insightFilter = 'unread';
        $this->insightCategory = 'all';
    }

    public function dismiss(int $id): void
    {
        $updated = $this->insightQuery()->visible()->whereKey($id)->update(['dismissed_at' => now()]);
        $this->insightActionError = $updated ? null : 'O alerta já não está disponível.';
        if ($updated) {
            $this->dispatch('toast', variant: 'success', text: 'Alerta dispensado.');
        }
    }

    public function feedback(int $id, string $value): void
    {
        if (! in_array($value, ['useful', 'not_useful', 'more'], true)) {
            throw ValidationException::withMessages(['feedback' => 'Escolhe uma avaliação válida.']);
        }

        $insight = $this->insightQuery()->visible()->whereKey($id)->first();
        if (! $insight) {
            $this->insightActionError = 'O alerta já não está disponível.';

            return;
        }
        $data = $insight->data ?? [];
        $data['feedback'] = $value;
        $data['feedback_at'] = now()->toIso8601String();
        $insight->update(['data' => $data]);
        $this->insightActionError = null;
        $this->dispatch('toast', variant: 'success', text: 'Obrigado pelo teu feedback.');
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

        $reportSections = null;
        $reportStatus = 'missing';
        $reportNeedsReview = false;
        $cachedReport = $workspace ? Cache::get(AiInsights::reportCacheKey(auth()->user())) : null;
        if ($cachedReport) {
            $text = data_get($cachedReport, 'text');
            $brain = app(AiBrainService::class);
            $reportStatus = 'unavailable';
            if ($workspace->type === 'personal' && is_string($text) && ! $brain->isClearlyCorruptedReport($text)) {
                [$reportSections] = $brain->personalReportSections($text);
                $reportNeedsReview = ($cachedReport['numeric_check_version'] ?? 0) < 1;
            }
        }

        $stats = [
            'unread' => $insights->whereNull('read_at')->count(),
            'critical' => $insights->whereNull('read_at')->whereIn('priority', ['critical', 'high'])->count(),
            'pending_actions' => $actions->whereIn('status', ['pending', 'awaiting_confirmation'])->count(),
            'completed_actions' => $actions->where('status', 'completed')->count(),
            'failed_actions' => $actions->where('status', 'failed')->count(),
        ];

        $visibleInsights = $insights
            ->when($this->insightFilter === 'unread', fn ($items) => $items->whereNull('read_at'))
            ->when($this->insightFilter === 'important', fn ($items) => $items->whereIn('priority', ['high', 'critical']))
            ->when($this->insightCategory !== 'all', fn ($items) => $items->where('category', $this->insightCategory));

        return view('livewire.ai-control-center', compact('health', 'insights', 'visibleInsights', 'actions', 'workspace', 'stats', 'reportSections', 'reportStatus', 'reportNeedsReview'));
    }
}
