<?php

namespace App\Services\AI;

use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class AiReviewService
{
    public function weekly(Workspace $workspace): void
    {
        $this->publish($workspace, 'weekly');
    }

    public function monthly(Workspace $workspace): void
    {
        $this->publish($workspace, 'monthly');
    }

    private function publish(Workspace $workspace, string $period): void
    {
        $owner = $workspace->owner;
        if (! $owner) {
            return;
        }

        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $health = $workspace->type === 'business'
            ? app(FinancialHealthScoreService::class)->business($workspace)
            : app(FinancialHealthScoreService::class)->personal($workspace);

        $title = $period === 'weekly' ? 'Revisão semanal Finance Pro AI' : 'Revisão mensal Finance Pro AI';
        $dedupe = sha1($period.'|'.$workspace->id.'|'.now()->format($period === 'weekly' ? 'o-W' : 'Y-m'));
        $message = $this->message($snapshot, $health, $period).' [AI-REVIEW:'.$dedupe.']';

        if (DB::table('app_notifications')->where('user_id', $owner->id)->where('workspace_id', $workspace->id)->where('type', 'ai_review')->where('message', 'like', '%[AI-REVIEW:'.$dedupe.']%')->exists()) {
            return;
        }

        DB::table('app_notifications')->insert([
            'user_id' => $owner->id,
            'workspace_id' => $workspace->id,
            'title' => $title,
            'message' => $message,
            'type' => 'ai_review',
            'link' => $workspace->type === 'business' ? route('hub.business.ai') : route('ai'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function message(array $snapshot, array $health, string $period): string
    {
        if (($snapshot['kind'] ?? 'personal') === 'business') {
            $metrics = $snapshot['metrics'] ?? [];

            return sprintf('%s: saúde %d/100. Receita recebida %s, custos %s, margem %s%% e valores vencidos %s. Dados calculados pelo backend.',
                $period === 'weekly' ? 'Resumo da semana' : 'Resumo do mês',
                $health['score'],
                $this->money($metrics['revenue_cash'] ?? 0, $snapshot['currency'] ?? 'EUR'),
                $this->money($metrics['total_costs'] ?? 0, $snapshot['currency'] ?? 'EUR'),
                number_format((float) ($metrics['margin'] ?? 0), 1, ',', ' '),
                $this->money($metrics['overdue_receivables'] ?? 0, $snapshot['currency'] ?? 'EUR'),
            );
        }

        return sprintf('%s: saúde %d/100. Rendimento %s, gastos %s, poupança %s e taxa de poupança %s%%. Dados calculados pelo backend.',
            $period === 'weekly' ? 'Resumo da semana' : 'Resumo do mês',
            $health['score'],
            $this->money($snapshot['income'] ?? 0, $snapshot['currency'] ?? 'EUR'),
            $this->money($snapshot['expenses'] ?? 0, $snapshot['currency'] ?? 'EUR'),
            $this->money($snapshot['savings'] ?? 0, $snapshot['currency'] ?? 'EUR'),
            number_format((float) ($snapshot['savings_rate'] ?? 0), 1, ',', ' '),
        );
    }

    private function money(float $amount, string $currency): string
    {
        $symbols = ['EUR' => '€', 'USD' => '$', 'GBP' => '£', 'CHF' => 'CHF'];

        return number_format($amount, 2, ',', ' ').' '.($symbols[strtoupper($currency)] ?? strtoupper($currency));
    }
}
