<?php

namespace App\Livewire\Public;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

class SupplierPortal extends Component
{
    use WithFileUploads;

    public $tax_number = '';
    public $token = '';      // O Código (PIN)
    public $isLoggedIn = false;
    public $supplier = null;

    // Campos de submissão (para depois de entrar)
    public $amount, $notes, $invoice_doc;

    #[Layout('layouts.guest')]
   public function login()
{
    $this->validate([
        'tax_number' => 'required|string',
        'token'      => 'required|string|size:6',
    ]);

    $cleanNifInput = preg_replace('/\s+/', '', $this->tax_number);
    $cleanTokenInput = preg_replace('/\s+/', '', $this->token);

    $supplier = Supplier::whereRaw("REPLACE(tax_number, ' ', '') = ?", [$cleanNifInput])
                    ->where('portal_token', $cleanTokenInput)
                    ->first();

    if ($supplier) {
        // ✅ REDIRECIONA PARA A NOVA PÁGINA USANDO O TOKEN ÚNICO
        return redirect()->route('supplier.dashboard', ['token' => $supplier->portal_token]);
    }

    session()->flash('error', 'CREDENCIAIS INVÁLIDAS. VERIFICA O NIF E O CÓDIGO.');
}

    public function submitInvoice()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'invoice_doc' => 'required|file|mimes:pdf,jpg,png|max:10240',
        ]);

        // Lógica de gravação da fatura aqui...
        session()->flash('success', 'Fatura submetida com sucesso!');
        $this->reset(['amount', 'notes', 'invoice_doc']);
    }

    public function render()
    {
        return view('livewire.public.supplier-portal');
    }
}
