<?php

namespace App\Livewire\Public;

use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BankDashboard extends Component
{
    public $workspace;

    #[Layout('layouts.guest')]
    public function mount()
    {
        $this->workspace = $this->authenticatedWorkspace();
    }

    private function authenticatedWorkspace(): Workspace
    {
        $workspaceId = session('bank_portal_workspace_id');
        abort_unless($workspaceId, 403);

        $workspace = Workspace::whereKey($workspaceId)
            ->where('audit_token_purpose', 'bank_audit')
            ->whereNull('audit_token_revoked_at')
            ->where(function ($query) {
                $query->whereNull('audit_token_expires_at')
                    ->orWhere('audit_token_expires_at', '>', now());
            })
            ->first();

        abort_unless($workspace, 403);

        return $workspace;
    }

    public function render()
    {
        $this->workspace = $this->authenticatedWorkspace();

        // 1. Cálculos de Liquidez Real
        $accounts = $this->workspace->bankAccounts()->get();
        $totalLiquidez = $accounts->where('type', '!=', 'credito')->sum('current_balance');
        $totalPassivo = $accounts->where('type', 'credito')->sum(fn ($a) => abs($a->current_balance));

        // 2. Rácios Financeiros (Fórmulas Reais)
        $ratioLiquidez = $totalPassivo > 0 ? ($totalLiquidez / $totalPassivo) : 10;

        // Determinar Rating baseado no rácio
        $rating = 'C';
        if ($ratioLiquidez > 5) {
            $rating = 'A+';
        } elseif ($ratioLiquidez > 3) {
            $rating = 'A';
        } elseif ($ratioLiquidez > 1.5) {
            $rating = 'B';
        }

        return view('livewire.public.bank-dashboard', [
            'accounts' => $accounts,
            'liquidez' => $totalLiquidez,
            'passivo' => $totalPassivo,
            'rating' => $rating,
            'solvencia' => $ratioLiquidez * 10,
        ]);
    }
}
