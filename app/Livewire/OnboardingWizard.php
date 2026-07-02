<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RecurringIncome;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class OnboardingWizard extends Component
{
    public int $step = 1;
    public int $totalSteps = 5;
    public bool $show = true; // <-- NOVO

    // Passo 2: Ordenado
    public string $salaryDescription = '';
    public string $salaryAmount = '';
    public string $salaryDay = '25';
    public string $salarySource = 'emprego';

    // Passo 3: Workspace
    public string $workspaceName = '';

    // Passo 4: Categoria
    public string $categoryName = '';
    public string $categoryColor = '#6366f1';

    public function mount() // <-- NOVO
    {
        $this->show = !auth()->user()->onboarding_completed;
    }

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

    private function saveStep2()
    {
        if ($this->salaryAmount && $this->salaryDescription) {
            $this->validate([
                'salaryDescription' => 'required|string|max:255',
                'salaryAmount'      => 'required|numeric|min:0.01',
                'salaryDay'         => 'required|integer|between:1,31',
            ]);

            $user = auth()->user();
            $workspaceId = $user->current_workspace_id
                ?? $user->workspaces()->first()?->id;

            if ($workspaceId) {
                RecurringIncome::create([
                    'user_id'      => $user->id,
                    'workspace_id' => $workspaceId,
                    'description'  => $this->salaryDescription,
                    'amount'       => $this->salaryAmount,
                    'day_of_month' => $this->salaryDay,
                    'source'       => $this->salarySource,
                    'frequency'    => 'mensal',
                    'is_active'    => true,
                ]);
            }
        }

        $this->step++;
    }

    private function saveStep3()
    {
        if ($this->workspaceName) {
            $this->validate([
                'workspaceName' => 'required|string|max:60',
            ]);

            auth()->user()->currentWorkspace?->update([
                'name' => $this->workspaceName,
            ]);
        }

        $this->step++;
    }

    private function saveStep4()
    {
        if ($this->categoryName) {
            $this->validate([
                'categoryName'  => 'required|string|max:50',
                'categoryColor' => 'required|string|max:9',
            ]);

            Category::create([
                'user_id'      => auth()->id(),
                'workspace_id' => auth()->user()->current_workspace_id, // <-- recomendado adicionar (ver nota anterior)
                'name'         => $this->categoryName,
                'color'        => $this->categoryColor,
            ]);
        }

        $this->step++;
    }

    public function skipStep()
    {
        $this->step++;
    }

    public function completeOnboarding()
    {
        auth()->user()->update(['onboarding_completed' => true]);
        $this->show = false; // <-- NOVO
    }

    public function render()
    {
        return view('livewire.onboarding-wizard');
    }
}
