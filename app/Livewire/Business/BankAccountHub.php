<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\BankAccount;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class BankAccountHub extends Component
{
    public $name, $type = 'corrente', $balance = 0, $color = '#6366f1';
    public $editingId = null;
    public $search = '';
    public $isBusinessMode = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|in:corrente,poupanca,cash,credito',
        'balance' => 'required|numeric',
    ];

    public function mount()
    {
        // Deteta o modo baseado na rota
        $this->isBusinessMode = request()->routeIs('hub.business.*');
    }

    public function save()
    {
        $this->validate();

        auth()->user()->currentWorkspace->bankAccounts()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => auth()->id(),
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'type' => $this->type,
                'is_business' => $this->isBusinessMode,
                'balance' => $this->balance,
                'color' => $this->color,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'bank-modal');
        $this->dispatch('toast', text: 'Conta guardada!');
    }

    public function edit($id)
    {
        $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $this->editingId = $account->id;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->balance = $account->balance;
        $this->color = $account->color;
        $this->dispatch('modal-show', name: 'bank-modal');
    }

    public function delete($id)
    {
        $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        if ($account->expenses()->exists() || $account->incomes()->exists()) {
            $this->dispatch('toast', text: 'Esta conta tem histórico e não pode ser apagada.', variant: 'error');
            return;
        }
        $account->delete();
        $this->dispatch('toast', text: 'Conta removida.', variant: 'warning');
    }

    public function resetForm() { $this->reset(['name', 'type', 'balance', 'color', 'editingId']); }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        // Filtra as contas pelo modo atual (Pessoal vs Empresa)
        $accounts = $workspace->bankAccounts()
            ->where('is_business', $this->isBusinessMode)
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();

        $totalLiquidez = $accounts->where('type', '!=', 'credito')->sum(fn($a) => $a->current_balance);
        $totalDividaCartao = $accounts->where('type', 'credito')->sum(fn($a) => abs($a->current_balance));

        return view('livewire.business.bank-account-hub', [
            'accounts' => $accounts,
            'totalLiquidez' => (float) $totalLiquidez,
            'totalDividaCartao' => (float) $totalDividaCartao,
            'netCash' => (float) ($totalLiquidez - $totalDividaCartao),
            'modeTitle' => $this->isBusinessMode ? 'Contas da Empresa' : 'Contas Pessoais'
        ]);
    }
}
