<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'is_timer_running',   // NOVO
        'timer_started_at',   // NOVO
        'total_seconds'       // NOVO
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'timer_started_at' => 'datetime', // NOVO
        'is_timer_running' => 'boolean',  // NOVO
    ];

    /**
     * RELAÇÕES
     */
    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    /**
     * LÓGICA DE TIME-TRACKING
     */

    // Retorna o tempo acumulado em formato 00:00:00
    public function getTimeFormattedAttribute(): string
    {
        $seconds = $this->total_seconds;

        // Se o timer estiver a correr agora, somamos o tempo que passou desde que ligou
        if ($this->is_timer_running && $this->timer_started_at) {
            $seconds += now()->diffInSeconds($this->timer_started_at);
        }

        return gmdate("H:i:s", $seconds);
    }

    /**
     * INTELIGÊNCIA DE PRODUTIVIDADE
     */

    public function isOverdue(): bool
    {
        if ($this->status === 'concluida' || !$this->due_date) return false;
        return $this->due_date->isPast();
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'critica' => 'red',
            'alta'    => 'orange',
            'media'   => 'blue',
            'baixa'   => 'zinc',
            default   => 'zinc'
        };
    }

    public function getStatusTimeAttribute(): string
    {
        if ($this->status === 'concluida' && $this->completed_at) {
            return 'Concluída ' . $this->completed_at->diffForHumans();
        }

        if ($this->due_date) {
            return $this->due_date->isPast()
                ? 'Atrasada há ' . $this->due_date->diffInDays(now()) . ' dias'
                : 'Faltam ' . $this->due_date->diffInDays(now()) . ' dias';
        }

        return 'Sem prazo';
    }

    public function markAsCompleted()
    {
        // Se o timer estiver a correr ao completar, paramos primeiro
        if ($this->is_timer_running) {
            $this->stopTimer();
        }

        $this->update([
            'status' => 'concluida',
            'completed_at' => now()
        ]);
    }

    // Funções auxiliares para o Hub
    public function stopTimer()
    {
        if ($this->is_timer_running) {
            $elapsed = now()->diffInSeconds($this->timer_started_at);
            $this->update([
                'is_timer_running' => false,
                'total_seconds' => $this->total_seconds + $elapsed,
                'timer_started_at' => null
            ]);
        }
    }
}
