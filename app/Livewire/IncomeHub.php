<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\Workspace;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Auth; // <--- FALTA ISTO
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class IncomeHub extends Component
{
    // Receita Extra
    public $description = '';

    public $amount = '';

    public string $currency = 'EUR';

    public bool $showExtraModal = false;

    public $received_at = '';

    // Destino do dinheiro: conta bancária ou dinheiro físico (vazio = físico)
    public $bankAccountId = '';

    // Destino usado pelos modais de Rendimentos Fixos (partilhado, só 1 modal aberto de cada vez)
    public $recBankAccountId = '';

    public bool $showRaiseModal = false;

    public $raiseValue = 0;

    public $recRentalGross = 0;

    public $recRentalExpenses = 0;

    public $recPropertyType = 'arrendamento';

    public $recDescription = '';

    public string $raiseMode = 'total'; // 'total' ou 'addition'

    public $selectedFixedId;

    public $recSalaryGross = 0;

    public $recMealAllowance = 0;

    public $recWorkingDays = 22;

    public bool $isRentalUpgrade = false;

    public $calculatedIRS = 0;

    public $calculatedSS = 0;

    public $calculatedSA = 0;

    public $type = 'Extra';

    public $source = 'emprego';

    public $frequency = 'pontual';

    public $tax_estimate = '';

    public $notes = '';

    // Receita Fixa
    public $recWorkspaceId = '';

    public $recAmount = '';

    public $recDay = '';

    public $recSource = 'emprego';

    public $recFrequency = 'mensal';

    public $recTaxEstimate = '';

    public $totalExtras = 0;

    public $workspaceId = 0;

    public $recNotes = '';

    // ── Freelance / Recibos Verdes ──
    public $recFreelanceActivity = '';

    public $recFreelanceType = 'prestacao_servicos';

    public $recFreelanceGross = 0;

    public $recFreelanceExpenses = 0;

    public $recFreelanceWithholding = 0;

    public $recFreelanceFrequency = 'mensal';

    // ── Investimentos / Dividendos ──
    public $recInvestmentType = 'dividendos';

    public $recInvestmentName = '';

    public $recInvestmentAmount = 0;

    public $recInvestmentExpenses = 0;

    public $recInvestmentFrequency = 'mensal';

    // ── Reforma / Pensão ──
    public $recPensionType = 'velhice';

    public $recPensionEntity = '';

    public $recPensionGross = 0;

    public $recPensionAmount = 0;

    // ── Bolsa de Estudo / Apoio à Formação ──
    public $recScholarshipType = 'estudo';

    public $recScholarshipEntity = '';

    public $recScholarshipAmount = 0;

    public $recScholarshipFrequency = 'mensal';

    public $recScholarshipEndDate = '';

    // ── Outra Fonte ──
    public $recOtherSourceDetail = '';

    public $recOtherAmount = 0;

    public $recOtherFrequency = 'mensal';

    // Contexto do modal de aumento (label/emoji dinâmicos por fonte)
    public string $raiseSourceType = 'emprego';

    public function openRaiseModal($id)
    {
        $fixed = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id);

        $this->selectedFixedId = $id;
        $this->raiseValue = 0;
        $this->raiseMode = 'total';

        $this->raiseSourceType = $fixed->source ?? 'emprego';
        // Mantido por compatibilidade com o Blade existente
        $this->isRentalUpgrade = ($fixed->source === 'imobiliario');

        $this->showRaiseModal = true;
        $this->dispatch('modal-show-upgrade');
    }

    public function saveEmployment()
    {
        // 1. Validação básica
        $this->validate([
            'recAmount' => 'required|numeric|min:1',
            'recDescription' => 'required|string',
            'recDay' => 'required|integer|between:1,31',
        ]);

        // 2. Preparar os dados (incluindo o que estava a 0 na imagem)
        $data = [
            'user_id' => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'description' => $this->recDescription,
            'amount' => $this->recAmount, // Resultado Líquido Final
            'day_of_month' => $this->recDay,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'salary_gross' => $this->recSalaryGross,   // Aqueles 1000€
                'meal_allowance' => $this->recMealAllowance, // Aqueles 8€/dia
                'working_days' => $this->recWorkingDays,   // Aqueles 22 dias
                'calculated_irs' => $this->calculatedIRS,
                'calculated_ss' => $this->calculatedSS,
                'calculated_sa' => $this->calculatedSA,
            ],
        ];

        // 3. Gravar ou Atualizar
        if ($this->editingFixedId) {
            RecurringIncome::where('id', $this->editingFixedId)->update($data);
        } else {
            RecurringIncome::create($data);
        }

        // 4. Fechar e avisar
        $this->dispatch('modal-close-emprego');
        $this->dispatch('toast', text: 'Contrato gravado com sucesso! ✅');

        // 5. Reset aos campos
        $this->reset(['recDescription', 'recAmount', 'recSalaryGross', 'recMealAllowance', 'recDay', 'recBankAccountId', 'editingFixedId']);
    }

    // Edição de rendimento fixo
    public ?int $editingFixedId = null;

    public function mount()
    {
        $user = auth()->user();

        $this->workspaceId = $user->current_workspace_id;

        $this->received_at = now()->format('Y-m-d');
        $this->currency = strtoupper((string) ($user->currentWorkspace?->currency ?? 'EUR'));

        // CARREGAR ENTRADAS EXTRAS
        $this->totalExtras = Income::where('workspace_id', $this->workspaceId)
            ->sum(DB::raw('COALESCE(amount_converted, amount)'));
    }

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
            ->orderBy('name')
            ->get();
    }

    public function saveExtra()
    {
        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'received_at' => 'required|date',
            'source' => 'required|in:emprego,imobiliario,freelance,investimento,reforma,bolsa,outro',
            'frequency' => 'required|in:pontual,semanal,mensal,anual',
            'tax_estimate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        Income::create([
            'user_id' => auth()->id(),
            'workspace_id' => $user->current_workspace_id,
            'bank_account_id' => $this->bankAccountId ?: null,
            'description' => $this->description,
            'amount' => $this->amount,
            'currency' => strtoupper($this->currency),
            'received_at' => $this->received_at,
            'type' => 'Extra',
            'source' => $this->source,
            'frequency' => $this->frequency,
            'tax_estimate' => $this->tax_estimate ?: null,
            'notes' => $this->notes ?: null,
        ]);

        $user->awardXp(25, 'receita registada');

        $this->reset(['description', 'amount', 'tax_estimate', 'notes', 'bankAccountId']);
        $this->currency = strtoupper((string) ($user->currentWorkspace?->currency ?? 'EUR'));
        $this->received_at = now()->format('Y-m-d');
        $this->source = 'emprego';
        $this->frequency = 'pontual';
        $this->showExtraModal = false;
        $this->dispatch('modal-close-receita-extra');
        $this->dispatch('toast', variant: 'success', text: $user->xpToastText(25, 'receita registada'));
    }

    // Método para abrir o modal

    // Método para aplicar o aumento
    public function applyRaise()
    {
        $this->validate([
            'raiseValue' => 'required|numeric|min:0.01',
        ]);

        $fixed = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($this->selectedFixedId);

        if ($this->raiseMode === 'addition') {
            // Soma ao valor atual
            $fixed->amount += (float) $this->raiseValue;
        } else {
            // Define como o novo valor total
            $fixed->amount = (float) $this->raiseValue;
        }

        $fixed->save();

        $this->showRaiseModal = false;

        $messages = [
            'imobiliario' => 'Renda atualizada com sucesso! 🏠',
            'freelance' => 'Valor de freelance atualizado! 💻',
            'investimento' => 'Rendimento de investimento atualizado! 📈',
            'reforma' => 'Pensão atualizada! 👴',
            'bolsa' => 'Bolsa atualizada! 🎓',
            'outro' => 'Rendimento atualizado! ✨',
        ];

        $this->reset(['raiseValue', 'selectedFixedId']);
        $this->dispatch('toast', text: $messages[$this->raiseSourceType] ?? 'Salário atualizado! Parabéns pelo aumento! 🚀');
    }

    public function updatedRecWorkspaceId($value)
    {
        if ($value) {
            $ws = Workspace::find($value);
            if ($ws) {
                $this->recDescription = 'Salário - '.$ws->name;
                $this->recSource = 'emprego';
                $this->recFrequency = 'mensal';

                $myRecord = Employee::where('workspace_id', $ws->id)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($myRecord) {
                    $this->recAmount = $myRecord->salary;
                    if ($myRecord->pay_day) {
                        $this->recDay = $myRecord->pay_day;
                    }
                }
            }
        } else {
            $this->recDescription = '';
            $this->recAmount = '';
            $this->recDay = '';
            $this->recSource = 'emprego';
            $this->recFrequency = 'mensal';
        }
    }

    public function saveFixed()
    {
        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
            'recSource' => 'required|in:emprego,freelance,investimento,outro',
            'recFrequency' => 'required|in:semanal,mensal,anual',
        ]);

        $user = auth()->user();

        RecurringIncome::create([
            'user_id' => auth()->id(),
            'workspace_id' => $user->current_workspace_id,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'description' => $this->recDescription,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'is_active' => true,
            'source' => $this->recSource,
            'frequency' => $this->recFrequency,
            'tax_estimate' => $this->recTaxEstimate ?: null,
            'notes' => $this->recNotes ?: null,
        ]);

        $user->awardXp(30, 'rendimento fixo configurado');

        $this->reset(['recWorkspaceId', 'recDescription', 'recAmount', 'recDay', 'recTaxEstimate', 'recNotes', 'recBankAccountId']);
        $this->recSource = 'emprego';
        $this->recFrequency = 'mensal';
        $this->dispatch('modal-close-salario');
        $this->dispatch('toast', variant: 'success', text: $user->xpToastText(30, 'rendimento fixo configurado'));
    }

    public function openExtraModal()
    {
        $this->reset(['description', 'amount', 'tax_estimate', 'notes', 'bankAccountId']);
        $this->currency = strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR'));
        $this->received_at = now()->format('Y-m-d');
        $this->source = 'emprego';
        $this->frequency = 'pontual';

        // 1. Primeiro ativamos a renderização do modal no Blade
        $this->showExtraModal = true;

        // 2. Disparamos o evento para o Alpine.js abrir a animação
        // Usamos um pequeno delay interno ou apenas o dispatch
        $this->dispatch('modal-show-receita-extra');
    }

    public function closeExtraModal()
    {
        $this->showExtraModal = false;
    }

    public function closeRaiseModal()
    {
        $this->showRaiseModal = false;
    }

    public function editFixed($id)
    {
        $record = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id);

        // Guardamos o ID para saber que estamos em modo EDIÇÃO
        $this->editingFixedId = $record->id;

        // Dados Comuns
        $this->recDescription = $record->description;
        $this->recDay = $record->day_of_month;
        $this->recAmount = $record->amount;
        $this->recSource = $record->source;
        $this->recBankAccountId = $record->bank_account_id ?: '';

        // LÓGICA DE DECISÃO: Qual modal abrir?
        if ($record->source === 'imobiliario') {
            // Puxar dados da "metadata" para o modal de rendas
            $meta = $record->metadata;
            $this->recRentalGross = $meta['rental_gross'] ?? $record->amount;
            $this->recRentalExpenses = $meta['rental_expenses'] ?? 0;
            $this->recPropertyType = $meta['property_type'] ?? 'arrendamento';

            // Dispara o evento que o teu modal de rendas ouve
            $this->dispatch('modal-show-renda');
        } elseif ($record->source === 'emprego') {
            // Puxar dados da "metadata" para o modal de emprego
            $meta = $record->metadata;
            $this->recSalaryGross = $meta['salary_gross'] ?? 0;
            $this->recMealAllowance = $meta['meal_allowance'] ?? 0;
            $this->recWorkingDays = $meta['working_days'] ?? 22;

            $this->dispatch('modal-show-emprego');
        } elseif ($record->source === 'freelance') {
            $meta = $record->metadata;
            $this->recFreelanceActivity = $meta['freelance_activity'] ?? '';
            $this->recFreelanceType = $meta['freelance_type'] ?? 'prestacao_servicos';
            $this->recFreelanceGross = $meta['freelance_gross'] ?? $record->amount;
            $this->recFreelanceExpenses = $meta['freelance_expenses'] ?? 0;
            $this->recFreelanceWithholding = $meta['freelance_withholding'] ?? 0;
            $this->recFreelanceFrequency = $record->frequency ?? 'mensal';

            $this->dispatch('modal-show-freelance');
        } elseif ($record->source === 'investimento') {
            $meta = $record->metadata;
            $this->recInvestmentType = $meta['investment_type'] ?? 'dividendos';
            $this->recInvestmentName = $meta['investment_name'] ?? '';
            $this->recInvestmentAmount = $meta['investment_amount'] ?? $record->amount;
            $this->recInvestmentExpenses = $meta['investment_expenses'] ?? 0;
            $this->recInvestmentFrequency = $record->frequency ?? 'mensal';

            $this->dispatch('modal-show-investimento');
        } elseif ($record->source === 'reforma') {
            $meta = $record->metadata;
            $this->recPensionType = $meta['pension_type'] ?? 'velhice';
            $this->recPensionEntity = $meta['pension_entity'] ?? '';
            $this->recPensionGross = $meta['pension_gross'] ?? $record->amount;
            $this->recPensionAmount = $record->amount;

            $this->dispatch('modal-show-reforma');
        } elseif ($record->source === 'bolsa') {
            $meta = $record->metadata;
            $this->recScholarshipType = $meta['scholarship_type'] ?? 'estudo';
            $this->recScholarshipEntity = $meta['scholarship_entity'] ?? '';
            $this->recScholarshipAmount = $record->amount;
            $this->recScholarshipFrequency = $record->frequency ?? 'mensal';
            $this->recScholarshipEndDate = $meta['scholarship_end_date'] ?? '';

            $this->dispatch('modal-show-bolsa');
        } elseif ($record->source === 'outro') {
            $meta = $record->metadata;
            $this->recOtherSourceDetail = $meta['other_source_detail'] ?? $record->description;
            $this->recOtherAmount = $record->amount;
            $this->recOtherFrequency = $record->frequency ?? 'mensal';

            $this->dispatch('modal-show-outro');
        }
    }

    public function updated($propertyName)
    {
        // --- 1. LÓGICA DE SALÁRIO (EMPREGO) ---
        if (in_array($propertyName, ['recSalaryGross', 'recMealAllowance', 'recWorkingDays'])) {
            $gross = (float) $this->recSalaryGross;

            // 1.1 Segurança Social (11%)
            $this->calculatedSS = $gross * 0.11;

            // 1.2 IRS (Tabela simplificada do Onboarding)
            if ($gross <= 1213) {
                $this->calculatedIRS = ($gross * 0.14) - 90;
            } elseif ($gross <= 1819) {
                $this->calculatedIRS = ($gross * 0.2410) - 193.33;
            } else {
                $this->calculatedIRS = ($gross * 0.30) - 250;
            }

            // 1.3 Subsídio Alimentação
            $this->calculatedSA = (float) $this->recMealAllowance * (int) $this->recWorkingDays;

            // 1.4 Valor Líquido Final (O que será guardado na BD)
            $this->recAmount = round(($gross - $this->calculatedSS - $this->calculatedIRS) + $this->calculatedSA, 2);
        }

        // --- 2. LÓGICA DE RENDAS (IMOBILIÁRIO) ---
        if (in_array($propertyName, ['recRentalGross', 'recRentalExpenses'])) {
            $grossIncome = (float) $this->recRentalGross;
            $expenses = (float) $this->recRentalExpenses;

            /*
             * No imobiliário, o valor líquido que vai para a BD é:
             * Renda Bruta - Despesas (com um mínimo de 0)
             */
            $this->recAmount = round(max(0, $grossIncome - $expenses), 2);
        }

        // --- 3. LÓGICA DE FREELANCE / RECIBOS VERDES ---
        if (in_array($propertyName, ['recFreelanceGross', 'recFreelanceExpenses', 'recFreelanceWithholding'])) {
            $this->recAmount = round(max(0, (float) $this->recFreelanceGross - (float) $this->recFreelanceExpenses - (float) $this->recFreelanceWithholding), 2);
        }

        // --- 4. LÓGICA DE INVESTIMENTOS / DIVIDENDOS ---
        if (in_array($propertyName, ['recInvestmentAmount', 'recInvestmentExpenses'])) {
            $this->recAmount = round(max(0, (float) $this->recInvestmentAmount - (float) $this->recInvestmentExpenses), 2);
        }

        // --- 5. LÓGICA DE REFORMA / PENSÃO ---
        if (in_array($propertyName, ['recPensionAmount'])) {
            $this->recAmount = round((float) $this->recPensionAmount, 2);
        }

        // --- 6. LÓGICA DE BOLSA DE ESTUDO ---
        if (in_array($propertyName, ['recScholarshipAmount'])) {
            $this->recAmount = round((float) $this->recScholarshipAmount, 2);
        }

        // --- 7. LÓGICA DE OUTRA FONTE ---
        if (in_array($propertyName, ['recOtherAmount'])) {
            $this->recAmount = round((float) $this->recOtherAmount, 2);
        }
    }

    public function saveRental()
    {
        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recRentalGross' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        $data = [
            'description' => $this->recDescription,
            'amount' => $this->recAmount, // Valor já calculado pelo updated()
            'day_of_month' => $this->recDay,
            'source' => 'imobiliario',
            'frequency' => 'mensal',
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'rental_gross' => $this->recRentalGross,
                'rental_expenses' => $this->recRentalExpenses,
                'property_type' => $this->recPropertyType,
            ],
        ];

        if ($this->editingFixedId) {
            // ATUALIZAÇÃO
            RecurringIncome::find($this->editingFixedId)->update($data);
            $this->dispatch('toast', text: 'Renda atualizada com sucesso! 🏠');
        } else {
            // CRIAÇÃO NOVA
            auth()->user()->currentWorkspace->recurringIncomes()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            $this->dispatch('toast', text: 'Novo rendimento imobiliário registado! 🏠');
        }

        $this->editingFixedId = null; // Limpa o estado de edição
        $this->dispatch('modal-close-renda');
        $this->reset(['recDescription', 'recAmount', 'recRentalGross', 'recRentalExpenses', 'recDay', 'recBankAccountId']);
    }

    // ══════════════════════════════════════════════════════════════
    // 💻 FREELANCE / RECIBOS VERDES
    // ══════════════════════════════════════════════════════════════
    public function openFreelanceModal()
    {
        $this->editingFixedId = null;
        $this->reset(['recDescription', 'recFreelanceActivity', 'recFreelanceGross', 'recFreelanceExpenses', 'recFreelanceWithholding', 'recAmount', 'recDay', 'recBankAccountId']);
        $this->recFreelanceType = 'prestacao_servicos';
        $this->recFreelanceFrequency = 'mensal';
        $this->recDay = 1;
        $this->dispatch('modal-show-freelance');
    }

    public function saveFreelance()
    {
        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recFreelanceGross' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        $data = [
            'description' => $this->recDescription,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'source' => 'freelance',
            'frequency' => $this->recFreelanceFrequency,
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'freelance_activity' => $this->recFreelanceActivity,
                'freelance_type' => $this->recFreelanceType,
                'freelance_gross' => $this->recFreelanceGross,
                'freelance_expenses' => $this->recFreelanceExpenses,
                'freelance_withholding' => $this->recFreelanceWithholding,
            ],
        ];

        if ($this->editingFixedId) {
            RecurringIncome::where('id', $this->editingFixedId)
                ->where('workspace_id', auth()->user()->current_workspace_id)
                ->update($data);
            $this->dispatch('toast', text: 'Rendimento de freelance atualizado! 💻');
        } else {
            auth()->user()->currentWorkspace->recurringIncomes()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            $this->dispatch('toast', text: 'Novo rendimento de freelance registado! 💻');
        }

        $this->editingFixedId = null;
        $this->dispatch('modal-close-freelance');
        $this->reset(['recDescription', 'recFreelanceActivity', 'recFreelanceGross', 'recFreelanceExpenses', 'recFreelanceWithholding', 'recAmount', 'recDay', 'recBankAccountId']);
    }

    // ══════════════════════════════════════════════════════════════
    // 📈 INVESTIMENTOS / DIVIDENDOS
    // ══════════════════════════════════════════════════════════════
    public function openInvestmentModal()
    {
        $this->editingFixedId = null;
        $this->reset(['recDescription', 'recInvestmentName', 'recInvestmentAmount', 'recInvestmentExpenses', 'recAmount', 'recDay', 'recBankAccountId']);
        $this->recInvestmentType = 'dividendos';
        $this->recInvestmentFrequency = 'mensal';
        $this->recDay = 1;
        $this->dispatch('modal-show-investimento');
    }

    public function saveInvestment()
    {
        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recInvestmentAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        $data = [
            'description' => $this->recDescription,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'source' => 'investimento',
            'frequency' => $this->recInvestmentFrequency,
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'investment_type' => $this->recInvestmentType,
                'investment_name' => $this->recInvestmentName,
                'investment_amount' => $this->recInvestmentAmount,
                'investment_expenses' => $this->recInvestmentExpenses,
            ],
        ];

        if ($this->editingFixedId) {
            RecurringIncome::where('id', $this->editingFixedId)
                ->where('workspace_id', auth()->user()->current_workspace_id)
                ->update($data);
            $this->dispatch('toast', text: 'Rendimento de investimento atualizado! 📈');
        } else {
            auth()->user()->currentWorkspace->recurringIncomes()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            $this->dispatch('toast', text: 'Novo rendimento de investimento registado! 📈');
        }

        $this->editingFixedId = null;
        $this->dispatch('modal-close-investimento');
        $this->reset(['recDescription', 'recInvestmentName', 'recInvestmentAmount', 'recInvestmentExpenses', 'recAmount', 'recDay', 'recBankAccountId']);
    }

    // ══════════════════════════════════════════════════════════════
    // 👴 REFORMA / PENSÃO
    // ══════════════════════════════════════════════════════════════
    public function openPensionModal()
    {
        $this->editingFixedId = null;
        $this->reset(['recDescription', 'recPensionEntity', 'recPensionGross', 'recPensionAmount', 'recAmount', 'recDay', 'recBankAccountId']);
        $this->recPensionType = 'velhice';
        $this->recDay = 1;
        $this->dispatch('modal-show-reforma');
    }

    public function savePension()
    {
        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recPensionAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        $data = [
            'description' => $this->recDescription,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'source' => 'reforma',
            'frequency' => 'mensal',
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'pension_type' => $this->recPensionType,
                'pension_entity' => $this->recPensionEntity,
                'pension_gross' => $this->recPensionGross,
            ],
        ];

        if ($this->editingFixedId) {
            RecurringIncome::where('id', $this->editingFixedId)
                ->where('workspace_id', auth()->user()->current_workspace_id)
                ->update($data);
            $this->dispatch('toast', text: 'Pensão atualizada! 👴');
        } else {
            auth()->user()->currentWorkspace->recurringIncomes()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            $this->dispatch('toast', text: 'Nova pensão registada! 👴');
        }

        $this->editingFixedId = null;
        $this->dispatch('modal-close-reforma');
        $this->reset(['recDescription', 'recPensionEntity', 'recPensionGross', 'recPensionAmount', 'recAmount', 'recDay', 'recBankAccountId']);
    }

    // ══════════════════════════════════════════════════════════════
    // 🎓 BOLSA DE ESTUDO / APOIO À FORMAÇÃO
    // ══════════════════════════════════════════════════════════════
    public function openScholarshipModal()
    {
        $this->editingFixedId = null;
        $this->reset(['recDescription', 'recScholarshipEntity', 'recScholarshipAmount', 'recScholarshipEndDate', 'recAmount', 'recDay', 'recBankAccountId']);
        $this->recScholarshipType = 'estudo';
        $this->recScholarshipFrequency = 'mensal';
        $this->recDay = 1;
        $this->dispatch('modal-show-bolsa');
    }

    public function saveScholarship()
    {
        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recScholarshipAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        $data = [
            'description' => $this->recDescription,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'source' => 'bolsa',
            'frequency' => $this->recScholarshipFrequency,
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'scholarship_type' => $this->recScholarshipType,
                'scholarship_entity' => $this->recScholarshipEntity,
                'scholarship_end_date' => $this->recScholarshipEndDate ?: null,
            ],
        ];

        if ($this->editingFixedId) {
            RecurringIncome::where('id', $this->editingFixedId)
                ->where('workspace_id', auth()->user()->current_workspace_id)
                ->update($data);
            $this->dispatch('toast', text: 'Bolsa atualizada! 🎓');
        } else {
            auth()->user()->currentWorkspace->recurringIncomes()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            $this->dispatch('toast', text: 'Nova bolsa registada! 🎓');
        }

        $this->editingFixedId = null;
        $this->dispatch('modal-close-bolsa');
        $this->reset(['recDescription', 'recScholarshipEntity', 'recScholarshipAmount', 'recScholarshipEndDate', 'recAmount', 'recDay', 'recBankAccountId']);
    }

    // ══════════════════════════════════════════════════════════════
    // ✨ OUTRA FONTE
    // ══════════════════════════════════════════════════════════════
    public function openOtherModal()
    {
        $this->editingFixedId = null;
        $this->reset(['recDescription', 'recOtherSourceDetail', 'recOtherAmount', 'recAmount', 'recDay', 'recBankAccountId']);
        $this->recOtherFrequency = 'mensal';
        $this->recDay = 1;
        $this->dispatch('modal-show-outro');
    }

    public function saveOther()
    {
        $this->validate([
            'recOtherSourceDetail' => 'required|string|max:255',
            'recOtherAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        $data = [
            'description' => $this->recOtherSourceDetail,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'source' => 'outro',
            'frequency' => $this->recOtherFrequency,
            'is_active' => true,
            'bank_account_id' => $this->recBankAccountId ?: null,
            'metadata' => [
                'other_source_detail' => $this->recOtherSourceDetail,
            ],
        ];

        if ($this->editingFixedId) {
            RecurringIncome::where('id', $this->editingFixedId)
                ->where('workspace_id', auth()->user()->current_workspace_id)
                ->update($data);
            $this->dispatch('toast', text: 'Rendimento atualizado! ✨');
        } else {
            auth()->user()->currentWorkspace->recurringIncomes()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            $this->dispatch('toast', text: 'Novo rendimento registado! ✨');
        }

        $this->editingFixedId = null;
        $this->dispatch('modal-close-outro');
        $this->reset(['recDescription', 'recOtherSourceDetail', 'recOtherAmount', 'recAmount', 'recDay']);
    }

    private function runSalaryAudit()
    {
        if ($this->recSource !== 'emprego' || $this->recSalaryGross <= 0) {
            $this->reset(['calculatedIRS', 'calculatedSS', 'calculatedSA']);

            return;
        }

        $gross = (float) $this->recSalaryGross;

        // 1. Segurança Social (11%)
        $this->calculatedSS = $gross * 0.11;

        // 2. Cálculo IRS (Tabela simplificada)
        if ($gross <= 1213) {
            $this->calculatedIRS = ($gross * 0.14) - 90;
        } elseif ($gross <= 1819) {
            $this->calculatedIRS = ($gross * 0.2410) - 193.33;
        } else {
            $this->calculatedIRS = ($gross * 0.30) - 250;
        }

        // 3. Subsídio de Alimentação
        $this->calculatedSA = (float) $this->recMealAllowance * (int) $this->recWorkingDays;

        // 4. Resultado Final (Valor Líquido)
        $this->recAmount = round(($gross - $this->calculatedSS - $this->calculatedIRS) + $this->calculatedSA, 2);
    }

    public function updateFixed()
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        RecurringIncome::where('id', $this->editingFixedId)
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->update([
                'description' => $this->recDescription,
                'amount' => $this->recAmount,
                'day_of_month' => $this->recDay,
                'source' => $this->recSource,
                'frequency' => $this->recFrequency,
                'tax_estimate' => $this->recTaxEstimate ?: null,
                'notes' => $this->recNotes ?: null,
                'bank_account_id' => $this->recBankAccountId ?: null,
            ]);

        $this->editingFixedId = null;
        $this->reset(['recWorkspaceId', 'recDescription', 'recAmount', 'recDay', 'recTaxEstimate', 'recNotes', 'recBankAccountId']);
        $this->recSource = 'emprego';
        $this->recFrequency = 'mensal';
        $this->dispatch('modal-close-salario');
        $this->dispatch('toast', text: 'Salário atualizado!');
    }

    public function deleteFixed($id)
    {
        if (! auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas o administrador pode apagar rendimentos.');

            return;
        }
        RecurringIncome::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Registo removido.');
    }

    public function deleteExtra($id)
    {
        if (! auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Não tens permissão para apagar este registo.');

            return;
        }
        Income::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Receita removida.');
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        // --- BUSCAR APENAS EMPRESAS ONDE O UTILIZADOR É COLABORADOR (Exclui Admin e Pessoal) ---
        $collabBusinesses = $user->workspaces()
            ->where('type', '!=', 'personal')
            ->wherePivot('role', '!=', 'admin')
            ->get();

        $fixedIncomes = RecurringIncome::with('bankAccount')->where('workspace_id', $workspaceId)->get();

        $extraIncomes = Income::with('bankAccount')->where('workspace_id', $workspaceId)
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->latest()
            ->get();

        $totalMonthly = $fixedIncomes->sum('amount') + $extraIncomes->sum(fn ($i) => (float) ($i->amount_converted ?? $i->amount));

        // Estatísticas
        $allIncomes = Income::where('workspace_id', $workspaceId)->get();

        // Salário fixo mensal (soma dos rendimentos recorrentes ativos)
        $monthlySalary = (float) RecurringIncome::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->sum('amount');

        // Média mensal (últimos 6 meses) — inclui salário fixo
        $monthlyTotals = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $monthExtra = Income::where('workspace_id', $workspaceId)
                ->whereMonth('received_at', $date->month)
                ->whereYear('received_at', $date->year)
                ->sum(DB::raw('COALESCE(amount_converted, amount)'));

            $monthlyTotals->push([
                'label' => $date->translatedFormat('M'),
                'total' => $monthlySalary + $monthExtra,
            ]);
        }

        $avgMonthly = $monthlyTotals->avg('total');
        $bestMonth = $monthlyTotals->sortByDesc('total')->first();

        // Total anual
        $monthsElapsed = min(now()->month, 12);
        $totalYear = (float) Income::where('workspace_id', $workspaceId)
            ->whereYear('received_at', now()->year)
            ->sum(DB::raw('COALESCE(amount_converted, amount)')) + ($monthlySalary * $monthsElapsed);

        // Breakdown por fonte
        $bySource = $allIncomes->groupBy('source')->map(fn ($group) => $group->sum(fn ($i) => (float) ($i->amount_converted ?? $i->amount)));

        // Imposto estimado
        $taxEstimated = $extraIncomes->sum(function ($i) {
            return $i->tax_estimate > 0 ? ($i->amount * $i->tax_estimate / 100) : 0;
        });

        return view('livewire.income-hub', [
            'collabBusinesses' => $collabBusinesses, // <--- ENVIADO PARA O BLADE
            'fixedIncomes' => $fixedIncomes,
            'extraIncomes' => $extraIncomes,
            'totalMonthly' => $totalMonthly,
            'avgMonthly' => $avgMonthly,
            'bestMonth' => $bestMonth,
            'totalYear' => $totalYear,
            'monthlyTotals' => $monthlyTotals,
            'bySource' => $bySource,
            'taxEstimated' => $taxEstimated,
            'currencyOptions' => CurrencyService::getSymbols(),
            'workspaceCurrency' => strtoupper((string) ($user->currentWorkspace?->currency ?? 'EUR')),
            'isOwner' => $user->isOwner(),
            'canManage' => ! $user->isViewer(),
        ]);
    }
}
