<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'user_id', 'workspace_id', 'name', 'legal_name', 'tax_number', 'email', 'phone',
        'status', 'address', 'notes', 'portal_token_hash',
    ];

    public static function findByPortalToken(string $token): ?self
    {
        $token = trim($token);

        if ($token === '' || strlen($token) !== 64 || ! preg_match('/^[A-Za-z0-9]+$/', $token)) {
            return null;
        }

        return static::where('portal_token_hash', hash('sha256', $token))
            ->with('workspace')
            ->first();
    }

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

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->invoices()->where('status', 'paga')->sum('total_amount');
    }

    public function getPendingDebtAttribute(): float
    {
        return (float) $this->invoices()->whereIn('status', ['pendente', 'vencida'])->sum('total_amount');
    }

    public function getAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=059669&background=ecfdf5&bold=true';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'ativo' => 'success', 'lead' => 'warning', 'inativo' => 'neutral', default => 'neutral'
        };
    }
}
