<?php

namespace App\Livewire;

use App\Models\Category;
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

    public array $fieldValues = [];

    public function mount(?int $id = null): void
    {
        $this->spent_at = now()->toDateString();

        $requestedCategoryId = request()->integer('category');
        if ($requestedCategoryId) {
            $category = $this->categoryQuery()->find($requestedCategoryId);
            if ($category) {
                $this->category_id = $category->id;
            }
        }

        if ($id) {
            $expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('id', $id)
                ->firstOrFail();

            $this->expenseId = $expense->id;
            $this->amount = (string) $expense->amount;
            $this->description = (string) ($expense->description ?? '');
            $this->category_id = $expense->category_id;
            $this->spent_at = $expense->spent_at->format('Y-m-d');
            $this->fieldValues = is_array($expense->metadata) ? $expense->metadata : [];
        }

        $this->syncFieldValues();
    }

    public function setCategory(int $categoryId): void
    {
        $this->category_id = $this->categoryQuery()->whereKey($categoryId)->value('id');
        if (! $this->category_id) {
            abort(404);
        }
        $this->syncFieldValues();
    }

    public function save(): void
    {
        $category = $this->categoryQuery()
            ->with(['fields' => fn ($query) => $query->orderBy('order')])
            ->findOrFail($this->category_id);

        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|integer',
            'spent_at' => 'required|date',
        ];

        foreach ($category->fields as $field) {
            $rules['fieldValues.'.$field->key] = $field->required
                ? $this->fieldRule($field).'|required'
                : $this->fieldRule($field).'|nullable';
        }

        $validated = $this->validate($rules);
        $metadata = [];

        foreach ($category->fields as $field) {
            $value = $this->fieldValues[$field->key] ?? null;
            if ($value !== null && $value !== '') {
                $metadata[$field->key] = $value;
            }
        }

        $user = auth()->user();
        $payload = [
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'category_id' => $category->id,
            'spent_at' => $validated['spent_at'],
            'metadata' => $metadata,
            'user_id' => auth()->id(),
            'workspace_id' => $user->current_workspace_id,
        ];

        if ($this->expenseId) {
            Expense::where('workspace_id', $user->current_workspace_id)
                ->whereKey($this->expenseId)
                ->firstOrFail()
                ->update($payload);
            session()->flash('ok', 'Despesa atualizada com sucesso.');
        } else {
            Expense::create($payload);
            $user->awardXp(20, 'despesa registada');
            session()->flash('ok', 'Nova despesa adicionada! '.$user->xpToastText(20, 'despesa registada'));
            session()->flash('xp_award', $user->xpToastText(20, 'despesa registada'));
        }

        $this->redirect(route('expenses'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('expenses'), navigate: true);
    }

    private function categoryQuery()
    {
        return Category::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('hidden_from_sidebar', false);
    }

    private function fieldRule($field): string
    {
        return match ($field->type) {
            'number' => 'numeric',
            'date' => 'date',
            'checkbox' => 'boolean',
            'select' => 'in:'.collect($field->options ?? [])->implode(','),
            default => 'string|max:1000',
        };
    }

    private function syncFieldValues(): void
    {
        if (! $this->category_id) {
            $this->fieldValues = [];

            return;
        }

        $keys = $this->categoryQuery()
            ->whereKey($this->category_id)
            ->with('fields')
            ->firstOrFail()
            ->fields
            ->pluck('key')
            ->all();

        $this->fieldValues = array_intersect_key($this->fieldValues, array_flip($keys));
    }

    public function render()
    {
        $category = $this->category_id
            ? $this->categoryQuery()->with(['fields' => fn ($query) => $query->orderBy('order')])->find($this->category_id)
            : null;

        return view('livewire.expense-form', [
            'category' => $category,
            'categories' => $this->categoryQuery()->orderBy('name')->get(),
            'isEditing' => (bool) $this->expenseId,
        ]);
    }
}
