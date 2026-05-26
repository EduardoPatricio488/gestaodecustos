<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class YearlyReport extends Component
{
    public $year;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function render()
    {
        $user = auth()->user();

        // 1. Dados Mensais (Janeiro a Dezembro)
        $monthlyStats = collect(range(1, 12))->map(function ($month) use ($user) {
            $spent = (float) $user->expenses()->whereYear('spent_at', $this->year)->whereMonth('spent_at', $month)->sum('amount');
            $earned = (float) $user->incomes()->whereYear('received_at', $this->year)->whereMonth('received_at', $month)->sum('amount');

            return [
                'month_name' => mb_convert_case(now()->month($month)->translatedFormat('F'), MB_CASE_TITLE),
                'spent' => $spent,
                'earned' => $earned,
                'balance' => $earned - $spent,
            ];
        });

        // 2. Totais Anuais
        $yearlyEarned = $monthlyStats->sum('earned');
        $yearlySpent = $monthlyStats->sum('spent');
        $yearlySavings = $yearlyEarned - $yearlySpent;

        // 3. Ranking de Categorias do Ano
        $categoryRanking = $user->expenses()
            ->whereYear('spent_at', $this->year)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        // 4. Recordes
        $bestMonth = $monthlyStats->sortByDesc('balance')->first();
        $worstMonth = $monthlyStats->sortBy('balance')->first();

        return view('livewire.yearly-report', [
            'monthlyStats' => $monthlyStats,
            'yearlyEarned' => $yearlyEarned,
            'yearlySpent' => $yearlySpent,
            'yearlySavings' => $yearlySavings,
            'categoryRanking' => $categoryRanking,
            'bestMonth' => $bestMonth,
            'worstMonth' => $worstMonth,
            'chartMax' => max($monthlyStats->max('spent'), $monthlyStats->max('earned'), 1),
        ]);
    }
}
