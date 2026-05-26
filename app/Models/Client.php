<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'legal_name',
        'tax_number',
        'email',
        'phone',
        'status',
        'address',
        'notes'
    ];

    /**
     * RELAÇÕES
     */

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * NOVA RELAÇÃO: PROPOSTAS / ORÇAMENTOS
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * INTELIGÊNCIA COMERCIAL (Calculado automaticamente)
     */

    // LTV (Lifetime Value): Total de dinheiro que este cliente já pagou à empresa
    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->invoices()
            ->where('status', 'paga')
            ->sum('total_amount');
    }

    // DÍVIDA ATUAL: Faturas pendentes deste cliente
    public function getPendingDebtAttribute(): float
    {
        return (float) $this->invoices()
            ->where('status', 'pendente')
            ->sum('total_amount');
    }

    // AVATAR PROFISSIONAL: Gera uma imagem com as iniciais se não houver foto
    public function getAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=059669&background=ecfdf5&bold=true';
    }

    // STATUS BADGE: Cor para a interface baseada no status
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'ativo'   => 'success',
            'lead'    => 'warning',
            'inativo' => 'neutral',
            default   => 'neutral'
        };
    }
}
