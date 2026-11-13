<?php

namespace App\Livewire;

use App\Exceptions\AiProviderException;
use App\Mail\CfoReportMail;
use App\Models\AiActionLog;
use App\Models\AiMemory;
use App\Models\User;
use App\Services\AI\AiBrainService;
use App\Services\AI\ContextEngine;
use App\Services\AI\FinancialHealthScoreService;
use App\Services\AI\FinancialIntelligenceService;
use App\Services\FinanceScoreService;
use App\Services\OpenRouterQuotaService;
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

    public ?string $analysisError = null;

    public ?string $lastGeneratedAt = null;

    public ?array $aiQuota = null;

    public bool $reportNeedsReview = false;

    public function mount(): void
    {
        $cached = Cache::get($this->cacheKey());
        if ($cached) {
            $text = $cached['text'] ?? '';
            if (app(AiBrainService::class)->isClearlyCorruptedReport($text)) {
                $this->analysisError = 'O diagnóstico guardado está ilegível. Gera um novo relatório.';
            } else {
                $workspace = app(ContextEngine::class)->resolveWorkspace(auth()->user());
                if ($workspace?->type === 'personal') {
                    $text = preg_replace('/^([ \t]*(?:\*\*|#{1,6}[ \t]+))O que aconselha(?=:?(?:\*\*)?[ \t]*$)/imu', '${1}O que aconselho', $text);
                }
                $this->aiAnalysis = $text;
                $this->lastGeneratedAt = $cached['at'] ?? null;
                $this->reportNeedsReview = $workspace?->type === 'personal' && ($cached['numeric_check_version'] ?? 0) < 1;
            }
        }
    }

    private function cacheKey(): string
    {
        return self::reportCacheKey(auth()->user());
    }

    public static function reportCacheKey(User $user): string
    {
        return 'ai-insights:'.$user->id.':workspace:'.($user->current_workspace_id ?? 'none');
    }

    private function xpKey(int $workspaceId): string
    {
        return 'ai-insights:xp:'.auth()->id().':workspace:'.$workspaceId.':'.now()->toDateString();
    }

    public function refreshAiQuota(OpenRouterQuotaService $quotaService): void
    {
        $this->aiQuota = $quotaService->dailyQuota(true);
    }

    public function generateInsights(AiBrainService $brain): void
    {
        set_time_limit(390);
        $this->isAnalyzing = true;
        $this->analysisError = null;
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
            $this->reportNeedsReview = false;
            Cache::put($this->cacheKey(), [
                'text' => $this->aiAnalysis,
                'at' => $this->lastGeneratedAt,
                'numeric_check_version' => $workspace->type === 'personal' ? 1 : 0,
            ], now()->addDays(7));

            // Regista a análise no Action Center: a geração do relatório é uma ação concluída da IA.
            AiActionLog::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'tool_name' => 'generate_executive_report',
                'action_type' => 'analysis',
                'status' => 'completed',
                'request_payload' => ['period' => now()->format('Y-m')],
                'result_payload' => [
                    'title' => 'Diagnóstico CFO gerado',
                    'summary' => 'Relatório executivo financeiro criado com os dados disponíveis no workspace.',
                    'tokens' => (int) ($result['tokens'] ?? 0),
                ],
                'completed_at' => now(),
            ]);

            // Guarda um snapshot factual e atualizado na memória persistente da IA.
            // Só utiliza números fornecidos pelo backend, nunca conclusões inventadas pelo modelo.
            $financialSnapshot = (array) ($result['snapshot'] ?? []);
            $memoryValue = [
                'schema_version' => 2,
                'period' => $financialSnapshot['period'] ?? now()->format('Y-m'),
                'currency' => $financialSnapshot['currency'] ?? 'EUR',
                'workspace_kind' => $financialSnapshot['kind'] ?? 'personal',
                'income' => [
                    'recurring' => (float) ($financialSnapshot['income_recurring'] ?? 0),
                    'actual' => (float) ($financialSnapshot['income_actual'] ?? $financialSnapshot['income'] ?? 0),
                    'projected' => (float) ($financialSnapshot['income_projected'] ?? $financialSnapshot['income'] ?? 0),
                ],
                'expenses' => [
                    'actual' => (float) ($financialSnapshot['expenses'] ?? 0),
                    'top_categories' => (array) ($financialSnapshot['top_categories'] ?? []),
                ],
                'savings' => [
                    'actual' => (float) ($financialSnapshot['savings_actual'] ?? 0),
                    'projected' => (float) ($financialSnapshot['savings_projected'] ?? $financialSnapshot['savings'] ?? 0),
                    'actual_rate' => (float) ($financialSnapshot['savings_rate_actual'] ?? 0),
                    'projected_rate' => (float) ($financialSnapshot['savings_rate'] ?? 0),
                ],
                'investments' => [
                    'current_value' => (float) ($financialSnapshot['investment_value'] ?? 0),
                    'invested_cost' => (float) ($financialSnapshot['investment_cost'] ?? 0),
                    'profit_loss' => (float) ($financialSnapshot['investment_profit_loss'] ?? 0),
                    'profit_loss_percent' => $financialSnapshot['investment_profit_loss_percent'] ?? null,
                    'holdings' => array_slice((array) ($financialSnapshot['investments'] ?? []), 0, 50),
                ],
                'budget' => (array) ($financialSnapshot['budget'] ?? []),
                'goals' => (array) ($financialSnapshot['goals'] ?? []),
                'subscriptions' => [
                    'active_count' => (int) ($financialSnapshot['active_subscriptions'] ?? 0),
                    'monthly_cost' => (float) ($financialSnapshot['active_subscription_cost'] ?? 0),
                ],
                'comparison' => [
                    'previous' => (array) ($financialSnapshot['previous'] ?? []),
                    'changes' => (array) ($financialSnapshot['changes'] ?? []),
                ],
                'data_quality' => $financialSnapshot['data_quality'] ?? 'deterministic_backend_calculation',
                'updated_at' => now()->toIso8601String(),
                'source' => 'cfo_executive_report_snapshot',
            ];

            AiMemory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'type' => 'financial_context',
                    'key' => 'cfo_snapshot_'.str_replace('-', '_', (string) $memoryValue['period']),
                ],
                [
                    'value' => $memoryValue,
                    'importance' => 85,
                    'is_active' => true,
                    'last_used_at' => now(),
                    'expires_at' => now()->addMonths(6),
                ]
            );

            // Atualiza o Insight Center com alertas determinísticos relevantes para este snapshot.
            try {
                app(\App\Services\AI\ProactiveAiService::class)->analyze($workspace);
            } catch (\Throwable $observerException) {
                Log::error('CFO report observer failed', [
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'message' => $observerException->getMessage(),
                ]);
            }

            $this->dispatch('cfo-report-generated');

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
            $this->analysisError = $e instanceof AiProviderException
                ? $e->getMessage()
                : 'Não consegui concluir esta análise neste momento. Os teus dados não foram inventados nem alterados.';
        } finally {
            $this->isAnalyzing = false;
            $this->dispatch('cfo-report-finished');
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

        return view('livewire.ai-intelligence-page', [
            'totalEarned' => $earned,
            'totalSpent' => $spent,
            'netWorth' => $netWorth,
            'healthScore' => $healthScore,
            'healthScoreDelta' => $previousHealthScore !== null ? $healthScore - (int) $previousHealthScore : null,
            'earnedDelta' => data_get($changes, 'income_percent'),
            'spentDelta' => data_get($changes, 'expenses_percent'),
            'netWorthDelta' => null,
            'reportGeneratedAt' => $this->lastGeneratedAt ? Carbon::parse($this->lastGeneratedAt) : null,
        ]);
    }
}
