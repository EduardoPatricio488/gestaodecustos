<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;

class OnboardingSubscription extends SubscriptionHub
{
    use AuthorizesOnboardingWorkspace;

    public bool $onboardingSaved = false;

    public function mount(bool $dashboardMode = true): void
    {
        $this->initializeOnboardingWorkspace();
        $this->dashboardMode = true;
    }

    public function openExtraModal()
    {
        $this->authorizeOnboardingWrite();
        $this->onboardingSaved = false;
        parent::openExtraModal();
    }

    public function save()
    {
        $this->authorizeOnboardingWrite();
        $this->editingId = null;
        parent::save();
        $this->onboardingSaved = true;
    }

    public function render()
    {
        if ($this->showExtraModal) {
            $this->authorizeOnboardingWrite();
        }

        $data = $this->showExtraModal ? parent::render()->getData() : [];

        return view('livewire.onboarding-subscription', compact('data'));
    }
}
