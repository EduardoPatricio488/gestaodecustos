<?php

namespace App\Livewire\Business;

use App\Services\BusinessAccessService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessRolesHub extends Component
{
    public $selectedUserId;
    public $selectedRole = 'employee';

    public array $roles = [
        'admin' => 'Administrador',
        'manager' => 'Manager',
        'accountant' => 'Contabilista',
        'employee' => 'Colaborador',
        'viewer' => 'Leitor',
    ];

    public function mount(): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_team', auth()->user(), $workspace);
    }

    public function selectMember(int $userId): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_team', auth()->user(), $workspace);

        $member = $workspace->users()->whereKey($userId)->firstOrFail();
        abort_unless((int) $workspace->owner_id !== (int) $member->id, 403, 'O proprietário não pode ter o papel alterado.');

        $this->selectedUserId = $member->id;
        $this->selectedRole = app(BusinessAccessService::class)->role($member, $workspace);
        if ($this->selectedRole === 'owner') $this->selectedRole = 'admin';
    }

    public function updateRole(): void
    {
        $access = app(BusinessAccessService::class);
        $workspace = $access->assertWorkspace();
        $actorRole = $access->role(auth()->user(), $workspace);
        $access->assert('manage_team', auth()->user(), $workspace);

        $this->validate(['selectedRole' => 'required|in:admin,manager,accountant,employee,viewer']);
        $member = $workspace->users()->whereKey($this->selectedUserId)->firstOrFail();

        abort_unless((int) $member->id !== (int) auth()->id(), 403, 'Não podes alterar o teu próprio papel nesta área.');
        abort_unless((int) $member->id !== (int) $workspace->owner_id, 403, 'O proprietário não pode ter o papel alterado.');

        if ($this->selectedRole === 'admin' && $actorRole !== 'owner') {
            abort(403, 'Apenas o proprietário pode atribuir Administrador.');
        }

        if ($actorRole === 'manager' && in_array($this->selectedRole, ['admin', 'manager', 'accountant'], true)) {
            abort(403, 'Um Manager só pode gerir papéis de colaborador ou leitor.');
        }

        DB::transaction(function () use ($workspace, $member): void {
            $workspace->users()->updateExistingPivot($member->id, ['role' => $this->selectedRole]);
        });

        $this->dispatch('toast', variant: 'success', text: 'Permissões atualizadas com sucesso.');
    }

    public function render()
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_team', auth()->user(), $workspace);

        return view('livewire.business.business-roles-hub', [
            'workspace' => $workspace,
            'members' => $workspace->users()->orderBy('name')->get(),
            'businessRole' => app(BusinessAccessService::class)->role(auth()->user(), $workspace),
        ]);
    }
}
