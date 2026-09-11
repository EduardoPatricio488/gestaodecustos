<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'name', 'description', 'budget', 'status', 'start_date', 'deadline', 'revenue', 'costs', 'margin', 'profit', 'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date', 'deadline' => 'date',
        'budget' => 'decimal:2', 'revenue' => 'decimal:2', 'costs' => 'decimal:2',
        'margin' => 'decimal:2', 'profit' => 'decimal:2',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

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
        if ($total === 0) {
            return 0;
        }

        return round(($this->tasks()->where('status', 'concluida')->count() / $total) * 100);
    }

    public function client(): BelongsTo
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
