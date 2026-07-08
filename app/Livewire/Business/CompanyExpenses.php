<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CompanyExpenses extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $categoryFilter = '';

    // Campos do formulário
    public $title, $amount, $category_id, $description, $spent_at;
    public $vat_amount = 0;

    protected $rules = [
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'category_id' => 'required|exists:categories,id',
        'spent_at' => 'required|date',
        'vat_amount' => 'nullable|numeric',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategoryFilter() { $this->resetPage(); }

    public function openModal()
    {
        $this->resetForm();
        $this->spent_at = now()->format('Y-m-d');
        $this->dispatch('modal-show', name: 'add-company-expense-modal');
    }

    public function edit($id)
    {
        $exp = Expense::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $exp->id;
        $this->title = $exp->title;
        $this->amount = $exp->amount;
        $this->vat_amount = $exp->vat_amount;
        $this->category_id = $exp->category_id;
        $this->description = $exp->description;
        $this->spent_at = \Carbon\Carbon::parse($exp->spent_at)->format('Y-m-d');

        $this->dispatch('modal-show', name: 'add-company-expense-modal');
    }

    public function updatedAmount($value)
    {
        if (is_numeric($value)) {
            // Sugestão automática de IVA (aprox 23% incluído no total)
            $this->vat_amount = round($value * 0.187, 2);
        }
    }

    public function saveExpense()
    {
        $this->validate();

        Expense::updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => Auth::id(),
                'workspace_id' => auth()->user()->current_workspace_id,
                'category_id' => $this->category_id,
                'amount' => $this->amount,
                'vat_amount' => $this->vat_amount ?? 0,
                'description' => $this->description,
                'spent_at' => $this->spent_at,
                'title' => $this->title,
                'is_company' => true,
            ]
        );

        $this->dispatch('modal-close', name: 'add-company-expense-modal');
        $this->dispatch('toast', text: $this->editingId ? 'Custo atualizado!' : 'Custo registado!');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['title', 'amount', 'category_id', 'description', 'vat_amount', 'editingId']);
        $this->spent_at = now()->format('Y-m-d');
    }

    public function delete($id)
    {
        Expense::where('user_id', auth()->id())->where('is_company', true)->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo removido.', variant: 'warning');
    }

    public function render()
    {
        $user = auth()->user();
        $query = Expense::where('expenses.user_id', $user->id)->where('is_company', true);
        $statsQuery = (clone $query)->whereMonth('spent_at', now()->month);

        $expenses = $query->with('category')
            ->where(function($q) {
                $q->where('expenses.description', 'like', "%{$this->search}%")
                  ->orWhere('expenses.title', 'like', "%{$this->search}%");
            })
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('spent_at', 'desc')
            ->paginate(10);

        return view('livewire.company-expenses', [
            'expenses' => $expenses,
            'categories' => Category::where('workspace_id', $user->current_workspace_id)->orderBy('name')->get(),
            'totalMonth' => (float) $statsQuery->sum('amount'),
            'totalVat' => (float) $statsQuery->sum('vat_amount'),
        ]);
    }
}
