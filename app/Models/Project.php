<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * RELAÇÃO QUE FALTAVA:
     * Um projeto tem muitas despesas associadas.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Um projeto tem muitas tarefas.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
public function getProgressAttribute()
{
    $total = $this->tasks()->count();
    if ($total === 0) return 0;

    $completed = $this->tasks()->where('status', 'concluida')->count();
    return round(($completed / $total) * 100);
}
public function client()
{
    return $this->belongsTo(Client::class);
}
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
