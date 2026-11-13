<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;

class OnboardingInvestmentImport extends XtbInvestmentImportHub
{
    use AuthorizesOnboardingWorkspace;

    public ?string $onboardingImportStatus = null;

    public function mount(): void
    {
        $this->initializeOnboardingWorkspace();
    }

    public function updatedReportFile(): void
    {
        $this->reset(['positions', 'reportSummary', 'reportDate', 'aiSummary', 'previewReady', 'onboardingImportStatus']);
        $this->resetValidation('reportFile');
    }

    public function updatedAforroNetFile(): void
    {
        $this->reset(['aforroNetSubscriptions', 'aforroNetPosition', 'aforroNetPreviewReady', 'aforroNetDiagnostic', 'onboardingImportStatus']);
        $this->resetValidation('aforroNetFile');
    }

    public function analyzeReport(): void
    {
        $this->authorizeOnboardingWrite();
        $this->updatedReportFile();
        parent::analyzeReport();
    }

    public function analyzeAforroNetFile(): void
    {
        $this->authorizeOnboardingWrite();
        $this->updatedAforroNetFile();
        parent::analyzeAforroNetFile();
    }

    public function importPositions(): void
    {
        $this->authorizeOnboardingWrite();
        $hadPreview = $this->previewReady && ! empty($this->positions);
        parent::importPositions();
        if ($hadPreview && ! $this->previewReady) {
            $this->onboardingImportStatus = 'Posições XTB importadas. Podes fechar esta janela e continuar.';
            $this->dispatch('onboarding-xtb-imported')->to(OnboardingInvestment::class);
        }
    }

    public function importAforroNetPosition(): void
    {
        $this->authorizeOnboardingWrite();
        $hadPreview = $this->aforroNetPreviewReady && ! empty($this->aforroNetSubscriptions) && ! empty($this->aforroNetPosition);
        parent::importAforroNetPosition();
        if ($hadPreview && ! $this->aforroNetPreviewReady) {
            $this->onboardingImportStatus = 'Subscrições AforroNet importadas. Podes fechar esta janela e continuar.';
        }
    }

    public function render()
    {
        $this->authorizeOnboardingWrite();

        return parent::render()->with('onboardingModal', true);
    }
}
