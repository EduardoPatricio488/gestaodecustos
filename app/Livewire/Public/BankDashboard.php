<?php

namespace App\Livewire\Public;

use App\Models\Workspace;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Income;
use Livewire\Component;
use Livewire\Attributes\Layout;

class BankDashboard extends Component
{
    public $workspace;

    #[Layout('layouts.guest')]
    public function mount($token)
    {
        // Valida se o token de auditoria existe
        $this->workspace = Workspace::where('audit_token', $token)->firstOrFail();
    }

    public function render()
    {
        // 1. Cálculos de Liquidez Real
        $accounts = BankAccount::where('workspace_id', $this->workspace->id)->get();
        $totalLiquidez = $accounts->where('type', '!=', 'credito')->sum('current_balance');
        $totalPassivo = $accounts->where('type', 'credito')->sum(fn($a) => abs($a->current_balance));

        // 2. Rácios Financeiros (Fórmulas Reais)
        $ratioLiquidez = $totalPassivo > 0 ? ($totalLiquidez / $totalPassivo) : 10;

        // Determinar Rating baseado no rácio
        $rating = 'C';
        if($ratioLiquidez > 5) $rating = 'A+';
        elseif($ratioLiquidez > 3) $rating = 'A';
        elseif($ratioLiquidez > 1.5) $rating = 'B';

        return view('livewire.public.bank-dashboard', [
            'accounts' => $accounts,
            'liquidez' => $totalLiquidez,
            'passivo' => $totalPassivo,
            'rating' => $rating,
            'solvencia' => $ratioLiquidez * 10,
        ]);
    }
}
