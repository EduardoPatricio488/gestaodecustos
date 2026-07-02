<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialFollow extends Model
{
    protected $fillable = [
        'follower_id', 'following_id', 'is_friend',
    ];

    protected function casts(): array
    {
        return [
            'is_friend' => 'boolean',
        ];
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    /**
     * Alterna o estado de "seguir" entre dois utilizadores.
     * Retorna true se passou a seguir, false se deixou de seguir.
     */
    public static function toggle(int $followerId, int $followingId): bool
    {
        if ($followerId === $followingId) {
            return false;
        }

        $existing = static::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        static::create([
            'follower_id'  => $followerId,
            'following_id' => $followingId,
        ]);

        SocialNotification::notify($followingId, $followerId, 'follow');

        return true;
    }
}
