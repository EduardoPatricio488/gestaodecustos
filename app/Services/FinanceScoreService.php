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
use Carbon\Carbon;
use Carbon\CarbonInterface;

class FinanceScoreService
{
    /**
     * Calculate one deterministic 0-100 personal finance score for a workspace.
     *
     * Important: components without enough data are excluded from the weighted
     * average instead of receiving an arbitrary score. This prevents an empty
     * account from being presented as financially healthy or unhealthy by default.
     */
    public function calculate(Workspace $workspace, ?CarbonInterface $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $earned = (float) $workspace->incomes()
            ->whereBetween('received_at', [$start, $end])
            ->sum('amount_converted');

        $spent = (float) $workspace->expenses()
            ->where('is_company', false)
            ->whereBetween('spent_at', [$start, $end])
            ->sum('amount_converted');

        $breakdown = [];
        $weightedScore = 0.0;
        $availableWeight = 0.0;

        // 30% — savings. Requires actual income for the selected month.
        if ($earned > 0) {
            $savingsRate = (($earned - $spent) / $earned) * 100;
            $savingsScore = min(100, max(0, $savingsRate * 2));
            $breakdown['savings'] = [
                'score' => round($savingsScore),
                'label' => 'Taxa de Poupança',
                'weight' => '30%',
                'available' => true,
                'value' => round($savingsRate, 2),
                'unit' => '%',
            ];
            $weightedScore += $savingsScore * 0.30;
            $availableWeight += 0.30;
        } else {
            $breakdown['savings'] = [
                'score' => null,
                'label' => 'Taxa de Poupança',
                'weight' => '30%',
                'available' => false,
                'reason' => 'Sem receitas registadas no período.',
            ];
        }

        // 20% — debt. Uses outstanding debt against annualised monthly income.
        $totalDebt = (float) $workspace->debts()
            ->where('is_paid', false)
            ->sum('amount');

        if ($earned > 0 || $totalDebt > 0) {
            $debtRatio = $earned > 0
                ? ($totalDebt / ($earned * 12)) * 100
                : 100;
            $debtScore = max(0, 100 - min(100, $debtRatio));
            $breakdown['debt'] = [
                'score' => round($debtScore),
                'label' => 'Gestão de Dívidas',
                'weight' => '20%',
                'available' => true,
                'value' => round($debtRatio, 2),
                'unit' => '% da receita anualizada',
            ];
            $weightedScore += $debtScore * 0.20;
            $availableWeight += 0.20;
        } else {
            $breakdown['debt'] = [
                'score' => null,
                'label' => 'Gestão de Dívidas',
                'weight' => '20%',
                'available' => false,
                'reason' => 'Sem dados suficientes de receitas ou dívidas.',
            ];
        }

        // 20% — budget adherence. Requires a configured budget.
        $budget = (float) $workspace->categories()->sum('budget_limit');
        if ($budget > 0) {
            $usageRate = ($spent / $budget) * 100;
            $budgetScore = max(0, min(100, 100 - max(0, $usageRate - 80) * 5));
            if ($usageRate <= 80) {
                $budgetScore = 100;
            }

            $breakdown['budget'] = [
                'score' => round($budgetScore),
                'label' => 'Disciplina Orçamental',
                'weight' => '20%',
                'available' => true,
                'value' => round($usageRate, 2),
                'unit' => '% do orçamento utilizado',
            ];
            $weightedScore += $budgetScore * 0.20;
            $availableWeight += 0.20;
        } else {
            $breakdown['budget'] = [
                'score' => null,
                'label' => 'Disciplina Orçamental',
                'weight' => '20%',
                'available' => false,
                'reason' => 'Não existem limites orçamentais configurados.',
            ];
        }

        // 15% — goal progress. Only available when at least one goal exists.
        $goals = $workspace->goals()->get(['target_amount', 'current_amount']);
        if ($goals->isNotEmpty()) {
            $validGoals = $goals->filter(fn ($goal) => (float) $goal->target_amount > 0);
            if ($validGoals->isNotEmpty()) {
                $goalsScore = (float) $validGoals->avg(fn ($goal) => min(
                    100,
                    max(0, ((float) $goal->current_amount / (float) $goal->target_amount) * 100)
                ));
                $breakdown['goals'] = [
                    'score' => round($goalsScore),
                    'label' => 'Metas',
                    'weight' => '15%',
                    'available' => true,
                    'value' => round($goalsScore, 2),
                    'unit' => '% de progresso médio',
                ];
                $weightedScore += $goalsScore * 0.15;
                $availableWeight += 0.15;
            }
        }

        if (! isset($breakdown['goals'])) {
            $breakdown['goals'] = [
                'score' => null,
                'label' => 'Metas',
                'weight' => '15%',
                'available' => false,
                'reason' => 'Não existem metas com valor-alvo válido.',
            ];
        }

        // 15% — diversification proxy. This is explicitly a proxy based on
        // recorded asset types, not a claim about portfolio risk/weighting.
        $investments = $workspace->investments()->get(['product_type']);
        if ($investments->isNotEmpty()) {
            $types = $investments->pluck('product_type')->filter()->unique()->count();
            $diversificationScore = min(100, 30 + ($types * 20) + min(30, $investments->count() * 5));
            $breakdown['diversification'] = [
                'score' => round($diversificationScore),
                'label' => 'Diversificação (proxy)',
                'weight' => '15%',
                'available' => true,
                'value' => $types,
                'unit' => 'tipos de ativo registados',
            ];
            $weightedScore += $diversificationScore * 0.15;
            $availableWeight += 0.15;
        } else {
            $breakdown['diversification'] = [
                'score' => null,
                'label' => 'Diversificação (proxy)',
                'weight' => '15%',
                'available' => false,
                'reason' => 'Não existem investimentos registados.',
            ];
        }

        $score = $availableWeight > 0
            ? (int) round($weightedScore / $availableWeight)
            : 0;
        $score = max(0, min(100, $score));

        $availableComponents = collect($breakdown)->where('available', true)->count();
        $totalComponents = count($breakdown);

        return [
            'score' => $score,
            'breakdown' => $breakdown,
            'tips' => $this->generateTips($breakdown, $score),
            'data_quality' => [
                'available_components' => $availableComponents,
                'total_components' => $totalComponents,
                'status' => $availableComponents >= 3 ? 'adequate' : ($availableComponents > 0 ? 'limited' : 'insufficient'),
            ],
        ];
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

        if (($breakdown['savings']['score'] ?? 100) !== null && ($breakdown['savings']['score'] ?? 100) < 50) {
            $tips[] = 'A tua taxa de poupança está baixa — revê as despesas variáveis e define um objetivo mensal realista.';
        }
        if (($breakdown['debt']['score'] ?? 100) !== null && ($breakdown['debt']['score'] ?? 100) < 60) {
            $tips[] = 'As tuas dívidas estão a pesar no score — avalia a amortização das dívidas com maior custo.';
        }
        if (($breakdown['budget']['score'] ?? 100) !== null && ($breakdown['budget']['score'] ?? 100) < 60) {
            $tips[] = 'Estás acima do teu orçamento em algumas categorias — ajusta limites ou despesas.';
        }
        if (($breakdown['goals']['score'] ?? 100) !== null && ($breakdown['goals']['score'] ?? 100) < 50) {
            $tips[] = 'O progresso das tuas metas está baixo — define valores e prazos que consigas acompanhar.';
        }
        if (($breakdown['diversification']['score'] ?? 100) !== null && ($breakdown['diversification']['score'] ?? 100) < 50) {
            $tips[] = 'A diversificação registada é limitada. Analisa o risco e a distribuição real da tua carteira antes de investir mais.';
        }
        if ($score >= 80) {
            $tips[] = 'Bom progresso financeiro. Mantém o acompanhamento regular dos teus indicadores.';
        }

        return array_slice($tips, 0, 3);
    }
}
