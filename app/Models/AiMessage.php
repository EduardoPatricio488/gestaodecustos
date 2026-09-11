<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    protected $fillable = [
        'ai_conversation_id',
        'user_id',
        'role',
        'content',
        'tool_name',
        'tool_call_id',
        'metadata',
        'tokens',
        'latency_ms',
        'is_error',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tokens' => 'integer',
        'latency_ms' => 'integer',
        'is_error' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
