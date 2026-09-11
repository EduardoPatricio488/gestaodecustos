<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Categories extends Component
{
    public string $name = '';

    public string $color = '#10b981';

    public $editBudgetLimit;

    public string $editIcon = 'tag';

    public string $icon = 'tag';

    public $budget_limit;

    public array $availableIcons = [
        'tag', 'banknotes', 'credit-card', 'calculator', 'briefcase', 'shield-check', 'key', 'currency-euro',
        'home', 'truck', 'bolt', 'light-bulb', 'wrench-screwdriver', 'map-pin', 'phone', 'wifi', 'fire',
        'shopping-cart', 'shopping-bag', 'gift', 'ticket', 'film', 'musical-note', 'camera', 'puzzle-piece', 'trophy', 'star',
        'heart', 'beaker', 'sun', 'face-smile', 'hand-thumb-up', 'user',
        'academic-cap', 'book-open', 'cpu-chip', 'globe-alt', 'sparkles', 'swatch', 'clipboard-document-list', 'cog-6-tooth',
        'users', 'chat-bubble-left-right', 'ellipsis-horizontal', 'magnifying-glass', 'plus',
    ];

    public ?int $editingId = null;

    public string $editName = '';

    public string $editColor = '#10b981';

    private function workspaceId(): int
    {
        return auth()->user()->currentWorkspace?->id ?? 0;
    }

    private function ensureOrdersAreSet()
    {
        $categories = Category::where('workspace_id', $this->workspaceId())
            ->whereNull('order')
            ->get();

        if ($categories->count() > 0) {
            $max = Category::where('workspace_id', $this->workspaceId())->max('order') ?? 0;
            foreach ($categories as $cat) {
                $max++;
                $cat->update(['order' => $max]);
            }
        }
    }

    public function moveUp($id)
    {
        $this->ensureOrdersAreSet();
        $category = Category::where('workspace_id', $this->workspaceId())->findOrFail($id);

        $prev = Category::where('workspace_id', $this->workspaceId())
            ->where('order', '<', $category->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($prev) {
            $oldOrder = $category->order;
            $category->update(['order' => $prev->order]);
            $prev->update(['order' => $oldOrder]);
            $this->clearSidebarCache();
        }
    }

    public function moveDown($id)
    {
        $this->ensureOrdersAreSet();
        $category = Category::where('workspace_id', $this->workspaceId())->findOrFail($id);

        $next = Category::where('workspace_id', $this->workspaceId())
            ->where('order', '>', $category->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($next) {
            $oldOrder = $category->order;
            $category->update(['order' => $next->order]);
            $next->update(['order' => $oldOrder]);
            $this->clearSidebarCache();
        }
    }

    private function clearSidebarCache()
    {
        Cache::forget("layout:user-categories:{$this->workspaceId()}");
        Cache::forget("layout:sidebar:categories:v100:{$this->workspaceId()}");
        Cache::forget("layout:sidebar:categories:v300:{$this->workspaceId()}");
        Cache::forget("layout:sidebar:categories:v400:{$this->workspaceId()}");
    }

    public function save(): mixed
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:9',
            'icon' => 'required|string|max:50',
        ]);

        $maxOrder = Category::where('workspace_id', $this->workspaceId())->max('order') ?? 0;
        $slug = Str::slug($this->name, '-');
        $baseSlug = $slug;
        $i = 2;
        while (Category::where('workspace_id', $this->workspaceId())->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        Category::create([
            'user_id' => auth()->id(),
            'workspace_id' => $this->workspaceId(),
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->icon,
            'slug' => $slug,
            'budget_limit' => $this->budget_limit,
            'is_fixed' => false,
            'order' => $maxOrder + 1,
        ]);

        $this->reset(['name', 'icon', 'budget_limit']);
        Cache::flush();

        return $this->redirect(route('categories'), navigate: true);
    }

    public function update(): mixed
    {
        $this->validate([
            'editName' => 'required|string|max:50',
            'editColor' => 'required|string|max:9',
            'editIcon' => 'required|string|max:50',
            'editBudgetLimit' => 'nullable|numeric|min:0',
        ]);

        $category = Category::where('workspace_id', $this->workspaceId())->findOrFail($this->editingId);
        $newSlug = Str::slug($this->editName, '-');
        $tempSlug = $newSlug;
        $i = 2;
        while (Category::where('workspace_id', $this->workspaceId())
            ->where('slug', $tempSlug)
            ->where('id', '!=', $category->id)
            ->exists()) {
            $tempSlug = $newSlug.'-'.$i++;
        }

        $category->update([
            'name' => $this->editName,
            'slug' => $tempSlug,
            'color' => $this->editColor,
            'icon' => $this->editIcon,
            'budget_limit' => $this->editBudgetLimit,
        ]);

        Cache::flush();
        $this->cancelEdit();

        return $this->redirect(route('categories'), navigate: true);
    }

    public function delete(int $id): mixed
    {
        $category = Category::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('user_id', auth()->id())
            ->whereKey($id)
            ->where('is_fixed', false)
            ->first();

        if (! $category) {
            return null;
        }

        $hasSubscriptions = Subscription::where('category_id', $category->id)->exists();
        $hasExpenses = Expense::where('category_id', $category->id)->exists();

        if ($hasSubscriptions || $hasExpenses) {
            $this->dispatch('toast',
                text: 'Não podes apagar esta categoria porque existem gastos ou subscrições associadas a ela.',
                variant: 'error'
            );

            return null;
        }

        $category->delete();
        $this->clearSidebarCache();
        $this->dispatch('toast', text: 'Categoria eliminada com sucesso.');

        return $this->redirect(route('categories'), navigate: true);
    }

    public function startEdit(int $id)
    {
        $category = Category::where('workspace_id', $this->workspaceId())->findOrFail($id);
        $this->editingId = $category->id;
        $this->editName = $category->name;
        $this->editColor = $category->color ?? '#10b981';
        $this->editIcon = $category->icon ?? 'tag';
        $this->editBudgetLimit = $category->budget_limit;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'editName', 'editColor', 'editIcon']);
    }

    public function updateOrder($items)
    {
        $wsId = auth()->user()->current_workspace_id;

        foreach ($items as $item) {
            Category::where('id', $item['value'])
                ->where('workspace_id', $wsId)
                ->update(['order' => $item['order']]);
        }

        Cache::flush();

        return $this->redirect(route('categories'), navigate: true);
    }

    public function render()
    {
        $monthStart = Carbon::now()->startOfMonth();

        $categories = Category::where('workspace_id', $this->workspaceId())
            ->where('hidden_from_sidebar', false)
            ->withCount(['expenses as expenses_count' => fn ($q) => $q->where('workspace_id', $this->workspaceId())
                ->where('spent_at', '>=', $monthStart),
            ])
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->unique('name');

        return view('livewire.categories', compact('categories'));
    }
}
