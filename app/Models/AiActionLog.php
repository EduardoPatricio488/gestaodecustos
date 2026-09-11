<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiActionLog extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'tool_name',
        'action_type',
        'status',
        'request_payload',
        'result_payload',
        'confirmation_token',
        'confirmed_at',
        'completed_at',
        'error_message',
        'latency_ms',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'result_payload' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'latency_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
