<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use RuntimeException;

trait BelongsToWorkspace
{
    protected static function bootBelongsToWorkspace(): void
    {
        static::creating(function ($model): void {
            if (! Auth::check()) return;
            $workspaceId = Auth::user()->current_workspace_id;
            if ($workspaceId && empty($model->workspace_id)) $model->workspace_id = $workspaceId;
            if ($workspaceId && $model->workspace_id && (int)$model->workspace_id !== (int)$workspaceId) {
                throw new RuntimeException('Não é permitido criar dados noutro workspace.');
            }
            if ($workspaceId && method_exists(Auth::user(), 'currentRole') && Auth::user()->currentRole() === 'viewer') {
                throw new AuthorizationException('Este perfil tem acesso apenas de leitura.');
            }
        });

        static::saving(function ($model): void {
            if (! Auth::check() || ! Auth::user()->current_workspace_id) return;
            $workspaceId = (int) Auth::user()->current_workspace_id;
            if ($model->workspace_id && (int)$model->workspace_id !== $workspaceId) {
                throw new RuntimeException('Não é permitido alterar dados de outro workspace.');
            }
            if (empty($model->workspace_id)) $model->workspace_id = $workspaceId;
            if (method_exists(Auth::user(), 'currentRole') && Auth::user()->currentRole() === 'viewer') {
                throw new AuthorizationException('Este perfil tem acesso apenas de leitura.');
            }
        });

        static::deleting(function ($model): void {
            if (Auth::check() && method_exists(Auth::user(), 'currentRole') && Auth::user()->currentRole() === 'viewer') {
                throw new AuthorizationException('Este perfil tem acesso apenas de leitura.');
            }
            if (Auth::check() && Auth::user()->current_workspace_id && (int)$model->workspace_id !== (int)Auth::user()->current_workspace_id) {
                throw new RuntimeException('Não é permitido eliminar dados de outro workspace.');
            }
        });

        static::addGlobalScope('workspace', function (Builder $builder): void {
            if (Auth::check() && Auth::user()->current_workspace_id) {
                $builder->where($builder->getModel()->getTable().'.workspace_id', Auth::user()->current_workspace_id);
            }
        });
    }
}
