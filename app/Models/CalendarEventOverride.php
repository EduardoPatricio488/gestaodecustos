<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEventOverride extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'event_key',
        'event_date',
        'event_data',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_data' => 'array',
    ];
}
