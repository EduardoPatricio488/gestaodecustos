<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model {
    protected $fillable = [
        'user_id', 'workspace_id', 'title', 'remind_at',
        'priority', 'frequency', 'is_completed', 'completed_at'
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
