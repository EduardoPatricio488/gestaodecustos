<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMessage extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'project_id',
        'content',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * RELAÇÕES
     */

    // Quem enviou a mensagem
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // A que empresa pertence
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    // A que projeto pertence (pode ser nulo se for chat geral)
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * INTELIGÊNCIA DO MESSENGER
     */

    // Verifica se a mensagem foi enviada pelo utilizador atual (para alinhar na UI)
    public function isFromAuthUser(): bool
    {
        return $this->user_id === auth()->id();
    }

    // Formata a hora de envio de forma amigável
    public function getSentAtAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        }

        return $this->created_at->format('d M, H:i');
    }

    // Verifica se a mensagem é um anúncio (ex: sistema ou bot)
    public function isSystemMessage(): bool
    {
        return $this->user_id === null;
    }
}
