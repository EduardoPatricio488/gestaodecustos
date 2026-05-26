<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'title',
        'message',
        'type',
        'link',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * RELAÇÕES
     */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }

    /**
     * SCOPES (Facilitam a busca na base de dados)
     */
    public function scopeUnread($query) {
        return $query->whereNull('read_at');
    }

    /**
     * INTELIGÊNCIA VISUAL (Para a UI do Centro de Notificações)
     */

    // Define a cor baseada no tipo (danger, warning, success, info)
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'danger'  => 'red',
            'warning' => 'orange',
            'success' => 'emerald',
            default   => 'blue'
        };
    }

    // Define o ícone do Flux para cada tipo de alerta
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'danger'  => 'exclamation-circle',
            'warning' => 'bell',
            'success' => 'check-circle',
            default   => 'information-circle'
        };
    }

    // Formata o tempo de forma amigável (ex: há 2 min)
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * LÓGICA DE AÇÃO
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
