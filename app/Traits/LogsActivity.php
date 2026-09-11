<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(fn ($model) => self::log($model, 'created', 'Criou '.class_basename($model).'.'));

        static::updated(function ($model): void {
            $dirty = $model->getDirty();
            $original = $model->getOriginal();
            $fields = array_intersect_key($original, $dirty);
            $changes = [
                'old' => self::redact($fields),
                'new' => self::redact($dirty),
            ];
            self::log($model, 'updated', 'Alterou '.class_basename($model).'.', $changes);
        });

        static::deleted(fn ($model) => self::log($model, 'deleted', 'Eliminou '.class_basename($model).'.'));
    }

    protected static function log($model, string $action, string $description, ?array $changes = null): void
    {
        if (! Auth::check()) {
            return;
        }

        $workspaceId = $model->workspace_id ?? Auth::user()->current_workspace_id;
        if (! $workspaceId) {
            return;
        }

        ActivityLog::create([
            'workspace_id' => $workspaceId,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'properties' => $changes,
            'metadata' => [
                'workspace_id' => $workspaceId,
                'ip' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ],
        ]);
    }

    protected static function redact(array $values): array
    {
        foreach (['password', 'remember_token', 'portal_token', 'invite_token', 'verification_code', 'two_factor_secret', 'two_factor_recovery_codes'] as $field) {
            if (array_key_exists($field, $values)) {
                $values[$field] = '[REDACTED]';
            }
        }

        return $values;
    }
}
