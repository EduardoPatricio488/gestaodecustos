<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    protected $fillable = ['name', 'is_group', 'created_by'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_conversation_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function otherUser(int $currentUserId): ?User
    {
        return $this->users->firstWhere('id', '!=', $currentUserId);
    }

    public static function findDirectBetween($userId1, $userId2)
    {
        return static::where('is_group', false)
            ->whereHas('users', function ($q) use ($userId1, $userId2) {
                $q->whereIn('user_id', [$userId1, $userId2]);
            })
            ->get()
            ->filter(function ($conversation) use ($userId1, $userId2) {
                $userIds = $conversation->users->pluck('id')->sort()->values();
                return $userIds->all() === collect([$userId1, $userId2])->sort()->values()->all();
            })
            ->first();
    }
}
