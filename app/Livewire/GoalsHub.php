<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Goal;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class GoalsHub extends Component
{
    public $name, $target_amount, $current_amount = 0, $deadline;
    public $editingGoalId = null;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'current_amount' => 'required|numeric|min:0',
            'deadline' => 'nullable|date',
        ]);

        Goal::updateOrCreate(
            ['id' => $this->editingGoalId, 'user_id' => auth()->id()],
            [
                'name' => $this->name,
                'target_amount' => $this->target_amount,
                'current_amount' => $this->current_amount,
                'deadline' => $this->deadline,
            ]
        );

        $this->reset(['name', 'target_amount', 'current_amount', 'deadline', 'editingGoalId']);
        $this->dispatch('modal-close', name: 'goal-modal');
        session()->flash('ok', 'Objetivo guardado!');
    }

    public function delete($id)
    {
        Goal::where('user_id', auth()->id())->find($id)->delete();
    }

    public function render()
    {
        return view('livewire.goals-hub', [
            'goals' => auth()->user()->goals()->get()
        ]);
    }
}
