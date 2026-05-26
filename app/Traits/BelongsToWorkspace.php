<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToWorkspace {
    protected static function bootBelongsToWorkspace() {
        // Ao criar: Associa automaticamente ao Workspace ativo do utilizador logado
        static::creating(function ($model) {
            if (Auth::check() && empty($model->workspace_id)) {
                $model->workspace_id = Auth::user()->current_workspace_id;
            }
        });

        // Ao consultar: Filtra TUDO pelo Workspace ativo
        static::addGlobalScope('workspace', function (Builder $builder) {
            if (Auth::check() && Auth::user()->current_workspace_id) {
                $builder->where($builder->getQuery()->from . '.workspace_id', Auth::user()->current_workspace_id);
            }
        });
    }
}
