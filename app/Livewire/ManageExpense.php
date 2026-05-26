<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
class ManageExpense extends Component
{
    use WithFileUploads;

    public ?Expense $expense = null;

    // Campos padrão
    public $amount;
    public $category_id;
    public $description;
    public $spent_at;

    // Campos dinâmicos e anexo
    public $meta = [];
    public $receipt;

    public function mount(Expense $expense = null)
    {
        if ($expense && $expense->exists) {
            $this->expense = $expense;
            $this->amount = $expense->amount;
            $this->category_id = $expense->category_id;
            $this->description = $expense->description;
            $this->spent_at = $expense->spent_at->format('Y-m-d');
            $this->meta = $expense->metadata ?? [];
        } else {
            $this->spent_at = now()->format('Y-m-d');
            $this->meta = [];
        }
    }

    public function save()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'spent_at' => 'required|date',
            'receipt' => 'nullable|image|max:2048',
        ]);

        // Vamos buscar a moeda definida no Workspace do utilizador
        $currentCurrency = auth()->user()->currentWorkspace->currency ?? 'EUR';

        $data = [
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'currency' => $currentCurrency, // Moeda fixa vinda das definições
            'amount_converted' => $this->amount, // Valor real introduzido
            'description' => $this->description,
            'spent_at' => $this->spent_at,
            'metadata' => $this->meta,
        ];

        // Lógica de Upload do Recibo
        $bonusXp = 0;
        if ($this->receipt) {
            if ($this->expense && $this->expense->receipt_path) {
                Storage::disk('public')->delete($this->expense->receipt_path);
            }
            $path = $this->receipt->store('receipts/' . auth()->user()->current_workspace_id, 'public');
            $data['receipt_path'] = $path;
            $bonusXp = 20;
        }

        if ($this->expense) {
            $this->expense->update($data);
            auth()->user()->addXp(10 + $bonusXp);
            session()->flash('ok', 'Registo atualizado!');
        } else {
            Expense::create($data);
            auth()->user()->addXp(50 + $bonusXp);
            session()->flash('ok', 'Gasto registado com sucesso!');
        }

        return $this->redirect(route('expenses'), navigate: true);
    }

    public function render()
    {
        $selectedCategorySlug = '';
        if ($this->category_id) {
            $cat = Category::find($this->category_id);
            $selectedCategorySlug = $cat ? strtolower($cat->name) : '';
        }

        return view('livewire.manage-expense', [
            'categories' => auth()->user()->categories()->orderBy('name')->get(),
            'isEditing' => $this->expense !== null,
            'categorySlug' => $selectedCategorySlug,
            'currentCurrencySymbol' => auth()->user()->currentWorkspace->currency ?? 'EUR'
        ]);
    }
}
