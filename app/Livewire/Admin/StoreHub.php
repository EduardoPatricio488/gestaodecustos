<?php

namespace App\Livewire\Admin;

use App\Models\StoreProduct;
use App\Models\StorePurchase;
use App\Services\StoreAdminService;
use App\Services\StoreCatalogService;
use App\Services\StoreTabsService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class StoreHub extends Component
{
    use WithPagination;

    public string $section = 'overview';

    public string $productSearch = '';

    public string $purchaseSearch = '';

    public ?int $editingProductId = null;

    public string $title = '';

    public string $slug = '';

    public string $type = 'ia';

    public string $description = '';

    public string $long_content = '';

    public $price = '';

    public string $image = '📦';

    public string $badge = '';

    public bool $is_featured = false;

    public $points_reward = 0;

    public string $featuresRaw = '';

    /** @var array<int, array{key: string, label: string, visible: bool, order: int}> */
    public array $storeTabs = [];

    public function mount(StoreTabsService $tabsService): void
    {
        $this->storeTabs = $tabsService->all();
    }

    public function setSection(string $section): void
    {
        $this->section = $section;
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->resetProductForm();
        $this->editingProductId = 0;
    }

    public function startEdit(int $id): void
    {
        $product = StoreProduct::findOrFail($id);

        $this->editingProductId = $product->id;
        $this->title = $product->title;
        $this->slug = $product->slug;
        $this->type = $product->type;
        $this->description = $product->description;
        $this->long_content = $product->long_content ?? '';
        $this->price = $product->price;
        $this->image = $product->image ?? '📦';
        $this->badge = $product->badge ?? '';
        $this->is_featured = (bool) $product->is_featured;
        $this->points_reward = $product->points_reward ?? 0;
        $this->featuresRaw = is_array($product->features)
            ? implode("\n", $product->features)
            : '';
    }

    public function cancelEdit(): void
    {
        $this->editingProductId = null;
        $this->resetProductForm();
    }

    public function saveProduct(): void
    {
        $this->validate([
            'title' => 'required|string|max:120',
            'type' => 'required|in:ia,widget,automation,data,course,guide,pack,plan',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'image' => 'required|string|max:20',
            'points_reward' => 'nullable|integer|min:0',
        ]);

        $slug = filled($this->slug)
            ? Str::slug($this->slug)
            : Str::slug($this->title);

        $baseSlug = $slug;
        $i = 2;
        while (StoreProduct::where('slug', $slug)
            ->when($this->editingProductId, fn ($q) => $q->where('id', '!=', $this->editingProductId))
            ->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $features = collect(preg_split('/\r\n|\r|\n/', $this->featuresRaw))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $data = [
            'title' => $this->title,
            'slug' => $slug,
            'type' => $this->type,
            'description' => $this->description,
            'long_content' => $this->long_content ?: null,
            'price' => $this->price,
            'image' => $this->image,
            'badge' => $this->badge ?: null,
            'is_featured' => $this->is_featured,
            'points_reward' => (int) $this->points_reward,
            'features' => $features ?: null,
        ];

        if ($this->editingProductId) {
            StoreProduct::findOrFail($this->editingProductId)->update($data);
            $message = 'Produto atualizado com sucesso.';
        } else {
            StoreProduct::create($data);
            $message = 'Produto criado com sucesso.';
        }

        app(StoreCatalogService::class)->clearCache();
        $this->cancelEdit();
        $this->dispatch('toast', text: $message);
    }

    public function deleteProduct(int $id): void
    {
        $product = StoreProduct::findOrFail($id);

        if ($product->purchases()->exists()) {
            $this->dispatch('toast', text: 'Este produto tem compras registadas — não pode ser eliminado.');

            return;
        }

        $product->delete();
        app(StoreCatalogService::class)->clearCache();
        $this->dispatch('toast', text: 'Produto eliminado.');
    }

    public function toggleFeatured(int $id): void
    {
        $product = StoreProduct::findOrFail($id);
        $product->update(['is_featured' => ! $product->is_featured]);
        app(StoreCatalogService::class)->clearCache();
    }

    public function saveStoreTabs(StoreTabsService $tabsService): void
    {
        $tabsService->save($this->storeTabs);
        $this->storeTabs = $tabsService->all();
        $this->dispatch('toast', text: 'Abas da loja atualizadas.');
    }

    public function resetStoreTabs(StoreTabsService $tabsService): void
    {
        $tabsService->reset();
        $this->storeTabs = $tabsService->all();
        $this->dispatch('toast', text: 'Abas repostas para o padrão.');
    }

    public function moveTabUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->storeTabs[$index])) {
            return;
        }

        [$this->storeTabs[$index - 1], $this->storeTabs[$index]] = [$this->storeTabs[$index], $this->storeTabs[$index - 1]];
        $this->reindexTabs();
    }

    public function moveTabDown(int $index): void
    {
        if (! isset($this->storeTabs[$index + 1])) {
            return;
        }

        [$this->storeTabs[$index + 1], $this->storeTabs[$index]] = [$this->storeTabs[$index], $this->storeTabs[$index + 1]];
        $this->reindexTabs();
    }

    private function reindexTabs(): void
    {
        foreach ($this->storeTabs as $i => &$tab) {
            $tab['order'] = $i;
        }
    }

    private function resetProductForm(): void
    {
        $this->title = '';
        $this->slug = '';
        $this->type = 'ia';
        $this->description = '';
        $this->long_content = '';
        $this->price = '';
        $this->image = '📦';
        $this->badge = '';
        $this->is_featured = false;
        $this->points_reward = 0;
        $this->featuresRaw = '';
    }

    public function render(StoreAdminService $admin, StoreTabsService $tabsService)
    {
        $productsQuery = StoreProduct::query()->orderByDesc('is_featured')->orderBy('title');

        if ($this->productSearch !== '') {
            $productsQuery->where(function ($q) {
                $q->where('title', 'like', '%'.$this->productSearch.'%')
                    ->orWhere('type', 'like', '%'.$this->productSearch.'%');
            });
        }

        $purchasesQuery = StorePurchase::query()
            ->with(['user:id,name,email', 'product:id,title,type,image'])
            ->where('payment_status', 'completed')
            ->latest();

        if ($this->purchaseSearch !== '') {
            $purchasesQuery->where(function ($q) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$this->purchaseSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->purchaseSearch.'%'))
                    ->orWhereHas('product', fn ($p) => $p->where('title', 'like', '%'.$this->purchaseSearch.'%'));
            });
        }

        $stats = $admin->overviewStats();
        $trend = $admin->purchasesTrend();
        $maxTrend = max(1, $trend->max('count'));

        return view('livewire.admin.store-hub', [
            'stats' => $stats,
            'topProducts' => $admin->topProducts(),
            'salesByType' => $admin->salesByType(),
            'trend' => $trend,
            'maxTrend' => $maxTrend,
            'products' => $productsQuery->get(),
            'purchases' => $purchasesQuery->paginate(12),
            'productTypes' => $admin->productTypes(),
            'tabKeys' => collect($tabsService->all())->pluck('label', 'key')->all(),
        ]);
    }
}
