<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model {
    use BelongsToWorkspace;

    protected $fillable = ['user_id', 'workspace_id', 'name', 'target_amount', 'current_amount', 'deadline'];
    protected $casts = ['deadline' => 'date', 'target_amount' => 'decimal:2', 'current_amount' => 'decimal:2'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
