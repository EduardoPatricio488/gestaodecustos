<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\FitnessActivity;
use App\Models\Workspace;
use Carbon\CarbonInterface;

class WellnessFinanceService
{
    public function getInsights(Workspace $workspace, ?CarbonInterface $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $healthCategory = Category::where('workspace_id', $workspace->id)
            ->where(fn ($q) => $q->where('slug', 'saude')->orWhere('name', 'like', '%Saúde%'))
            ->first();

        $healthSpent = $healthCategory
            ? (float) Expense::where('workspace_id', $workspace->id)
                ->where('category_id', $healthCategory->id)
                ->whereBetween('spent_at', [$start, $end])
                ->sum('amount')
            : 0;

        $activities = FitnessActivity::where('user_id', auth()->id())
            ->whereBetween('activity_date', [$start, $end])
            ->get();

        $totalKm = (float) $activities->sum('distance_km');
        $totalCalories = (float) $activities->sum('calories');
        $activityCount = $activities->count();

        $costPerKm = $totalKm > 0 && $healthSpent > 0
            ? round($healthSpent / $totalKm, 2)
            : null;

        $verdict = $this->generateVerdict($healthSpent, $totalKm, $activityCount);

        return [
            'health_spent' => $healthSpent,
            'total_km' => $totalKm,
            'total_calories' => $totalCalories,
            'activity_count' => $activityCount,
            'cost_per_km' => $costPerKm,
            'verdict' => $verdict,
            'month' => $month->translatedFormat('F'),
        ];
    }

    private function generateVerdict(float $spent, float $km, int $activities): string
    {
        if ($activities === 0 && $spent > 50) {
            return "Gastaste ".number_format($spent, 0, ',', '.')."€ em saúde este mês mas não registaste atividade. Vale a pena mover-te!";
        }
        if ($km >= 50 && $spent > 0) {
            return "Correste/caminhaste {$km}km este mês — cada km 'custou' ".number_format($spent / $km, 2, ',', '.').'€ em saúde. Bom investimento!';
        }
        if ($activities >= 10) {
            return "Excelente! {$activities} atividades este mês. Continua assim!";
        }
        if ($spent === 0.0 && $activities === 0) {
            return 'Regista as tuas atividades e despesas de saúde para ver insights personalizados.';
        }

        return "Tens {$activities} atividades e ".number_format($spent, 0, ',', '.').'€ em saúde este mês.';
    }
}
