<?php

namespace App\Traits;

use App\Models\Employee;
use App\Services\BusinessAccessService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

trait BelongsToWorkspace
{
    protected static function bootBelongsToWorkspace(): void
    {
        static::creating(function ($model): void {
            if (! Auth::check()) {
                return;
            }
            $user = Auth::user();
            $workspaceId = $user->current_workspace_id;
            if ($workspaceId && empty($model->workspace_id)) {
                $model->workspace_id = $workspaceId;
            }
            if ($workspaceId && $model->workspace_id && (int) $model->workspace_id !== (int) $workspaceId) {
                throw new RuntimeException('Não é permitido criar dados noutro workspace.');
            }
            static::assertMutationAllowed($model, 'create');
        });

        static::saving(function ($model): void {
            if (! Auth::check() || ! Auth::user()->current_workspace_id) {
                return;
            }
            $workspaceId = (int) Auth::user()->current_workspace_id;
            if ($model->workspace_id && (int) $model->workspace_id !== $workspaceId) {
                throw new RuntimeException('Não é permitido alterar dados de outro workspace.');
            }
            if (empty($model->workspace_id)) {
                $model->workspace_id = $workspaceId;
            }
            static::assertMutationAllowed($model, $model->exists ? 'update' : 'create');
        });

        static::deleting(function ($model): void {
            if (! Auth::check()) {
                return;
            }
            if (Auth::user()->current_workspace_id && (int) $model->workspace_id !== (int) Auth::user()->current_workspace_id) {
                throw new RuntimeException('Não é permitido eliminar dados de outro workspace.');
            }
            static::assertMutationAllowed($model, 'delete');
        });

        static::addGlobalScope('workspace', function (Builder $builder): void {
            if (Auth::check() && Auth::user()->current_workspace_id) {
                $builder->where($builder->getModel()->getTable().'.workspace_id', Auth::user()->current_workspace_id);
            }
        });
    }

    protected static function assertMutationAllowed($model, string $operation): void
    {
        $user = Auth::user();
        $workspace = $user?->currentWorkspace;

        if (! $user || ! $workspace || ! in_array($workspace->type, ['business', 'company'], true)) {
            if (method_exists($user, 'currentRole') && $user->currentRole() === 'viewer') {
                throw new AuthorizationException('Este perfil tem acesso apenas de leitura.');
            }

            return;
        }

        $access = app(BusinessAccessService::class);
        $role = $access->role($user, $workspace);
        if ($role === 'viewer') {
            throw new AuthorizationException('Este perfil tem acesso apenas de leitura.');
        }

        $modelClass = class_basename($model);
        $isOwnExpense = $modelClass === 'Expense' && (int) ($model->user_id ?? 0) === (int) $user->id;
        if ($modelClass === 'Expense' && $role === 'employee' && $isOwnExpense && $operation !== 'delete') {
            return;
        }

        if ($modelClass === 'Absence') {
            $employee = Employee::withoutGlobalScopes()->find($model->employee_id);
            if ($employee && (int) $employee->workspace_id === (int) $workspace->id && (int) $employee->user_id === (int) $user->id && $operation !== 'delete') {
                return;
            }
            $access->assert('manage_team', $user, $workspace);

            return;
        }

        $permission = match ($modelClass) {
            'Employee' => 'manage_team',
            'Expense' => $operation === 'delete' ? 'delete_financials' : 'create_expense',
            'Invoice', 'Client', 'Supplier', 'Project', 'Product', 'Task', 'Proposal', 'Category', 'BankAccount', 'BusinessDocument', 'AtInvoice' => $operation === 'delete' ? 'delete_financials' : 'manage_financials',
            default => 'manage_financials',
        };

        $access->assert($permission, $user, $workspace);
    }
}
