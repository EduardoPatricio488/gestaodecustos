<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\BankAccount;
use App\Models\Income;
use App\Models\Expense;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class BankAccountHub extends Component
{
    // CAMPOS PRINCIPAIS
    public $name;
    public $type = 'corrente';
    public $historyTransactions = [];
public $selectedAccountName = '';
    public $balance = 0;
    public $color = '#6366f1';
    public $editingId = null;
    public $search = '';
    public $isBusinessMode = false;

    // DADOS BANCÁRIOS
    public $bank_name;
    public $country;
    public $iban;
    public $swift;
    public $holder_name;

    // FINANCEIRO AVANÇADO
    public $credit_limit;
    public $forecast_balance;
    public $risk_score;
    public $generatedAuditCode = '';
public $companyTaxNumber = '';


    // TAGS E NOTAS
    public $tags_input;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|string',
        'balance' => 'required|numeric',
        'iban' => 'nullable|string|max:50',
        'swift' => 'nullable|string|max:20',
        'bank_name' => 'nullable|string|max:100',
        'country' => 'nullable|string|max:50',
        'holder_name' => 'nullable|string|max:100',
        'credit_limit' => 'nullable|numeric',
        'forecast_balance' => 'nullable|numeric',
        'risk_score' => 'nullable|numeric|min:0|max:100',
        'tags_input' => 'nullable|string',
        'notes' => 'nullable|string',
    ];



public function generateAuditCode()
{
    $workspace = auth()->user()->currentWorkspace;

    // 1. Verificamos se a empresa já tem um token de auditoria.
    // Se não tiver (estiver NULL ou vazio), geramos um NOVO para sempre.
    if (!$workspace->audit_token) {
        do {
            $passcode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            // Garante que este token não existe em mais nenhuma empresa na BD
            $exists = \App\Models\Workspace::where('audit_token', $passcode)->exists();
        } while ($exists);

        $workspace->update(['audit_token' => $passcode]);
    }

    // 2. Preenchemos as variáveis do modal com o código permanente da base de dados
    $this->generatedAuditCode = $workspace->audit_token;
    $this->companyTaxNumber = $workspace->tax_number;

    // 3. Abrimos o modal
    $this->dispatch('modal-show', name: 'audit-code-modal');
}
    public function mount()
    {
        $this->isBusinessMode = request()->routeIs('hub.business.*');
    }

    public function save()
    {
        $this->validate();

        $tags = $this->tags_input
            ? collect(explode(',', $this->tags_input))
                ->map(fn($t) => trim($t))
                ->filter()
                ->values()
                ->toArray()
            : [];

        auth()->user()->currentWorkspace->bankAccounts()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => auth()->id(),
                'workspace_id' => auth()->user()->current_workspace_id,

                'name' => $this->name,
                'type' => $this->type,
                'is_business' => $this->isBusinessMode,

                'bank_name' => $this->bank_name,
                'country' => $this->country,
                'iban' => $this->iban,
                'swift' => $this->swift,
                'holder_name' => $this->holder_name,

                'balance' => $this->balance,
                'credit_limit' => $this->credit_limit,
                'forecast_balance' => $this->forecast_balance,
                'risk_score' => $this->risk_score,

                'tags' => $tags,
                'notes' => $this->notes,

                'color' => $this->color,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'bank-modal');
        $this->dispatch('toast', text: 'Conta guardada com sucesso!');
    }
public function openHistory($id)
{
    $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
    $this->selectedAccountName = $account->name;

    // Buscar as últimas 30 despesas desta conta
    $expenses = $account->expenses()->with('category')->latest()->take(30)->get()->map(fn($e) => [
        'date' => $e->spent_at,
        'desc' => $e->description ?: $e->category->name,
        'amount' => -$e->amount,
        'type' => 'expense'
    ]);

    // Buscar as últimas 30 receitas desta conta
    $incomes = $account->incomes()->latest()->take(30)->get()->map(fn($i) => [
        'date' => $i->received_at,
        'desc' => $i->description,
        'amount' => $i->amount,
        'type' => 'income'
    ]);

    // Juntar tudo, ordenar por data e transformar em array
    $this->historyTransactions = $expenses->concat($incomes)
        ->sortByDesc('date')
        ->take(30)
        ->toArray();

    $this->dispatch('modal-show', name: 'account-history-modal');
}
    public function edit($id)
    {
        $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        $this->editingId = $account->id;

        $this->name = $account->name;
        $this->type = $account->type;
        $this->color = $account->color;

        $this->bank_name = $account->bank_name;
        $this->country = $account->country;
        $this->iban = $account->iban;
        $this->swift = $account->swift;
        $this->holder_name = $account->holder_name;

        $this->balance = $account->balance;
        $this->credit_limit = $account->credit_limit;
        $this->forecast_balance = $account->forecast_balance;
        $this->risk_score = $account->risk_score;

        $this->tags_input = $account->tags ? implode(', ', $account->tags) : '';
        $this->notes = $account->notes;

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

    public function resetForm()
    {
        $this->reset([
            'name', 'type', 'balance', 'color', 'editingId',
            'bank_name', 'country', 'iban', 'swift', 'holder_name',
            'credit_limit', 'forecast_balance', 'risk_score',
            'tags_input', 'notes'
        ]);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $accounts = $workspace->bankAccounts()
            ->where('is_business', $this->isBusinessMode)
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();

        // KPIs BASE
        $totalLiquidez = $accounts
            ->where('type', '!=', 'credito')
            ->sum(fn($a) => $a->current_balance);

        $totalDividaCartao = $accounts
            ->where('type', 'credito')
            ->sum(fn($a) => abs($a->current_balance));

        $forecastCash = $accounts->sum(fn($a) => $a->forecast_balance ?? $a->current_balance);

        $globalRisk = round($accounts->avg('risk_score') ?? 0);

        // KPIs AVANÇADOS
        $creditAccounts = $accounts->where('type', 'credito');

        $limiteTotalCartoes = $creditAccounts->sum('credit_limit');

        $percentUtilizacao = $limiteTotalCartoes > 0
            ? round(($totalDividaCartao / $limiteTotalCartoes) * 100, 1)
            : 0;

        $riscoCartoes = round($creditAccounts->avg('risk_score') ?? 0);

        // Fluxos do dia
        $entradasHoje = Income::where('workspace_id', $workspace->id)->sum('amount');
        $saidasHoje = Expense::where('workspace_id', $workspace->id)->sum('amount');
        $fluxoHoje = $entradasHoje - $saidasHoje;

        // Forecast avançado
        $forecast7 = $forecastCash + ($fluxoHoje * 7);
        $forecast30 = $forecastCash + ($fluxoHoje * 30);

        return view('livewire.business.bank-account-hub', [
            'accounts' => $accounts,

            // KPIs BASE
            'totalLiquidez' => (float) $totalLiquidez,
            'totalDividaCartao' => (float) $totalDividaCartao,
            'netCash' => (float) ($totalLiquidez - $totalDividaCartao),
            'forecastCash' => (float) $forecastCash,
            'globalRisk' => $globalRisk,

            // KPIs AVANÇADOS
            'limiteTotalCartoes' => $limiteTotalCartoes,
            'percentUtilizacao' => $percentUtilizacao,
            'riscoCartoes' => $riscoCartoes,

            'entradasHoje' => $entradasHoje,
            'saidasHoje' => $saidasHoje,
            'fluxoHoje' => $fluxoHoje,

            'forecast7' => $forecast7,
            'forecast30' => $forecast30,

            'modeTitle' => $this->isBusinessMode ? 'Contas da Empresa' : 'Contas Pessoais'
        ]);
    }
}
