<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'budget',
        'status',
        'start_date',
        'deadline',
        'revenue',
        'costs',
        'margin',
        'profit',
        'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
public function members()
{
    return $this->belongsToMany(User::class, 'project_user');
    // Nota: Isto assume que tens uma tabela pivot 'project_user'
}
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
