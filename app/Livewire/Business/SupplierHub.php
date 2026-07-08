<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Expense;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SupplierHub extends Component
{
    use WithPagination;

    // Propriedades do Formulário
    public $name, $legal_name, $tax_number, $email, $phone, $payment_terms, $address;
    public $editingId = null;
   public $generatedPasscode = '';
public $supplierTaxNumber = '';
public $generatedPortalUrl = '';
    public $search = '';

// No topo da classe, garante que tens estas 3 variáveis públicas


public function generatePortalLink($id)
{
    // 1. Procurar o fornecedor
    $supplier = auth()->user()->suppliers()->findOrFail($id);

    // 2. Se o fornecedor ainda não tiver código, gerar um agora
    if (!$supplier->portal_token) {
        do {
            $passcode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $exists = \App\Models\Supplier::where('portal_token', $passcode)->exists();
        } while ($exists);

        $supplier->update(['portal_token' => $passcode]);
        // Atualiza a instância local para ter o código novo
        $supplier->refresh();
    }

    // 3. ATRIBUIR OS VALORES ÀS VARIÁVEIS PÚBLICAS (Obrigatório para o Blade ver)
    $this->generatedPasscode = $supplier->portal_token;
    $this->supplierTaxNumber = $supplier->tax_number;
    $this->generatedPortalUrl = route('supplier.portal');

    // 4. Abrir o modal
    $this->dispatch('modal-show', name: 'supplier-portal-modal');
}
    // Filtros de Visualização
    public $viewMode = 'grid'; // 'grid' ou 'list'

    protected $rules = [
        'name' => 'required|string|max:100',
        'tax_number' => 'nullable|string|max:20',
        'email' => 'nullable|email',
        'payment_terms' => 'nullable|string',
    ];

    /**
     * Guardar ou Atualizar Fornecedor
     */
    public function save()
    {
        $this->validate();

        auth()->user()->suppliers()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'legal_name' => $this->legal_name,
                'tax_number' => $this->tax_number,
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
        $this->tax_number = $supplier->tax_number;
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

        // Query principal de fornecedores
        $suppliers = Supplier::where('workspace_id', $workspaceId)
            ->where('name', 'like', '%' . $this->search . '%')
            ->get()
            ->map(function ($supplier) {
                // Inteligência: Calcula quanto já gastámos com este fornecedor
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
