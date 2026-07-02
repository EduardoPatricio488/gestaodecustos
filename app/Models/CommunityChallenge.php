<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityChallenge extends Model
{
    protected $fillable = [
        'title', 'description', 'badge_name', 'type', 'start_date', 'end_date', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(CommunityChallengeParticipant::class);
    }

    public function isActive(): bool
    {
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }
}
