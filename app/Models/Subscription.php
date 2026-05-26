<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model {
    use BelongsToWorkspace;

    protected $fillable = ['user_id', 'workspace_id', 'category_id', 'name', 'amount', 'billing_day', 'cycle', 'is_active'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
