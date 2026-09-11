<?php

namespace App\Livewire\Business;

use App\Models\Expense;
use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class SupplierHub extends Component
{
    use WithPagination;

    public $name;
    public $legal_name;
    public $tax_number;
    public $email;
    public $phone;
    public $payment_terms;
    public $address;
    public $editingId = null;
    public $generatedPasscode = '';
    public $supplierTaxNumber = '';
    public $generatedPortalUrl = '';
    public $search = '';

    public function updatedTaxNumber($value): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        $digits = substr($digits, 0, 9);
        $this->tax_number = implode(' ', str_split($digits, 3));
    }

    public function generatePortalLink($id)
    {
        $supplier = auth()->user()->suppliers()->findOrFail($id);

        if (! $supplier->portal_token) {
            do {
                $passcode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $exists = Supplier::where('portal_token', $passcode)->exists();
            } while ($exists);

            $supplier->update(['portal_token' => $passcode]);
            $supplier->refresh();
        }

        $this->generatedPasscode = $supplier->portal_token;
        $this->supplierTaxNumber = $supplier->tax_number;
        $this->generatedPortalUrl = route('supplier.portal');

        $this->dispatch('modal-show', name: 'supplier-portal-modal');
    }

    public $viewMode = 'grid';

    protected $rules = [
        'name' => 'required|string|max:100',
        'tax_number' => 'nullable|string|max:11',
        'email' => 'nullable|email',
        'payment_terms' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        $taxNumber = preg_replace('/\D/', '', (string) $this->tax_number);

        auth()->user()->suppliers()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'legal_name' => $this->legal_name,
                'tax_number' => $taxNumber !== '' ? $taxNumber : null,
                'email' => $this->email,
                'phone' => $this->phone,
                'payment_terms' => $this->payment_terms,
                'address' => $this->address,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'supplier-modal');
        $this->dispatch('toast', text: 'Fornecedor registado com sucesso!');
    }

    public function edit($id)
    {
        $supplier = auth()->user()->suppliers()->findOrFail($id);
        $this->editingId = $supplier->id;
        $this->name = $supplier->name;
        $this->legal_name = $supplier->legal_name;
        $this->tax_number = $supplier->tax_number ? implode(' ', str_split(preg_replace('/\D/', '', (string) $supplier->tax_number), 3)) : null;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->payment_terms = $supplier->payment_terms;
        $this->address = $supplier->address;

        $this->dispatch('modal-show', name: 'supplier-modal');
    }

    public function resetForm()
    {
        $this->reset(['name', 'legal_name', 'tax_number', 'email', 'phone', 'payment_terms', 'address', 'editingId']);
    }

    public function delete($id)
    {
        auth()->user()->suppliers()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Fornecedor removido da base de dados.', variant: 'warning');
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        $suppliers = Supplier::where('workspace_id', $workspaceId)
            ->where('name', 'like', '%'.$this->search.'%')
            ->get()
            ->map(function ($supplier) {
                $supplier->total_spent = Expense::where('supplier_id', $supplier->id)->sum('amount');
                $supplier->bills_count = Expense::where('supplier_id', $supplier->id)->count();

                return $supplier;
            })->sortByDesc('total_spent');

        return view('livewire.business.supplier-hub', [
            'suppliers' => $suppliers,
            'totalActiveSuppliers' => $suppliers->count(),
            'topSupplier' => $suppliers->first(),
        ]);
    }
}
