<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ClientPortalToken extends Model
{
    protected $fillable = ['workspace_id', 'client_id', 'token', 'expires_at', 'is_active'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function generateFor(Client $client): self
    {
        return self::create([
            'workspace_id' => $client->workspace_id,
            'client_id' => $client->id,
            'token' => Str::random(48),
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);
    }

    public function isValid(): bool
    {
        return $this->is_active
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function getUrlAttribute(): string
    {
        return route('client.portal', $this->token);
    }
}
