<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Absence;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AbsenceHub extends Component
{
    use WithPagination;

    public $typeFilter = '';

    // Inicializamos como string vazia para o validador reconhecer
    public $employee_id = '';
    public $type = 'ferias';
    public $start_date;
    public $end_date;
    public $notes = '';
    public $editingId = null;

    protected $rules = [
        'employee_id' => 'required',
        'type' => 'required|in:ferias,doenca,falta_justificada,pessoal',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    public function save()
    {
        $this->validate();

        $workspaceId = auth()->user()->current_workspace_id;

        Absence::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => $workspaceId,
                'employee_id' => $this->employee_id,
                'type' => $this->type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'notes' => $this->notes,
                'status' => 'pendente'
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'absence-modal');
        $this->dispatch('toast', text: 'Ausência registada com sucesso!');
    }

    public function approve($id) { Absence::find($id)->update(['status' => 'aprovado']); }
    public function reject($id) { Absence::find($id)->update(['status' => 'recusado']); }
    public function delete($id) { Absence::find($id)->delete(); }

    public function resetForm()
    {
        $this->reset(['employee_id', 'type', 'start_date', 'end_date', 'notes', 'editingId']);
        $this->employee_id = ''; // Volta ao estado vazio
        $this->type = 'ferias';
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $query = $workspace->absences()->with('employee')
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->orderBy('start_date', 'desc');

        return view('livewire.business.absence-hub', [
            'absences' => $query->paginate(10),
            'employees' => $workspace->employees()->orderBy('name')->get(),
            'absentTodayCount' => $workspace->employees()->get()->filter(fn($e) => $e->is_absent_today)->count(),
            'pendingApprovals' => $workspace->absences()->where('status', 'pendente')->count(),
            'totalDaysMonth' => $workspace->absences()->where('status', 'aprovado')->whereMonth('start_date', now()->month)->get()->sum('business_days')
        ]);
    }
}
