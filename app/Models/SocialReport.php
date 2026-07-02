<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialReport extends Model
{
    protected $fillable = ['user_id', 'social_post_id', 'reason', 'status'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function social_post(): BelongsTo {
        return $this->belongsTo(SocialPost::class, 'social_post_id');
    }
}
