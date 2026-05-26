<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Categories extends Component
{
    public string $name = '';

    public string $color = '#10b981';

    public function add(): void
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:9',
        ]);

        Category::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'color' => $this->color,
        ]);

        $this->reset('name');
        $this->color = '#10b981';

        session()->flash('ok', 'Categoria criada.');
    }

    public function delete(int $id): void
    {
        Category::where('user_id', auth()->id())->where('id', $id)->delete();
    }

    public function render()
    {
        $monthStart = Carbon::now()->startOfMonth();

        $categories = auth()->user()->categories()
            ->withCount(['expenses as expenses_count' => fn ($q) => $q->where('spent_at', '>=', $monthStart)])
            ->withSum(['expenses as month_total' => fn ($q) => $q->where('spent_at', '>=', $monthStart)], 'amount')
            ->orderBy('name')
            ->get();

        return view('livewire.categories', compact('categories'));
    }
}
