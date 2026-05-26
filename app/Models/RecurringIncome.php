<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringIncome extends Model {
    use BelongsToWorkspace;

    protected $fillable = ['user_id', 'workspace_id', 'description', 'amount', 'day_of_month', 'is_active'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
