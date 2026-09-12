<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = 'all';

    public $filterRole = 'all';

    public $filterDate = 'all';

    public $orderBy = 'created_at|desc';

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

    private function requireAdministrator(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showUserDetails($userId)
    {
        $user = User::findOrFail($userId);
        $this->selectedUser = $user;

        $plan = DB::table('workspaces')
            ->join('workspace_user', 'workspaces.id', '=', 'workspace_user.workspace_id')
            ->where('workspace_user.user_id', $userId)
            ->value('plan') ?? 'free';

        $this->userStats = [
            'plan' => $plan,
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
                ->get(),
        ];
    }

    public function closeDetails()
    {
        $this->selectedUser = null;
    }

    public function toggleActive($userId)
    {
        $this->requireAdministrator();
        if ($userId === auth()->id()) {
            return;
        }
        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);
        $msg = $user->is_active ? "Conta de {$user->name} ativada." : "Conta de {$user->name} bloqueada.";
        $this->dispatch('toast', text: $msg);
    }

    public function verifyEmailManually($userId)
    {
        $this->requireAdministrator();
        $user = User::findOrFail($userId);
        $user->markEmailAsVerified();
        auth()->user()->logActivity("Verificou manualmente o email de {$user->name}", 'seguranca');
        $this->dispatch('toast', text: "Email de {$user->name} marcado como verificado!");
    }

    public function resetPassword($userId)
    {
        $this->requireAdministrator();
        $user = User::findOrFail($userId);
        $temporaryPassword = bin2hex(random_bytes(16));
        $user->update(['password' => Hash::make($temporaryPassword)]);
        auth()->user()->logActivity("Redefiniu a password de {$user->name}", 'seguranca');
        $this->dispatch('toast', text: "Password temporária para {$user->name}: {$temporaryPassword}");
    }

    public function forceLogout($userId)
    {
        $this->requireAdministrator();
        $user = User::findOrFail($userId);
        $user->update(['remember_token' => null]);
        auth()->user()->logActivity("Expulsou {$user->name} das sessões ativas", 'seguranca');
        $this->dispatch('toast', text: "Sessões de dispositivo invalidadas para {$user->name}.");
    }

    public function deleteUser($userId)
    {
        $this->requireAdministrator();
        if ($userId === auth()->id()) {
            return;
        }
        $user = User::findOrFail($userId);
        $name = $user->name;
        $user->delete();
        auth()->user()->logActivity("Eliminou permanentemente a conta de {$name}", 'seguranca');
        $this->dispatch('toast', text: "Utilizador {$name} removido do sistema.", variant: 'warning');
        $this->selectedUser = null;
    }

    public function openRoleModal($userId)
    {
        $this->requireAdministrator();
        $user = User::findOrFail($userId);
        $this->userToEditRole = $user;
        $this->newRole = $user->role;
        $this->adminPassword = '';
        $this->dispatch('modal-show', name: 'change-role-modal');
    }

    public function updateRole()
    {
        $this->requireAdministrator();
        $this->validate([
            'newRole' => 'required|in:user,analyst,moderator,admin',
            'adminPassword' => 'required',
        ]);

        if (! Hash::check($this->adminPassword, auth()->user()->password)) {
            $this->addError('adminPassword', 'Password de administrador incorreta.');

            return;
        }

        $user = User::find($this->userToEditRole->id);
        abort_unless($user, 404);
        if ($user->id === auth()->id() && $this->newRole !== 'admin') {
            $this->addError('newRole', 'Não podes remover o teu próprio cargo de administrador.');

            return;
        }

        $user->role = $this->newRole;
        $user->is_admin = $this->newRole === 'admin';
        $user->save();
        auth()->user()->logActivity("Alterou cargo de {$user->name} para {$this->newRole}", 'seguranca');
        $this->dispatch('modal-close', name: 'change-role-modal');
        $this->dispatch('toast', text: 'Cargo atualizado!');
        $this->reset(['adminPassword', 'userToEditRole', 'newRole']);
    }

    public function render()
    {
        $query = User::query();
        $query->addSelect([
            'current_plan' => DB::table('workspaces')
                ->join('workspace_user', 'workspaces.id', '=', 'workspace_user.workspace_id')
                ->whereColumn('workspace_user.user_id', 'users.id')
                ->select('plan')
                ->limit(1),
        ]);

        if ($this->search) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"));
        }
        if ($this->filterStatus !== 'all') {
            $query->where('is_active', $this->filterStatus === 'active');
        }
        if ($this->filterRole !== 'all') {
            $query->where('role', $this->filterRole);
        }

        $sort = explode('|', $this->orderBy);
        $allowedSorts = ['created_at', 'name', 'email', 'role', 'is_active'];
        $sortColumn = in_array($sort[0] ?? '', $allowedSorts, true) ? $sort[0] : 'created_at';
        $sortDirection = ($sort[1] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        return view('livewire.admin.user-management', [
            'users' => $query->paginate(12),
        ]);
    }
}
