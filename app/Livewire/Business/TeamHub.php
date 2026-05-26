<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Employee;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class TeamHub extends Component
{
    public $name, $role, $salary, $pay_day = 25;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'salary' => 'required|numeric|min:0',
        'pay_day' => 'required|integer|between:1,31',
    ];

    public function save()
    {
        $this->validate();

        Employee::updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => auth()->id(),
                'workspace_id' => auth()->user()->current_workspace_id, // LIGAÇÃO AO WORKSPACE
                'name' => $this->name,
                'role' => $this->role,
                'salary' => $this->salary,
                'pay_day' => $this->pay_day,
            ]
        );

        $this->reset(['name', 'role', 'salary', 'pay_day', 'editingId']);
        $this->dispatch('modal-close', name: 'add-employee-modal');
        session()->flash('ok', 'Colaborador guardado!');
    }

    public function delete($id)
    {
        Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id)->delete();
    }

    public function render()
    {
        // FILTRAR SEMPRE POR WORKSPACE ATIVO
        $employees = Employee::where('workspace_id', auth()->user()->current_workspace_id)->latest()->get();
        $totalPayroll = $employees->sum('salary');

        return view('livewire.business.team-hub', [
            'employees' => $employees,
            'totalPayroll' => $totalPayroll,
            'employeeCount' => $employees->count(),
        ]);
    }
}
