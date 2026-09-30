<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $hidden = ['portal_token_hash'];

    protected $fillable = [
        'user_id', 'workspace_id', 'name', 'legal_name', 'tax_number', 'email', 'phone',
        'website', 'address', 'portal_token_hash', 'payment_terms', 'status',
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

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }
}
