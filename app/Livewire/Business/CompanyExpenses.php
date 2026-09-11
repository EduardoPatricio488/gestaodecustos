<?php

namespace App\Livewire\Business;

use App\Models\Category;
use App\Models\Expense;
use App\Services\BusinessAccessService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class CompanyExpenses extends Component
{
    use WithPagination;

    public $search = '';

    public $editingId = null;

    public $categoryFilter = '';

    public $title;

    public $amount;

    public $category_id;

    public $description;

    public $spent_at;

    public $vat_amount = 0;

    protected $rules = [
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'category_id' => 'required|integer',
        'spent_at' => 'required|date',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        app(BusinessAccessService::class)->assert('create_expense');
        $this->resetForm();
        $this->spent_at = now()->format('Y-m-d');
        $this->dispatch('modal-show', name: 'add-company-expense-modal');
    }

    public function edit($id): void
    {
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $role = $access->role(auth()->user(), $workspace);
        $query = Expense::where('workspace_id', $workspace->id)->where('is_company', true);

        if ($role === 'employee') {
            $query->where('user_id', auth()->id());
        } else {
            $access->assert('manage_financials', auth()->user(), $workspace);
        }

        $exp = $query->findOrFail($id);
        $this->editingId = $exp->id;
        $this->title = $exp->title;
        $this->amount = $exp->amount;
        $this->vat_amount = $exp->vat_amount;
        $this->category_id = $exp->category_id;
        $this->description = $exp->description;
        $this->spent_at = Carbon::parse($exp->spent_at)->format('Y-m-d');

        $this->dispatch('modal-show', name: 'add-company-expense-modal');
    }

    public function updatedAmount($value): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        if (is_numeric($value)) {
            $rate = (float) ($workspace->vat_rate ?? 23);
            $this->vat_amount = $workspace->vat_regime === 'isento'
                ? 0
                : round((float) $value * ($rate / 100), 2);
        }
    }

    public function saveExpense(): void
    {
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $role = $access->role(auth()->user(), $workspace);

        $this->validate();
        abort_unless(Category::where('workspace_id', $workspace->id)->whereKey($this->category_id)->exists(), 422, 'Categoria inválida.');

        if ($this->editingId) {
            $query = Expense::where('workspace_id', $workspace->id)->where('is_company', true);
            if ($role === 'employee') {
                $query->where('user_id', auth()->id());
            } else {
                $access->assert('manage_financials', auth()->user(), $workspace);
            }
            $expense = $query->findOrFail($this->editingId);
            $ownerUserId = $expense->user_id;
        } else {
            $access->assert('create_expense', auth()->user(), $workspace);
            $expense = new Expense;
            $ownerUserId = Auth::id();
        }

        $this->updatedAmount($this->amount);
        $expense->fill([
            'user_id' => $ownerUserId,
            'workspace_id' => $workspace->id,
            'category_id' => $this->category_id,
            'amount' => round((float) $this->amount, 2),
            'vat_amount' => round((float) $this->vat_amount, 2),
            'description' => $this->description,
            'spent_at' => $this->spent_at,
            'title' => $this->title,
            'is_company' => true,
        ]);
        $expense->save();

        $wasEditing = (bool) $this->editingId;
        $this->dispatch('modal-close', name: 'add-company-expense-modal');
        $this->dispatch('toast', text: $wasEditing ? 'Custo atualizado!' : 'Custo registado!');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'amount', 'category_id', 'description', 'vat_amount', 'editingId']);
        $this->spent_at = now()->format('Y-m-d');
    }

    public function delete($id): void
    {
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $role = $access->role(auth()->user(), $workspace);
        $query = Expense::where('workspace_id', $workspace->id)->where('is_company', true);

        if ($role === 'employee') {
            $query->where('user_id', auth()->id());
            abort(403, 'Os colaboradores não podem eliminar despesas empresariais.');
        } else {
            $access->assert('delete_financials', auth()->user(), $workspace);
        }

        $query->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo removido.', variant: 'warning');
    }

    public function render()
    {
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $query = Expense::where('workspace_id', $workspace->id)->where('is_company', true);
        $statsQuery = (clone $query)->whereBetween('spent_at', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);

        $expenses = $query->with('category')
            ->where(function ($q) {
                $q->where('expenses.description', 'like', "%{$this->search}%")
                    ->orWhere('expenses.title', 'like', "%{$this->search}%");
            })
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('spent_at', 'desc')
            ->paginate(10);

        return view('livewire.company-expenses', [
            'expenses' => $expenses,
            'categories' => Category::where('workspace_id', $workspace->id)->orderBy('name')->get(),
            'businessRole' => $access->role(auth()->user(), $workspace),
            'totalMonth' => (float) $statsQuery->sum('amount'),
            'totalVat' => (float) $statsQuery->sum('vat_amount'),
        ]);
    }
}
