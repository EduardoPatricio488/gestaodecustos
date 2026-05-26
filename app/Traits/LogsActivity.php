<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity {
    protected static function bootLogsActivity() {
        static::created(fn($model) => self::log($model, 'created', "Criou um registo de " . $model->amount . "€"));

        static::updated(function($model) {
            $changes = [
                'old' => array_intersect_key($model->getOriginal(), $model->getDirty()),
                'new' => $model->getDirty(),
            ];
            self::log($model, 'updated', "Alterou detalhes do registo", $changes);
        });

        static::deleted(fn($model) => self::log($model, 'deleted', "Eliminou um registo de " . $model->amount . "€"));
    }

    protected static function log($model, $action, $description, $changes = null) {
        if (Auth::check()) {
            ActivityLog::create([
                'workspace_id' => Auth::user()->current_workspace_id,
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'model_type' => class_basename($model),
                'model_id' => $model->id,
                'changes' => $changes,
            ]);
        }
    }
}
