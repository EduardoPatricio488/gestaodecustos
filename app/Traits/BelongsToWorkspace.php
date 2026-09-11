<?php

namespace App\Traits;

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

            $workspaceId = Auth::user()->current_workspace_id;
            if ($workspaceId && empty($model->workspace_id)) {
                $model->workspace_id = $workspaceId;
            }

            if ($workspaceId && $model->workspace_id && (int) $model->workspace_id !== (int) $workspaceId) {
                throw new RuntimeException('Não é permitido criar dados noutro workspace.');
            }
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
        });

        static::addGlobalScope('workspace', function (Builder $builder): void {
            if (Auth::check() && Auth::user()->current_workspace_id) {
                $builder->where(
                    $builder->getModel()->getTable().'.workspace_id',
                    Auth::user()->current_workspace_id
                );
            }
        });
    }
}
