<?php

namespace App\Services\AI;

use App\Models\AiInsight;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class ProactiveAiService
{
    public function analyze(Workspace $workspace): array
    {
        $owner = $workspace->owner;
        if (! $owner) {
            return [];
        }

        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $candidates = $workspace->type === 'business' || $workspace->type === 'company'
            ? $this->businessCandidates($snapshot)
            : $this->personalCandidates($snapshot);

        $created = [];

        foreach ($candidates as $candidate) {
            if (! $this->shouldCreate($owner, $workspace, $candidate['dedupe_key'])) {
                continue;
            }

            $insight = AiInsight::create([
                'user_id' => $owner->id,
                'workspace_id' => $workspace->id,
                'type' => $candidate['type'],
                'priority' => $candidate['priority'],
                'title' => $candidate['title'],
                'message' => $candidate['message'],
                'category' => $candidate['category'],
                'source' => 'deterministic_ai_observer',
                'data' => $candidate['data'],
                'action' => $candidate['action'],
                'link' => $candidate['link'],
                'dedupe_key' => $candidate['dedupe_key'],
                'confidence' => $candidate['confidence'],
                'score' => $candidate['score'],
            ]);

            if (in_array($candidate['priority'], ['high', 'critical'], true)) {
                DB::table('app_notifications')->insert([
                    'user_id' => $owner->id,
                    'workspace_id' => $workspace->id,
                    'title' => $candidate['title'],
                    'message' => $candidate['message'],
                    'type' => $candidate['priority'] === 'critical' ? 'danger' : 'warning',
                    'link' => $candidate['link'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $created[] = $insight->id;
        }

        return $created;
    }

    private function personalCandidates(array $snapshot): array
    {
        $period = $snapshot['period'];
        $candidates = [];
        $expenseChange = $snapshot['changes']['expenses_percent'] ?? null;

        if ($expenseChange !== null && $expenseChange >= 20) {
            $candidates[] = $this->candidate(
                'anomaly', 'medium', 'Gastos acima do normal',
                sprintf('Os teus gastos estão %.1f%% acima do mês anterior.', $expenseChange),
                'spending', $period, 78, 82, ['hub' => 'expenses']
            );
        }

        if (($snapshot['savings_rate'] ?? 0) < 10 && ($snapshot['income'] ?? 0) > 0) {
            $candidates[] = $this->candidate(
                'risk', 'medium', 'Margem de poupança reduzida',
                sprintf('A taxa de poupança deste mês está em %.1f%%.', $snapshot['savings_rate']),
                'savings', $period, 84, 80, ['hub' => 'budget']
            );
        }

        foreach (($snapshot['goals'] ?? []) as $goal) {
            if (($goal['target'] ?? 0) > 0 && ($goal['progress_percent'] ?? 0) < 50 && ! empty($goal['deadline'])) {
                $deadline = now()->parse($goal['deadline']);
                if ($deadline->isFuture() && $deadline->diffInDays(now()) <= 60) {
                    $candidates[] = $this->candidate(
                        'goal_risk', 'high', 'Objetivo perto do prazo',
                        sprintf('O objetivo "%s" está a %.1f%% e tem prazo em %s.', $goal['name'], $goal['progress_percent'], $deadline->format('d/m/Y')),
                        'goals', $period, 92, 88, ['hub' => 'goals']
                    );
                }
            }
        }

        return $candidates;
    }

    private function businessCandidates(array $snapshot): array
    {
        $period = $snapshot['period'];
        $candidates = [];
        $marginPoints = $snapshot['changes']['margin_points'] ?? 0;
        $revenueChange = $snapshot['changes']['revenue_percent'] ?? null;

        if ($marginPoints <= -5) {
            $candidates[] = $this->candidate(
                'business_risk', 'high', 'Margem em queda',
                sprintf('A margem caiu %.1f pontos percentuais face ao mês anterior.', abs($marginPoints)),
                'margin', $period, 94, 92, ['hub' => 'business.pnl']
            );
        }

        if ($revenueChange !== null && $revenueChange <= -15) {
            $candidates[] = $this->candidate(
                'business_risk', 'high', 'Receita em queda',
                sprintf('A receita recebida caiu %.1f%% face ao mês anterior.', abs($revenueChange)),
                'revenue', $period, 94, 91, ['hub' => 'business.dashboard']
            );
        }

        if (($snapshot['metrics']['overdue_receivables'] ?? 0) > 0) {
            $candidates[] = $this->candidate(
                'collections', 'medium', 'Existem valores vencidos',
                sprintf('Existem %s em recebimentos vencidos ou em atraso.', $this->money($snapshot['metrics']['overdue_receivables'], $snapshot['currency'])),
                'receivables', $period, 97, 86, ['hub' => 'business.invoices']
            );
        }

        return $candidates;
    }

    private function candidate(string $type, string $priority, string $title, string $message, string $category, string $period, int $confidence, int $score, array $action): array
    {
        $link = match ($action['hub'] ?? null) {
            'expenses' => route('expenses'),
            'budget' => route('hub.budget'),
            'goals' => route('hub.goals'),
            'business.pnl' => route('hub.business.pnl'),
            'business.dashboard' => route('hub.business.dashboard'),
            'business.invoices' => route('hub.business.invoices'),
            default => route('dashboard'),
        };

        return [
            'type' => $type,
            'priority' => $priority,
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'confidence' => $confidence,
            'score' => $score,
            'data' => ['period' => $period, 'confidence_type' => 'deterministic'],
            'action' => ['type' => 'navigate', 'label' => 'Analisar', 'target' => $action['hub']],
            'link' => $link,
            'dedupe_key' => sha1(implode('|', [$type, $category, $period])),
        ];
    }

    private function shouldCreate(User $user, Workspace $workspace, string $dedupeKey): bool
    {
        return ! AiInsight::query()
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->where('dedupe_key', $dedupeKey)
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();
    }

    private function money(float $amount, string $currency): string
    {
        $symbols = ['EUR' => '€', 'USD' => '$', 'GBP' => '£', 'CHF' => 'CHF'];
        $symbol = $symbols[strtoupper($currency)] ?? strtoupper($currency);
        return number_format($amount, 2, ',', ' ').' '.$symbol;
    }
}
