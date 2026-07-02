<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Absence, Employee, User};
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\{Auth, DB};

#[Layout('components.layouts.app')]
class AbsenceHub extends Component
{
    use WithPagination;

    public $employee_id = '';
    public $type = 'ferias';
    public $start_date;
    public $end_date;
    public $notes = '';

    /**
     * O CEO aprova um pedido
     */
    public function approve($id)
    {
        Absence::find($id)->update(['status' => 'aprovado']);
        $this->dispatch('toast', variant: 'success', text: 'Pedido aprovado com sucesso!');
    }

    /**
     * O CEO ou Colaborador elimina um registo
     */
    public function delete($id)
    {
        Absence::find($id)->delete();
        $this->dispatch('toast', variant: 'warning', text: 'Registo removido.');
    }

    /**
     * O CEO regista uma ausência (fica logo aprovada)
     */
    public function save()
    {
        $this->validate([
            'employee_id' => 'required',
            'type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Absence::create([
            'workspace_id' => Auth::user()->current_workspace_id,
            'employee_id' => $this->employee_id,
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'notes' => $this->notes,
            'status' => 'aprovado'
        ]);

        $this->reset(['employee_id', 'start_date', 'end_date', 'notes']);
        $this->dispatch('modal-close', name: 'absence-modal');
        $this->dispatch('toast', text: 'Ausência registada pela administração.');
    }

    /**
     * O Colaborador solicita férias (fica pendente)
     */
    public function submitRequest()
    {
        $this->validate([
            'type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->where('workspace_id', $user->current_workspace_id)->firstOrFail();

        Absence::create([
            'workspace_id' => $user->current_workspace_id,
            'employee_id' => $employee->id,
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'notes' => $this->notes,
            'status' => 'pendente'
        ]);

        $this->reset(['type', 'start_date', 'end_date', 'notes']);
        $this->dispatch('modal-close', name: 'absence-modal');
        $this->dispatch('toast', heading: 'Enviado', message: 'O teu pedido aguarda aprovação.');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        // USANDO A MESMA LÓGICA DA SIDEBAR QUE JÁ FUNCIONA
        $isManager = $user->isAdminRole() || $user->isOwner();

        $query = $workspace->absences()->with('employee');

        if (!$isManager) {
            $employee = Employee::where('user_id', $user->id)->where('workspace_id', $workspace->id)->first();
            $query->where('employee_id', $employee?->id);
        }

        $absentTodayCount = $workspace->employees()->get()->filter(fn($e) => $e->is_absent_today)->count();
        $myEmp = Employee::where('user_id', $user->id)->where('workspace_id', $workspace->id)->first();

        return view('livewire.business.absence-hub', [
            'absences' => $query->orderBy('start_date', 'desc')->paginate(10),
            'employees' => $workspace->employees()->orderBy('name')->get(),
            'isManager' => $isManager,
            'absentTodayCount' => $absentTodayCount,
            'pendingApprovals' => $workspace->absences()->where('status', 'pendente')->count(),
            'totalDaysMonth' => $workspace->absences()->where('status', 'aprovado')->whereMonth('start_date', now()->month)->get()->sum('business_days'),

            // Dados para vista de colaborador
            'usedDays' => $myEmp?->vacation_days_used ?? 0,
            'pendingCount' => Absence::where('employee_id', $myEmp?->id)->where('status', 'pendente')->count(),
            'approvedCount' => Absence::where('employee_id', $myEmp?->id)->where('status', 'aprovado')->count(),
        ]);
    }
}
