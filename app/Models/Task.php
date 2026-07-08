<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'workspace_id',
        'project_id',
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'completed_at',
        'estimated_hours',
        'is_timer_running',
        'timer_started_at',
        'total_seconds'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'timer_started_at' => 'datetime',
        'is_timer_running' => 'boolean',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    /**
     * Verifica se a tarefa está atrasada
     */
    public function isOverdue(): bool
    {
        if (! $this->due_date || $this->status === 'concluida') {
            return false;
        }

        // Como due_date está no $casts como 'date', ele já é um objeto Carbon
        return $this->due_date->isPast() && !$this->due_date->isToday();
    }

    /**
     * Retorna a cor baseada na prioridade (Para a UI)
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'baixa'    => 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
            'media'    => 'text-amber-600 bg-amber-50 dark:bg-amber-900/20',
            'alta'     => 'text-orange-600 bg-orange-50 dark:bg-orange-900/20',
            'critica'  => 'text-red-600 bg-red-50 dark:bg-red-900/20',
            default    => 'text-zinc-600 bg-zinc-50 dark:bg-zinc-900/20',
        };
    }

    /**
     * Formatação do Cronómetro
     */
    public function getTimeFormattedAttribute(): string
    {
        $seconds = $this->total_seconds;
        if ($this->is_timer_running && $this->timer_started_at) {
            $seconds += now()->diffInSeconds($this->timer_started_at);
        }
        return gmdate("H:i:s", $seconds);
    }
}
