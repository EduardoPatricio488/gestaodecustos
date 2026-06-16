<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model {
    use BelongsToWorkspace;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'category_id',
        'name',
        'amount',
        'billing_day',
        'cycle',
        'is_active',
        'payment_method',
        'status',
        'started_at',
        'renewal_date',
        'notes',
        'notify_before_billing',
        'notify_days_before',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'started_at' => 'date',
        'renewal_date' => 'date',
        'notify_before_billing' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
