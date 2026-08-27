<?php

namespace App\Livewire;

use App\Models\{Debt, Expense, FitnessActivity, Income, RecurringIncome, Reminder, Subscription};
use Illuminate\Support\Carbon;
use Livewire\Attributes\{Computed, Layout};
use Livewire\Component;

#[Layout('components.layouts.app')]
class PersonalCalendar extends Component
{
    public $month;
    public $year;

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

    #[Computed]
    public function dayEvents()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $expenses = Expense::where('workspace_id', $workspaceId)
            ->whereBetween('spent_at', [$start, $end])
            ->get()
            ->map(fn ($expense) => [
                'date' => $expense->spent_at->format('Y-m-d'),
                'type' => 'expense',
                'amount' => (float) ($expense->amount_converted ?? $expense->amount),
                'label' => $expense->description ?: 'Gasto',
                'color' => 'text-red-500',
            ]);

        $incomes = Income::where('workspace_id', $workspaceId)
            ->whereBetween('received_at', [$start, $end])
            ->get()
            ->map(fn ($income) => [
                'date' => $income->received_at->format('Y-m-d'),
                'type' => 'income',
                'amount' => (float) ($income->amount_converted ?? $income->amount),
                'label' => $income->description ?: 'Receita',
                'color' => 'text-emerald-500',
            ]);

        $recurringIncomes = RecurringIncome::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get()
            ->map(function ($income) use ($start) {
                $day = max(1, min($start->daysInMonth, (int) ($income->day_of_month ?: 1)));

                return [
                    'date' => $start->copy()->day($day)->format('Y-m-d'),
                    'type' => 'salary',
                    'amount' => (float) $income->amount,
                    'label' => $income->description ?: 'Salario',
                    'color' => 'text-emerald-600',
                ];
            });

        $subscriptions = Subscription::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get()
            ->map(function ($subscription) use ($start) {
                $day = max(1, min($start->daysInMonth, (int) ($subscription->billing_day ?: 1)));

                return [
                    'date' => $start->copy()->day($day)->format('Y-m-d'),
                    'type' => 'subscription',
                    'amount' => (float) $subscription->amount,
                    'label' => $subscription->name ?: 'Assinatura',
                    'color' => 'text-purple-500',
                ];
            });

        $debts = Debt::where('workspace_id', $workspaceId)
            ->where('is_paid', false)
            ->whereBetween('due_at', [$start, $end])
            ->get()
            ->map(fn ($debt) => [
                'date' => $debt->due_at->format('Y-m-d'),
                'type' => 'debt',
                'amount' => (float) $debt->amount,
                'label' => $debt->description ?: ($debt->person_name ?: 'Parcela'),
                'color' => 'text-amber-500',
            ]);

        $fitness = FitnessActivity::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->whereBetween('activity_date', [$start, $end])
            ->get()
            ->map(fn ($fitness) => [
                'date' => $fitness->activity_date->format('Y-m-d'),
                'type' => 'fitness',
                'label' => $fitness->type,
                'meta' => $fitness->duration_minutes.'m',
                'color' => 'text-orange-500',
            ]);

        $reminders = Reminder::where('workspace_id', $workspaceId)
            ->whereBetween('remind_at', [$start, $end])
            ->get()
            ->map(fn ($reminder) => [
                'date' => $reminder->remind_at->format('Y-m-d'),
                'type' => 'reminder',
                'label' => $reminder->title,
                'done' => $reminder->is_completed,
                'color' => 'text-indigo-500',
            ]);

        return collect()
            ->concat($expenses)
            ->concat($incomes)
            ->concat($recurringIncomes)
            ->concat($subscriptions)
            ->concat($debts)
            ->concat($fitness)
            ->concat($reminders)
            ->groupBy('date');
    }

    #[Computed]
    public function cashRiskDays()
    {
        $currentDate = Carbon::create($this->year, $this->month, 1);
        $balance = 0.0;
        $riskDays = collect();

        for ($day = 1; $day <= $currentDate->daysInMonth; $day++) {
            $dateKey = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);
            $events = $this->dayEvents->get($dateKey, collect());

            $income = $events
                ->whereIn('type', ['income', 'salary'])
                ->sum(fn ($event) => (float) ($event['amount'] ?? 0));

            $outflow = $events
                ->whereIn('type', ['expense', 'subscription', 'debt'])
                ->sum(fn ($event) => (float) ($event['amount'] ?? 0));

            $balance += $income - $outflow;

            if ($balance < 0) {
                $riskDays->put($dateKey, [
                    'date' => Carbon::create($this->year, $this->month, $day),
                    'projected_balance' => round($balance, 2),
                ]);
            }
        }

        return $riskDays;
    }

    #[Computed]
    public function monthSummary(): array
    {
        $events = $this->dayEvents->flatten(1);

        $income = (float) $events
            ->whereIn('type', ['income', 'salary'])
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0));

        $outflow = (float) $events
            ->whereIn('type', ['expense', 'subscription', 'debt'])
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0));

        $fixedOutflow = (float) $events
            ->whereIn('type', ['subscription', 'debt'])
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0));

        return [
            'income' => $income,
            'outflow' => $outflow,
            'fixed_outflow' => $fixedOutflow,
            'projected_balance' => round($income - $outflow, 2),
            'risk_days' => $this->cashRiskDays->count(),
        ];
    }

    public function render()
    {
        $currentDate = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $currentDate->daysInMonth;

        $firstDay = $currentDate->dayOfWeek;
        $padding = ($firstDay === 0) ? 6 : $firstDay - 1;

        return view('livewire.personal-calendar', [
            'totalDays' => $daysInMonth,
            'paddingDays' => $padding,
            'currentMonthName' => $currentDate->translatedFormat('F'),
            'monthSummary' => $this->monthSummary,
            'riskDays' => $this->cashRiskDays,
        ]);
    }
}
