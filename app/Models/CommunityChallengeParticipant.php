<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityChallengeParticipant extends Model
{
    protected $fillable = ['community_challenge_id', 'user_id', 'progress_pct', 'joined_at'];

    protected $casts = ['joined_at' => 'datetime'];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(CommunityChallenge::class, 'community_challenge_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
