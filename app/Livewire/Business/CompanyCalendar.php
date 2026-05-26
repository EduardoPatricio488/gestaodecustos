<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Absence, Expense, Income, Employee};
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CompanyCalendar extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $days = [];

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        $this->buildCalendar();
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->buildCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->buildCalendar();
    }

    public function buildCalendar()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $startOfCalendar = $date->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $date->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $this->days = [];
        $currentDay = $startOfCalendar->copy();

        // Dados do Workspace
        $workspaceId = auth()->user()->current_workspace_id;

        // Procurar Ausências/Férias
        $absences = Absence::with('employee')
            ->where('workspace_id', $workspaceId)
            ->where('status', 'aprovado')
            ->get();

        // Procurar Movimentos Financeiros
        $expenses = Expense::where('workspace_id', $workspaceId)->get();
        $incomes = Income::where('workspace_id', $workspaceId)->get();

        while ($currentDay <= $endOfCalendar) {
            $dateString = $currentDay->format('Y-m-d');

            $events = [];

            // Adicionar Ausências ao dia
            foreach ($absences as $abs) {
                if ($currentDay->between($abs->start_date, $abs->end_date)) {
                    $events[] = [
                        'type' => 'absence',
                        'label' => 'Ausência: ' . explode(' ', $abs->employee->name)[0],
                        'color' => $abs->type_color,
                        'icon' => 'user-minus'
                    ];
                }
            }

            // Adicionar Gastos do dia
            foreach ($expenses as $exp) {
                if ($exp->spent_at->format('Y-m-d') === $dateString) {
                    $events[] = [
                        'type' => 'expense',
                        'label' => 'Saída: ' . number_format($exp->amount, 0) . '€',
                        'color' => 'red',
                        'icon' => 'arrow-down-right'
                    ];
                }
            }

            // Adicionar Receitas do dia
            foreach ($incomes as $inc) {
                if ($inc->received_at->format('Y-m-d') === $dateString) {
                    $events[] = [
                        'type' => 'income',
                        'label' => 'Entrada: ' . number_format($inc->amount, 0) . '€',
                        'color' => 'emerald',
                        'icon' => 'arrow-up-right'
                    ];
                }
            }

            $this->days[] = [
                'date' => $currentDay->copy(),
                'isCurrentMonth' => $currentDay->month == $this->selectedMonth,
                'isToday' => $currentDay->isToday(),
                'events' => $events
            ];

            $currentDay->addDay();
        }
    }

    public function render()
    {
        return view('livewire.business.company-calendar', [
            'monthName' => Carbon::create($this->selectedYear, $this->selectedMonth, 1)->translatedFormat('F Y')
        ]);
    }
}
