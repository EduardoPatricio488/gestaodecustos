<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BusinessDocument extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'category',
        'file_path',
        'expiry_date',
        'notes'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * RELAÇÃO
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * INTELIGÊNCIA DE ARQUIVO
     */

    // Verifica se o documento já expirou
    public function isExpired(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->isPast();
    }

    // Verifica se expira nos próximos 30 dias (Alerta preventivo)
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->isFuture() && $this->expiry_date->diffInDays(now()) <= 30;
    }

    // Obtém o ícone baseado na categoria
    public function getIcon()
    {
        return match($this->category) {
            'Legal'    => 'document-text',
            'RH'       => 'users',
            'Seguros'  => 'shield-check',
            'Impostos' => 'receipt-percent',
            default    => 'document'
        };
    }

    // Atalho para URL do ficheiro
    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }
}
