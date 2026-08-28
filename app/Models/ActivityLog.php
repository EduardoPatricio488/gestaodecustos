<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'workspace_id', // 🔥 ADICIONADO: Obrigatório para o MySQL funcionar
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'metadata',
        'properties',   // Adicionado por segurança caso uses
        'type',         // Adicionado por segurança
    ];

    protected $casts = [
        'metadata' => 'json',
        'properties' => 'json',
    ];

    /**
     * Relacionamento com o Workspace
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Relacionamento com o Utilizador
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
