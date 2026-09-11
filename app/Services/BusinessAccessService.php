<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\AuthorizationException;

class BusinessAccessService
{
    public const ROLES = ['owner', 'admin', 'manager', 'accountant', 'employee', 'viewer'];

    public function workspace(?User $user = null): ?Workspace
    {
        $user ??= auth()->user();
        if (! $user?->current_workspace_id) return null;

        $workspace = $user->workspaces()->whereKey($user->current_workspace_id)->first();
        return $workspace && in_array($workspace->type, ['business', 'company'], true) ? $workspace : null;
    }

    public function role(?User $user = null, ?Workspace $workspace = null): string
    {
        $user ??= auth()->user();
        $workspace ??= $this->workspace($user);
        if (! $user || ! $workspace) return 'viewer';
        if ((int) $workspace->owner_id === (int) $user->id) return 'owner';

        $pivotRole = $workspace->users()->whereKey($user->id)->first()?->pivot?->role;
        return match (strtolower((string) $pivotRole)) {
            'admin' => 'admin', 'manager', 'editor' => 'manager', 'accountant' => 'accountant',
            'employee', 'member' => 'employee', 'viewer' => 'viewer', default => 'viewer',
        };
    }

    public function can(string $permission, ?User $user = null, ?Workspace $workspace = null): bool
    {
        $role = $this->role($user, $workspace);

        return match ($permission) {
            'view_business' => in_array($role, self::ROLES, true),
            'view_financials' => in_array($role, ['owner', 'admin', 'manager', 'accountant', 'viewer'], true),
            'view_bank_accounts' => in_array($role, ['owner', 'admin', 'accountant'], true),
            'create_expense' => in_array($role, ['owner', 'admin', 'manager', 'accountant', 'employee'], true),
            'manage_financials' => in_array($role, ['owner', 'admin', 'manager', 'accountant'], true),
            'delete_financials' => in_array($role, ['owner', 'admin', 'accountant'], true),
            'manage_clients_suppliers' => in_array($role, ['owner', 'admin', 'manager', 'accountant'], true),
            'manage_team' => in_array($role, ['owner', 'admin', 'manager'], true),
            'view_payroll' => in_array($role, ['owner', 'admin', 'accountant'], true),
            'manage_payroll' => in_array($role, ['owner', 'admin', 'accountant'], true),
            'manage_settings' => in_array($role, ['owner', 'admin'], true),
            'export_reports' => in_array($role, ['owner', 'admin', 'manager', 'accountant'], true),
            'view_audit' => in_array($role, ['owner', 'admin', 'manager', 'accountant'], true),
            'approve_expenses' => in_array($role, ['owner', 'admin', 'manager', 'accountant'], true),
            'manage_budget' => in_array($role, ['owner', 'admin', 'manager', 'accountant'], true),
            default => false,
        };
    }

    public function assert(string $permission, ?User $user = null, ?Workspace $workspace = null): void
    {
        if (! $this->can($permission, $user, $workspace)) {
            throw new AuthorizationException('Não tens permissão para executar esta ação na empresa.');
        }
    }

    public function assertWorkspace(?User $user = null): Workspace
    {
        $workspace = $this->workspace($user);
        abort_unless($workspace, 403, 'Workspace empresarial inválido.');
        return $workspace;
    }
}
