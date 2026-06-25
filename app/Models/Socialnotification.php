<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialNotification extends Model
{
    protected $fillable = [
        'user_id', 'actor_id', 'type', 'post_id', 'preview', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'post_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Cria uma notificação, evitando notificar o próprio autor da ação
     * e evitando duplicar a mesma notificação de like repetidamente.
     */
    public static function notify(int $recipientId, int $actorId, string $type, ?int $postId = null, ?string $preview = null): void
    {
        if ($recipientId === $actorId) {
            return; // não notificar a própria pessoa
        }

        static::create([
            'user_id'  => $recipientId,
            'actor_id' => $actorId,
            'type'     => $type,
            'post_id'  => $postId,
            'preview'  => $preview ? \Illuminate\Support\Str::limit($preview, 80) : null,
        ]);
    }
}
