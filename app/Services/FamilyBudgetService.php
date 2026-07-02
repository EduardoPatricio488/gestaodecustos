<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\FamilyBudgetPermission;
use App\Models\User;
use App\Models\Workspace;

class FamilyBudgetService
{
    public function canViewCategory(User $user, Workspace $workspace, ?int $categoryId): bool
    {
        if ($user->is_admin || $this->isWorkspaceAdmin($user, $workspace)) {
            return true;
        }

        $perm = FamilyBudgetPermission::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $perm) {
            return true;
        }

        if ($perm->can_view_all) {
            return true;
        }

        if ($categoryId === null) {
            return false;
        }

        return FamilyBudgetPermission::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->exists();
    }

    public function canEdit(User $user, Workspace $workspace): bool
    {
        if ($user->is_admin || $this->isWorkspaceAdmin($user, $workspace)) {
            return true;
        }

        return FamilyBudgetPermission::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('can_edit', true)
            ->exists();
    }

    public function allowanceRemaining(User $user, Workspace $workspace): ?float
    {
        $perm = FamilyBudgetPermission::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->whereNotNull('allowance_limit')
            ->whereNull('category_id')
            ->first();

        if (! $perm) {
            return null;
        }

        $spent = (float) Expense::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('spent_at', '>=', now()->startOfMonth())
            ->sum('amount');

        return max(0, (float) $perm->allowance_limit - $spent);
    }

    public function setCategoryAccess(Workspace $workspace, int $userId, int $categoryId, bool $canView): void
    {
        if ($canView) {
            FamilyBudgetPermission::updateOrCreate(
                ['workspace_id' => $workspace->id, 'user_id' => $userId, 'category_id' => $categoryId],
                ['can_view_all' => false, 'can_edit' => false]
            );
        } else {
            FamilyBudgetPermission::where('workspace_id', $workspace->id)
                ->where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->delete();
        }
    }

    public function getMemberPermissions(Workspace $workspace, int $userId): array
    {
        return FamilyBudgetPermission::where('workspace_id', $workspace->id)
            ->where('user_id', $userId)
            ->with('category:id,name')
            ->get()
            ->toArray();
    }

    private function isWorkspaceAdmin(User $user, Workspace $workspace): bool
    {
        return $user->workspaces()
            ->where('workspaces.id', $workspace->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }
}
