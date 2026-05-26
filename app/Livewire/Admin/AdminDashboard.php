<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Expense;
use App\Models\Income;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AdminDashboard extends Component
{
    /**
     * Renderiza o painel de administração com estatísticas globais.
     */
    public function render()
    {
        // Puxamos a soma de todos os gastos e receitas de todos os utilizadores do site
        // Usamos withoutGlobalScopes() para saltar a barreira do "workspace_id"
        $totalExpenses = Expense::withoutGlobalScopes()->sum('amount');
        $totalIncomes = Income::withoutGlobalScopes()->sum('amount');

        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'bannedUsers' => User::where('is_active', false)->count(),
            'totalWorkspaces' => Workspace::count(),
            'totalCashflow' => $totalExpenses + $totalIncomes,
            'latestUsers' => User::latest()->take(5)->get(),
            'serverStatus' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database' => config('database.default'),
            ]
        ]);
    }
}
