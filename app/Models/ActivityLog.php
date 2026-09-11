<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class ActivityLog extends Model
{
    protected $fillable = [
        'workspace_id', 'user_id', 'action', 'description', 'model_type', 'model_id', 'metadata', 'properties', 'type',
    ];

    protected $casts = [
        'metadata' => 'json', 'properties' => 'json',
    ];

    protected static function booted(): void
    {
        static::creating(function (ActivityLog $log): void {
            $user = auth()->user();
            if (! $user) return;

            if (! $log->workspace_id) {
                $log->workspace_id = $user->current_workspace_id;
            }

            if (! $log->workspace_id || ! $user->workspaces()->whereKey($log->workspace_id)->exists()) {
                throw new RuntimeException('O audit log tem de pertencer a um workspace do utilizador.');
            }

            $log->user_id ??= $user->id;
        });

        static::updating(function (): void {
            throw new RuntimeException('Os registos de auditoria são imutáveis.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Os registos de auditoria são imutáveis.');
        });
    }

    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
