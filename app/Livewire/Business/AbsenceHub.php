<?php

namespace App\Livewire\Business;

use App\Models\Absence;
use App\Models\Employee;
use App\Services\BusinessAccessService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class AbsenceHub extends Component
{
    public function mount(): void
    {
        app(BusinessAccessService::class)->assert('view_business');
    }

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
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $access->assert('manage_team', auth()->user(), $workspace);
        Absence::where('workspace_id', $workspace->id)->findOrFail($id)->update(['status' => 'aprovado']);
        $this->dispatch('toast', variant: 'success', text: 'Pedido aprovado com sucesso!');
    }

    /**
     * O CEO ou Colaborador elimina um registo
     */
    public function delete($id)
    {
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $absence = Absence::where('workspace_id', $workspace->id)->find($id);
        if (! $absence) {
            return;
        }

        $isOwnerOfRecord = $absence->employee && $absence->employee->user_id === auth()->id();
        $canManage = $access->can('manage_team', auth()->user(), $workspace);
        abort_unless($canManage || $isOwnerOfRecord, 403);

        $absence->delete();
        $this->dispatch('toast', variant: 'warning', text: 'Registo removido.');
    }

    /**
     * O CEO regista uma ausência (fica logo aprovada)
     */
    public function save()
    {
        app(BusinessAccessService::class)->assert('manage_team');
        $this->validate([
            'employee_id' => 'required',
            'type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $workspaceId = Auth::user()->current_workspace_id;
        abort_unless(Employee::where('workspace_id', $workspaceId)->whereKey($this->employee_id)->exists(), 422, 'Colaborador inválido.');

        Absence::create([
            'workspace_id' => $workspaceId,
            'employee_id' => $this->employee_id,
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'notes' => $this->notes,
            'status' => 'aprovado',
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
            'status' => 'pendente',
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
        $isManager = app(BusinessAccessService::class)->can('manage_team', $user, $workspace);

        $query = $workspace->absences()->with('employee');

        if (! $isManager) {
            $employee = Employee::where('user_id', $user->id)->where('workspace_id', $workspace->id)->first();
            $query->where('employee_id', $employee?->id);
        }

        $absentTodayCount = $workspace->employees()->get()->filter(fn ($e) => $e->is_absent_today)->count();
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
