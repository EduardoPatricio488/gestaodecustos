<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransitItem extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'type',
        'name',
        'amount',
        'direction',
        'origin',
        'destination',
        'status',
        'expected_date',
        'confirmed_date',
        'description',
        'is_business',
    ];

    protected $casts = [
        'amount'         => 'float',
        'expected_date'  => 'date',
        'confirmed_date' => 'date',
        'is_business'    => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
