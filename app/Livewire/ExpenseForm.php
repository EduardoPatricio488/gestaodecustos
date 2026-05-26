<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ExpenseForm extends Component
{
    public string $amount = '';
    public string $description = '';
    public ?int $category_id = null;
    public string $spent_at = '';

    public ?int $expenseId = null;

    public function mount(?int $id = null): void
    {
        $this->spent_at = now()->toDateString();

        // Selecciona a primeira categoria por defeito
        $firstCategory = auth()->user()->categories()->first();
        if ($firstCategory) {
            $this->category_id = $firstCategory->id;
        }

        // Se vier um ID, é edição
        if ($id) {
            $expense = Expense::where('user_id', auth()->id())
                ->where('id', $id)
                ->firstOrFail();

            $this->expenseId   = $expense->id;
            $this->amount      = (string) $expense->amount;
            $this->description = (string) ($expense->description ?? '');
            $this->category_id = $expense->category_id;
            $this->spent_at    = $expense->spent_at->format('Y-m-d');
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id,user_id,' . auth()->id(),
            'spent_at'    => 'required|date',
        ]);

        if ($this->expenseId) {
            Expense::where('user_id', auth()->id())
                ->where('id', $this->expenseId)
                ->firstOrFail()
                ->update($data);

            session()->flash('ok', 'Despesa atualizada com sucesso.');
        } else {
            Expense::create([
                ...$data,
                'user_id' => auth()->id(),
            ]);

            session()->flash('ok', 'Despesa criada com sucesso.');
        }

        $this->redirect(route('expenses'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('expenses'), navigate: true);
    }

    public function render()
    {
        return view('livewire.expense-form', [
            'categories' => auth()->user()->categories()->orderBy('name')->get(),
            'isEditing'  => (bool) $this->expenseId,
        ]);
    }
}
