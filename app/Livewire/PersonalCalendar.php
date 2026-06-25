<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Expense, Income, Subscription, FitnessActivity, Reminder};
use Illuminate\Support\Carbon;
use Livewire\Attributes\{Layout, Computed};

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

    /**
     * Navegação: Recuar um mês
     */
    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    /**
     * Navegação: Avançar um mês
     */
    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    /**
     * MOTOR DE BUSCA DE EVENTOS:
     * Vai buscar todos os registos do site para o mês selecionado
     */
    #[Computed]
    public function dayEvents()
    {
        $wsId = auth()->user()->current_workspace_id;
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // 1. Gastos (Expenses)
        $expenses = Expense::where('workspace_id', $wsId)
            ->whereBetween('spent_at', [$start, $end])
            ->get()
            ->map(fn($e) => ['date' => $e->spent_at->format('Y-m-d'), 'type' => 'expense', 'amount' => $e->amount, 'label' => $e->description ?: 'Gasto', 'color' => 'text-red-500']);

        // 2. Ganhos (Incomes)
        $incomes = Income::where('workspace_id', $wsId)
            ->whereBetween('received_at', [$start, $end])
            ->get()
            ->map(fn($i) => ['date' => $i->received_at->format('Y-m-d'), 'type' => 'income', 'amount' => $i->amount, 'label' => $i->description, 'color' => 'text-emerald-500']);

        // 3. Treinos (Fitness)
        $fitness = FitnessActivity::where('workspace_id', $wsId)
            ->where('user_id', auth()->id())
            ->whereBetween('activity_date', [$start, $end])
            ->get()
            ->map(fn($f) => ['date' => $f->activity_date->format('Y-m-d'), 'type' => 'fitness', 'label' => $f->type, 'meta' => $f->duration_minutes . 'm', 'color' => 'text-orange-500']);

        // 4. Lembretes (Reminders)
        $reminders = Reminder::where('workspace_id', $wsId)
            ->whereBetween('due_date', [$start, $end])
            ->get()
            ->map(fn($r) => ['date' => $r->due_date->format('Y-m-d'), 'type' => 'reminder', 'label' => $r->title, 'done' => $r->is_completed, 'color' => 'text-indigo-500']);

        // Fundir tudo numa coleção e agrupar por dia (YYYY-MM-DD)
        return collect()
            ->concat($expenses)
            ->concat($incomes)
            ->concat($fitness)
            ->concat($reminders)
            ->groupBy('date');
    }

    public function render()
    {
        $currentDate = Carbon::create($this->year, $this->month, 1);

        // Dados para a grelha visual
        $daysInMonth = $currentDate->daysInMonth;

        // Calcular o padding (espaços vazios no início da semana)
        // 0 (Dom) a 6 (Sáb). Ajustamos para Segunda-feira ser o dia 1.
        $firstDay = $currentDate->dayOfWeek;
        $padding = ($firstDay === 0) ? 6 : $firstDay - 1;

        return view('livewire.personal-calendar', [
            'totalDays' => $daysInMonth,
            'paddingDays' => $padding,
            'currentMonthName' => $currentDate->translatedFormat('F'),
        ]);
    }
}
