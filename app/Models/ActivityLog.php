<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['workspace_id', 'user_id', 'action', 'description', 'model_type', 'model_id', 'changes'];
    protected $casts = ['changes' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
}
