<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'client_id',
        'title',
        'proposal_number',
        'amount',
        'status',
        'valid_until',
        'notes'
    ];

    protected $casts = [
        'valid_until' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * RELAÇÕES
     */

    // Quem criou a proposta
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // A que empresa pertence
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    // A que cliente se destina
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * LÓGICA DE NEGÓCIO
     */

    // Verifica se a proposta expirou (se a data passou e não foi ganha)
    public function isExpired(): bool
    {
        if (!$this->valid_until) return false;
        return $this->valid_until->isPast() && !in_array($this->status, ['aceite', 'convertida']);
    }

    // Retorna a cor do badge baseada no estado atual
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'rascunho'   => 'neutral',
            'enviada'    => 'info',
            'aceite'     => 'success',
            'recusada'   => 'danger',
            'convertida' => 'emerald',
            default      => 'neutral',
        };
    }
}
