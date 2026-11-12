<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarVacation extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
