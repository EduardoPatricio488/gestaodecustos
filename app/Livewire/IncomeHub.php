<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\SalaryDocument;
use App\Models\SalaryPayment;
use App\Models\Workspace;
use App\Services\CurrencyService;
use App\Services\PortugueseNetPayCalculator;
use App\Services\SalaryPaymentAutomationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // <--- FALTA ISTO
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class IncomeHub extends Component
{
    use WithFileUploads;

    // Receita Extra
    public $description = '';

    public $amount = '';

    public string $currency = 'EUR';

    public bool $showExtraModal = false;

    public $received_at = '';

    public $extraSalaryId = '';

    public ?int $editingSalaryExtraIncomeId = null;

    public $serviceUnitAmount = '';

    public int $serviceQuantity = 1;

    public array $serviceFixedPrices = [];

    public array $serviceQuickQuantities = [];

    public string $extraDestination = 'cash';

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

    public string $recPosition = '';

    public string $recDepartment = '';

    public string $recContractType = 'sem_termo';

    public string $recContractStatus = 'active';

    public string $recStartDate = '';

    public string $recEndDate = '';

    public int $recAnnualPayments = 14;

    public int $recDuodecimosPercentage = 0;

    public string $recCivilStatus = 'solteiro';

    public int $recDependents = 0;

    public string $recTaxRegion = 'continente';

    public string $recWithholdingStatus = 'normal';

    public float $recOtherComplements = 0;

    public float $recProductivityBonus = 0;

    public float $recOvertime = 0;

    public float $recOtherIncome = 0;

    public float $recOriginalMonthlyTotal = 0;

    public string $recHealthInsurance = '';

    public float $recExpenseAllowance = 0;

    public string $recAdditionalBenefits = '';

    public $contractScanPhoto = null;

    public string $salaryFormMode = 'manual';

    public bool $contractScanProcessing = false;

    public string $raiseMode = 'total'; // 'total' ou 'addition'

    public string $raiseNextDate = '';

    public bool $hasAnnualRaise = false;

    public $selectedFixedId;

    public $recSalaryGross = 0;

    public string $recBonusType = 'none';

    public $recBonusGross = 0;

    public $recBonusNet = '';

    public bool $recBonusDeductIrs = true;

    public float $recBonusIrsPercentage = 0;

    public float $recBonusIrsAmount = 0;

    public bool $recBonusDeductSocialSecurity = true;

    public float $recBonusSocialSecurityPercentage = 11;

    public float $recBonusSocialSecurityAmount = 0;

    public string $recBonusDate = '';

    public bool $showSalaryBonusModal = false;

    public ?int $salaryBonusId = null;

    public ?int $salaryIncomeHistoryId = null;

    public string $salaryIncomeHistoryType = 'service';

    public int $statementExportMonth = 1;

    public int $statementExportYear = 2026;

    public ?int $salaryDetailsId = null;

    public string $salaryDetailsTab = 'overview';

    public array $salaryPaymentForm = [];

    public ?int $editingSalaryPaymentId = null;

    public $salaryDocumentFile = null;

    public string $salaryDocumentType = 'payslip';

    public $recMealAllowance = 0;

    public $recWorkingDays = 22;

    public bool $recSalaryReceivedThisMonth = false;

    public string $recSalaryDestination = 'cash';

    public string $recMealDestination = 'same';

    public bool $recHolidayAllowanceSameAsSalary = true;

    public $recHolidayAllowanceGross = 0;

    public bool $recChristmasAllowanceSameAsSalary = true;

    public $recChristmasAllowanceGross = 0;

    public bool $recAllowancesInDuodecimos = false;

    public bool $recIrsJovem = false;

    public int $recIrsJovemYear = 1;

    public string $recIrsJovemStartYear = '';

    public bool $isRentalUpgrade = false;

    public $calculatedIRS = 0;

    public $calculatedIRSBeforeJovem = 0;

    public $calculatedIRSJovemSavings = 0;

    public float $calculatedIRSJovemRate = 0;

    public float $calculatedIRSJovemExemptIncome = 0;

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
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        $this->selectedFixedId = $id;
        $annualRaise = $fixed->metadata['annual_raise'] ?? [];
        $this->hasAnnualRaise = ! empty($annualRaise['enabled']);
        $this->raiseValue = $this->hasAnnualRaise ? $annualRaise['value'] : 0;
        $this->raiseMode = match ($annualRaise['type'] ?? null) {
            'fixed' => 'annual_fixed',
            'percentage' => 'annual_percentage',
            default => 'total',
        };
        $this->raiseNextDate = $annualRaise['next_date'] ?? now()->addYear()->toDateString();

        $this->raiseSourceType = $fixed->source ?? 'emprego';
        // Mantido por compatibilidade com o Blade existente
        $this->isRentalUpgrade = ($fixed->source === 'imobiliario');

        $this->showRaiseModal = true;
        $this->dispatch('modal-show-upgrade');
    }

    public function scanEmploymentContract(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');
            return;
        }

        $this->validate(['contractScanPhoto' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240']);
        $apiKey = config('services.openrouter.api_key');
        if (! filled($apiKey)) {
            $this->dispatch('toast', variant: 'error', text: 'O serviço de IA não está configurado.');
            return;
        }

        $this->contractScanProcessing = true;
        try {
            $mime = $this->contractScanPhoto->getMimeType() ?: 'image/jpeg';
            $image = base64_encode(file_get_contents($this->contractScanPhoto->getRealPath()));
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Finance Pro AI',
            ])->connectTimeout(10)->timeout(90)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => in_array(config('services.openrouter.model'), [null, '', 'openrouter/free'], true)
                    ? 'openai/gpt-4o-mini'
                    : config('services.openrouter.model'),
                'temperature' => 0,
                'max_tokens' => 2400,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'És um assistente especializado na leitura integral de contratos de trabalho portugueses. Analisa TODA a fotografia, incluindo rodapé, anexos visíveis, tabelas, listas e texto em letras pequenas. Extrai apenas informação explicitamente visível; nunca inventes nem omitas benefícios. Devolve exclusivamente um objeto JSON válido com estes campos: company, position, department, contract_type (sem_termo, termo_certo, termo_incerto, estagio, prestacao_servicos, outro), start_date (YYYY-MM-DD), end_date (YYYY-MM-DD), salary_gross (número), annual_payments (12 ou 14), meal_allowance (número por dia), working_days (inteiro), holiday_allowance_gross (número), christmas_allowance_gross (número), allowances_in_duodecimos (boolean), health_insurance (texto), expense_allowance (número mensal), additional_benefits (array de strings). MUITO IMPORTANTE: em additional_benefits inclui TODOS os benefícios, regalias, subsídios, seguros, prémios, ajudas de custo, viatura, combustível, telemóvel, computador, formação, férias adicionais, descontos, apoio à infância, alimentação, transporte e quaisquer outras vantagens que estejam escritas. Cada benefício deve ser um elemento separado do array, mantendo o nome e os detalhes/valores/condições exatamente como constam. Não juntes tudo num único benefício, não escolhas apenas o mais importante e não resumas uma lista. Se existirem vários pontos, devolve todos. Se não houver benefícios, devolve []. Para campos ausentes usa null. Distingue salário bruto de líquido e não uses o líquido como salário bruto. Converte datas portuguesas para YYYY-MM-DD. Subsídio de alimentação é valor diário.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => 'Lê cuidadosamente toda a fotografia do contrato, do topo ao fundo. Identifica e lista individualmente TODOS os benefícios e regalias mencionados, mesmo que estejam em secções diferentes ou em letras pequenas. Não omitas nenhum. Extrai também os restantes dados para preencher o formulário.'],
                            ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mime.';base64,'.$image]],
                        ],
                    ],
                ],
            ]);

            if (! $response->successful()) {
                $providerMessage = $response->json('error.message')
                    ?? $response->json('message')
                    ?? 'O fornecedor de IA não devolveu uma explicação.';
                $providerMessage = is_string($providerMessage)
                    ? trim(preg_replace('/\\s+/', ' ', $providerMessage))
                    : 'Resposta de erro não reconhecida do fornecedor de IA.';

                report(new \RuntimeException(
                    'Scanner de contrato: OpenRouter HTTP '.$response->status().' — '.mb_substr($providerMessage, 0, 500)
                ));

                throw new \RuntimeException(
                    'A IA não conseguiu analisar a fotografia (HTTP '.$response->status().'). '.$providerMessage
                );
            }
            $content = $response->json('choices.0.message.content');

            // Alguns modelos devolvem o conteúdo em blocos e/ou dentro de blocos
            // Markdown, mesmo quando é solicitado JSON estruturado.
            if (is_array($content)) {
                $content = collect($content)
                    ->map(function ($part) {
                        if (is_string($part)) return $part;
                        return data_get($part, 'text', data_get($part, 'content', ''));
                    })
                    ->filter(fn ($part) => is_string($part) && trim($part) !== '')
                    ->implode("\n");
            }

            $content = trim((string) $content);
            $content = preg_replace('/^\x60{3}(?:json)?\s*|\s*\x60{3}$/i', '', $content);
            $data = json_decode($content, true);

            // Fallback: extrair o objeto JSON se a IA acrescentar texto explicativo.
            if (! is_array($data)) {
                $jsonStart = strpos($content, '{');
                $jsonEnd = strrpos($content, '}');
                if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
                    $data = json_decode(substr($content, $jsonStart, $jsonEnd - $jsonStart + 1), true);
                }
            }

            if (! is_array($data)) {
                $reason = $response->json('choices.0.message.refusal')
                    ?: $response->json('error.message')
                    ?: 'A resposta da IA não veio num formato JSON válido. Tenta novamente ou usa uma fotografia mais nítida.';
                throw new \RuntimeException($reason);
            }

            if (filled($data['company'] ?? null)) $this->recDescription = mb_substr(trim((string) $data['company']), 0, 255);
            if (filled($data['position'] ?? null)) $this->recPosition = mb_substr((string) $data['position'], 0, 255);
            if (filled($data['department'] ?? null)) $this->recDepartment = mb_substr((string) $data['department'], 0, 255);
            if (in_array($data['contract_type'] ?? null, ['sem_termo', 'termo_certo', 'termo_incerto', 'estagio', 'prestacao_servicos', 'outro'], true)) $this->recContractType = $data['contract_type'];

            foreach (['start_date' => 'recStartDate', 'end_date' => 'recEndDate'] as $key => $property) {
                if (filled($data[$key] ?? null) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $data[$key])) $this->{$property} = $data[$key];
            }
            if (is_numeric($data['salary_gross'] ?? null) && (float) $data['salary_gross'] > 0) $this->recSalaryGross = (float) $data['salary_gross'];
            if (in_array((int) ($data['annual_payments'] ?? 0), [12, 14], true)) $this->recAnnualPayments = (int) $data['annual_payments'];
            if (is_numeric($data['meal_allowance'] ?? null)) $this->recMealAllowance = (float) $data['meal_allowance'];
            if (is_numeric($data['working_days'] ?? null)) $this->recWorkingDays = max(0, min(31, (int) $data['working_days']));
            if (is_numeric($data['holiday_allowance_gross'] ?? null)) {
                $this->recHolidayAllowanceSameAsSalary = false;
                $this->recHolidayAllowanceGross = (float) $data['holiday_allowance_gross'];
            }
            if (is_numeric($data['christmas_allowance_gross'] ?? null)) {
                $this->recChristmasAllowanceSameAsSalary = false;
                $this->recChristmasAllowanceGross = (float) $data['christmas_allowance_gross'];
            }
            if (is_bool($data['allowances_in_duodecimos'] ?? null)) {
                $this->recAllowancesInDuodecimos = $data['allowances_in_duodecimos'];
                $this->recDuodecimosPercentage = $this->recAllowancesInDuodecimos ? 100 : 0;
            }
            if (filled($data['health_insurance'] ?? null)) $this->recHealthInsurance = mb_substr((string) $data['health_insurance'], 0, 255);
            if (is_numeric($data['expense_allowance'] ?? null)) $this->recExpenseAllowance = (float) $data['expense_allowance'];
            if (filled($data['additional_benefits'] ?? null)) {
                $benefits = $data['additional_benefits'];
                if (is_array($benefits)) {
                    $benefits = collect($benefits)
                        ->flatten()
                        ->map(fn ($benefit) => is_scalar($benefit) ? trim((string) $benefit) : '')
                        ->filter()
                        ->unique()
                        ->map(fn ($benefit) => '• '.$benefit)
                        ->implode("\n");
                } else {
                    $benefits = trim((string) $benefits);
                }

                $this->recAdditionalBenefits = mb_substr($benefits, 0, 2000);
            }

            $this->recContractStatus = 'active';
            $this->recalculateEmploymentSalary();
            $this->salaryFormMode = 'manual';
            $this->dispatch('toast', variant: 'success', text: 'Contrato analisado. Confirma os dados preenchidos antes de guardar.');
        } catch (\Throwable $exception) {
            report($exception);
            $this->dispatch('toast', variant: 'error', text: $exception->getMessage() ?: 'Ocorreu um erro ao analisar o contrato.');
        } finally {
            $this->contractScanProcessing = false;
        }
    }

    public function saveEmployment()
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        // 1. Validação básica
        $this->validate([
            'recAmount' => 'required|numeric|min:1',
            'recDescription' => 'required|string|max:255',
            'recPosition' => 'nullable|string|max:255',
            'recDepartment' => 'nullable|string|max:255',
            'recContractType' => 'required|in:sem_termo,termo_certo,termo_incerto,estagio,prestacao_servicos,outro',
            'recContractStatus' => 'required|in:active,ended,suspended',
            'recStartDate' => 'nullable|date',
            'recEndDate' => 'nullable|date|after_or_equal:recStartDate',
            'recDay' => 'required|integer|between:1,31',
            'recSalaryGross' => 'required|numeric|min:0',
            'recAnnualPayments' => 'required|integer|in:12,14',
            'recDuodecimosPercentage' => 'required|integer|in:0,50,100',
            'recCivilStatus' => 'required|in:solteiro,casado_1,casado_2',
            'recDependents' => 'required|integer|min:0|max:20',
            'recTaxRegion' => 'required|in:continente,madeira,acores',
            'recWithholdingStatus' => 'required|in:normal,exempt',
            'recOtherComplements' => 'required|numeric|min:0',
            'recProductivityBonus' => 'required|numeric|min:0',
            'recOvertime' => 'required|numeric|min:0',
            'recOtherIncome' => 'required|numeric|min:0',
            'recHealthInsurance' => 'nullable|string|max:255',
            'recExpenseAllowance' => 'required|numeric|min:0',
            'recAdditionalBenefits' => 'nullable|string|max:2000',
            'recNotes' => 'nullable|string|max:5000',
            'recMealAllowance' => 'required|numeric|min:0',
            'recWorkingDays' => 'required|integer|between:0,31',
            'recSalaryReceivedThisMonth' => 'boolean',
            'recIrsJovem' => 'boolean',
            'recIrsJovemYear' => 'required_if:recIrsJovem,true|integer|between:1,10',
            'recIrsJovemStartYear' => 'nullable|integer|between:2022,2100',
            'recHolidayAllowanceSameAsSalary' => 'boolean',
            'recHolidayAllowanceGross' => 'required|numeric|min:0',
            'recChristmasAllowanceSameAsSalary' => 'boolean',
            'recChristmasAllowanceGross' => 'required|numeric|min:0',
            'recAllowancesInDuodecimos' => 'boolean',
            'recDuodecimosPercentage' => 'required|integer|in:0,50,100',
            'recSalaryDestination' => [
                'required',
                'string',
                Rule::in(array_merge(
                    ['cash'],
                    $this->bankAccounts
                        ->where('user_id', auth()->id())
                        ->map(fn (BankAccount $account): string => 'bank:'.$account->id)
                        ->all(),
                )),
            ],
            'recMealDestination' => [
                'required',
                'string',
                Rule::in(array_merge(
                    ['same', 'meal_card', 'cash'],
                    $this->bankAccounts
                        ->where('user_id', auth()->id())
                        ->map(fn (BankAccount $account): string => 'bank:'.$account->id)
                        ->all(),
                )),
            ],
        ]);

        $isSeparateMealCard = $this->recMealDestination === 'meal_card';
        $mealCardIncomeId = null;
        $existingSalary = $this->editingFixedId
            ? RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('user_id', auth()->id())
                ->findOrFail($this->editingFixedId)
            : null;
        $bonusData = $existingSalary?->metadata['bonus'] ?? null;
        $monthlyBonusGross = ($bonusData['type'] ?? null) === 'monthly' ? (float) ($bonusData['gross'] ?? 0) : 0;
        $monthlyBonusPayroll = $monthlyBonusGross > 0
            ? $this->calculateEmploymentPayroll($monthlyBonusGross)
            : null;
        if ($monthlyBonusPayroll) {
            $bonusData['net'] = round(max(0, $monthlyBonusPayroll['net'] - $this->calculateEmploymentPayroll()['net']), 2);
        }
        $monthlyBonusNet = ($bonusData['type'] ?? null) === 'monthly' ? (float) ($bonusData['net'] ?? 0) : 0;
        $salaryAmount = max(0, (float) $this->recAmount - ($isSeparateMealCard ? (float) $this->calculatedSA : 0) + $monthlyBonusNet);

        // 2. Preparar os dados
        $data = [
            'user_id' => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'description' => $this->recDescription,
            'amount' => $salaryAmount,
            'day_of_month' => $this->recDay,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'notes' => $this->recNotes ?: null,
            'is_active' => $this->recContractStatus === 'active',
            'bank_account_id' => str_starts_with($this->recSalaryDestination, 'bank:')
                ? (int) substr($this->recSalaryDestination, 5)
                : null,
            'metadata' => [
                'salary_gross' => $this->recSalaryGross,   // Aqueles 1000€
                'position' => $this->recPosition,
                'department' => $this->recDepartment,
                'contract_type' => $this->recContractType,
                'contract_status' => $this->recContractStatus,
                'start_date' => $this->recStartDate ?: null,
                'end_date' => $this->recEndDate ?: null,
                'annual_payments' => $this->recAnnualPayments,
                'duodecimos_percentage' => $this->recDuodecimosPercentage ?: ($this->recAllowancesInDuodecimos ? 100 : 0),
                'civil_status' => $this->recCivilStatus,
                'dependents' => $this->recDependents,
                'tax_region' => $this->recTaxRegion,
                'withholding_status' => $this->recWithholdingStatus,
                'other_complements' => $this->recOtherComplements,
                'productivity_bonus' => $this->recProductivityBonus,
                'overtime' => $this->recOvertime,
                'other_income' => $this->recOtherIncome,
                'health_insurance' => $this->recHealthInsurance,
                'expense_allowance' => $this->recExpenseAllowance,
                'additional_benefits' => $this->recAdditionalBenefits,
                'meal_allowance' => $this->recMealAllowance, // Aqueles 8€/dia
                'working_days' => $this->recWorkingDays,   // Aqueles 22 dias
                'received_month' => $this->recSalaryReceivedThisMonth ? now()->format('Y-m') : null,
                'meal_allowance_destination' => str_starts_with($this->recMealDestination, 'bank:') ? 'bank' : $this->recMealDestination,
                'meal_bank_account_id' => str_starts_with($this->recMealDestination, 'bank:')
                    ? (int) substr($this->recMealDestination, 5)
                    : null,
                'holiday_allowance_same_as_salary' => $this->recHolidayAllowanceSameAsSalary,
                'holiday_allowance_gross' => $this->recHolidayAllowanceGross,
                'christmas_allowance_same_as_salary' => $this->recChristmasAllowanceSameAsSalary,
                'christmas_allowance_gross' => $this->recChristmasAllowanceGross,
                'allowances_in_duodecimos' => $this->recDuodecimosPercentage > 0 || $this->recAllowancesInDuodecimos,
                'monthly_duodecimos' => $this->calculateMonthlyDuodecimos(),
                'calculated_irs' => $monthlyBonusPayroll['irs'] ?? $this->calculatedIRS,
                'estimated_irs_jovem_savings' => $this->calculatedIRSJovemSavings,
                'irs_jovem' => $this->recIrsJovem,
                'irs_jovem_year' => $this->recIrsJovemYear,
                'irs_jovem_start_year' => $this->recIrsJovemStartYear ?: null,
                'calculated_ss' => $monthlyBonusPayroll['social_security'] ?? $this->calculatedSS,
                'calculated_sa' => $this->calculatedSA,
                'service_unit_price' => $existingSalary?->metadata['service_unit_price'] ?? null,
            ],
        ];
        if ($bonusData !== null) {
            $data['metadata']['bonus'] = $bonusData;
        }

        // 3. Gravar ou Atualizar
        if (str_starts_with($this->recMealDestination, 'bank:')) {
            $mealAccountId = (int) substr($this->recMealDestination, 5);
            abort_unless(BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('user_id', auth()->id())
                ->whereKey($mealAccountId)
                ->exists(), 422, 'Conta bancária do subsídio de alimentação inválida.');
        }

        DB::transaction(function () use ($data, $isSeparateMealCard, &$mealCardIncomeId): void {
            if ($this->editingFixedId) {
                $salary = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
                    ->where('user_id', auth()->id())
                    ->whereKey($this->editingFixedId)->firstOrFail();
                $mealCardIncomeId = $salary->metadata['meal_card_income_id'] ?? null;
                if (isset($salary->metadata['annual_raise'])) {
                    $data['metadata']['annual_raise'] = $salary->metadata['annual_raise'];
                }
                $salary->fill($data);
                $salary->save();
            } else {
                $salary = RecurringIncome::create($data);
            }

            if ($isSeparateMealCard && (float) $this->calculatedSA > 0) {
                $mealIncomeData = [
                    'user_id' => $salary->user_id,
                    'workspace_id' => auth()->user()->current_workspace_id,
                    'description' => 'Subsídio de alimentação (cartão) — '.$this->recDescription,
                    'amount' => $this->calculatedSA,
                    'day_of_month' => $this->recDay,
                    'source' => 'emprego',
                    'frequency' => 'mensal',
                    'is_active' => true,
                    'bank_account_id' => null,
                    'metadata' => [
                        'income_type' => 'meal_card_allowance',
                        'linked_salary_id' => $salary->id,
                        'linked_salary_description' => $this->recDescription,
                        'start_date' => $this->recStartDate ?: null,
                        'end_date' => $this->recEndDate ?: null,
                    ],
                ];

                if ($mealCardIncomeId) {
                    $mealIncome = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
                        ->whereKey($mealCardIncomeId)
                        ->firstOrFail();
                    $mealIncome->fill($mealIncomeData);
                    $mealIncome->save();
                } else {
                    $mealIncome = RecurringIncome::create($mealIncomeData);
                }

                $salary->metadata = array_merge($salary->metadata ?? [], ['meal_card_income_id' => $mealIncome->id]);
                $salary->save();
            } elseif ($mealCardIncomeId) {
                RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
                    ->where('user_id', auth()->id())
                    ->whereKey($mealCardIncomeId)
                    ->delete();

                $salary->metadata = array_diff_key($salary->metadata ?? [], ['meal_card_income_id' => true]);
                $salary->save();
            }
        });

        // 4. Fechar e avisar
        $this->contractScanPhoto = null;
        $this->salaryFormMode = 'manual';
        $this->dispatch('modal-close-emprego');
        $this->dispatch('toast', text: 'Contrato gravado com sucesso! ✅');

        // 5. Reset aos campos
        $this->reset([
            'recDescription', 'recAmount', 'recSalaryGross', 'recMealAllowance', 'recDay',
            'recBankAccountId', 'editingFixedId', 'recIrsJovem', 'calculatedIRSBeforeJovem',
            'recHolidayAllowanceGross', 'recChristmasAllowanceGross', 'recAllowancesInDuodecimos',
            'recMealDestination', 'recSalaryDestination', 'recSalaryReceivedThisMonth',
            'recBonusGross', 'recBonusNet',
            'recPosition', 'recDepartment', 'recContractType', 'recContractStatus',
            'recStartDate', 'recEndDate', 'recAnnualPayments', 'recDuodecimosPercentage',
            'recCivilStatus', 'recDependents', 'recTaxRegion', 'recWithholdingStatus',
            'recOtherComplements', 'recProductivityBonus', 'recOvertime', 'recOtherIncome',
            'recOriginalMonthlyTotal', 'recHealthInsurance', 'recExpenseAllowance',
            'recAdditionalBenefits', 'recNotes', 'recIrsJovemStartYear',
        ]);
        $this->recBonusType = 'none';
        $this->recBonusDate = now()->format('Y-m-d');
        $this->recHolidayAllowanceSameAsSalary = true;
        $this->recChristmasAllowanceSameAsSalary = true;
        $this->recIrsJovemYear = 1;
    }

    // Edição de rendimento fixo
    public ?int $editingFixedId = null;

    public function mount()
    {
        $user = auth()->user();

        $this->workspaceId = $user->current_workspace_id;

        $this->received_at = now()->format('Y-m-d');
        $this->recBonusDate = now()->format('Y-m-d');
        $this->currency = strtoupper((string) ($user->currentWorkspace?->currency ?? 'EUR'));
        $this->statementExportMonth = (int) now()->month;
        $this->statementExportYear = (int) now()->year;

        // CARREGAR ENTRADAS EXTRAS
        $this->totalExtras = Income::where('workspace_id', $this->workspaceId)
            ->sum(DB::raw('COALESCE(amount_converted, amount)'));

        if (request()->boolean('open_extra')) {
            $this->openExtraModal();
        }
    }

    #[Computed]
    public function bankAccounts()
    {
        $user = auth()->user();
        $query = BankAccount::where('workspace_id', $user->current_workspace_id);

        if ($user->currentWorkspace?->type === 'personal') {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('name')->get();
    }

    public function saveExtra()
    {
        $user = auth()->user();
        $salary = null;
        if ($this->extraSalaryId) {
            $salary = RecurringIncome::where('workspace_id', $user->current_workspace_id)
                ->where('user_id', $user->id)
                ->where('source', 'emprego')
                ->whereNull('metadata->income_type')
                ->findOrFail($this->extraSalaryId);
        }

        if ($salary) {
            $this->amount = round((float) $this->serviceUnitAmount * $this->serviceQuantity, 2);
            $metadata = $salary->metadata ?? [];
            $metadata['service_unit_price'] = round((float) $this->serviceUnitAmount, 2);
            $salary->metadata = $metadata;
            $salary->save();
        }

        $extra = $this->editingSalaryExtraIncomeId
            ? Income::where('workspace_id', $user->current_workspace_id)
                ->where('user_id', $user->id)
                ->where('salary_recurring_income_id', $salary?->id)
                ->where(function ($query): void {
                    $query->whereNull('salary_income_type')->orWhere('salary_income_type', '!=', 'prize');
                })
                ->findOrFail($this->editingSalaryExtraIncomeId)
            : null;

        $allowedDestinations = ['cash'];
        if ($salary) {
            $allowedDestinations[] = 'salary';
        }
        foreach ($this->bankAccounts as $account) {
            if ((int) $account->user_id === (int) $user->id) {
                $allowedDestinations[] = 'bank:'.$account->id;
            }
        }

        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'received_at' => 'required|date',
            'tax_estimate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
            'extraSalaryId' => 'nullable|integer',
            'serviceUnitAmount' => [$salary ? 'required' : 'nullable', 'numeric', 'min:0.01'],
            'serviceQuantity' => [$salary ? 'required' : 'nullable', 'integer', 'min:1', 'max:1000'],
            'extraDestination' => ['required', Rule::in($allowedDestinations)],
        ]);

        $bankAccountId = null;
        if (str_starts_with($this->extraDestination, 'bank:')) {
            $bankAccountId = (int) substr($this->extraDestination, 5);
            abort_unless(BankAccount::where('workspace_id', $user->current_workspace_id)
                ->where('user_id', auth()->id())
                ->whereKey($bankAccountId)
                ->exists(), 422, 'Conta bancária inválida.');
        }

        $incomeData = [
            'user_id' => auth()->id(),
            'workspace_id' => $user->current_workspace_id,
            'salary_recurring_income_id' => $salary?->id,
            'salary_income_type' => $salary ? 'service' : null,
            'service_unit_amount' => $salary ? $this->serviceUnitAmount : null,
            'service_quantity' => $salary ? $this->serviceQuantity : null,
            'added_to_salary' => $salary && $this->extraDestination === 'salary',
            'bank_account_id' => $bankAccountId,
            'description' => $this->description,
            'amount' => $this->amount,
            'currency' => strtoupper($this->currency),
            'received_at' => $this->received_at,
            'type' => 'Extra',
            'source' => 'emprego',
            'frequency' => 'pontual',
            'tax_estimate' => $this->tax_estimate ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($extra) {
            $extra->fill($incomeData)->save();
        } else {
            Income::create($incomeData);
            $user->awardXp(25, 'receita registada');
        }

        $wasEditing = (bool) $extra;
        $this->reset(['description', 'amount', 'tax_estimate', 'notes', 'bankAccountId', 'extraSalaryId', 'serviceUnitAmount', 'editingSalaryExtraIncomeId']);
        $this->serviceQuantity = 1;
        $this->extraDestination = 'cash';
        $this->currency = strtoupper((string) ($user->currentWorkspace?->currency ?? 'EUR'));
        $this->received_at = now()->format('Y-m-d');
        $this->source = 'emprego';
        $this->frequency = 'pontual';
        $this->showExtraModal = false;
        $this->dispatch('modal-close-receita-extra');
        $this->dispatch('toast', variant: 'success', text: $wasEditing ? 'Serviço extra atualizado.' : $user->xpToastText(25, 'receita registada'));
    }

    // Método para abrir o modal

    // Método para aplicar o aumento
    public function applyRaise()
    {
        $fixed = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($this->selectedFixedId);

        if ($this->raiseMode === 'none' && $this->raiseSourceType === 'emprego') {
            $metadata = $fixed->metadata ?? [];
            unset($metadata['annual_raise']);
            $fixed->metadata = $metadata;
            $fixed->save();
            $this->finishRaise('Salário mantido sem aumento.');

            return;
        }

        if (in_array($this->raiseMode, ['annual_fixed', 'annual_percentage'], true)) {
            $this->validate([
                'raiseValue' => 'required|numeric|min:0.01'.($this->raiseMode === 'annual_percentage' ? '|max:100' : ''),
                'raiseNextDate' => 'required|date|after_or_equal:today',
            ], [
                'raiseValue.required' => 'Indica o valor do aumento.',
                'raiseValue.numeric' => 'O valor do aumento tem de ser numérico.',
                'raiseValue.min' => 'O valor do aumento tem de ser superior a zero.',
                'raiseValue.max' => 'A percentagem do aumento não pode exceder 100%.',
            ]);

            $metadata = $fixed->metadata ?? [];
            $metadata['annual_raise'] = [
                'enabled' => true,
                'type' => $this->raiseMode === 'annual_fixed' ? 'fixed' : 'percentage',
                'value' => (float) $this->raiseValue,
                'next_date' => $this->raiseNextDate,
            ];
            $fixed->metadata = $metadata;
            $fixed->save();
            $this->finishRaise('Aumento anual configurado e será aplicado automaticamente na data escolhida.');

            return;
        }

        $this->validate([
            'raiseValue' => 'required|numeric|min:0.01',
        ], [
            'raiseValue.required' => 'Indica o valor do aumento.',
            'raiseValue.numeric' => 'O valor do aumento tem de ser numérico.',
            'raiseValue.min' => 'O valor do aumento tem de ser superior a zero.',
        ]);

        if ($this->raiseMode === 'addition') {
            // Soma ao valor atual
            $fixed->amount += (float) $this->raiseValue;
        } else {
            // Define como o novo valor total
            $fixed->amount = (float) $this->raiseValue;
        }

        $fixed->save();

        $messages = [
            'imobiliario' => 'Renda atualizada com sucesso! 🏠',
            'freelance' => 'Valor de freelance atualizado! 💻',
            'investimento' => 'Rendimento de investimento atualizado! 📈',
            'reforma' => 'Pensão atualizada! 👴',
            'bolsa' => 'Bolsa atualizada! 🎓',
            'outro' => 'Rendimento atualizado! ✨',
        ];

        $this->finishRaise($messages[$this->raiseSourceType] ?? 'Salário atualizado! Parabéns pelo aumento! 🚀');
    }

    public function cancelAnnualRaise(): void
    {
        $fixed = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($this->selectedFixedId);

        $metadata = $fixed->metadata ?? [];
        unset($metadata['annual_raise']);
        $fixed->metadata = $metadata;
        $fixed->save();

        $this->finishRaise('Aumento anual automático cancelado.');
    }

    private function finishRaise(string $message): void
    {
        $this->showRaiseModal = false;
        $this->reset(['raiseValue', 'selectedFixedId', 'hasAnnualRaise']);
        $this->raiseMode = 'total';
        $this->raiseNextDate = '';
        $this->dispatch('modal-close-upgrade');
        $this->dispatch('toast', text: $message);
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

    public function openExtraModal(?int $salaryId = null, ?int $quantity = null)
    {
        $this->reset(['description', 'amount', 'tax_estimate', 'notes', 'bankAccountId', 'extraSalaryId', 'serviceUnitAmount', 'editingSalaryExtraIncomeId']);
        $this->serviceQuantity = 1;
        $this->extraDestination = 'cash';
        $this->currency = strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR'));
        $this->received_at = now()->format('Y-m-d');
        $this->source = 'emprego';
        $this->frequency = 'pontual';

        if ($salaryId) {
            $user = auth()->user();
            $salary = RecurringIncome::where('workspace_id', $user->current_workspace_id)
                ->where('user_id', $user->id)
                ->where('source', 'emprego')
                ->whereNull('metadata->income_type')
                ->findOrFail($salaryId);
            $this->extraSalaryId = $salary->id;
            $this->serviceUnitAmount = (float) ($salary->metadata['service_unit_price'] ?? '');
            $this->serviceQuantity = max(1, $quantity ?? 1);
            $this->extraDestination = 'salary';
        }

        // 1. Primeiro ativamos a renderização do modal no Blade
        $this->showExtraModal = true;

        // 2. Disparamos o evento para o Alpine.js abrir a animação
        // Usamos um pequeno delay interno ou apenas o dispatch
        $this->dispatch('modal-show-receita-extra');
    }

    public function editSalaryExtraIncome(int $incomeId): void
    {
        $salary = $this->salaryDetailsContract((int) $this->salaryIncomeHistoryId);
        $income = Income::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('salary_recurring_income_id', $salary->id)
            ->where(function ($query): void {
                $query->whereNull('salary_income_type')->orWhere('salary_income_type', '!=', 'prize');
            })
            ->findOrFail($incomeId);

        $this->salaryIncomeHistoryId = null;
        $this->editingSalaryExtraIncomeId = $income->id;
        $this->extraSalaryId = (string) $salary->id;
        $this->description = $income->description;
        $this->serviceUnitAmount = (float) ($income->service_unit_amount ?: $income->amount);
        $this->serviceQuantity = max(1, (int) ($income->service_quantity ?: 1));
        $this->amount = round((float) $this->serviceUnitAmount * $this->serviceQuantity, 2);
        $this->currency = strtoupper($income->currency);
        $this->received_at = $income->received_at?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->notes = $income->notes ?? '';
        $this->extraDestination = $income->added_to_salary ? 'salary' : ($income->bank_account_id ? 'bank:'.$income->bank_account_id : 'cash');
        $this->showExtraModal = true;
        $this->dispatch('modal-show-receita-extra');
    }

    public function newEmployment(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        $this->editingFixedId = null;
        $this->contractScanPhoto = null;
        $this->salaryFormMode = 'manual';
        $this->recDescription = '';
        $this->recPosition = '';
        $this->recDepartment = '';
        $this->recContractType = 'sem_termo';
        $this->recContractStatus = 'active';
        $this->recStartDate = now()->toDateString();
        $this->recEndDate = '';
        $this->recAnnualPayments = 14;
        $this->recDuodecimosPercentage = 0;
        $this->recCivilStatus = 'solteiro';
        $this->recDependents = 0;
        $this->recTaxRegion = 'continente';
        $this->recWithholdingStatus = 'normal';
        $this->recOtherComplements = 0;
        $this->recProductivityBonus = 0;
        $this->recOvertime = 0;
        $this->recOtherIncome = 0;
        $this->recHealthInsurance = '';
        $this->recExpenseAllowance = 0;
        $this->recAdditionalBenefits = '';
        $this->recNotes = '';
        $this->recSalaryGross = 0;
        $this->recWorkingDays = 22;
        $this->recMealAllowance = 0;
        $this->recHolidayAllowanceSameAsSalary = true;
        $this->recHolidayAllowanceGross = 0;
        $this->recChristmasAllowanceSameAsSalary = true;
        $this->recChristmasAllowanceGross = 0;
        $this->recAllowancesInDuodecimos = false;
        $this->recIrsJovem = false;
        $this->recIrsJovemYear = 1;
        $this->recIrsJovemStartYear = '';
        $this->recBonusType = 'none';
        $this->recBonusGross = 0;
        $this->recBonusNet = '';
        $this->recBonusDeductIrs = true;
        $this->recBonusIrsPercentage = 0;
        $this->recBonusIrsAmount = 0;
        $this->recBonusDeductSocialSecurity = true;
        $this->recBonusSocialSecurityPercentage = 11;
        $this->recBonusSocialSecurityAmount = 0;
        $this->recSalaryDestination = 'cash';
        $this->recMealDestination = 'same';
        $this->recDay = 25;
        $this->recAmount = 0;
        $this->recOriginalMonthlyTotal = 0;
        $this->recSalaryReceivedThisMonth = false;
        $this->recBankAccountId = '';

        $this->recalculateEmploymentSalary();
        $this->dispatch('modal-show-emprego');
    }

    public function openSalaryDetails(int $id): void
    {
        $salary = $this->salaryDetailsContract($id);
        app(SalaryPaymentAutomationService::class)->processSalaryPayment($salary);
        $this->salaryDetailsId = $id;
        $this->salaryDetailsTab = 'overview';
    }

    public function openSalaryPaymentHistory(int $id): void
    {
        $salary = $this->salaryDetailsContract($id);
        app(SalaryPaymentAutomationService::class)->processSalaryPayment($salary);
        $this->salaryDetailsId = $id;
        $this->salaryDetailsTab = 'payments';
    }

    public function openIncomeSource(string $source): void
    {
        abort_unless(in_array($source, ['emprego', 'renda', 'freelance', 'investimento', 'reforma', 'bolsa', 'outro'], true), 404);

        if ($source === 'emprego') {
            $this->newEmployment();

            return;
        }

        $this->dispatch('modal-show-'.$source);
    }

    public function closeSalaryDetails(): void
    {
        $this->salaryDetailsId = null;
        $this->editingSalaryPaymentId = null;
        $this->salaryPaymentForm = [];
        $this->salaryDocumentFile = null;
    }

    public function setSalaryDetailsTab(string $tab): void
    {
        abort_unless(in_array($tab, ['overview', 'professional', 'composition', 'taxes', 'benefits', 'payments', 'documents', 'notes'], true), 404);
        $this->salaryDetailsTab = $tab;
    }

    public function openSalaryPaymentForm(?int $paymentId = null): void
    {
        if (! $paymentId) {
            $this->dispatch('toast', variant: 'error', text: 'Os pagamentos salariais são criados automaticamente.');

            return;
        }

        $salary = $this->salaryDetailsContract((int) $this->salaryDetailsId);
        $payment = $this->salaryPaymentsQuery($salary)->findOrFail($paymentId);

        $this->editingSalaryPaymentId = $payment->id;
        $this->salaryPaymentForm = [
            'salary_base_gross' => (float) $payment->salary_base_gross,
            'allowances_duodecimos' => (float) $payment->allowances_duodecimos,
            'other_complements' => (float) $payment->other_complements,
            'social_security' => (float) $payment->social_security,
            'irs' => (float) $payment->irs,
            'other_deductions' => (float) $payment->other_deductions,
            'meal_allowance' => (float) $payment->meal_allowance,
            'net_received' => (float) $payment->net_received,
            'notes' => $payment->notes ?? '',
        ];
        $this->salaryDetailsTab = 'payments';
    }

    public function saveSalaryPayment(): void
    {
        if (! $this->editingSalaryPaymentId) {
            $this->dispatch('toast', variant: 'error', text: 'Não é possível adicionar pagamentos. São criados automaticamente.');

            return;
        }

        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        $this->validate([
            'salaryDetailsId' => 'required|integer',
            'salaryPaymentForm.salary_base_gross' => 'required|numeric|min:0',
            'salaryPaymentForm.allowances_duodecimos' => 'required|numeric|min:0',
            'salaryPaymentForm.other_complements' => 'required|numeric|min:0',
            'salaryPaymentForm.social_security' => 'required|numeric|min:0',
            'salaryPaymentForm.irs' => 'required|numeric|min:0',
            'salaryPaymentForm.other_deductions' => 'required|numeric|min:0',
            'salaryPaymentForm.meal_allowance' => 'required|numeric|min:0',
            'salaryPaymentForm.net_received' => 'required|numeric|min:0',
            'salaryPaymentForm.notes' => 'nullable|string|max:2000',
        ]);

        $salary = $this->salaryDetailsContract((int) $this->salaryDetailsId);
        $payment = $this->salaryPaymentsQuery($salary)->findOrFail($this->editingSalaryPaymentId);
        DB::transaction(function () use ($payment, $salary): void {
            $payment->fill([
                'salary_base_gross' => $this->salaryPaymentForm['salary_base_gross'],
                'allowances_duodecimos' => $this->salaryPaymentForm['allowances_duodecimos'],
                'other_complements' => $this->salaryPaymentForm['other_complements'],
                'social_security' => $this->salaryPaymentForm['social_security'],
                'irs' => $this->salaryPaymentForm['irs'],
                'other_deductions' => $this->salaryPaymentForm['other_deductions'],
                'meal_allowance' => $this->salaryPaymentForm['meal_allowance'],
                'net_received' => $this->salaryPaymentForm['net_received'],
                'notes' => $this->salaryPaymentForm['notes'] ?? null,
            ]);
            $payment->save();
            $this->syncAutomaticSalaryPaymentBalance($payment, $salary);
        });

        $this->editingSalaryPaymentId = null;
        $this->salaryPaymentForm = [];
        $this->dispatch('toast', text: 'Pagamento salarial guardado.');
    }

    public function markSalaryPaymentPaid(int $paymentId): void
    {
        $this->dispatch('toast', variant: 'error', text: 'O pagamento será marcado como pago automaticamente na data prevista.');
    }

    public function deleteSalaryPayment(int $paymentId): void
    {
        if (! auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas o administrador pode apagar pagamentos.');

            return;
        }

        $salary = $this->salaryDetailsContract((int) $this->salaryDetailsId);
        DB::transaction(function () use ($salary, $paymentId): void {
            $payment = $this->salaryPaymentsQuery($salary)->findOrFail($paymentId);
            if ($payment->is_automatic) {
                abort(409, 'Os pagamentos automáticos não podem ser eliminados.');
            }
            $payment->delete();
        });
        $this->dispatch('toast', text: 'Pagamento salarial eliminado.');
    }

    private function syncAutomaticSalaryPaymentBalance(SalaryPayment $payment, RecurringIncome $salary): void
    {
        if (! $payment->is_automatic) {
            return;
        }

        $targetAmount = 0.0;
        if ($payment->status === 'paid' && $payment->bank_account_id) {
            $targetAmount = (float) $payment->net_received;
            if (($salary->metadata['meal_allowance_destination'] ?? null) === 'meal_card') {
                $targetAmount -= (float) $payment->meal_allowance;
            }
            $targetAmount = round(max(0, $targetAmount), 2);
        }

        $account = $payment->bank_account_id
            ? BankAccount::withoutGlobalScopes()
                ->where('workspace_id', $payment->workspace_id)
                ->find($payment->bank_account_id)
            : null;

        if (! $account) {
            $payment->bank_balance_amount = 0;
            $payment->bank_account_id = null;
            $payment->save();

            return;
        }

        $balanceDelta = round($targetAmount - (float) $payment->bank_balance_amount, 2);
        if ($balanceDelta > 0) {
            $account->increment('balance', $balanceDelta);
        } elseif ($balanceDelta < 0) {
            $account->decrement('balance', abs($balanceDelta));
        }

        $payment->bank_balance_amount = $targetAmount;
        $payment->save();
    }

    public function uploadSalaryDocument(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        $this->validate([
            'salaryDetailsId' => 'required|integer',
            'salaryDocumentFile' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'salaryDocumentType' => 'required|in:contract,payslip,irs,payment_proof,other',
        ]);

        $salary = $this->salaryDetailsContract((int) $this->salaryDetailsId);
        $path = $this->salaryDocumentFile->store("salary-documents/{$salary->workspace_id}/{$salary->user_id}/{$salary->id}", 'local');
        if (! $path) {
            throw new \RuntimeException('Não foi possível guardar o documento salarial.');
        }

        SalaryDocument::create([
            'recurring_income_id' => $salary->id,
            'user_id' => $salary->user_id,
            'workspace_id' => $salary->workspace_id,
            'document_type' => $this->salaryDocumentType,
            'original_name' => $this->salaryDocumentFile->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $this->salaryDocumentFile->getMimeType() ?: 'application/octet-stream',
            'file_size' => $this->salaryDocumentFile->getSize(),
        ]);
        $this->reset(['salaryDocumentFile']);
        $this->dispatch('toast', text: 'Documento salarial carregado.');
    }

    public function downloadSalaryDocument(int $documentId)
    {
        $document = $this->salaryDocumentsQuery($this->salaryDetailsContract((int) $this->salaryDetailsId))
            ->findOrFail($documentId);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function deleteSalaryDocument(int $documentId): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        $document = $this->salaryDocumentsQuery($this->salaryDetailsContract((int) $this->salaryDetailsId))
            ->findOrFail($documentId);
        if (! Storage::disk('local')->delete($document->file_path)) {
            throw new \RuntimeException('Não foi possível eliminar o documento salarial.');
        }
        $document->delete();
        $this->dispatch('toast', text: 'Documento salarial eliminado.');
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
        abort_if($record->source === 'emprego' && (int) $record->user_id !== (int) auth()->id(), 404);

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
            $this->recSalaryDestination = $record->bank_account_id
                ? 'bank:'.$record->bank_account_id
                : 'cash';
            $this->recSalaryReceivedThisMonth = ($meta['received_month'] ?? null) === now()->format('Y-m');
            $this->recSalaryGross = $meta['salary_gross'] ?? 0;
            $this->recOriginalMonthlyTotal = (float) $record->amount
                + (! empty($meta['meal_card_income_id']) ? (float) ($meta['calculated_sa'] ?? 0) : 0);
            $this->recPosition = $meta['position'] ?? '';
            $this->recDepartment = $meta['department'] ?? '';
            $this->recContractType = $meta['contract_type'] ?? 'sem_termo';
            $this->recContractStatus = $meta['contract_status'] ?? ($record->is_active ? 'active' : 'suspended');
            $this->recStartDate = $meta['start_date'] ?? $record->created_at?->toDateString() ?? '';
            $this->recEndDate = $meta['end_date'] ?? '';
            $this->recAnnualPayments = (int) ($meta['annual_payments'] ?? 14);
            $this->recDuodecimosPercentage = (int) ($meta['duodecimos_percentage'] ?? (! empty($meta['allowances_in_duodecimos']) ? 100 : 0));
            $this->recCivilStatus = $meta['civil_status'] ?? 'solteiro';
            $this->recDependents = (int) ($meta['dependents'] ?? 0);
            $this->recTaxRegion = $meta['tax_region'] ?? 'continente';
            $this->recWithholdingStatus = $meta['withholding_status'] ?? 'normal';
            $this->recOtherComplements = (float) ($meta['other_complements'] ?? 0);
            $this->recProductivityBonus = (float) ($meta['productivity_bonus'] ?? 0);
            $this->recOvertime = (float) ($meta['overtime'] ?? 0);
            $this->recOtherIncome = (float) ($meta['other_income'] ?? 0);
            $this->recHealthInsurance = $meta['health_insurance'] ?? '';
            $this->recExpenseAllowance = (float) ($meta['expense_allowance'] ?? 0);
            $this->recAdditionalBenefits = $meta['additional_benefits'] ?? '';
            $this->recNotes = $record->notes ?? '';
            $this->recMealAllowance = $meta['meal_allowance'] ?? 0;
            $this->recWorkingDays = $meta['working_days'] ?? 22;
            $this->recMealDestination = match ($meta['meal_allowance_destination'] ?? 'same') {
                'bank' => ! empty($meta['meal_bank_account_id']) ? 'bank:'.$meta['meal_bank_account_id'] : 'same',
                'meal_card', 'cash' => $meta['meal_allowance_destination'],
                default => 'same',
            };
            $this->recHolidayAllowanceSameAsSalary = (bool) ($meta['holiday_allowance_same_as_salary'] ?? true);
            $this->recHolidayAllowanceGross = $meta['holiday_allowance_gross'] ?? 0;
            $this->recChristmasAllowanceSameAsSalary = (bool) ($meta['christmas_allowance_same_as_salary'] ?? true);
            $this->recChristmasAllowanceGross = $meta['christmas_allowance_gross'] ?? 0;
            $this->recAllowancesInDuodecimos = (bool) ($meta['allowances_in_duodecimos'] ?? false);
            $this->recIrsJovem = (bool) ($meta['irs_jovem'] ?? false);
            $this->recIrsJovemYear = (int) ($meta['irs_jovem_year'] ?? 1);
            $this->recIrsJovemStartYear = (string) ($meta['irs_jovem_start_year'] ?? '');
            $bonus = $meta['bonus'] ?? [];
            $this->recBonusType = $bonus['type'] ?? 'none';
            $this->recBonusGross = $bonus['gross'] ?? 0;
            $this->recBonusNet = $bonus['net'] ?? '';
            $this->recBonusDate = $bonus['received_at'] ?? now()->format('Y-m-d');
            $this->recalculateEmploymentSalary();
            $payroll = $this->calculateEmploymentPayroll();
            $this->recBonusDeductIrs = (bool) ($bonus['deduct_irs'] ?? true);
            $this->recBonusIrsPercentage = (float) ($bonus['irs_percentage'] ?? $payroll['effective_irs_rate']);
            $this->recBonusDeductSocialSecurity = (bool) ($bonus['deduct_social_security'] ?? true);
            $this->recBonusSocialSecurityPercentage = (float) ($bonus['social_security_percentage'] ?? 11);
            $this->recalculateBonusAmounts();

            $this->closeSalaryDetails();
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

    public function editSalaryBonus(int $id): void
    {
        $salary = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('source', 'emprego')
            ->whereNull('metadata->income_type')
            ->findOrFail($id);
        $metadata = $salary->metadata ?? [];
        $bonus = $metadata['bonus'] ?? [];

        $this->salaryBonusId = $salary->id;
        $this->recDescription = $salary->description;
        $this->recSalaryGross = $metadata['salary_gross'] ?? 0;
        $this->recPosition = $metadata['position'] ?? '';
        $this->recDepartment = $metadata['department'] ?? '';
        $this->recContractType = $metadata['contract_type'] ?? 'sem_termo';
        $this->recContractStatus = $metadata['contract_status'] ?? ($salary->is_active ? 'active' : 'suspended');
        $this->recStartDate = $metadata['start_date'] ?? $salary->created_at?->toDateString() ?? '';
        $this->recEndDate = $metadata['end_date'] ?? '';
        $this->recAnnualPayments = (int) ($metadata['annual_payments'] ?? 14);
        $this->recDuodecimosPercentage = (int) ($metadata['duodecimos_percentage'] ?? (! empty($metadata['allowances_in_duodecimos']) ? 100 : 0));
        $this->recCivilStatus = $metadata['civil_status'] ?? 'solteiro';
        $this->recDependents = (int) ($metadata['dependents'] ?? 0);
        $this->recTaxRegion = $metadata['tax_region'] ?? 'continente';
        $this->recWithholdingStatus = $metadata['withholding_status'] ?? 'normal';
        $this->recOtherComplements = (float) ($metadata['other_complements'] ?? 0);
        $this->recProductivityBonus = (float) ($metadata['productivity_bonus'] ?? 0);
        $this->recOvertime = (float) ($metadata['overtime'] ?? 0);
        $this->recOtherIncome = (float) ($metadata['other_income'] ?? 0);
        $this->recHealthInsurance = $metadata['health_insurance'] ?? '';
        $this->recExpenseAllowance = (float) ($metadata['expense_allowance'] ?? 0);
        $this->recAdditionalBenefits = $metadata['additional_benefits'] ?? '';
        $this->recMealAllowance = $metadata['meal_allowance'] ?? 0;
        $this->recWorkingDays = $metadata['working_days'] ?? 22;
        $this->recMealDestination = match ($metadata['meal_allowance_destination'] ?? 'same') {
            'bank' => ! empty($metadata['meal_bank_account_id']) ? 'bank:'.$metadata['meal_bank_account_id'] : 'same',
            'meal_card', 'cash' => $metadata['meal_allowance_destination'],
            default => 'same',
        };
        $this->recHolidayAllowanceSameAsSalary = (bool) ($metadata['holiday_allowance_same_as_salary'] ?? true);
        $this->recHolidayAllowanceGross = $metadata['holiday_allowance_gross'] ?? 0;
        $this->recChristmasAllowanceSameAsSalary = (bool) ($metadata['christmas_allowance_same_as_salary'] ?? true);
        $this->recChristmasAllowanceGross = $metadata['christmas_allowance_gross'] ?? 0;
        $this->recAllowancesInDuodecimos = (bool) ($metadata['allowances_in_duodecimos'] ?? false);
        $this->recIrsJovem = (bool) ($metadata['irs_jovem'] ?? false);
        $this->recIrsJovemYear = (int) ($metadata['irs_jovem_year'] ?? 1);
        $this->recIrsJovemStartYear = (string) ($metadata['irs_jovem_start_year'] ?? '');
        $this->recBonusGross = $bonus['gross'] ?? 0;
        $this->recBonusNet = $bonus['net'] ?? '';
        $this->recBonusDate = $bonus['received_at'] ?? now()->format('Y-m-d');
        $this->recalculateEmploymentSalary();
        $payroll = $this->calculateEmploymentPayroll();
        $this->recBonusDeductIrs = (bool) ($bonus['deduct_irs'] ?? true);
        $this->recBonusIrsPercentage = (float) ($bonus['irs_percentage'] ?? $payroll['effective_irs_rate']);
        $this->recBonusDeductSocialSecurity = (bool) ($bonus['deduct_social_security'] ?? true);
        $this->recBonusSocialSecurityPercentage = (float) ($bonus['social_security_percentage'] ?? 11);
        $this->recalculateBonusAmounts();

        $this->showSalaryBonusModal = true;
        $this->dispatch('modal-show-salary-bonus');
    }

    public function saveSalaryBonus(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');

            return;
        }

        $this->recalculateBonusAmounts();
        $this->validate([
            'salaryBonusId' => 'required|integer',
            'recBonusGross' => 'required|numeric|min:0',
            'recBonusDeductIrs' => 'required|boolean',
            'recBonusIrsPercentage' => 'required|numeric|between:0,100',
            'recBonusDeductSocialSecurity' => 'required|boolean',
            'recBonusSocialSecurityPercentage' => 'required|numeric|between:0,100',
            'recBonusDate' => 'required|date',
        ]);

        DB::transaction(function (): void {
            $salary = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('user_id', auth()->id())
                ->where('source', 'emprego')
                ->whereNull('metadata->income_type')
                ->lockForUpdate()
                ->findOrFail($this->salaryBonusId);
            $metadata = $salary->metadata ?? [];
            $oldBonus = $metadata['bonus'] ?? [];
            $oldBonusIncomeId = $oldBonus['income_id'] ?? null;

            $bonusData = null;
            if ((float) $this->recBonusGross > 0) {
                $bonusData = [
                    'type' => 'one_off',
                    'gross' => round((float) $this->recBonusGross, 2),
                    'net' => round((float) $this->recBonusNet, 2),
                    'deduct_irs' => $this->recBonusDeductIrs,
                    'irs_percentage' => (float) $this->recBonusIrsPercentage,
                    'irs_amount' => (float) $this->recBonusIrsAmount,
                    'deduct_social_security' => $this->recBonusDeductSocialSecurity,
                    'social_security_percentage' => (float) $this->recBonusSocialSecurityPercentage,
                    'social_security_amount' => (float) $this->recBonusSocialSecurityAmount,
                    'received_at' => $this->recBonusDate,
                ];
            }

            $basePayroll = $this->calculateEmploymentPayroll();

            $metadata['calculated_irs'] = $basePayroll['irs'];
            $metadata['calculated_ss'] = $basePayroll['social_security'];
            $metadata['bonus'] = $bonusData ?? null;
            $salary->amount = max(
                0,
                (float) $basePayroll['net']
                    - (! empty($metadata['meal_card_income_id']) ? (float) ($metadata['calculated_sa'] ?? 0) : 0),
            );
            $salary->metadata = $metadata;
            $salary->save();

            if ($bonusData !== null) {
                $bonusIncome = $oldBonusIncomeId
                    ? Income::where('workspace_id', $salary->workspace_id)
                        ->where('user_id', $salary->user_id)
                        ->whereKey($oldBonusIncomeId)
                        ->firstOrFail()
                    : new Income;
                $bonusIncome->fill([
                    'user_id' => $salary->user_id,
                    'workspace_id' => $salary->workspace_id,
                    'salary_recurring_income_id' => $salary->id,
                    'salary_income_type' => 'prize',
                    'description' => 'Prémio — '.$salary->description,
                    'amount' => $bonusData['net'],
                    'currency' => 'EUR',
                    'received_at' => $bonusData['received_at'],
                    'type' => 'Extra',
                    'source' => 'emprego',
                    'frequency' => 'pontual',
                    'notes' => 'Valor bruto: '.number_format($bonusData['gross'], 2, ',', ' ').' €. IRS: '.number_format($bonusData['irs_amount'], 2, ',', ' ').' €. Segurança Social: '.number_format($bonusData['social_security_amount'], 2, ',', ' ').' €. Valor líquido estimado: '.number_format($bonusData['net'], 2, ',', ' ').' €.',
                ]);
                $bonusIncome->save();
                $bonusData['income_id'] = $bonusIncome->id;
                $salary->metadata = array_merge($salary->metadata ?? [], ['bonus' => $bonusData]);
                $salary->save();
            } elseif ($oldBonusIncomeId) {
                Income::where('workspace_id', $salary->workspace_id)
                    ->where('user_id', $salary->user_id)
                    ->whereKey($oldBonusIncomeId)
                    ->delete();
            }
        });

        $this->showSalaryBonusModal = false;
        $this->dispatch('modal-close-salary-bonus');
        $this->dispatch('toast', text: 'Prémio do contrato atualizado.');
        $this->reset(['salaryBonusId', 'recBonusGross', 'recBonusNet']);
        $this->recBonusType = 'none';
        $this->recBonusGross = 0;
        $this->recBonusDeductIrs = true;
        $this->recBonusIrsPercentage = 0;
        $this->recBonusIrsAmount = 0;
        $this->recBonusDeductSocialSecurity = true;
        $this->recBonusSocialSecurityPercentage = 11;
        $this->recBonusSocialSecurityAmount = 0;
        $this->recBonusDate = now()->format('Y-m-d');
    }

    public function closeSalaryBonusModal(): void
    {
        $this->showSalaryBonusModal = false;
        $this->dispatch('modal-close-salary-bonus');
    }

    public function openSalaryIncomeHistory(int $salaryId, string $type = 'service'): void
    {
        abort_unless(in_array($type, ['service', 'prize'], true), 422);

        $user = auth()->user();
        $salary = RecurringIncome::where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id)
            ->where('source', 'emprego')
            ->whereNull('metadata->income_type')
            ->findOrFail($salaryId);

        $this->salaryIncomeHistoryId = $salary->id;
        $this->salaryIncomeHistoryType = $type;
    }

    public function closeSalaryIncomeHistory(): void
    {
        $this->salaryIncomeHistoryId = null;
        $this->salaryIncomeHistoryType = 'service';
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'recContractType' && $this->recContractType === 'sem_termo') {
            $this->recEndDate = '';
        }

        if ($propertyName === 'source' && $this->source !== 'emprego') {
            $this->extraSalaryId = '';
            $this->serviceUnitAmount = '';
            $this->serviceQuantity = 1;
            $this->amount = '';
        } elseif ($propertyName === 'extraSalaryId' && ! $this->extraSalaryId) {
            $this->serviceUnitAmount = '';
            $this->serviceQuantity = 1;
            $this->amount = '';
        }

        if (in_array($propertyName, ['serviceUnitAmount', 'serviceQuantity'], true)) {
            $this->amount = round((float) $this->serviceUnitAmount * max(0, (int) $this->serviceQuantity), 2);
        }

        // --- 1. LÓGICA DE SALÁRIO (EMPREGO) ---
        if (in_array($propertyName, [
            'recSalaryGross', 'recMealAllowance', 'recWorkingDays',
            'recHolidayAllowanceSameAsSalary', 'recHolidayAllowanceGross',
            'recChristmasAllowanceSameAsSalary', 'recChristmasAllowanceGross',
            'recAllowancesInDuodecimos', 'recDuodecimosPercentage', 'recIrsJovem', 'recIrsJovemYear',
            'recIrsJovemStartYear', 'recCivilStatus', 'recDependents', 'recWithholdingStatus',
            'recOtherComplements', 'recProductivityBonus', 'recOvertime', 'recOtherIncome',
            'recMealDestination', 'recSalaryDestination',
        ], true)) {
            if ($propertyName === 'recAllowancesInDuodecimos') {
                $this->recDuodecimosPercentage = $this->recAllowancesInDuodecimos
                    ? max(0, $this->recDuodecimosPercentage) ?: 100
                    : 0;
            } elseif ($propertyName === 'recDuodecimosPercentage') {
                $this->recAllowancesInDuodecimos = (int) $this->recDuodecimosPercentage > 0;
            }

            $this->recalculateEmploymentSalary();
        }

        if (in_array($propertyName, [
            'recBonusGross',
            'recBonusDeductIrs',
            'recBonusIrsPercentage',
            'recBonusDeductSocialSecurity',
            'recBonusSocialSecurityPercentage',
        ], true)) {
            $this->recalculateBonusAmounts();
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
            RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)->where('user_id', auth()->id())->findOrFail($this->editingFixedId)->update($data);
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
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
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
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
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

    private function recalculateEmploymentSalary(): void
    {
        if ((float) $this->recSalaryGross <= 0) {
            $this->reset([
                'calculatedIRS',
                'calculatedIRSBeforeJovem',
                'calculatedIRSJovemSavings',
                'calculatedIRSJovemRate',
                'calculatedIRSJovemExemptIncome',
                'calculatedSS',
                'calculatedSA',
            ]);

            return;
        }

        $result = $this->calculateEmploymentPayroll();

        $this->calculatedSS = $result['social_security'];
        $this->calculatedIRS = $result['irs'];
        $this->calculatedIRSBeforeJovem = $result['irs_before_jovem'];
        $this->calculatedIRSJovemSavings = $result['irs_jovem_estimated_savings'];
        $this->calculatedIRSJovemRate = $result['irs_jovem_rate'];
        $this->calculatedIRSJovemExemptIncome = $result['irs_jovem_exempt_income'];
        $this->calculatedSA = $result['meal_total'];
        $this->recAmount = $result['net'];
    }

    private function recalculateBonusAmounts(): void
    {
        $gross = max(0, (float) $this->recBonusGross);
        $this->recBonusIrsAmount = $this->recBonusDeductIrs
            ? round($gross * max(0, min(100, $this->recBonusIrsPercentage)) / 100, 2)
            : 0;
        $this->recBonusSocialSecurityAmount = $this->recBonusDeductSocialSecurity
            ? round($gross * max(0, min(100, $this->recBonusSocialSecurityPercentage)) / 100, 2)
            : 0;
        $this->recBonusNet = round(max(0, $gross - $this->recBonusIrsAmount - $this->recBonusSocialSecurityAmount), 2);
    }

    private function calculateEmploymentPayroll(float $additionalGross = 0): array
    {
        $result = app(PortugueseNetPayCalculator::class)->calculate(
            gross: (float) $this->recSalaryGross
                + $this->calculateMonthlyDuodecimos()
                + (float) $this->recOtherComplements
                + (float) $this->recProductivityBonus
                + (float) $this->recOvertime
                + (float) $this->recOtherIncome
                + $additionalGross,
            civilStatus: $this->recCivilStatus,
            dependents: (int) $this->recDependents,
            mealPerDay: (float) $this->recMealAllowance,
            workingDays: (int) $this->recWorkingDays,
            mealPayment: $this->recMealDestination === 'meal_card' ? 'cartao' : 'numerario',
            irsJovem: $this->recIrsJovem,
            irsJovemYear: $this->recIrsJovemYear,
        );

        if ($this->recWithholdingStatus === 'exempt') {
            $result['irs'] = 0.0;
            $result['effective_irs_rate'] = 0.0;
            $result['net'] = round($result['gross'] - $result['social_security'] + $result['meal_total'], 2);
        }

        return $result;
    }

    private function calculateMonthlyDuodecimos(): float
    {
        if (! $this->recAllowancesInDuodecimos) {
            return 0;
        }

        $percentage = max(0, min(100, $this->recDuodecimosPercentage));
        if ($percentage === 0) {
            $percentage = 100;
        }

        $allowancesGross = ($this->recHolidayAllowanceSameAsSalary
            ? (float) $this->recSalaryGross
            : (float) $this->recHolidayAllowanceGross)
            + ($this->recChristmasAllowanceSameAsSalary
                ? (float) $this->recSalaryGross
                : (float) $this->recChristmasAllowanceGross);

        return round(($allowancesGross * $percentage / 100) / 12, 2);
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
        $fixed = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->whereKey($id)->firstOrFail();
        abort_if($fixed->source === 'emprego' && (int) $fixed->user_id !== (int) auth()->id(), 404);

        DB::transaction(function () use ($fixed): void {
            foreach ($this->salaryDocumentsQuery($fixed)->get() as $document) {
                if (Storage::disk('local')->exists($document->file_path) && ! Storage::disk('local')->delete($document->file_path)) {
                    throw new \RuntimeException('Não foi possível eliminar um documento do contrato.');
                }
            }
            $mealCardIncomeId = $fixed->metadata['meal_card_income_id'] ?? null;
            if ($mealCardIncomeId) {
                RecurringIncome::where('workspace_id', $fixed->workspace_id)
                    ->where('user_id', $fixed->user_id)
                    ->whereKey($mealCardIncomeId)
                    ->delete();
            }
            $fixed->delete();
        });
        $this->dispatch('toast', text: 'Registo removido.');
    }

    public function deleteExtra($id)
    {
        if (! auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Não tens permissão para apagar este registo.');

            return;
        }
        Income::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->whereKey($id)->delete();
        $this->dispatch('toast', text: 'Receita removida.');
    }

    private function salaryDetailsContract(int $id): RecurringIncome
    {
        $user = auth()->user();

        return RecurringIncome::where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id)
            ->where('source', 'emprego')
            ->whereNull('metadata->income_type')
            ->findOrFail($id);
    }

    private function salaryPaymentsQuery(RecurringIncome $salary)
    {
        $user = auth()->user();

        return SalaryPayment::where('recurring_income_id', $salary->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id);
    }

    private function salaryDocumentsQuery(RecurringIncome $salary)
    {
        $user = auth()->user();

        return SalaryDocument::where('recurring_income_id', $salary->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id);
    }

    private function salaryPayrollForMetadata(array $metadata): array
    {
        $monthlyDuodecimos = (float) ($metadata['monthly_duodecimos'] ?? 0);
        $gross = (float) ($metadata['salary_gross'] ?? 0)
            + $monthlyDuodecimos
            + (float) ($metadata['other_complements'] ?? 0)
            + (float) ($metadata['productivity_bonus'] ?? 0)
            + (float) ($metadata['overtime'] ?? 0)
            + (float) ($metadata['other_income'] ?? 0);
        $result = app(PortugueseNetPayCalculator::class)->calculate(
            gross: $gross,
            civilStatus: $metadata['civil_status'] ?? 'solteiro',
            dependents: (int) ($metadata['dependents'] ?? 0),
            mealPerDay: (float) ($metadata['meal_allowance'] ?? 0),
            workingDays: (int) ($metadata['working_days'] ?? 0),
            mealPayment: ($metadata['meal_allowance_destination'] ?? null) === 'meal_card' ? 'cartao' : 'numerario',
            irsJovem: (bool) ($metadata['irs_jovem'] ?? false),
            irsJovemYear: (int) ($metadata['irs_jovem_year'] ?? 1),
        );

        if (($metadata['withholding_status'] ?? 'normal') === 'exempt') {
            $result['irs'] = 0.0;
            $result['effective_irs_rate'] = 0.0;
            $result['net'] = round($result['gross'] - $result['social_security'] + $result['meal_total'], 2);
        }

        return $result;
    }

    public function associateExtraWithSalary(int $incomeId, $salaryId): void
    {
        $user = auth()->user();
        $income = Income::where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id)
            ->whereNull('salary_recurring_income_id')
            ->findOrFail($incomeId);

        $salary = null;
        if (filled($salaryId)) {
            $salary = RecurringIncome::where('workspace_id', $user->current_workspace_id)
                ->where('user_id', $user->id)
                ->where('source', 'emprego')
                ->whereNull('metadata->income_type')
                ->findOrFail((int) $salaryId);
        }

        $income->salary_recurring_income_id = $salary?->id;
        $income->save();
        $this->dispatch('toast', text: $salary ? 'Entrada associada ao contrato.' : 'Associação ao contrato removida.');
    }


    public function exportIncomeStatement(string $period = 'previous_month')
    {
        $user = auth()->user();
        $workspace = $user?->currentWorkspace;
        abort_unless($user && $workspace && (int) $workspace->id === (int) $user->current_workspace_id, 403);

        $isBusiness = in_array($workspace->type, ['business', 'company'], true);
        if ($isBusiness) {
            app(\App\Services\BusinessAccessService::class)->assert('export_reports', $user, $workspace);
        }

        if ($period === 'previous_month') {
            $start = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $periodLabel = 'Último mês — '.$start->translatedFormat('F Y');
        } elseif ($period === 'previous_year') {
            $start = Carbon::now()->subYear()->startOfYear();
            $periodLabel = 'Último ano — '.$start->year;
        } elseif ($period === 'specific_month') {
            $this->validate([
                'statementExportMonth' => 'required|integer|between:1,12',
                'statementExportYear' => 'required|integer|between:2000,'.now()->year,
            ]);

            $start = Carbon::create((int) $this->statementExportYear, (int) $this->statementExportMonth, 1)->startOfMonth();
            $periodLabel = ucfirst($start->translatedFormat('F Y'));
        } elseif ($period === 'specific_year') {
            $this->validate([
                'statementExportYear' => 'required|integer|between:2000,'.now()->year,
            ]);

            $start = Carbon::create((int) $this->statementExportYear, 1, 1)->startOfYear();
            $periodLabel = 'Ano específico — '.$start->year;
        } else {
            abort(422, 'Período de exportação inválido.');
        }

        $end = in_array($period, ['previous_year', 'specific_year'], true)
            ? $start->copy()->endOfYear()
            : $start->copy()->endOfMonth();

        $scopeIncome = fn ($query) => $query
            ->where('workspace_id', $workspace->id)
            ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id));

        $incomes = Income::with(['bankAccount', 'salaryContract'])
            ->where($scopeIncome)
            ->whereBetween('received_at', [$start->toDateString(), $end->toDateString()])
            ->orderBy('received_at')
            ->orderBy('id')
            ->get();

        $salaryPayments = SalaryPayment::with(['recurringIncome', 'bankAccount'])
            ->where('workspace_id', $workspace->id)
            ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id))
            ->where(function ($query) use ($start, $end): void {
                $query->whereBetween('paid_at', [$start->toDateString(), $end->toDateString()])
                    ->orWhere(function ($pending) use ($start, $end): void {
                        $pending->where('status', '!=', 'paid')
                            ->whereRaw(
                                '(payment_year * 100 + payment_month) between ? and ?',
                                [$start->year * 100 + $start->month, $end->year * 100 + $end->month],
                            );
                    });
            })
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();

        $recurringIncomes = RecurringIncome::with('bankAccount')
            ->where('workspace_id', $workspace->id)
            ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('source')
            ->orderBy('description')
            ->get();

        $currency = strtoupper((string) ($workspace->currency ?? 'EUR'));
        $normalIncomes = $incomes->filter(fn (Income $income) => ! (bool) $income->added_to_salary);
        $deferredIncomes = $incomes->filter(fn (Income $income) => (bool) $income->added_to_salary);

        $incomeReceivedTotal = (float) $normalIncomes->sum(
            fn (Income $income) => (float) ($income->amount_converted ?? $income->amount)
        );
        $deferredIncomeTotal = (float) $deferredIncomes->sum(
            fn (Income $income) => (float) ($income->amount_converted ?? $income->amount)
        );

        $salaryPaymentValue = fn (SalaryPayment $payment): float => round(
            (float) $payment->net_received
                + (($payment->recurringIncome?->metadata['meal_allowance_destination'] ?? null) === 'meal_card'
                    ? 0
                    : (float) $payment->meal_allowance),
            2
        );

        $paidSalaryPayments = $salaryPayments->filter(fn (SalaryPayment $payment) => $payment->status === 'paid');
        $pendingSalaryPayments = $salaryPayments->reject(fn (SalaryPayment $payment) => $payment->status === 'paid');
        $paidSalaryTotal = (float) $paidSalaryPayments->sum($salaryPaymentValue);
        $pendingSalaryTotal = (float) $pendingSalaryPayments->sum($salaryPaymentValue);

        $incomeBySource = $normalIncomes
            ->groupBy(fn (Income $income) => $income->source ?: 'outro')
            ->map(fn ($group) => (float) $group->sum(fn (Income $income) => (float) ($income->amount_converted ?? $income->amount)));

        $data = [
            'workspaceName' => $workspace->name,
            'userName' => $user->name,
            'periodLabel' => $periodLabel,
            'startDate' => $start->format('d/m/Y'),
            'endDate' => $end->format('d/m/Y'),
            'generatedAt' => now()->format('d/m/Y H:i'),
            'currency' => $currency,
            'incomes' => $incomes,
            'salaryPayments' => $salaryPayments,
            'recurringIncomes' => $recurringIncomes,
            'incomeReceivedTotal' => $incomeReceivedTotal,
            'deferredIncomeTotal' => $deferredIncomeTotal,
            'paidSalaryTotal' => $paidSalaryTotal,
            'pendingSalaryTotal' => $pendingSalaryTotal,
            'totalReceived' => round($incomeReceivedTotal + $paidSalaryTotal, 2),
            'incomeBySource' => $incomeBySource,
            'salaryPaymentValue' => $salaryPaymentValue,
        ];

        $fileName = 'Extrato_Receitas_'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf';
        $pdfContent = Pdf::loadView('pdf.income-statement', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true, 'defaultFont' => 'DejaVu Sans'])
            ->output();

        $token = \Illuminate\Support\Str::random(48);
        $filePath = 'income-statements/'.$user->id.'/'.$token.'.pdf';
        Storage::disk('local')->put($filePath, $pdfContent);
        session()->put('income_statement_downloads.'.$token, ['path' => $filePath, 'name' => $fileName]);

        $this->dispatch('income-statement-download', url: route('hub.incomes.statement.download', ['token' => $token]));
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

        $isPersonalWorkspace = $user->currentWorkspace?->type === 'personal';

        $fixedIncomes = RecurringIncome::with('bankAccount')
            ->where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->get();

        $allSalaryContracts = $fixedIncomes
            ->where('source', 'emprego')
            ->where('user_id', $user->id)
            ->reject(fn (RecurringIncome $income) => ($income->metadata['income_type'] ?? null) === 'meal_card_allowance');
        $salaryContracts = $allSalaryContracts->values();
        foreach ($salaryContracts as $salaryContract) {
            $this->serviceFixedPrices[$salaryContract->id] ??= (float) ($salaryContract->metadata['service_unit_price'] ?? 0);
            $this->serviceQuickQuantities[$salaryContract->id] ??= 1;
        }
        $salaryContractIds = $allSalaryContracts->modelKeys();
        $salaryExtraIncomes = Income::with('bankAccount')
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->whereIn('salary_recurring_income_id', $salaryContractIds)
            ->latest('received_at')
            ->get();
        $unlinkedExtraIncomes = Income::where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->whereNull('salary_recurring_income_id')
            ->latest('received_at')
            ->get();

        $netPayCalculator = app(PortugueseNetPayCalculator::class);
        $fixedIncomes->each(function (RecurringIncome $income) use ($netPayCalculator): void {
            if ($income->source === 'emprego' && ($income->metadata['income_type'] ?? null) !== 'meal_card_allowance') {
                $income->setAttribute('annual_net_estimate', $this->estimateAnnualEmploymentNet($income, $netPayCalculator));
            }
        });

        $extraIncomes = Income::with('bankAccount')
            ->where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->where(function ($query): void {
                $query->whereNull('added_to_salary')->orWhere('added_to_salary', false);
            })
            ->latest()
            ->get();

        // Movimentos efetivamente recebidos em contas bancárias (inclui contas
        // identificadas como MB WAY pelo nome). Não inclui previsões nem dinheiro físico.
        $bankReceivedIncomes = Income::with('bankAccount')
            ->where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->whereNotNull('bank_account_id')
            ->where(function ($query): void {
                $query->whereNull('added_to_salary')->orWhere('added_to_salary', false);
            })
            ->latest('received_at')
            ->get();

        $bankReceivedSalaryPayments = SalaryPayment::with(['recurringIncome', 'bankAccount'])
            ->where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'paid')
            ->whereNotNull('bank_account_id')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->get();

        $bankTransactionsTotal = (float) $bankReceivedIncomes->sum(
            fn (Income $income) => (float) ($income->amount_converted ?? $income->amount)
        );
        $bankTransactionsTotal += (float) $bankReceivedSalaryPayments->sum(function (SalaryPayment $payment): float {
            if ((float) $payment->bank_balance_amount > 0) {
                return (float) $payment->bank_balance_amount;
            }

            $mealCard = ($payment->recurringIncome?->metadata['meal_allowance_destination'] ?? null) === 'meal_card';

            return max(0, (float) $payment->net_received - ($mealCard ? (float) $payment->meal_allowance : 0));
        });
        $bankTransactionsTotal = round($bankTransactionsTotal, 2);

        $salaryIncomeHistoryContract = $this->salaryIncomeHistoryId
            ? $allSalaryContracts->firstWhere('id', $this->salaryIncomeHistoryId)
            : null;
        $salaryIncomeHistory = $salaryIncomeHistoryContract
            ? $salaryExtraIncomes
                ->where('salary_recurring_income_id', $salaryIncomeHistoryContract->id)
                ->filter(fn ($extra) => $this->salaryIncomeHistoryType === 'prize'
                    ? $extra->salary_income_type === 'prize'
                    : ($extra->salary_income_type ?? 'service') !== 'prize')
                ->values()
            : collect();

        $salaryDetails = $this->salaryDetailsId ? $this->salaryDetailsContract($this->salaryDetailsId) : null;
        $salaryDetailsPayroll = $salaryDetails ? $this->salaryPayrollForMetadata($salaryDetails->metadata ?? []) : [];
        $salaryPayments = $salaryDetails
            ? $this->salaryPaymentsQuery($salaryDetails)->orderByDesc('payment_year')->orderByDesc('payment_month')->get()
            : collect();
        $salaryDocuments = $salaryDetails
            ? $this->salaryDocumentsQuery($salaryDetails)->latest()->get()
            : collect();
        $projectedSalaryContracts = $allSalaryContracts
            ->filter(fn (RecurringIncome $income) => ($income->metadata['contract_status'] ?? ($income->is_active ? 'active' : 'suspended')) === 'active')
            ->values();
        $projectedSalaryContracts->each(function (RecurringIncome $income): void {
            $metadata = $income->metadata ?? [];
            $mealAllowance = (float) ($metadata['calculated_sa'] ?? 0);
            $income->setAttribute(
                'projected_salary_net',
                round(max(0, (float) $income->amount - (empty($metadata['meal_card_income_id']) ? $mealAllowance : 0)), 2),
            );
        });
        $salaryActiveCount = $projectedSalaryContracts->count();
        $salaryMonthlyTotal = $projectedSalaryContracts->sum('amount');
        $projectedContractIds = $projectedSalaryContracts->modelKeys();
        $previousMonth = now()->copy()->subMonthNoOverflow();
        $salaryAdditionsThisMonth = $salaryExtraIncomes
            ->whereIn('salary_recurring_income_id', $projectedContractIds)
            ->where('added_to_salary', true)
            ->filter(fn (Income $income) => $income->received_at?->month === $previousMonth->month && $income->received_at?->year === $previousMonth->year)
            ->sum(fn (Income $income) => (float) ($income->amount_converted ?? $income->amount));
        $projectedSalaryNetTotal = $projectedSalaryContracts->sum('projected_salary_net') + $salaryAdditionsThisMonth;
        $projectedSalaryAnnualTotal = round($projectedSalaryNetTotal * 12, 2);
        $projectedMealAllowanceTotal = $projectedSalaryContracts->sum(fn (RecurringIncome $income) => (float) ($income->metadata['calculated_sa'] ?? 0));
        $salaryAnnualTotal = $projectedSalaryContracts->sum(fn (RecurringIncome $income) => (float) ($income->annual_net_estimate ?? ((float) $income->amount * (int) ($income->metadata['annual_payments'] ?? 12))));

        // Total anual líquido de todos os rendimentos recorrentes ativos do workspace.
        // Os contratos de trabalho usam a estimativa de processamento salarial;
        // os restantes registos são anualizados pela periodicidade configurada.
        $allRecordsAnnualNetTotal = $fixedIncomes
            ->filter(function (RecurringIncome $income): bool {
                $metadata = $income->metadata ?? [];
                $status = $metadata['contract_status'] ?? ($income->is_active ? 'active' : 'suspended');

                return $status === 'active'
                    && ($metadata['income_type'] ?? null) !== 'meal_card_allowance';
            })
            ->sum(function (RecurringIncome $income): float {
                if ($income->source === 'emprego') {
                    return (float) ($income->annual_net_estimate ?? (
                        (float) $income->amount * (int) ($income->metadata['annual_payments'] ?? 12)
                    ));
                }

                $annualOccurrences = match ($income->frequency ?? 'mensal') {
                    'semanal' => 52,
                    'trimestral' => 4,
                    'semestral' => 2,
                    'anual' => 1,
                    'variavel' => 12,
                    default => 12,
                };

                return max(0, (float) $income->amount) * $annualOccurrences;
            });
        $allRecordsAnnualNetTotal = round($allRecordsAnnualNetTotal, 2);

        // Histórico detalhado apresentado nos cartões de salário e alimentação.
        $projectedSalaryHistoryPayments = SalaryPayment::with(['recurringIncome', 'bankAccount'])
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->whereIn('recurring_income_id', $salaryContractIds)
            ->orderByDesc('payment_year')
            ->orderByDesc('payment_month')
            ->orderByDesc('paid_at')
            ->get();

        $projectedSalaryHistoryExtras = $salaryExtraIncomes
            ->sortByDesc(fn (Income $income) => $income->received_at?->timestamp ?? 0)
            ->values();

        $totalMonthly = $fixedIncomes->sum('amount') + $extraIncomes->sum(fn ($i) => (float) ($i->amount_converted ?? $i->amount));

        // Estatísticas
        $allIncomes = Income::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->get();

        // Salário fixo mensal (soma dos rendimentos recorrentes ativos)
        $monthlySalary = (float) RecurringIncome::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->where('is_active', true)
            ->sum('amount');

        $lastMonth = now()->startOfMonth()->subMonth();
        $lastMonthReceived = (float) Income::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->whereMonth('received_at', $lastMonth->month)
            ->whereYear('received_at', $lastMonth->year)
            ->sum(DB::raw('COALESCE(amount_converted, amount)'));
        $lastMonthReceived += (float) SalaryPayment::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'paid')
            ->whereMonth('paid_at', $lastMonth->month)
            ->whereYear('paid_at', $lastMonth->year)
            ->sum('net_received');

        $thisMonth = now();
        // Recebimentos do mês: salário líquido + subsídio de alimentação + serviços/prémios.
        // As receitas associadas a contratos são somadas separadamente para evitar duplicações.
        $thisMonthIncomeTotal = (float) Income::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->whereMonth('received_at', $thisMonth->month)
            ->whereYear('received_at', $thisMonth->year)
            ->whereNull('salary_recurring_income_id')
            ->sum(DB::raw('COALESCE(amount_converted, amount)'));

        $paidSalaryPayments = SalaryPayment::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'paid')
            ->whereMonth('paid_at', $thisMonth->month)
            ->whereYear('paid_at', $thisMonth->year)
            ->get(['recurring_income_id', 'net_received', 'meal_allowance']);

        $thisMonthPaidSalaryNet = (float) $paidSalaryPayments->sum(function (SalaryPayment $payment): float {
            // O subsídio já está incluído no líquido quando é pago em cartão.
            $contract = RecurringIncome::find($payment->recurring_income_id);
            $mealCard = ($contract?->metadata['meal_allowance_destination'] ?? null) === 'meal_card';

            return (float) $payment->net_received + ($mealCard ? 0 : (float) $payment->meal_allowance);
        });

        // Só contar contratos sem pagamento registado quando o utilizador
        // confirmou explicitamente que já recebeu o salário neste mês.
        $currentReceivedMonth = $thisMonth->format('Y-m');
        $receivedSalaryContracts = $projectedSalaryContracts
            ->filter(fn (RecurringIncome $income) => ($income->metadata['received_month'] ?? null) === $currentReceivedMonth)
            ->reject(fn (RecurringIncome $income) => $paidSalaryPayments->contains('recurring_income_id', $income->id));

        $thisMonthPendingSalaryNet = (float) $receivedSalaryContracts->sum(function (RecurringIncome $income): float {
            $metadata = $income->metadata ?? [];

            return (float) $income->amount + (float) ($metadata['calculated_sa'] ?? 0);
        });

        $thisMonthSalaryExtras = (float) $salaryExtraIncomes
            ->filter(fn (Income $income) => $income->received_at?->month === $thisMonth->month
                && $income->received_at?->year === $thisMonth->year
                && ! (bool) $income->added_to_salary)
            ->sum(fn (Income $income) => (float) ($income->amount_converted ?? $income->amount));

        $thisMonthSalaryTotal = $thisMonthPaidSalaryNet + $thisMonthPendingSalaryNet;
        $thisMonthPaidSalaryTotal = $thisMonthPaidSalaryNet;
        $thisMonthPendingSalaryTotal = $thisMonthPendingSalaryNet;
        $thisMonthReceived = $thisMonthIncomeTotal + $thisMonthSalaryTotal + $thisMonthSalaryExtras;

        // Média mensal (últimos 6 meses) — inclui salário fixo
        $monthlyTotals = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $monthExtra = Income::where('workspace_id', $workspaceId)
                ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
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
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
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
            'salaryContracts' => $salaryContracts,
            'salaryContractOptions' => $allSalaryContracts,
            'salaryDetails' => $salaryDetails,
            'salaryDetailsPayroll' => $salaryDetailsPayroll,
            'salaryPayments' => $salaryPayments,
            'salaryDocuments' => $salaryDocuments,
            'salaryActiveCount' => $salaryActiveCount,
            'salaryMonthlyTotal' => $salaryMonthlyTotal,
            'salaryAnnualTotal' => $salaryAnnualTotal,
            'allRecordsAnnualNetTotal' => $allRecordsAnnualNetTotal,
            'projectedSalaryContracts' => $projectedSalaryContracts,
            'projectedSalaryNetTotal' => $projectedSalaryNetTotal,
            'projectedSalaryAnnualTotal' => $projectedSalaryAnnualTotal,
            'projectedMealAllowanceTotal' => $projectedMealAllowanceTotal,
            'projectedSalaryHistoryPayments' => $projectedSalaryHistoryPayments,
            'projectedSalaryHistoryExtras' => $projectedSalaryHistoryExtras,
            'salaryExtraIncomes' => $salaryExtraIncomes,
            'unlinkedExtraIncomes' => $unlinkedExtraIncomes,
            'salaryIncomeHistoryContract' => $salaryIncomeHistoryContract,
            'salaryIncomeHistory' => $salaryIncomeHistory,
            'extraIncomes' => $extraIncomes,
            'bankReceivedIncomes' => $bankReceivedIncomes,
            'bankReceivedSalaryPayments' => $bankReceivedSalaryPayments,
            'bankTransactionsTotal' => $bankTransactionsTotal,
            'totalMonthly' => $totalMonthly,
            'avgMonthly' => $avgMonthly,
            'lastMonth' => $lastMonth,
            'lastMonthReceived' => $lastMonthReceived,
            'thisMonthReceived' => $thisMonthReceived,
            'thisMonthIncomeTotal' => $thisMonthIncomeTotal,
            'thisMonthPaidSalaryTotal' => $thisMonthPaidSalaryTotal,
            'thisMonthPendingSalaryTotal' => $thisMonthPendingSalaryTotal,
            'thisMonthSalaryExtras' => $thisMonthSalaryExtras,
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

    private function estimateAnnualEmploymentNet(RecurringIncome $income, PortugueseNetPayCalculator $calculator): float
    {
        $metadata = $income->metadata ?? [];
        $mealAllowance = (float) ($metadata['calculated_sa'] ?? 0);
        $hasSeparateMealCard = ! empty($metadata['meal_card_income_id']);

        $annualNet = (float) $income->amount * 12;
        $annualNet += $hasSeparateMealCard ? $mealAllowance * 11 : -$mealAllowance;

        $bonus = $metadata['bonus'] ?? [];
        if (($bonus['type'] ?? null) === 'annual') {
            $annualNet += (float) ($bonus['net'] ?? 0);
        } elseif (($bonus['type'] ?? null) === 'one_off' && ! empty($bonus['received_at'])) {
            $bonusDate = \Illuminate\Support\Carbon::parse($bonus['received_at']);
            if ($bonusDate->betweenIncluded(now()->startOfDay(), now()->addYear()->endOfDay())) {
                $annualNet += (float) ($bonus['net'] ?? 0);
            }
        }

        $duodecimosPercentage = (int) ($metadata['duodecimos_percentage'] ?? (($metadata['allowances_in_duodecimos'] ?? false) ? 100 : 0));
        if ((int) ($metadata['annual_payments'] ?? 14) === 14 && $duodecimosPercentage < 100) {
            $salaryGross = (float) ($metadata['salary_gross'] ?? 0);
            $holidayGross = ($metadata['holiday_allowance_same_as_salary'] ?? true)
                ? $salaryGross
                : (float) ($metadata['holiday_allowance_gross'] ?? 0);
            $christmasGross = ($metadata['christmas_allowance_same_as_salary'] ?? true)
                ? $salaryGross
                : (float) ($metadata['christmas_allowance_gross'] ?? 0);
            $irsJovem = (bool) ($metadata['irs_jovem'] ?? false);
            $irsJovemYear = (int) ($metadata['irs_jovem_year'] ?? 1);

            foreach ([$holidayGross, $christmasGross] as $allowanceGross) {
                $remainingAllowanceGross = $allowanceGross * (100 - $duodecimosPercentage) / 100;
                if ($remainingAllowanceGross > 0) {
                    $allowancePayroll = $calculator->calculate(
                        gross: $remainingAllowanceGross,
                        civilStatus: (string) ($metadata['civil_status'] ?? 'solteiro'),
                        dependents: (int) ($metadata['dependents'] ?? 0),
                        irsJovem: $irsJovem,
                        irsJovemYear: $irsJovemYear,
                    );
                    $annualNet += ($metadata['withholding_status'] ?? 'normal') === 'exempt'
                        ? max(0, $allowancePayroll['gross'] - $allowancePayroll['social_security'])
                        : $allowancePayroll['net'];
                }
            }
        }

        return round(max(0, $annualNet), 2);
    }
}