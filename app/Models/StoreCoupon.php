<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCoupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_purchase', 'max_uses', 'used_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
