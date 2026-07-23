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
     public $moduleRestrictions = [];
    public $workspaceName;
       public $selectedCategories = [];
    public $restrictBusiness = false;
public $restrictStore = false;
public $allowanceFrequency = 'monthly'; // Adicionar esta
public $spendingLimit = '';
public $restrictFitness = false;
public $restrictBudget = false;
public $restrictImport = false;
public $restrictIncomes = false;
public $restrictDebts = false;
public $restrictInvestments = false;
public $restrictSubs = false;
public $restrictBank = false;
public $restrictCalendar = false;
public $restrictReminders = false;
public $restrictGoals = false;
public $restrictWrapped = false;

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

    $pw = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();
    $this->personalWorkspaceName = $pw ? $pw->name : '';

    // LÓGICA FINAL: Só é Admin se for o Dono do Workspace atual
    // OU se o seu cargo na tabela de ligação for 'admin'
    $role = DB::table('workspace_user')
        ->where('workspace_id', $workspace->id)
        ->where('user_id', $user->id)
        ->value('role');

    $this->iAmAdmin = ($workspace->owner_id === $user->id) || ($role === 'admin');
}
  public function updatedPhoto()
{
    $workspace = auth()->user()->currentWorkspace;
    if (auth()->id() !== $workspace->owner_id) return;

    $this->validate(['photo' => 'image|max:2048']);

    // store() devolve o caminho relativo: "workspaces/logos/nome.jpg"
    $path = $this->photo->store('workspaces/logos', 'public');

    // Guardamos o caminho completo para o browser ler
    $workspace->update([
        'logo_path' => 'storage/' . $path
    ]);

    $this->photo = null;
    $this->dispatch('toast', text: 'Foto da gestão atualizada! ✅');
}
private function resetRestrictions()
{
    $this->reset([
        'restrictBusiness', 'restrictStore', 'restrictFitness', 'restrictBudget',
        'restrictImport', 'restrictIncomes', 'restrictInvestments', 'restrictSubs',
        'restrictBank', 'restrictCalendar', 'restrictReminders', 'restrictGoals', 'restrictWrapped'
    ]);
}
public function updatePrivileges()
{
    if (!$this->permUserId) {
        $this->dispatch('toast', variant: 'error', text: 'Selecione um utilizador primeiro.');
        return;
    }

    $workspaceId = auth()->user()->current_workspace_id;

    // Procuramos o registo global (onde category_id é null)
    $perm = \App\Models\FamilyBudgetPermission::firstOrNew([
        'workspace_id' => $workspaceId,
        'user_id' => $this->permUserId,
        'category_id' => null,
    ]);

    // Atualizamos APENAS os campos de restrição, preservando a mesada
    $perm->fill([
        'restrict_business' => $this->restrictBusiness,
        'restrict_store' => $this->restrictStore,
        'restrict_fitness' => $this->restrictFitness,
        'restrict_budget' => $this->restrictBudget,
        'restrict_import' => $this->restrictImport,
        'restrict_incomes' => $this->restrictIncomes,
        'restrict_investments' => $this->restrictInvestments,
        'restrict_subs' => $this->restrictSubs,
        'restrict_bank' => $this->restrictBank,
        'restrict_calendar' => $this->restrictCalendar,
        'restrict_reminders' => $this->restrictReminders,
        'restrict_goals' => $this->restrictGoals,
        'restrict_wrapped' => $this->restrictWrapped,
    ])->save();

    // Sincronizar Categorias Bloqueadas
    \App\Models\FamilyBudgetPermission::where('workspace_id', $workspaceId)
        ->where('user_id', $this->permUserId)
        ->whereNotNull('category_id')
        ->delete();

    foreach ($this->selectedCategories as $catId) {
        \App\Models\FamilyBudgetPermission::create([
            'workspace_id' => $workspaceId,
            'user_id' => $this->permUserId,
            'category_id' => $catId,
        ]);
    }

    $this->dispatch('toast', text: 'Privilégios guardados com sucesso! 🔐');
}
public function updatedPermUserId($id)
{
    // Se tirar a seleção do utilizador, limpa tudo
    if (!$id) {
        $this->resetRestrictions();
        $this->selectedCategories = [];
        return;
    }

    $workspaceId = auth()->user()->current_workspace_id;

    // 1. CARREGAR AS CATEGORIAS BLOQUEADAS (Para as checkboxes ficarem marcadas)
    $this->selectedCategories = \App\Models\FamilyBudgetPermission::where('user_id', $id)
        ->where('workspace_id', $workspaceId)
        ->whereNotNull('category_id')
        ->pluck('category_id')
        ->map(fn($cid) => (string)$cid) // Converter para string para compatibilidade com o HTML
        ->toArray();

    // 2. CARREGAR AS RESTRIÇÕES GLOBAIS
    $perm = \App\Models\FamilyBudgetPermission::where('user_id', $id)
        ->where('workspace_id', $workspaceId)
        ->whereNull('category_id')
        ->first();

    if ($perm) {
        $this->restrictBusiness    = (bool) $perm->restrict_business;
        $this->restrictStore       = (bool) $perm->restrict_store;
        $this->restrictFitness     = (bool) $perm->restrict_fitness;
        $this->restrictBudget      = (bool) $perm->restrict_budget;
        $this->restrictImport      = (bool) $perm->restrict_import;
        $this->restrictIncomes     = (bool) $perm->restrict_incomes;
        $this->restrictDebts       = (bool) $perm->restrict_debts;
        $this->restrictInvestments = (bool) $perm->restrict_investments;
        $this->restrictSubs        = (bool) $perm->restrict_subs;
        $this->restrictNetworth    = (bool) $perm->restrict_networth;
        $this->restrictBank        = (bool) $perm->restrict_bank;
        $this->restrictCalendar    = (bool) $perm->restrict_calendar;
        $this->restrictReminders   = (bool) $perm->restrict_reminders;
        $this->restrictGoals       = (bool) $perm->restrict_goals;
        $this->restrictWrapped     = (bool) $perm->restrict_wrapped;
    } else {
        $this->resetRestrictions();
    }
}
public function updatedPersonalPhoto()
{
    $this->validate(['personalPhoto' => 'image|max:2048']);
    $user = auth()->user();
    $pw = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();

    if ($pw) {
        $path = $this->personalPhoto->store('workspaces/logos', 'public');

        // Uniformizado: Guardar com "storage/" no início
        $pw->update([
            'logo_path' => 'storage/' . $path
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
        'allowanceUserId' => 'required',
        'allowanceLimit' => 'nullable|numeric',
        'spendingLimit' => 'nullable|numeric',
        'allowanceFrequency' => 'required'
    ]);

    $perm = \App\Models\FamilyBudgetPermission::firstOrNew([
        'workspace_id' => auth()->user()->current_workspace_id,
        'user_id' => $this->allowanceUserId,
        'category_id' => null,
    ]);

    $perm->allowance_limit = $this->allowanceLimit ?: 0;
    $perm->spending_limit = $this->spendingLimit ?: 0;
    $perm->allowance_frequency = $this->allowanceFrequency;
    $perm->save();

    // Importante: NÃO fazer reset aqui para o utilizador ver o que acabou de salvar
    $this->dispatch('toast', text: 'Configuração Guardada! 💰');
}
public function updatedAllowanceUserId($id)
{
    if (!$id) {
        $this->reset(['allowanceLimit', 'spendingLimit', 'allowanceFrequency']);
        return;
    }

    $perm = \App\Models\FamilyBudgetPermission::where('user_id', $id)
        ->where('workspace_id', auth()->user()->current_workspace_id)
        ->whereNull('category_id')
        ->first();

    if ($perm) {
        // Atribuir valores existentes para aparecerem nos inputs do form
        $this->allowanceLimit = $perm->allowance_limit;
        $this->spendingLimit = $perm->spending_limit;
        $this->allowanceFrequency = $perm->allowance_frequency ?? 'monthly';
    } else {
        $this->reset(['allowanceLimit', 'spendingLimit', 'allowanceFrequency']);
    }
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
public function createPersonalSpace()
{
    $user = auth()->user();

    // 1. Criar o Workspace
    $ws = \App\Models\Workspace::create([
        'name' => 'Cofre de ' . explode(' ', $user->name)[0],
        'type' => 'personal',
        'owner_id' => $user->id,
        'invite_code' => strtoupper(\Illuminate\Support\Str::random(8)),
    ]);

    // 2. Ligar o utilizador como Admin
    $user->workspaces()->attach($ws->id, ['role' => 'admin']);

    // 3. CRIAR CATEGORIAS PADRÃO (O que faltava)
    $defaults = [
        ['name' => 'Carro', 'icon' => 'truck', 'color' => '#f59e0b'],
        ['name' => 'Casa', 'icon' => 'home', 'color' => '#10b981'],
        ['name' => 'Alimentação', 'icon' => 'shopping-cart', 'color' => '#ef4444'],
        ['name' => 'Saúde', 'icon' => 'heart', 'color' => '#ec4899'],
        ['name' => 'Tecnologia', 'icon' => 'cpu-chip', 'color' => '#3b82f6'],
        ['name' => 'Lazer', 'icon' => 'film', 'color' => '#8b5cf6'],
    ];

    foreach ($defaults as $cat) {
        \App\Models\Category::create([
            'workspace_id' => $ws->id,
            'user_id' => $user->id,
            'name' => $cat['name'],
            'slug' => \Illuminate\Support\Str::slug($cat['name']),
            'icon' => $cat['icon'],
            'color' => $cat['color'],
        ]);
    }

    $this->dispatch('toast', text: 'Cofre e Categorias ativadas! 🔒');
    return redirect()->route('hub.family.manage');
}

    public function render()
{
    $user = auth()->user();
    $currentWs = $user->currentWorkspace;
    $startOfMonth = now()->startOfMonth();
    $workspaceId = $currentWs->id;

    // 1. Identificar espaços para o Widget e Navegação
    $personalWs = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();
    $sharedWorkspaces = $user->workspaces()
        ->where('type', '!=', 'business')
        ->where('workspaces.id', '!=', ($personalWs->id ?? 0))
        ->get();

    $isAtPrivate = $personalWs && $currentWs->id === $personalWs->id;

    // 2. Membros e estatísticas
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

    // 5. LÓGICA DA SIDEBAR PARA CATEGORIAS
    $exclude = ['Streaming (Vídeo/TV)', 'Música & Podcasts', 'Software & SaaS', 'Gaming', 'Fitness & Ginásio', 'Cloud & Armazenamento', 'Notícias & Revistas', 'Educação & Cursos', 'VPN & Segurança', 'Seguros & Finanças', 'Serviços Casa (Net/TV)', 'Outros'];

    $sidebarCategories = Category::where('workspace_id', $workspaceId)
        ->whereNotIn('name', $exclude)
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->orderBy('order', 'asc')
        ->orderBy('name', 'asc')
        ->get();

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
        'categories' => $sidebarCategories,
        // --- CORREÇÃO AQUI ---
        'allowances' => FamilyBudgetPermission::where('workspace_id', $workspaceId)
            ->whereNull('category_id') // Puxar apenas registos globais
            ->where(function($q) {
                $q->where('allowance_limit', '>', 0)
                  ->orWhere('spending_limit', '>', 0); // Puxa se tiver limite também
            })
            ->with('user')
            ->get(),
        // -----------------------
    ]);
}
}
