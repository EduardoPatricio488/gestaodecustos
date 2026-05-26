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
        'deadline'
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    /**
     * RELAÇÕES
     */
    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }

    /**
     * INTELIGÊNCIA FINANCEIRA (P&L)
     */

    public function getRevenueAttribute(): float
    {
        return (float) $this->invoices()->where('status', 'paga')->sum('total_amount');
    }

    public function getCostsAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getProfitAttribute(): float
    {
        return $this->revenue - $this->costs;
    }

    public function getMarginAttribute(): float
    {
        if ($this->revenue <= 0) return 0;
        return ($this->profit / $this->revenue) * 100;
    }

    /**
     * INTELIGÊNCIA DE TIME-TRACKING (Métricas de Eficiência)
     */

    // Soma total de segundos gastos em todas as tarefas do projeto
    public function getTotalTimeSecondsAttribute(): int
    {
        return (int) $this->tasks()->sum('total_seconds');
    }

    // Formata o tempo total do projeto em HH:MM
    public function getTotalTimeFormattedAttribute(): string
    {
        $hours = floor($this->total_time_seconds / 3600);
        $minutes = floor(($this->total_time_seconds / 60) % 60);

        return sprintf('%02dh %02dm', $hours, $minutes);
    }

    /**
     * LUCRO POR HORA: A métrica mais importante para o CEO
     * Divide o lucro total pelo número de horas trabalhadas.
     */
    public function getHourlyProfitAttribute(): float
    {
        $hours = $this->total_time_seconds / 3600;

        if ($hours <= 0) return 0;

        return $this->profit / $hours;
    }
}
