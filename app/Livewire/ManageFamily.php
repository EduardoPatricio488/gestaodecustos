<?php

namespace App\Livewire;

use App\Models\{User, Workspace, ActivityLog, FamilyBudgetPermission, Goal, Category};
use App\Services\FamilyBudgetService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\{DB, Auth};

use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ManageFamily extends Component
{
     use WithFileUploads;
    public $workspaceName;
    public $personalPhoto;
     public $photo;          // Foto da Esquerda (Maria)
    public $inviteCode;

    public $personalWorkspaceName;
    public $iAmAdmin;
    public $inviteCodeInput = '';
    public $allowanceUserId = null;
    public $allowanceLimit = '';
    public $permUserId = null;
    public $permCategoryId = null;

    public function mount()
{
    $user = auth()->user();
    $workspace = $user->currentWorkspace;

    if ($workspace) {
        $this->workspaceName = $workspace->name;
        $this->inviteCode = $workspace->invite_code;
    }

    // Busca o nome do cofre pessoal especificamente
    $pw = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();
    $this->personalWorkspaceName = $pw ? $pw->name : '';

    $this->iAmAdmin = $user->isAdminRole() || $user->isOwner();
}
  public function updatedPhoto()
{
    $workspace = auth()->user()->currentWorkspace;
    if (auth()->id() !== $workspace->owner_id) return;

    $this->validate(['photo' => 'image|max:2048']);
    $path = $this->photo->store('workspaces/logos', 'public');

    // MUDANÇA AQUI: de logo_url para logo_path
    $workspace->update([
        'logo_path' => 'storage/' . $path
    ]);

    $this->photo = null;
    $this->dispatch('toast', text: 'Foto da gestão atualizada! ✅');
}

public function updatedPersonalPhoto()
{
    $this->validate(['personalPhoto' => 'image|max:2048']);
    $user = auth()->user();
    $pw = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();

    if ($pw) {
        // store() devolve "workspaces/logos/nome.jpg"
        $path = $this->personalPhoto->store('workspaces/logos', 'public');

        // GUARDAR APENAS O $path. O Model Workspace já trata do resto.
        $pw->update([
            'logo_path' => $path
        ]);

        $this->personalPhoto = null;
        $this->dispatch('toast', text: 'Foto pessoal atualizada! 🔒');
    }
}
public function updatePersonalName()
{
    $user = auth()->user();
    $pw = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();

    if ($pw) {
        $pw->update(['name' => $this->personalWorkspaceName]);
        $this->dispatch('toast', text: 'Nome da tua gestão pessoal atualizado!');

        // Se estiveres atualmente no cofre pessoal, atualiza a variável geral também
        if ($user->current_workspace_id === $pw->id) {
            $this->workspaceName = $this->personalWorkspaceName;
        }
    }
}
    public function updateWorkspaceName()
    {
        if (!$this->iAmAdmin) return;
        $this->validate(['workspaceName' => 'required|string|min:3|max:50']);
        auth()->user()->currentWorkspace->update(['name' => $this->workspaceName]);
        $this->dispatch('toast', text: 'Identidade atualizada!');
    }

    public function setIndividualName()
    {
        $this->workspaceName = "Conta Individual de " . explode(' ', auth()->user()->name)[0];
    }

    public function setFamilyName()
    {
        $this->workspaceName = "Gestão da Família " . collect(explode(' ', auth()->user()->name))->last();
    }

    public function setCategoryPermission(): void
    {
        if (!$this->iAmAdmin) return;

        $this->validate([
            'permUserId' => 'required|exists:users,id',
            'permCategoryId' => 'required|exists:categories,id',
        ]);

        app(FamilyBudgetService::class)->setCategoryAccess(
            auth()->user()->currentWorkspace,
            $this->permUserId,
            $this->permCategoryId,
            true
        );

        $this->reset(['permUserId', 'permCategoryId']);
        $this->dispatch('toast', text: 'Permissão configurada!');
    }

    public function setAllowance(): void
    {
        if (!$this->iAmAdmin) return;

        $this->validate([
            'allowanceUserId' => 'required|exists:users,id',
            'allowanceLimit' => 'required|numeric|min:0',
        ]);

        FamilyBudgetPermission::updateOrCreate(
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'user_id' => $this->allowanceUserId,
                'category_id' => null,
            ],
            ['allowance_limit' => $this->allowanceLimit, 'can_view_all' => false, 'can_edit' => true]
        );

        $this->reset(['allowanceUserId', 'allowanceLimit']);
        $this->dispatch('toast', text: 'Mesada digital configurada!');
    }

    public function updateRole($userId, $newRole)
    {
        if (!$this->iAmAdmin) return;
        if ($userId === auth()->id()) return;

        DB::table('workspace_user')
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', $userId)
            ->update(['role' => $newRole]);

        $this->dispatch('toast', text: 'Permissão atualizada!');
    }

    public function removeMember($userId)
    {
        if (!$this->iAmAdmin || $userId === auth()->id()) return;
        auth()->user()->currentWorkspace->users()->detach($userId);
        $this->dispatch('toast', text: 'Membro removido.');
    }

    public function generateInviteCode()
    {
        if (!$this->iAmAdmin) return;
        $workspace = auth()->user()->currentWorkspace;
        $workspace->update(['invite_code' => strtoupper(\Illuminate\Support\Str::random(8))]);
        $this->inviteCode = $workspace->invite_code;
        $this->dispatch('toast', text: 'Novo código gerado!');
    }

    public function joinWorkspace()
    {
        $this->validate(['inviteCodeInput' => 'required|string|exists:workspaces,invite_code']);
        $workspace = Workspace::where('invite_code', $this->inviteCodeInput)->first();

        if ($workspace->users()->where('user_id', auth()->id())->exists()) {
            $this->dispatch('toast', variant: 'error', text: 'Já fazes parte desta conta.');
            return;
        }

        auth()->user()->workspaces()->attach($workspace->id, ['role' => 'member']);
        auth()->user()->update(['current_workspace_id' => $workspace->id]);
        return redirect()->route('dashboard');
    }

    public function render()
    {
        $user = auth()->user();
        $currentWs = $user->currentWorkspace;
        $startOfMonth = now()->startOfMonth();

        // 1. Identificar espaços para o Widget e Navegação
        $personalWs = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();
        $sharedWorkspaces = $user->workspaces()
            ->where('type', '!=', 'business')
            ->where('workspaces.id', '!=', ($personalWs->id ?? 0))
            ->get();

        $isAtPrivate = $personalWs && $currentWs->id === $personalWs->id;

        // 2. Membros e estatísticas (Filtrar para não mostrar funcionários de empresas aqui)
        $membersQuery = $currentWs->users()->withPivot('role');
        if (in_array($currentWs->type, ['business', 'company'])) {
            $membersQuery->wherePivot('role', 'admin');
        }
        $members = $membersQuery->get();
        $memberIds = $members->pluck('id');

        // 3. Auditoria de Rendimento
        $memberStats = $members->map(function ($u) use ($currentWs, $startOfMonth) {
            $u->total_incomes = $u->incomes()->where('workspace_id', $currentWs->id)->where('received_at', '>=', $startOfMonth)->sum('amount') ?: 0;
            $u->total_expenses = $u->expenses()->where('workspace_id', $currentWs->id)->where('spent_at', '>=', $startOfMonth)->sum('amount') ?: 0;
            $u->net_balance = $u->total_incomes - $u->total_expenses;
            return $u;
        });

        // 4. Atividade e Mesadas
        $topRecorders = $currentWs->users()->whereIn('users.id', $memberIds)->withCount(['expenses' => fn($q) => $q->where('workspace_id', $currentWs->id)])->orderBy('expenses_count', 'desc')->take(5)->get();
        $recentActivities = ActivityLog::whereIn('user_id', $memberIds)->latest()->take(10)->get();

        return view('livewire.manage-family', [
            'currentWs' => $currentWs,
            'members' => $members,
            'isAtPrivate' => $isAtPrivate,
            'myPersonalWs' => $personalWs,
            'sharedWorkspaces' => $sharedWorkspaces,
            'memberStats' => $memberStats,
            'topRecorders' => $topRecorders,
            'recentActivities' => $recentActivities,
            'familyGoals' => Goal::where('workspace_id', $currentWs->id)->get(),
            'categories' => Category::where('workspace_id', $currentWs->id)->orderBy('name')->get(),
            'allowances' => FamilyBudgetPermission::where('workspace_id', $currentWs->id)
                ->whereNotNull('allowance_limit')
                ->with('user')
                ->get(),
        ]);
    }
}
