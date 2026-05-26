<?php

namespace App\Livewire;

use App\Models\Debt;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class DebtHub extends Component
{
    // Campos para nova dívida
    public $type = 'owe'; // 'owe' ou 'owed'
    public $person_name, $amount, $description, $due_at;

    /**
     * Guarda um novo registo de dívida ou empréstimo.
     */
    public function save()
    {
        $this->validate([
            'person_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:owe,owed',
            'due_at' => 'nullable|date',
        ]);

        Debt::create([
            'user_id' => auth()->id(),
            'type' => $this->type,
            'person_name' => $this->person_name,
            'amount' => $this->amount,
            'description' => $this->description,
            'due_at' => $this->due_at,
            'is_paid' => false,
        ]);

        // Gamificação: Ganha 30 XP por organizar dívidas
        auth()->user()->addXp(30);

        $this->reset(['person_name', 'amount', 'description', 'due_at']);
        $this->dispatch('modal-close', name: 'add-debt-modal');
        $this->dispatch('toast', text: 'Registo de dívida guardado!');
    }

    /**
     * Alterna o estado de pagamento (Liquidado / Pendente).
     */
    public function togglePaid($id)
    {
        $debt = Debt::findOrFail($id);
        $debt->update(['is_paid' => !$debt->is_paid]);

        $status = $debt->is_paid ? 'liquidada' : 'reaberta';
        $this->dispatch('toast', text: "Dívida {$status}!");
    }

    /**
     * Elimina o registo.
     */
    public function delete($id)
    {
        Debt::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    public function render()
    {
        // O Trait BelongsToWorkspace filtra automaticamente pelo grupo atual
        $allDebts = Debt::latest()->get();

        return view('livewire.debt-hub', [
            'iOwe' => $allDebts->where('type', 'owe')->where('is_paid', false),
            'theyOweMe' => $allDebts->where('type', 'owed')->where('is_paid', false),
            'history' => $allDebts->where('is_paid', true)->take(10),

            // Totais para os cards
            'totalIOwe' => $allDebts->where('type', 'owe')->where('is_paid', false)->sum('amount'),
            'totalTheyOweMe' => $allDebts->where('type', 'owed')->where('is_paid', false)->sum('amount'),
        ]);
    }
}
