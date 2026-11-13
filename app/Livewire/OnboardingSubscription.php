<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;
use App\Models\Subscription;

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
        $savedSubscriptions = Subscription::query()
            ->with(['category:id,name', 'bankAccount:id,name'])
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('livewire.onboarding-subscription', compact('data', 'savedSubscriptions'));
    }
}
