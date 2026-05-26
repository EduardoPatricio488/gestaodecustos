<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ExpenseChart extends Component
{
    public $labels = [];
    public $data = [];

    public function mount()
    {
        $userId = Auth::id();

        $expenses = Expense::selectRaw('category_id, SUM(amount) as total')
            ->where('user_id', $userId)
            ->groupBy('category_id')
            ->get();

        $this->labels = Category::whereIn('id', $expenses->pluck('category_id'))->pluck('name')->toArray();
        $this->data = $expenses->pluck('total')->map(fn($e) => (float) $e)->toArray();
    }

    public function render()
    {
        return view('components.expense-chart');
    }
}
