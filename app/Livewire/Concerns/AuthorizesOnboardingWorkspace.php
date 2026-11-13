<?php

namespace App\Livewire\Concerns;

use App\Services\BusinessAccessService;
use Livewire\Attributes\Locked;

trait AuthorizesOnboardingWorkspace
{
    #[Locked]
    public ?int $onboardingWorkspaceId = null;

    protected function initializeOnboardingWorkspace(): void
    {
        $this->onboardingWorkspaceId = auth()->user()->current_workspace_id;
        $this->authorizeOnboardingWrite();
    }

    protected function authorizeOnboardingWrite(): void
    {
        $user = auth()->user();
        abort_unless($this->onboardingWorkspaceId && (int) $user->current_workspace_id === $this->onboardingWorkspaceId, 403, 'O espaço financeiro ativo foi alterado. Reabre este passo.');
        $workspace = $user->current_workspace_id
            ? $user->workspaces()->whereKey($user->current_workspace_id)->first()
            : null;

        abort_unless($workspace, 403, 'Não existe um espaço financeiro ativo e acessível.');
        abort_if($user->isViewer(), 403, 'Não tens permissão para guardar neste espaço.');

        if (in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', $user, $workspace);
        }
    }
}
