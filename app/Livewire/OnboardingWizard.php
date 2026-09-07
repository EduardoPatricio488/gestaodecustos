<?php

namespace App\Livewire;

use App\Models\RecurringIncome;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class OnboardingWizard extends Component
{
    public int $step = 1;

    public int $totalSteps = 5;

    public bool $show = true;

    // =========================================================
    // PASSO 2 — FONTE DE RENDIMENTO
    // =========================================================

    public string $salarySource = 'emprego';

    public string $otherSourceDetail = '';

    public string $salaryDescription = '';

    // Resultado final considerado pelo Finance Pro AI
    public $salaryGross = 0;

    public $salaryAmount = 0;

    // Dia em que o rendimento é recebido
    public int $salaryDay = 25;

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
        $this->show = ! $user->onboarding_completed;

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
            $this->saveStep2();

            return;
        }

        if ($this->step === 3) {
            $this->saveStep3();

            return;
        }

        if ($this->step === 4) {
            $this->saveStep4();

            return;
        }

        $this->step++;
    }

    // =========================================================
    // GUARDAR PASSO 2
    // =========================================================

    private function saveStep2()
    {
        /*
         * Validação base para todas as fontes.
         */
        $rules = [
            'salaryAmount' => 'required|numeric|min:0.01',
            'salaryDay' => 'required|integer|between:1,31',
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
    // PASSO 3 — WORKSPACE
    // =========================================================

    public function saveStep3()
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
                    $user->update(['current_workspace_id' => $workspace->id]);
                }

                // LIMPAR A CACHE: Isto força a Sidebar e o Header a lerem o nome novo imediatamente
                Cache::flush();
            }
        }

        $this->step++;
    }

    // =========================================================
    // PASSO 4 — CATEGORIA
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
