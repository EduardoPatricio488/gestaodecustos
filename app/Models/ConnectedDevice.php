<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'emoji',
        'provider',
        'provider_user_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_synced_at',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_synced_at'   => 'datetime',
        'is_active'        => 'boolean',
        'meta'             => 'array',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProviderLabelAttribute(): string
    {
        return match($this->provider) {
            'strava'        => 'Strava',
            'garmin'        => 'Garmin Connect',
            'fitbit'        => 'Fitbit',
            'healthconnect' => 'Health Connect',
            'mifitness'     => 'Mi Fitness',
            default         => 'Manual',
        };
    }

    public function getProviderColorAttribute(): string
    {
        return match($this->provider) {
            'strava'        => 'bg-orange-600/10 text-orange-500 border-orange-600/20',
            'garmin'        => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            'fitbit'        => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
            'mifitness'     => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            default         => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) return 'Inativo';
        if (!$this->last_synced_at) return 'Nunca sincronizado';
        if ($this->last_synced_at->diffInHours() < 24) return 'Sincronizado';
        return 'Desatualizado';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) return 'text-zinc-500';
        if (!$this->last_synced_at) return 'text-yellow-500';
        if ($this->last_synced_at->diffInHours() < 24) return 'text-emerald-500';
        return 'text-orange-500';
    }
}
