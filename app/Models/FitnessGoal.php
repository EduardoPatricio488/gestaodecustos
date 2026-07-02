<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessGoal extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'type',
        'target',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
        'target' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function getTypeUnitAttribute(): string
    {
        return match($this->type) {
            'distancia_semanal' => 'km esta semana',
            'calorias_mensais' => 'kcal este mês',
            'sessoes_semanais' => 'sessões esta semana',
            'tempo_semanal' => 'min esta semana',
            default => '',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'distancia_semanal' => '📍 Distância Semanal',
            'calorias_mensais' => '🔥 Calorias Mensais',
            'sessoes_semanais' => '⚡ Sessões Semanais',
            'tempo_semanal' => '⏱️ Tempo Semanal',
            default => $this->type,
        };
    }
}
