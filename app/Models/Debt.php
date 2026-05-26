<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'type',
        'person_name',
        'amount',
        'description',
        'due_at',
        'is_paid'
    ];

    protected $casts = [
        'due_at' => 'date',
        'amount' => 'decimal:2',
        'is_paid' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
