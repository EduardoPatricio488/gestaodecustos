<?php

namespace App\Livewire;

use App\Models\RecurringIncome;
use App\Services\BusinessAccessService;
use Livewire\Attributes\Locked;

class OnboardingSalary extends IncomeHub
{
    #[Locked]
    public ?int $onboardingSalaryId = null;

    #[Locked]
    public int $onboardingWorkspaceId;

    #[Locked]
    public int $footerTotal;

    public function mount(bool $dashboardMode = false, ?int $salaryId = null, int $footerTotal = 10)
    {
        parent::mount($dashboardMode);
        $this->footerTotal = $footerTotal;
        $this->onboardingWorkspaceId = $this->workspaceId;
        $this->assertWorkspaceAccess(false);

        if ($salaryId) {
            $salary = RecurringIncome::where('workspace_id', $this->onboardingWorkspaceId)
                ->where('user_id', auth()->id())
                ->where('source', 'emprego')
                ->findOrFail($salaryId);
            abort_if(($salary->metadata['income_type'] ?? null) === 'meal_card_allowance', 404);
            $this->onboardingSalaryId = $salary->id;
            $this->editFixed($salary->id, false);
        } else {
            $this->newEmployment(false);
        }
    }

    public function scanEmploymentContract(): void
    {
        $this->assertWorkspaceAccess();
        parent::scanEmploymentContract();
    }

    public function saveEmployment(bool $closeModal = true): ?int
    {
        $this->assertWorkspaceAccess();
        $this->editingFixedId = $this->onboardingSalaryId;
        $this->recalculateEmploymentSalary();
        $salaryId = parent::saveEmployment(false);

        if ($salaryId !== null) {
            $this->onboardingSalaryId = $salaryId;
            $this->dispatch('onboarding-salary-saved', salaryId: $salaryId)->to(OnboardingWizard::class);
        }

        return $salaryId;
    }

    protected function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'required_if' => 'O campo :attribute é obrigatório.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'string' => 'O campo :attribute deve ser texto.',
            'boolean' => 'O campo :attribute é inválido.',
            'in' => 'Seleciona uma opção válida para :attribute.',
            'date' => 'Indica uma data válida para :attribute.',
            'after_or_equal' => 'A data de fim não pode ser anterior à data de início.',
            'min' => ['numeric' => 'O campo :attribute deve ser pelo menos :min.'],
            'max' => [
                'numeric' => 'O campo :attribute não pode ser superior a :max.',
                'string' => 'O campo :attribute não pode ultrapassar :max caracteres.',
            ],
            'between' => ['numeric' => 'O campo :attribute deve estar entre :min e :max.'],
            'contractScanPhoto.required' => 'Seleciona uma fotografia do contrato.',
            'contractScanPhoto.image' => 'Seleciona uma imagem válida.',
            'contractScanPhoto.mimes' => 'A fotografia deve estar em formato JPG, PNG ou WEBP.',
            'contractScanPhoto.max' => 'A fotografia não pode ultrapassar 10 MB.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'recDescription' => 'nome da empresa',
            'recAmount' => 'rendimento líquido',
            'recSalaryGross' => 'salário bruto',
            'recPosition' => 'cargo',
            'recDepartment' => 'departamento',
            'recContractType' => 'tipo de contrato',
            'recContractStatus' => 'estado do contrato',
            'recStartDate' => 'data de início',
            'recEndDate' => 'data de fim',
            'recDay' => 'dia de recebimento',
            'recWithholdingStatus' => 'estado da retenção',
            'recMealAllowance' => 'subsídio de alimentação',
            'recWorkingDays' => 'dias trabalhados',
            'recSalaryDestination' => 'destino do salário',
            'recMealDestination' => 'destino do subsídio de alimentação',
            'recHolidayAllowanceGross' => 'subsídio de férias',
            'recChristmasAllowanceGross' => 'subsídio de Natal',
            'recIrsJovemYear' => 'ano do IRS Jovem',
            'recAdditionalBenefits' => 'benefícios e regalias',
        ];
    }

    private function assertWorkspaceAccess(bool $write = true): void
    {
        $user = auth()->user();
        abort_unless((int) $user->current_workspace_id === $this->onboardingWorkspaceId, 403);
        $workspace = $user->workspaces()->whereKey($this->onboardingWorkspaceId)->firstOrFail();
        if ($write && in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', $user, $workspace);
        }
    }

    public function render()
    {
        return view('livewire.onboarding-salary');
    }
}
