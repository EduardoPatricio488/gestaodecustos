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
        'notes',
        'portal_token' // 1. ADICIONA ISTO para poderes gravar o código de acesso
    ];

    /**
     * RELAÇÕES
     */

    // 2. RELAÇÃO QUE FALTAVA: O cliente tem muitos projetos
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * INTELIGÊNCIA COMERCIAL
     */

    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->invoices()
            ->where('status', 'paga')
            ->sum('total_amount');
    }

    public function getPendingDebtAttribute(): float
    {
        return (float) $this->invoices()
            ->where('status', 'pendente')
            ->sum('total_amount');
    }

    public function getAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=059669&background=ecfdf5&bold=true';
    }

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
