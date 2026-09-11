<?php

namespace App\Livewire\Business;

use App\Mail\SupplierPortalAccessMail;
use App\Models\Expense;
use App\Models\PortalAccessRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\Mail;
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

    public function sendPortalEmail(): void
    {
        $supplier = auth()->user()->suppliers()
            ->where('portal_token', $this->generatedPasscode)
            ->firstOrFail();

        if (! $supplier->email) {
            $this->dispatch('toast', text: 'Este fornecedor não tem email registado.', variant: 'warning');

            return;
        }

        Mail::to($supplier->email)->send(new SupplierPortalAccessMail(
            $supplier,
            auth()->user()->currentWorkspace,
            $supplier->portal_token,
            $this->generatedPortalUrl,
        ));

        $this->dispatch('toast', text: 'Código de acesso enviado para '.$supplier->email.'.', variant: 'success');
    }

    public function getPendingAccessRequests(): array
    {
        return $this->pendingAccessRequestsQuery()
            ->latest('requested_at')
            ->limit(50)
            ->get()
            ->map(fn (PortalAccessRequest $request) => [
                'id' => $request->id,
                'name' => $request->requester_name,
                'email' => $request->requester_email,
                'tax_number' => $request->tax_number,
                'requested_at' => optional($request->requested_at)->format('d/m/Y H:i'),
            ])
            ->values()
            ->all();
    }

    public function approveAccessRequest($id): void
    {
        $request = $this->pendingAccessRequestsQuery()->findOrFail($id);

        $taxNumber = preg_replace('/\D/', '', (string) $request->tax_number);
        $taxNumber = substr($taxNumber, 0, 9);

        $supplierQuery = auth()->user()->suppliers();

        $supplier = null;
        if ($request->requester_email) {
            $supplier = (clone $supplierQuery)->where('email', $request->requester_email)->first();
        }

        if (! $supplier && $taxNumber) {
            $supplier = (clone $supplierQuery)->where('tax_number', $taxNumber)->first();
        }

        if (! $supplier) {
            $supplier = Supplier::create([
                'user_id' => auth()->id(),
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $request->requester_name,
                'legal_name' => $request->requester_name,
                'tax_number' => $taxNumber ?: null,
                'email' => $request->requester_email,
                'portal_token' => $this->generateUniquePortalToken(),
            ]);
        } else {
            $supplier->update([
                'name' => $supplier->name ?: $request->requester_name,
                'email' => $supplier->email ?: $request->requester_email,
                'tax_number' => $supplier->tax_number ?: ($taxNumber ?: null),
                'portal_token' => $supplier->portal_token ?: $this->generateUniquePortalToken(),
            ]);
            $supplier->refresh();
        }

        $portalUrl = route('supplier.portal');

        Mail::to($supplier->email)->send(new SupplierPortalAccessMail(
            $supplier,
            auth()->user()->currentWorkspace,
            $supplier->portal_token,
            $portalUrl,
        ));

        $request->update([
            'status' => 'approved',
            'responded_at' => now(),
        ]);

        $this->dispatch('toast', text: 'Pedido aprovado. Fornecedor criado e credenciais enviadas por email.', variant: 'success');
        $this->dispatch('supplier-access-request-updated');
    }

    public function rejectAccessRequest($id): void
    {
        $request = $this->pendingAccessRequestsQuery()->findOrFail($id);

        $request->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        $this->dispatch('toast', text: 'Pedido de acesso rejeitado.', variant: 'warning');
        $this->dispatch('supplier-access-request-updated');
    }

    private function pendingAccessRequestsQuery()
    {
        return PortalAccessRequest::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('portal_type', 'supplier')
            ->where('status', 'pending');
    }

    private function generateUniquePortalToken(): string
    {
        do {
            $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Supplier::where('portal_token', $token)->exists());

        return $token;
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
