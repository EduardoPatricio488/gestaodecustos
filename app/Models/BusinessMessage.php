<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMessage extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'user_id', 'project_id', 'content', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isFromAuthUser(): bool
    {
        return $this->user_id === auth()->id();
    }

    public function getSentAtAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        }
        if ($this->created_at->isYesterday()) {
            return 'Ontem '.$this->created_at->format('H:i');
        }

        return $this->created_at->format('d/m/Y H:i');
    }
}
