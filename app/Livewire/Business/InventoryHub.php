<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class InventoryHub extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    // Campos do formulário
    public $name, $sku, $stock_quantity = 0, $min_stock_alert = 5, $unit_cost = 0, $unit_price = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'stock_quantity' => 'required|integer|min:0',
        'unit_cost' => 'required|numeric|min:0',
        'unit_price' => 'required|numeric|min:0',
    ];

    public function save()
    {
        $this->validate();

        auth()->user()->currentWorkspace->products()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'sku' => $this->sku,
                'stock_quantity' => $this->stock_quantity,
                'min_stock_alert' => $this->min_stock_alert,
                'unit_cost' => $this->unit_cost,
                'unit_price' => $this->unit_price,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'product-modal');
        $this->dispatch('toast', text: 'Inventário atualizado!');
    }

    public function edit($id)
    {
        $product = auth()->user()->currentWorkspace->products()->findOrFail($id);
        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->stock_quantity = $product->stock_quantity;
        $this->min_stock_alert = $product->min_stock_alert;
        $this->unit_cost = $product->unit_cost;
        $this->unit_price = $product->unit_price;

        $this->dispatch('modal-show', name: 'product-modal');
    }

    public function delete($id)
    {
        auth()->user()->currentWorkspace->products()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Produto removido.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['name', 'sku', 'stock_quantity', 'min_stock_alert', 'unit_cost', 'unit_price', 'editingId']);
    }

    public function render()
    {
        $products = auth()->user()->currentWorkspace->products()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return view('livewire.business.inventory-hub', [
            'products' => $products,
            'totalInventoryValue' => $products->sum(fn($p) => $p->stock_quantity * $p->unit_price),
            'lowStockCount' => $products->filter(fn($p) => $p->stock_quantity <= $p->min_stock_alert)->count(),
        ]);
    }
}
