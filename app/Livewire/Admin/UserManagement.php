<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    // Filtros e Pesquisa
    public $search = '';
    public $filterStatus = 'all';
    public $filterRole = 'all';
    public $filterDate = 'all';
    public $orderBy = 'created_at|desc';

    // Estados do Modal
    public $selectedUser = null;
    public $userStats = [];
    public $userToEditRole = null;
    public $newRole = '';
    public $adminPassword = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
        'filterRole' => ['except' => 'all'],
        'orderBy' => ['except' => 'created_at|desc'],
    ];

    public function updatingSearch() { $this->resetPage(); }

    /**
     * Carrega o dossiê completo do utilizador com dados de plano e uso
     */
    public function showUserDetails($userId)
    {
        $user = User::findOrFail($userId);
        $this->selectedUser = $user;

        // Puxamos o plano do Workspace principal deste utilizador
        $plan = DB::table('workspaces')
            ->join('workspace_user', 'workspaces.id', '=', 'workspace_user.workspace_id')
            ->where('workspace_user.user_id', $userId)
            ->value('plan') ?? 'free';

        $this->userStats = [
            'plan' => $plan, // Novo dado: premium, business ou free
            'expenses_count' => DB::table('expenses')->where('user_id', $userId)->count(),
            'expenses_sum' => DB::table('expenses')->where('user_id', $userId)->sum('amount'),
            'incomes_sum' => DB::table('incomes')->where('user_id', $userId)->sum('amount'),
            'goals_count' => DB::table('goals')->where('user_id', $userId)->count(),
            'ai_messages' => DB::table('chat_messages')->where('user_id', $userId)->count(),
            'reminders' => DB::table('reminders')->where('user_id', $userId)->count(),
            'workspaces' => DB::table('workspace_user')->where('user_id', $userId)->count(),

            'page_views' => DB::table('activity_logs')
                ->where('user_id', $userId)
                ->where('action', 'like', 'Acedeu a%')
                ->select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->limit(4)
                ->get()
        ];
    }

    public function closeDetails() { $this->selectedUser = null; }

    /**
     * AÇÕES DE SEGURANÇA E CONTA
     */

    public function toggleActive($userId)
    {
        if ($userId === auth()->id()) return;
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);

        $msg = $user->is_active ? "Conta de {$user->name} ativada." : "Conta de {$user->name} bloqueada.";
        $this->dispatch('toast', text: $msg);
    }

    public function verifyEmailManually($userId)
    {
        $user = User::findOrFail($userId);
        $user->markEmailAsVerified();

        auth()->user()->logActivity("Verificou manualmente o email de {$user->name}", "seguranca");
        $this->dispatch('toast', text: "Email de {$user->name} marcado como verificado!");
    }

    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['password' => Hash::make('password123')]);

        auth()->user()->logActivity("Redefiniu a password de {$user->name}", "seguranca");
        $this->dispatch('toast', text: "Password de {$user->name} resetada para 'password123'.");
    }

    public function forceLogout($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['remember_token' => null]);

        auth()->user()->logActivity("Expulsou {$user->name} das sessões ativas", "seguranca");
        $this->dispatch('toast', text: "Sessões de dispositivo invalidadas para {$user->name}.");
    }

    public function deleteUser($userId)
    {
        if ($userId === auth()->id()) return;
        $user = User::findOrFail($userId);
        $name = $user->name;
        $user->delete();

        auth()->user()->logActivity("Eliminou permanentemente a conta de {$name}", "seguranca");
        $this->dispatch('toast', text: "Utilizador {$name} removido do sistema.");
        $this->selectedUser = null;
    }

    /**
     * GESTÃO DE NÍVEIS (ROLES)
     */
    public function openRoleModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->userToEditRole = $user;
        $this->newRole = $user->role;
        $this->adminPassword = '';
        $this->dispatch('modal-show', name: 'change-role-modal');
    }

    public function updateRole()
    {
        $this->validate([
            'newRole' => 'required|in:user,analyst,moderator,admin',
            'adminPassword' => 'required',
        ]);

        if (!Hash::check($this->adminPassword, auth()->user()->password)) {
            $this->addError('adminPassword', 'Password de administrador incorreta.');
            return;
        }

        $user = User::find($this->userToEditRole->id);
        $user->role = $this->newRole;
        $user->is_admin = in_array($this->newRole, ['admin', 'moderator', 'analyst']);
        $user->save();

        auth()->user()->logActivity("Alterou cargo de {$user->name} para {$this->newRole}", "seguranca");

        $this->dispatch('modal-close', name: 'change-role-modal');
        $this->dispatch('toast', text: "Cargo atualizado!");
        $this->reset(['adminPassword', 'userToEditRole', 'newRole']);
    }

    public function render()
    {
        $query = User::query();

        // ACRESCENTADO: Subquery para trazer o Plano atual da tabela Workspaces para a tabela principal
        $query->addSelect([
            'current_plan' => DB::table('workspaces')
                ->join('workspace_user', 'workspaces.id', '=', 'workspace_user.workspace_id')
                ->whereColumn('workspace_user.user_id', 'users.id')
                ->select('plan')
                ->limit(1)
        ]);

        if ($this->search) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"));
        }

        if ($this->filterStatus !== 'all') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        if ($this->filterRole !== 'all') {
            $query->where('role', $this->filterRole);
        }

        $sort = explode('|', $this->orderBy);
        $query->orderBy($sort[0], $sort[1]);

        return view('livewire.admin.user-management', [
            'users' => $query->paginate(12)
        ]);
    }
}
