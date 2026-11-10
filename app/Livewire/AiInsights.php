<?php

namespace App\Livewire;

use App\Mail\CfoReportMail;
use App\Services\AI\AiBrainService;
use App\Services\AI\ContextEngine;
use App\Services\AI\FinancialHealthScoreService;
use App\Services\AI\FinancialIntelligenceService;
use App\Services\FinanceScoreService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AiInsights extends Component
{
    public bool $isAnalyzing = false;

    public string $aiAnalysis = '';

    public ?string $lastGeneratedAt = null;

    public function mount(): void
    {
        $cached = Cache::get($this->cacheKey());
        if ($cached) {
            $this->aiAnalysis = $cached['text'] ?? '';
            $this->lastGeneratedAt = $cached['at'] ?? null;
        }
    }

    private function cacheKey(): string
    {
        $workspaceId = auth()->user()?->current_workspace_id ?? 'none';

        return 'ai-insights:'.auth()->id().':workspace:'.$workspaceId;
    }

    private function xpKey(int $workspaceId): string
    {
        return 'ai-insights:xp:'.auth()->id().':workspace:'.$workspaceId.':'.now()->toDateString();
    }

    public function generateInsights(AiBrainService $brain): void
    {
        set_time_limit(120);
        $this->isAnalyzing = true;
        $user = auth()->user();
        $workspace = app(ContextEngine::class)->resolveWorkspace($user);

        if (! $workspace) {
            $this->aiAnalysis = 'Não existe um workspace ativo para analisar.';
            $this->isAnalyzing = false;

            return;
        }

        try {
            $result = $brain->generateExecutiveReport($user, $workspace);
            $this->aiAnalysis = $result['content'] ?? 'Não foi possível gerar o diagnóstico.';
            $this->lastGeneratedAt = now()->toIso8601String();
            Cache::put($this->cacheKey(), ['text' => $this->aiAnalysis, 'at' => $this->lastGeneratedAt], now()->addDays(7));

            if (method_exists($user, 'addXp') && Cache::add($this->xpKey($workspace->id), true, now()->endOfDay())) {
                $user->addXp(150);
            }

            if ($user->email) {
                $mailUser = $user;
                $mailAnalysis = $this->aiAnalysis;
                $mailSnapshot = $result['snapshot'] ?? [];
                $mailHealthScore = app(FinancialHealthScoreService::class)->personal($workspace)['score'];

                // Envia o email depois de devolver o relatório à página, sem bloquear o pedido Livewire.
                app()->terminating(function () use ($mailUser, $mailAnalysis, $mailSnapshot, $mailHealthScore): void {
                    try {
                        Mail::to($mailUser->email)->send(new CfoReportMail($mailUser, $mailAnalysis, [
                            'earned' => (float) data_get($mailSnapshot, 'income', 0),
                            'spent' => (float) data_get($mailSnapshot, 'expenses', 0),
                            'healthScore' => $mailHealthScore,
                        ]));
                    } catch (\Throwable $mailException) {
                        Log::warning('CfoReportMail failed', ['message' => $mailException->getMessage()]);
                    }
                });

                $this->dispatch('toast', variant: 'success', text: 'Relatório gerado! O envio do email será processado em segundo plano. 📧');
            }
        } catch (\Throwable $e) {
            report($e);
            $this->aiAnalysis = 'Não consegui concluir esta análise neste momento. Os teus dados não foram inventados nem alterados.';
            $this->dispatch('toast', variant: 'error', text: 'O AI Brain não está disponível neste momento.');
        } finally {
            $this->isAnalyzing = false;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $workspace = app(ContextEngine::class)->resolveWorkspace($user);
        $snapshot = $workspace ? app(FinancialIntelligenceService::class)->snapshot($workspace) : [];
        $earned = (float) data_get($snapshot, 'income', 0);
        $spent = (float) data_get($snapshot, 'expenses', 0);
        $previous = (array) data_get($snapshot, 'previous', []);
        $changes = (array) data_get($snapshot, 'changes', []);
        $netWorth = $workspace ? (float) $workspace->getLiquidezAtual() + (float) data_get($snapshot, 'investment_value', 0) : 0;
        $healthScore = $workspace ? app(FinancialHealthScoreService::class)->personal($workspace)['score'] : 0;

        $previousHealthScore = null;
        if ($workspace) {
            $previousHealthScore = app(FinanceScoreService::class)->calculate(
                $workspace,
                now()->copy()->subMonth()
            )['score'] ?? null;
        }

        $manualInsights = [];
        if ($earned > 0 && $spent > $earned) {
            $manualInsights[] = ['type' => 'danger', 'icon' => 'arrow-trending-down', 'title' => 'Saldo Negativo', 'text' => 'Estás a gastar mais do que o rendimento registado neste período.'];
        }
        if ($earned > 0 && ($spent / $earned) > 0.9) {
            $manualInsights[] = ['type' => 'warning', 'icon' => 'bell', 'title' => 'Margem Crítica', 'text' => 'Mais de 90% do rendimento registado está comprometido com gastos.'];
        }

        return view('livewire.ai-intelligence-page', [
            'totalEarned' => $earned,
            'totalSpent' => $spent,
            'netWorth' => $netWorth,
            'healthScore' => $healthScore,
            'healthScoreDelta' => $previousHealthScore !== null ? $healthScore - (int) $previousHealthScore : null,
            'earnedDelta' => data_get($changes, 'income_percent'),
            'spentDelta' => data_get($changes, 'expenses_percent'),
            'netWorthDelta' => null,
            'insights' => $manualInsights,
            'reportGeneratedAt' => $this->lastGeneratedAt ? Carbon::parse($this->lastGeneratedAt) : null,
        ]);
    }
}
