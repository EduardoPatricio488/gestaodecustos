<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\{Client, Project, Invoice, Expense, Supplier, Product, Task, BusinessDocument, Income, Goal, Investment};
use Illuminate\Support\Facades\Auth;

class GlobalSearch extends Component
{
    public $search = '';
    public $isOpen = false;
    public $isBusinessMode = false;

    #[On('open-global-search')]
    public function openSearch()
    {
        $this->isOpen = true;
        $this->search = '';

        // Deteta se o utilizador estava numa página de empresa quando abriu a busca
        $url = url()->previous();
        $this->isBusinessMode = str_contains($url, '/empresa') || str_contains($url, '/company-expenses');
    }

    public function render()
    {
        $groupedResults = collect();

        if (strlen($this->search) >= 2) {
            $user = Auth::user();
            $workspaceId = $user->current_workspace_id;
            $term = "%{$this->search}%";
            $results = collect();

            if ($this->isBusinessMode) {
                // --- MODO EMPRESARIAL: NAVEGAÇÃO ---
                $pages = collect([
                    ['title' => 'Dashboard Business', 'type' => 'Navegação', 'icon' => 'chart-pie', 'url' => route('hub.business.dashboard')],
                    ['title' => 'Faturação & Vendas', 'type' => 'Navegação', 'icon' => 'document-text', 'url' => route('hub.business.invoices')],
                    ['title' => 'Propostas Comerciais', 'type' => 'Navegação', 'icon' => 'newspaper', 'url' => route('hub.business.proposals')],
                    ['title' => 'Clientes (CRM)', 'type' => 'Navegação', 'icon' => 'user-group', 'url' => route('hub.business.clients')],
                    ['title' => 'Projetos', 'type' => 'Navegação', 'icon' => 'briefcase', 'url' => route('hub.business.projects')],
                    ['title' => 'Stock & Inventário', 'type' => 'Navegação', 'icon' => 'archive-box', 'url' => route('hub.business.inventory')],
                    ['title' => 'Timeline de Operações', 'type' => 'Navegação', 'icon' => 'command-line', 'url' => route('hub.business.timeline')],
                    ['title' => 'Custos Empresa', 'type' => 'Navegação', 'icon' => 'building-office-2', 'url' => route('company-expenses')],
                ]);

                // --- MODO EMPRESARIAL: DADOS ---
                $results = $results->concat(Project::where('workspace_id', $workspaceId)->where('name', 'like', $term)->limit(3)->get()
                    ->map(fn($i) => ['type' => 'Projetos', 'title' => $i->name, 'sub' => 'Dados Business', 'icon' => 'briefcase', 'url' => route('hub.business.projects')]));

                $results = $results->concat(Client::where('workspace_id', $workspaceId)->where('name', 'like', $term)->limit(3)->get()
                    ->map(fn($i) => ['type' => 'Clientes', 'title' => $i->name, 'sub' => 'Dados Business', 'icon' => 'user-group', 'url' => route('hub.business.clients')]));

                $results = $results->concat(Invoice::where('workspace_id', $workspaceId)->where('client_name', 'like', $term)->limit(3)->get()
                    ->map(fn($i) => ['type' => 'Faturas', 'title' => $i->client_name, 'sub' => "#$i->invoice_number", 'icon' => 'document-text', 'url' => route('hub.business.invoices')]));

            } else {
                // --- MODO PESSOAL: NAVEGAÇÃO ---
                $pages = collect([
                    ['title' => 'Dashboard Pessoal', 'type' => 'Navegação', 'icon' => 'squares-2x2', 'url' => route('dashboard')],
                    ['title' => 'Receitas & Ganhos', 'type' => 'Navegação', 'icon' => 'arrow-trending-up', 'url' => route('hub.incomes')],
                    ['title' => 'Património Líquido', 'type' => 'Navegação', 'icon' => 'briefcase', 'url' => route('hub.networth')],
                    ['title' => 'Metas & Sonhos', 'type' => 'Navegação', 'icon' => 'trophy', 'url' => route('hub.goals')],
                    ['title' => 'Investimentos', 'type' => 'Navegação', 'icon' => 'chart-bar-square', 'url' => route('hub.investments')],
                    ['title' => 'Assinaturas', 'type' => 'Navegação', 'icon' => 'credit-card', 'url' => route('hub.subscriptions')],
                    ['title' => 'Agenda Financeira', 'type' => 'Navegação', 'icon' => 'calendar-days', 'url' => route('hub.calendar')],
                ]);

                // --- MODO PESSOAL: DADOS ---
                $results = $results->concat(Expense::where('user_id', $user->id)->where('is_company', false)->where('description', 'like', $term)->limit(5)->get()
                    ->map(fn($i) => ['type' => 'Despesas', 'title' => $i->description, 'sub' => number_format($i->amount, 2).'€', 'icon' => 'banknotes', 'url' => route('expenses')]));

                $results = $results->concat(Goal::where('user_id', $user->id)->where('name', 'like', $term)->get()
                    ->map(fn($i) => ['type' => 'Metas', 'title' => $i->name, 'sub' => 'Objetivo de Poupança', 'icon' => 'trophy', 'url' => route('hub.goals')]));
            }

            // Filtrar as páginas de navegação pelo termo
            $matchedPages = $pages->filter(fn($p) => str_contains(strtolower($p['title']), strtolower($this->search)));

            $results = $matchedPages->concat($results);
            $groupedResults = $results->groupBy('type');
        }

        return view('livewire.global-search', [
            'groupedResults' => $groupedResults
        ]);
    }
}
