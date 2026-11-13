<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\RecurringIncome;
use App\Services\BusinessAccessService;
use App\Services\IbanProofService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class OnboardingWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public int $totalSteps = 10;

    public bool $show = true;

    #[Locked]
    public array $encouragementIndexes = [];

    // =========================================================
    // PASSO 3 — CONTAS BANCÁRIAS
    // =========================================================

    public array $bankAccounts = [];

    public array $ibanProofs = [null];

    #[Locked]
    public array $savedAccountIds = [null];

    // =========================================================
    // PASSO 4 — FONTE DE RENDIMENTO
    // =========================================================

    public string $salarySource = 'emprego';

    #[Locked]
    public ?int $savedSalaryId = null;

    public string $otherSourceDetail = '';

    public string $salaryDescription = '';

    // Resultado final considerado pelo Finance Pro AI
    public $salaryGross = 0;

    public $salaryAmount = 0;

    // Dia em que o rendimento é recebido
    public int $salaryDay = 25;

    public string $salaryBankAccountId = '';

    // =========================================================
    // EMPREGO / CONTRATO DE TRABALHO
    // =========================================================

    public string $civilStatus = 'solteiro';

    public int $dependents = 0;

    public bool $isDisabled = false;

    public $mealAllowance = 0;

    public int $workingDays = 22;

    public string $mealPayment = 'cartao';

    public bool $irsJovem = false;

    // Breakdown do salário
    public float $calculatedIRS = 0;

    public float $calculatedSS = 0;

    public float $calculatedSA = 0;

    // =========================================================
    // TRABALHO INDEPENDENTE / RECIBOS VERDES
    // =========================================================

    public string $freelanceActivity = '';

    public string $freelanceType = 'servicos';

    public $freelanceGross = 0;

    public $freelanceExpenses = 0;

    public $freelanceWithholding = 0;

    // =========================================================
    // INVESTIMENTOS / DIVIDENDOS
    // =========================================================

    public string $investmentType = 'dividendos';

    public string $investmentFrequency = 'mensal';

    public $investmentGross = 0;

    public $investmentWithholding = 0;

    // =========================================================
    // RENDIMENTOS IMOBILIÁRIOS
    // =========================================================

    public $rentalGross = 0;      // Isto é o Rendimento

    public $rentalExpenses = 0;   // Isto são as Despesas

    public $rentalWithholding = 0; // Isto é o IRS

    public string $propertyType = 'arrendamento';

    public string $propertyDescription = '';

    // =========================================================
    // REFORMA / PENSÃO
    // =========================================================

    public string $pensionType = 'velhice';

    public $pensionGross = 0;

    public $pensionWithholding = 0;

    // =========================================================
    // BOLSA / APOIO À FORMAÇÃO
    // =========================================================

    public string $scholarshipType = 'estudo';

    public string $scholarshipEntity = '';

    public $scholarshipAmount = 0;

    // =========================================================
    // OUTRA FONTE
    // =========================================================

    public $otherAmount = 0;

    // =========================================================
    // PASSOS SEGUINTES
    // =========================================================

    public string $workspaceName = '';

    public string $categoryName = '';

    public string $categoryColor = '#6366f1';

    // =========================================================
    // MOUNT
    // =========================================================

    public function mount()
    {
        $user = auth()->user();
        foreach (range(1, $this->totalSteps) as $step) {
            $this->encouragementIndexes[$step] = random_int(0, 2);
        }
        $this->show = ! $user->onboarding_completed;
        $this->bankAccounts = [$this->newBankAccount()];

        // Carregar o nome atual do workspace para o input
        if ($user->currentWorkspace) {
            $this->workspaceName = $user->currentWorkspace->name;
        } else {
            // Fallback caso a relação falhe
            $this->workspaceName = $user->workspaces()->first()?->name ?? '';
        }
    }

    // =========================================================
    // ALTERAÇÕES DOS CAMPOS
    // =========================================================

    public function updated($propertyName)
    {
        $fieldsThatRecalculate = [
            // Emprego
            'salaryGross',
            'mealAllowance',
            'workingDays',
            'irsJovem',
            'dependents',
            'salarySource',

            // Independente
            'freelanceGross',
            'freelanceExpenses',
            'freelanceWithholding',

            // Investimentos
            'investmentGross',
            'investmentWithholding',

            // Imobiliário
            'rentalGross',
            'rentalExpenses',
            'rentalWithholding',

            // Reforma
            'pensionGross',
            'pensionWithholding',

            // Bolsa
            'scholarshipAmount',

            // Outra fonte
            'otherAmount',
        ];

        if (in_array($propertyName, $fieldsThatRecalculate)) {

            $this->recalculateLiquid();

        }
    }

    // =========================================================
    // ALTERAÇÃO DA FONTE DE RENDIMENTO
    // =========================================================

    public function updatedSalarySource()
    {
        /*
         * Quando o utilizador muda a fonte de rendimento,
         * recalculamos imediatamente o valor final.
         *
         * Não apagamos os valores anteriores para permitir
         * ao utilizador voltar atrás sem perder os dados.
         */

        $this->recalculateLiquid();

        // Se a descrição estiver vazia, colocamos uma
        // descrição automática adequada à fonte.
        if (empty(trim($this->salaryDescription))) {
            $this->salaryDescription = $this->getDefaultDescription();
        }
    }

    // =========================================================
    // DESCRIÇÃO AUTOMÁTICA
    // =========================================================

    private function getDefaultDescription(): string
    {
        return match ($this->salarySource) {
            'emprego' => 'Salário Mensal',
            'freelance' => 'Rendimento de Trabalho Independente',
            'investimento' => 'Rendimento de Investimentos',
            'imobiliario' => 'Rendimento de Rendas',
            'reforma' => 'Reforma / Pensão',
            'bolsa' => 'Bolsa / Apoio à Formação',
            'outro' => $this->otherSourceDetail ?: 'Outro Rendimento',
            default => 'Outro Rendimento',
        };
    }

    public function bankAccountOptions()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id ?? $user->workspaces()->first()?->id;

        if (! $workspaceId) {
            return collect();
        }

        return BankAccount::query()
            ->where('workspace_id', $workspaceId)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'bank_name', 'currency']);
    }

    // =========================================================
    // CÁLCULO DO VALOR LÍQUIDO
    // =========================================================

    private function recalculateLiquid()
    {
        // Reset dos valores de auditoria
        $this->calculatedIRS = 0;
        $this->calculatedSS = 0;
        $this->calculatedSA = 0;

        switch ($this->salarySource) {

            // =================================================
            // EMPREGO / CONTRATO DE TRABALHO
            // =================================================

            case 'emprego':

                if ($this->salaryGross <= 0) {
                    $this->salaryAmount = 0;

                    return;
                }

                $gross = (float) $this->salaryGross;

                // 1. Segurança Social — 11%
                $this->calculatedSS = $gross * 0.11;

                // 2. Cálculo IRS
                //
                // Mantido de acordo com o cálculo que já tinhas.
                if ($gross <= 1213) {

                    $this->calculatedIRS = ($gross * 0.14) - 90;

                } elseif ($gross <= 1819) {

                    $this->calculatedIRS = ($gross * 0.2410) - 193.33;

                } else {

                    $this->calculatedIRS = ($gross * 0.30) - 250;
                }

                // IRS Jovem
                if ($this->irsJovem) {
                    $this->calculatedIRS *= 0.5;
                }

                // 3. Subsídio de alimentação
                $this->calculatedSA =
                    (float) $this->mealAllowance *
                    (int) $this->workingDays;

                // 4. Valor líquido final
                $this->salaryAmount = round(
                    (
                        $gross
                        - $this->calculatedSS
                        - $this->calculatedIRS
                    )
                    + $this->calculatedSA,
                    2
                );

                break;

                // =================================================
                // TRABALHO INDEPENDENTE / RECIBOS VERDES
                // =================================================

            case 'freelance':

                $gross = (float) $this->freelanceGross;
                $expenses = (float) $this->freelanceExpenses;
                $withholding = (float) $this->freelanceWithholding;

                /*
                 * Aqui não tentamos adivinhar impostos.
                 * O utilizador indica:
                 *
                 * - rendimento bruto
                 * - despesas
                 * - retenção efetivamente feita
                 *
                 * O resultado é o valor disponível.
                 */

                $this->salaryAmount = round(
                    max(0, $gross - $expenses - $withholding),
                    2
                );

                break;

                // =================================================
                // INVESTIMENTOS / DIVIDENDOS
                // =================================================

            case 'investimento':

                $gross = (float) $this->investmentGross;
                $withholding = (float) $this->investmentWithholding;

                /*
                 * Não aplicamos uma taxa fiscal automática.
                 * O utilizador coloca a retenção efetivamente
                 * realizada.
                 */

                $this->salaryAmount = round(
                    max(0, $gross - $withholding),
                    2
                );

                break;

                // =================================================
                // RENDIMENTOS IMOBILIÁRIOS
                // =================================================

            case 'imobiliario':
                // Forçamos a conversão para float para evitar erros de string
                $gross = (float) ($this->rentalGross ?: 0);
                $expenses = (float) ($this->rentalExpenses ?: 0);
                $withholding = (float) ($this->rentalWithholding ?: 0);

                $this->salaryAmount = round(max(0, $gross - $expenses - $withholding), 2);
                break;

                // =================================================
                // REFORMA / PENSÃO
                // =================================================

            case 'reforma':

                $gross = (float) $this->pensionGross;
                $withholding = (float) $this->pensionWithholding;

                $this->salaryAmount = round(
                    max(0, $gross - $withholding),
                    2
                );

                break;

                // =================================================
                // BOLSA / APOIO À FORMAÇÃO
                // =================================================

            case 'bolsa':

                /*
                 * Aqui assumimos que o valor indicado é o valor
                 * que o utilizador recebe mensalmente.
                 */

                $this->salaryAmount = round(
                    max(0, (float) $this->scholarshipAmount),
                    2
                );

                break;

                // =================================================
                // OUTRA FONTE
                // =================================================

            case 'outro':

                $this->salaryAmount = round(
                    max(0, (float) $this->otherAmount),
                    2
                );

                break;

            default:

                $this->salaryAmount = 0;

                break;
        }
    }

    // =========================================================
    // AVANÇAR NO WIZARD
    // =========================================================

    public function nextStep()
    {
        if ($this->step === 2) {
            $this->saveWorkspace();

            return;
        }

        if ($this->step === 3) {
            $this->saveBankAccounts();

            return;
        }

        if ($this->step === 4) {
            if ($this->salarySource === 'emprego') {
                $this->addError('salary', 'Usa o formulário de salário para guardar e continuar.');

                return;
            }
            $this->saveStepSalary();

            return;
        }

        if ($this->step === 9) {
            $this->saveStep4();

            return;
        }

        $this->step++;
    }

    // =========================================================
    // GUARDAR PASSO 3 — CONTAS BANCÁRIAS
    // =========================================================

    public function addBankAccount(): void
    {
        $this->bankAccounts[] = $this->newBankAccount();
        $this->ibanProofs[] = null;
        $this->savedAccountIds[] = null;
    }

    public function removeBankAccount(int $index): void
    {
        if (count($this->bankAccounts) === 1 || ! array_key_exists($index, $this->bankAccounts)) {
            return;
        }

        array_splice($this->bankAccounts, $index, 1);
        array_splice($this->ibanProofs, $index, 1);
        array_splice($this->savedAccountIds, $index, 1);
        $this->resetValidation();
    }

    private function newBankAccount(): array
    {
        return [
            'name' => '', 'type' => 'corrente', 'balance' => null, 'bank_name' => '',
            'currency' => 'EUR', 'account_number' => '', 'iban' => '', 'holder_name' => '',
            'swift' => '', 'color' => '#6366f1', 'icon' => 'building-library', 'alert_below' => null,
            'is_business' => false, 'include_in_total' => true, 'entry_mode' => 'manual', 'proof_message' => '',
        ];
    }

    public function updatedIbanProofs($file, $index): void
    {
        if (! is_numeric($index) || ! array_key_exists((int) $index, $this->bankAccounts)) {
            $this->addError('ibanProofs', 'Não foi possível identificar a conta para importar o comprovativo.');

            return;
        }

        $index = (int) $index;
        $field = 'ibanProofs.'.$index;
        $this->resetValidation($field);
        $this->bankAccounts[$index]['proof_message'] = '';
        $this->validate([
            $field => 'required|file|mimes:pdf|max:5120',
        ], [
            $field.'.mimes' => 'Carrega um comprovativo em formato PDF.',
            $field.'.max' => 'O ficheiro não pode ultrapassar 5 MB.',
        ]);

        try {
            $holder = $this->bankAccounts[$index]['holder_name'] ?? '';
            $extracted = app(IbanProofService::class)->extract($this->ibanProofs[$index]->getRealPath(), is_string($holder) ? $holder : '');
            $this->bankAccounts[$index] = array_replace($this->bankAccounts[$index], $extracted, [
                'entry_mode' => 'manual',
                'proof_message' => 'Comprovativo lido. Confirma os dados e introduz o saldo atual da conta.',
            ]);
        } catch (\Throwable $exception) {
            if (! $exception instanceof \RuntimeException) {
                report($exception);
            }
            $this->addError($field, $exception instanceof \RuntimeException
                ? $exception->getMessage()
                : 'Não foi possível ler este PDF. Confirma que é um comprovativo de IBAN legível.');
        }
    }

    private function saveBankAccounts(): void
    {
        $validated = $this->validate([
            'bankAccounts' => 'required|array|min:1',
            'bankAccounts.*.name' => 'required|string|max:100',
            'bankAccounts.*.bank_name' => 'nullable|string|max:100',
            'bankAccounts.*.type' => 'required|in:corrente,poupanca,cash,credito,investimento,tesouraria,paypal,outro',
            'bankAccounts.*.balance' => 'required|numeric',
            'bankAccounts.*.currency' => 'required|in:EUR,USD,GBP,CHF',
            'bankAccounts.*.iban' => 'nullable|string|max:34',
            'bankAccounts.*.account_number' => 'nullable|string|max:30',
            'bankAccounts.*.holder_name' => 'nullable|string|max:255',
            'bankAccounts.*.swift' => 'nullable|string|max:11',
            'bankAccounts.*.color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'bankAccounts.*.alert_below' => 'nullable|numeric',
            'bankAccounts.*.is_business' => 'required|boolean',
            'bankAccounts.*.include_in_total' => 'required|boolean',
        ], [
            'bankAccounts.*.name.required' => 'O nome da conta é obrigatório.',
            'bankAccounts.*.balance.required' => 'O saldo atual é obrigatório.',
        ]);

        $user = auth()->user();
        $workspace = $user->current_workspace_id
            ? $user->workspaces()->whereKey($user->current_workspace_id)->first()
            : $user->workspaces()->first();
        if (! $workspace) {
            $this->addError('bankAccounts', 'Não existe um espaço financeiro acessível para guardar as contas.');

            return;
        }
        if (in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', $user, $workspace);
        }

        $ids = DB::transaction(function () use ($validated, $user, $workspace): array {
            $ids = [];
            foreach ($validated['bankAccounts'] as $index => $account) {
                $record = isset($this->savedAccountIds[$index])
                    ? BankAccount::where('workspace_id', $workspace->id)->where('user_id', $user->id)->findOrFail($this->savedAccountIds[$index])
                    : new BankAccount;
                $record->fill([
                    'workspace_id' => $workspace->id,
                    'user_id' => $user->id,
                    'name' => trim($account['name']),
                    'bank_name' => trim($account['bank_name'] ?? '') ?: null,
                    'type' => $account['type'],
                    'balance' => (float) $account['balance'],
                    'iban' => trim($account['iban'] ?? '') ?: null,
                    'currency' => $account['currency'],
                    'account_number' => trim($account['account_number'] ?? '') ?: null,
                    'holder_name' => trim($account['holder_name'] ?? '') ?: null,
                    'swift' => trim($account['swift'] ?? '') ?: null,
                    'color' => $account['color'],
                    'icon' => 'building-library',
                    'alert_below' => $account['alert_below'] === '' ? null : $account['alert_below'],
                    'is_business' => $account['is_business'],
                    'include_in_total' => $account['include_in_total'],
                ])->save();
                $ids[] = $record->id;
            }

            return $ids;
        });
        $this->savedAccountIds = $ids;
        $this->step++;
        $this->prepareSalaryWorkspace();
    }

    // GUARDAR PASSO 4 — RENDIMENTO
    // =========================================================

    private function saveStepSalary()
    {
        /*
         * Validação base para todas as fontes.
         */
        $rules = [
            'salaryAmount' => 'required|numeric|min:0.01',
            'salaryDay' => 'required|integer|between:1,31',
            'salaryBankAccountId' => 'nullable|integer',
        ];

        /*
         * Validações específicas de cada fonte.
         */
        switch ($this->salarySource) {

            case 'emprego':

                $rules['salaryGross'] =
                    'required|numeric|min:0.01';

                break;

            case 'freelance':

                $rules['freelanceActivity'] =
                    'required|string|max:255';

                $rules['freelanceGross'] =
                    'required|numeric|min:0';

                break;

            case 'investimento':

                $rules['investmentGross'] =
                    'required|numeric|min:0';

                break;

            case 'imobiliario':

                $rules['propertyDescription'] =
                    'required|string|max:255';

                $rules['rentalGross'] =
                    'required|numeric|min:0';

                break;

            case 'reforma':

                $rules['pensionGross'] =
                    'required|numeric|min:0';

                break;

            case 'bolsa':

                $rules['scholarshipEntity'] =
                    'required|string|max:255';

                $rules['scholarshipAmount'] =
                    'required|numeric|min:0.01';

                break;

            case 'outro':

                $rules['otherSourceDetail'] =
                    'required|string|max:255';

                $rules['otherAmount'] =
                    'required|numeric|min:0.01';

                break;
        }

        $this->validate($rules);

        $user = auth()->user();

        $workspaceId =
            $user->current_workspace_id
            ?? $user->workspaces()->first()?->id;

        if ($workspaceId) {

            $bankAccountId = null;
            if ($this->salaryBankAccountId !== '') {
                $bankAccountId = BankAccount::where('workspace_id', $workspaceId)
                    ->where('id', $this->salaryBankAccountId)
                    ->value('id');
            }

            $description = trim($this->salaryDescription);

            if (empty($description)) {
                $description = $this->getDefaultDescription();
            }

            /*
             * Adicionamos informação útil à descrição quando
             * fizer sentido, sem alterar a estrutura da BD.
             */

            if (
                $this->salarySource === 'freelance'
                && ! empty($this->freelanceActivity)
            ) {
                $description =
                    $description
                    ?: $this->freelanceActivity;
            }

            if (
                $this->salarySource === 'imobiliario'
                && ! empty($this->propertyDescription)
            ) {
                $description =
                    $description
                    ?: $this->propertyDescription;
            }

            if (
                $this->salarySource === 'outro'
                && ! empty($this->otherSourceDetail)
            ) {
                $description = $this->otherSourceDetail;
            }

            RecurringIncome::create([
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,

                'description' => $description,

                // Valor líquido/disponível mensal
                'amount' => $this->salaryAmount,

                'day_of_month' => $this->salaryDay,

                'bank_account_id' => $bankAccountId,

                // Fonte selecionada
                'source' => $this->salarySource,

                // Mantemos mensal para compatibilidade
                // com a estrutura atual.
                'frequency' => 'mensal',

                'is_active' => true,
            ]);
        }

        $this->step++;
    }

    // =========================================================
    // PASSO 2 — WORKSPACE
    // =========================================================

    public function saveWorkspace()
    {
        if ($this->workspaceName) {
            $user = auth()->user();

            // Procurar o workspace ativo
            $workspace = $user->currentWorkspace ?? $user->workspaces()->first();

            if ($workspace) {
                $workspace->update([
                    'name' => $this->workspaceName,
                ]);

                // IMPORTANTE: Se o utilizador não tiver um ID de workspace definido no perfil, definimos agora
                if (! $user->current_workspace_id) {
                    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
                }

                // LIMPAR A CACHE: Isto força a Sidebar e o Header a lerem o nome novo imediatamente
                Cache::flush();
            }
        }

        $this->step++;
    }

    // =========================================================
    // PASSO 9 — CATEGORIA
    // =========================================================

    public function saveStep4()
    {
        // Não precisamos de criar nada aqui, pois as categorias fixas
        // já são tratadas pelo sistema no momento da criação do Workspace.
        $this->step++;
    }

    // =========================================================
    // SALTAR PASSO
    // =========================================================

    public function skipStep()
    {
        $this->step++;
        $this->prepareSalaryWorkspace();
    }

    // =========================================================
    // TERMINAR ONBOARDING
    // =========================================================
    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    #[On('onboarding-salary-saved')]
    public function salarySaved(int $salaryId): void
    {
        abort_unless($this->step === 4 && $this->salarySource === 'emprego', 403);
        $salary = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('source', 'emprego')
            ->findOrFail($salaryId);
        abort_if(($salary->metadata['income_type'] ?? null) === 'meal_card_allowance', 404);
        $this->savedSalaryId = $salary->id;
        $this->step++;
    }

    private function prepareSalaryWorkspace(): void
    {
        $user = auth()->user();
        if ($this->step === 4 && ! $user->current_workspace_id && ($workspace = $user->workspaces()->first())) {
            $user->forceFill(['current_workspace_id' => $workspace->id])->save();
            $user->setRelation('currentWorkspace', $workspace);
        }
    }

    public function completeOnboarding()
    {
        auth()->user()->update([
            'onboarding_completed' => true,
        ]);

        $this->show = false;
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        return view('livewire.onboarding-wizard');
    }
}