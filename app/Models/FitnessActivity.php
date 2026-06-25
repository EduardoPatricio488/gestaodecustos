<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessActivity extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'type',
        'distance_km',
        'duration_minutes',
        'calories',
        'photo_path',
        'notes',
        'activity_date',
        // --- NOVOS CAMPOS DE PERFORMANCE ---
        'pace',
        'hr_avg',
        'hr_max',
        'steps',
        'cadence',
        'stride',
        'te_aerobic',
        'te_anaerobic',
        'recovery_time',
        'training_load',
        'zone_vo2',
        'zone_anaerobic',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'distance_km' => 'float',
        'duration_minutes' => 'integer',
        'calories' => 'float',
        'te_aerobic' => 'float',
        'te_anaerobic' => 'float',
        'training_load' => 'integer',
        'recovery_time' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'corrida' => '🏃',
            'ciclismo' => '🚴',
            'ginasio' => '🏋️',
            'natacao' => '🏊',
            'caminhada' => '🚶',
            'yoga' => '🧘',
            default => '⚡',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'corrida' => 'text-orange-500 bg-orange-500/10',
            'ciclismo' => 'text-blue-500 bg-blue-500/10',
            'ginasio' => 'text-purple-500 bg-purple-500/10',
            'natacao' => 'text-cyan-500 bg-cyan-500/10',
            'caminhada' => 'text-emerald-500 bg-emerald-500/10',
            'yoga' => 'text-pink-500 bg-pink-500/10',
            default => 'text-zinc-500 bg-zinc-500/10',
        };
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        return $hours > 0 ? "{$hours}h {$minutes}min" : "{$minutes}min";
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? \Storage::url($this->photo_path) : null;
    }
}
