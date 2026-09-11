<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInsight extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'type',
        'priority',
        'title',
        'message',
        'category',
        'source',
        'data',
        'action',
        'link',
        'dedupe_key',
        'confidence',
        'score',
        'read_at',
        'dismissed_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'action' => 'array',
        'confidence' => 'integer',
        'score' => 'integer',
        'read_at' => 'datetime',
        'dismissed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function scopeVisible($query)
    {
        return $query
            ->whereNull('dismissed_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
