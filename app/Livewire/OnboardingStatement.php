<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;

class OnboardingStatement extends StatementImportHub
{
    use AuthorizesOnboardingWorkspace;

    public bool $showImportForm = false;

    public function mount(): void
    {
        $this->initializeOnboardingWorkspace();
    }

    public function openImportForm(): void
    {
        $this->authorizeOnboardingWrite();
        $this->showImportForm = true;
        $this->dispatch('onboarding-statement-import-opened');
    }

    public function updatedStatementFile(): void
    {
        $this->clearPreview();
    }

    public function generatePreview(): void
    {
        $this->authorizeOnboardingWrite();
        parent::generatePreview();
    }

    public function importStatement(): void
    {
        $this->authorizeOnboardingWrite();
        parent::importStatement();
    }

    public function render()
    {
        if ($this->showImportForm) {
            $this->authorizeOnboardingWrite();
        }

        $data = $this->showImportForm ? parent::render()->getData() : [];

        return view('livewire.onboarding-statement', compact('data'));
    }
}
