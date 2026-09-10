<?php

namespace App\Livewire\Business;

use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BankAccountHub extends Component
{
    public $name;
    public $type = 'corrente';
    public $historyTransactions = [];
    public $selectedAccountName = '';
    public $balance = 0;
    public $color = '#6366f1';
    public $editingId = null;
    public $search = '';
    public $isBusinessMode = false;
    public $bank_name;
    public $country;
    public $iban;
    public $swift;
    public $holder_name;
    public $credit_limit;
    public $forecast_balance;
    public $risk_score;
    public $generatedAuditCode = '';
    public $companyTaxNumber = '';
    public $tags_input;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|string',
        'balance' => 'required|numeric',
        'iban' => ['nullable', 'string', 'regex:/^PT50[0-9]{21}$/'],
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
        $plainToken = $workspace->audit_access_code;
        if (! $plainToken) {
            do {
                $plainToken = strtoupper(Str::random(8));
            } while (Workspace::where('audit_access_code', $plainToken)->exists());
        }
        $workspace->update([
            'audit_token' => Hash::make($plainToken),
            'audit_access_code' => $plainToken,
            'audit_token_expires_at' => null,
            'audit_token_revoked_at' => null,
            'audit_token_purpose' => 'bank_audit',
        ]);
        $this->generatedAuditCode = $plainToken;
        $this->companyTaxNumber = $workspace->tax_number;
        $this->dispatch('modal-show', name: 'audit-code-modal');
    }

    public function revokeAuditCode(): void
    {
        $workspace = auth()->user()->currentWorkspace;
        $workspace->update(['audit_token_revoked_at' => now()]);
        $this->generatedAuditCode = '';
        $this->dispatch('modal-close', name: 'audit-code-modal');
        $this->dispatch('toast', text: 'Acesso bancário revogado.');
    }

    public function mount() { $this->isBusinessMode = request()->routeIs('hub.business.*'); }

    private function moneyValue($value): ?float
    {
        if ($value === null || trim((string) $value) === '') return null;
        $value = str_replace(['€', ' '], '', (string) $value);
        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        return is_numeric($value) ? (float) $value : null;
    }

    private function formatMoney($value): string
    {
        $number = $this->moneyValue($value);
        return $number === null ? '' : number_format($number, 2, ',', ' ');
    }

    private function normalizeIban($value): string { return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $value)); }
    private function formatIban($value): string { return trim(implode(' ', str_split($this->normalizeIban($value), 4))); }

    public function save()
    {
        $this->iban = $this->normalizeIban($this->iban);
        $this->balance = $this->moneyValue($this->balance) ?? 0;
        $this->credit_limit = $this->moneyValue($this->credit_limit);
        $this->forecast_balance = $this->moneyValue($this->forecast_balance);
        $this->risk_score = ($this->risk_score === null || trim((string) $this->risk_score) === '') ? null : (int) $this->risk_score;
        $this->validate();
        $tags = $this->tags_input ? collect(explode(',', $this->tags_input))->map(fn ($t) => trim($t))->filter()->values()->toArray() : [];

        auth()->user()->currentWorkspace->bankAccounts()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => auth()->id(), 'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name, 'type' => $this->type, 'is_business' => $this->isBusinessMode,
                'bank_name' => $this->bank_name, 'country' => $this->country, 'iban' => $this->iban ?: null,
                'swift' => $this->swift, 'holder_name' => $this->holder_name, 'balance' => $this->balance,
                'credit_limit' => $this->credit_limit, 'forecast_balance' => $this->forecast_balance,
                'risk_score' => $this->risk_score, 'tags' => $tags, 'notes' => $this->notes, 'color' => $this->color,
            ]
        );
        $this->resetForm();
        $this->dispatch('modal-close', name: 'bank-modal');
        $this->dispatch('toast', text: 'Conta guardada com sucesso!');
    }

    public function updatedIban($value): void { $this->iban = $this->formatIban(substr($this->normalizeIban($value), 0, 25)); }
    public function updatedBalance($value): void { $this->balance = $this->formatMoney($value); }
    public function updatedCreditLimit($value): void { $this->credit_limit = $this->formatMoney($value); }
    public function updatedForecastBalance($value): void { $this->forecast_balance = $this->formatMoney($value); }

    public function openHistory($id)
    {
        $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $this->selectedAccountName = $account->name;
        $expenses = $account->expenses()->with('category')->latest()->take(30)->get()->map(fn ($e) => ['date'=>$e->spent_at,'desc'=>$e->description ?: $e->category->name,'amount'=>-$e->amount,'type'=>'expense']);
        $incomes = $account->incomes()->latest()->take(30)->get()->map(fn ($i) => ['date'=>$i->received_at,'desc'=>$i->description,'amount'=>$i->amount,'type'=>'income']);
        $this->historyTransactions = $expenses->concat($incomes)->sortByDesc('date')->take(30)->toArray();
        $this->dispatch('modal-show', name: 'account-history-modal');
    }

    public function edit($id)
    {
        $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $this->editingId=$account->id; $this->name=$account->name; $this->type=$account->type; $this->color=$account->color;
        $this->bank_name=$account->bank_name; $this->country=$account->country; $this->iban=$this->formatIban($account->iban);
        $this->swift=$account->swift; $this->holder_name=$account->holder_name; $this->balance=$this->formatMoney($account->balance);
        $this->credit_limit=$this->formatMoney($account->credit_limit); $this->forecast_balance=$this->formatMoney($account->forecast_balance);
        $this->risk_score=$account->risk_score; $this->tags_input=$account->tags ? implode(', ', $account->tags) : ''; $this->notes=$account->notes;
        $this->dispatch('modal-show', name: 'bank-modal');
    }

    public function delete($id)
    {
        $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        if ($account->expenses()->exists() || $account->incomes()->exists()) { $this->dispatch('toast', text: 'Esta conta tem histórico e não pode ser apagada.', variant: 'error'); return; }
        $account->delete(); $this->dispatch('toast', text: 'Conta removida.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['name','type','balance','color','editingId','bank_name','country','iban','swift','holder_name','credit_limit','forecast_balance','risk_score','tags_input','notes']);
        $this->type='corrente'; $this->balance=0; $this->color='#6366f1';
    }

    public function render()
    {
        $workspace=auth()->user()->currentWorkspace;
        $accounts=$workspace->bankAccounts()->where('is_business',$this->isBusinessMode)->where('name','like','%'.$this->search.'%')->get();
        $totalLiquidez=$accounts->where('type','!=','credito')->sum(fn($a)=>$a->current_balance);
        $totalDividaCartao=$accounts->where('type','credito')->sum(fn($a)=>abs($a->current_balance));
        $forecastCash=$accounts->sum(fn($a)=>$a->forecast_balance ?? $a->current_balance); $globalRisk=round($accounts->avg('risk_score') ?? 0);
        $creditAccounts=$accounts->where('type','credito'); $limiteTotalCartoes=$creditAccounts->sum('credit_limit');
        $percentUtilizacao=$limiteTotalCartoes>0?round(($totalDividaCartao/$limiteTotalCartoes)*100,1):0; $riscoCartoes=round($creditAccounts->avg('risk_score') ?? 0);
        $entradasHoje=Income::where('workspace_id',$workspace->id)->whereDate('received_at',today())->sum('amount'); $saidasHoje=Expense::where('workspace_id',$workspace->id)->whereDate('spent_at',today())->sum('amount'); $fluxoHoje=$entradasHoje-$saidasHoje;
        $forecast7=$forecastCash+($fluxoHoje*7); $forecast30=$forecastCash+($fluxoHoje*30);
        return view('livewire.business.bank-account-hub',compact('accounts','totalLiquidez','totalDividaCartao','forecastCash','globalRisk','limiteTotalCartoes','percentUtilizacao','riscoCartoes','entradasHoje','saidasHoje','fluxoHoje','forecast7','forecast30')+['netCash'=>(float)($totalLiquidez-$totalDividaCartao),'modeTitle'=>$this->isBusinessMode?'Contas da Empresa':'Contas Pessoais']);
    }
}
