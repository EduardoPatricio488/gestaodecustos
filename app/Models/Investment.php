<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model {
    use BelongsToWorkspace;

    protected $fillable = ['user_id', 'workspace_id', 'name', 'symbol', 'type', 'quantity', 'average_price', 'current_price'];
}
