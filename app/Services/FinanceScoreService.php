<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\FinanceScoreSnapshot;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Workspace;
use Carbon\CarbonInterface;

class FinanceScoreService
{
    public function calculate(Workspace $workspace, ?CarbonInterface $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $earned = (float) Income::where('workspace_id', $workspace->id)
            ->whereBetween('received_at', [$start, $end])
            ->sum('amount');

        $spent = (float) Expense::where('workspace_id', $workspace->id)
            ->where('is_company', false)
            ->whereBetween('spent_at', [$start, $end])
            ->sum('amount');

        $savingsRate = $earned > 0 ? max(0, (($earned - $spent) / $earned) * 100) : 0;
        $savingsScore = min(100, $savingsRate * 2);

        $totalDebt = (float) Debt::where('workspace_id', $workspace->id)
            ->where('is_paid', false)
            ->sum('amount');
        $debtRatio = $earned > 0 ? ($totalDebt / ($earned * 12)) * 100 : ($totalDebt > 0 ? 100 : 0);
        $debtScore = max(0, 100 - min(100, $debtRatio));

        $budget = (float) Category::where('workspace_id', $workspace->id)->sum('budget_limit');
        $budgetAdherence = $budget > 0 ? max(0, (1 - min($spent, $budget) / $budget) * 100) : 70;
        $budgetScore = $budget > 0 ? $budgetAdherence : 70;

        $goals = Goal::where('workspace_id', $workspace->id)->get();
        $goalsScore = 70;
        if ($goals->isNotEmpty()) {
            $avgProgress = $goals->avg(fn ($g) => $g->target_amount > 0
                ? min(100, ($g->current_amount / $g->target_amount) * 100)
                : 0);
            $goalsScore = (float) $avgProgress;
        }

        $investments = Investment::where('workspace_id', $workspace->id)->get();
        $diversificationScore = 50;
        if ($investments->isNotEmpty()) {
            $types = $investments->pluck('product_type')->filter()->unique()->count();
            $diversificationScore = min(100, 30 + ($types * 20) + min(30, $investments->count() * 5));
        }

        $score = (int) round(
            ($savingsScore * 0.30) +
            ($debtScore * 0.20) +
            ($budgetScore * 0.20) +
            ($goalsScore * 0.15) +
            ($diversificationScore * 0.15)
        );

        $score = max(0, min(100, $score));

        $breakdown = [
            'savings' => ['score' => round($savingsScore), 'label' => 'Taxa de Poupança', 'weight' => '30%'],
            'debt' => ['score' => round($debtScore), 'label' => 'Gestão de Dívidas', 'weight' => '20%'],
            'budget' => ['score' => round($budgetScore), 'label' => 'Disciplina Orçamental', 'weight' => '20%'],
            'goals' => ['score' => round($goalsScore), 'label' => 'Metas', 'weight' => '15%'],
            'diversification' => ['score' => round($diversificationScore), 'label' => 'Diversificação', 'weight' => '15%'],
        ];

        $tips = $this->generateTips($breakdown, $score);

        return compact('score', 'breakdown', 'tips');
    }

    public function snapshot(Workspace $workspace, int $userId, ?CarbonInterface $month = null): FinanceScoreSnapshot
    {
        $month = $month ?? now();
        $data = $this->calculate($workspace, $month);

        return FinanceScoreSnapshot::updateOrCreate(
            [
                'workspace_id' => $workspace->id,
                'month' => $month->month,
                'year' => $month->year,
            ],
            [
                'user_id' => $userId,
                'score' => $data['score'],
                'breakdown' => $data['breakdown'],
            ]
        );
    }

    public function getTrend(Workspace $workspace, int $months = 6): array
    {
        return FinanceScoreSnapshot::where('workspace_id', $workspace->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit($months)
            ->get()
            ->reverse()
            ->map(fn ($s) => [
                'label' => Carbon::create($s->year, $s->month)->translatedFormat('M'),
                'score' => $s->score,
            ])
            ->values()
            ->toArray();
    }

    public function getGrade(int $score): string
    {
        return match (true) {
            $score >= 90 => 'Excelente',
            $score >= 75 => 'Muito Bom',
            $score >= 60 => 'Bom',
            $score >= 40 => 'Regular',
            default => 'Precisa Melhorar',
        };
    }

    private function generateTips(array $breakdown, int $score): array
    {
        $tips = [];

        if ($breakdown['savings']['score'] < 50) {
            $tips[] = 'Tenta poupar pelo menos 20% das tuas receitas mensais.';
        }
        if ($breakdown['debt']['score'] < 60) {
            $tips[] = 'As tuas dívidas estão a pesar no score — prioriza amortizações.';
        }
        if ($breakdown['budget']['score'] < 60) {
            $tips[] = 'Define limites por categoria e acompanha no Hub de Orçamento.';
        }
        if ($breakdown['goals']['score'] < 50) {
            $tips[] = 'Cria metas concretas para melhorar a tua saúde financeira.';
        }
        if ($breakdown['diversification']['score'] < 50) {
            $tips[] = 'Diversifica os teus investimentos em diferentes classes de ativos.';
        }
        if ($score >= 80) {
            $tips[] = 'Excelente trabalho! Mantém a disciplina e partilha o teu progresso.';
        }

        return array_slice($tips, 0, 3);
    }
}
