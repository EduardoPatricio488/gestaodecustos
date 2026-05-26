<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Subscription;
use App\Models\RecurringIncome;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class FinancialCalendar extends Component
{
    public $month, $year;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function render()
    {
        $user = auth()->user();
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();

        $daysInMonth = [];
        $tempDate = (clone $startDate)->startOfWeek(); // Começa na segunda-feira da semana inicial

        while ($tempDate->lte((clone $endDate)->endOfWeek())) {
            $dateStr = $tempDate->toDateString();

            // 1. Gastos Reais
            $expenses = $user->expenses()->whereDate('spent_at', $dateStr)->get();

            // 2. Receitas Reais
            $incomes = $user->incomes()->whereDate('received_at', $dateStr)->get();

            // 3. Assinaturas Planeadas (Sempre que o dia coincide)
            $subs = $user->subscriptions()->where('billing_day', $tempDate->day)->where('is_active', true)->get();

            // 4. Salários Planeados
            $recIncomes = $user->recurringIncomes()->where('day_of_month', $tempDate->day)->where('is_active', true)->get();

            $daysInMonth[] = [
                'date' => (clone $tempDate),
                'is_current_month' => $tempDate->month === (int)$this->month,
                'expenses' => $expenses,
                'incomes' => $incomes,
                'subscriptions' => $subs,
                'fixedIncomes' => $recIncomes,
                'total_out' => $expenses->sum('amount') + $subs->sum('amount'),
                'total_in' => $incomes->sum('amount') + $recIncomes->sum('amount'),
            ];

            $tempDate->addDay();
        }

        return view('livewire.financial-calendar', [
            'days' => $daysInMonth,
            'monthName' => mb_convert_case($startDate->translatedFormat('F Y'), MB_CASE_TITLE)
        ]);
    }
}
