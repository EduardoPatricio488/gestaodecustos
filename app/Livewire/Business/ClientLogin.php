<?php

namespace App\Livewire\Business;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ClientLogin extends Component
{
    public $tax_number = ''; // ✅ Novo campo para o NIF
    public $token = '';      // O Código (PIN)

    #[Layout('layouts.guest')]
   public function login()
{
    $this->validate([
        'tax_number' => 'required|string',
        'token'      => 'required|string|size:6',
    ]);

    // 1. Limpamos os espaços de tudo o que o utilizador digitou
    $cleanNifInput = preg_replace('/\s+/', '', $this->tax_number);
    $cleanTokenInput = preg_replace('/\s+/', '', $this->token);

    // 2. Procuramos na BD limpando também os espaços da coluna tax_number
    // Isto garante que '500123987' encontre '500 123 987'
    $client = Client::whereRaw("REPLACE(tax_number, ' ', '') = ?", [$cleanNifInput])
                    ->where('portal_token', $cleanTokenInput)
                    ->first();

    if ($client) {
        return redirect()->route('client.portal', ['token' => $client->portal_token]);
    }

    session()->flash('error', 'CREDENCIAIS INVÁLIDAS. VERIFICA O NIF E O CÓDIGO.');
}
    public function render()
    {
        return view('livewire.business.client-login');
    }
}
