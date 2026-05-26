<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\CarbonInterface;

class Absence extends Model
{
    protected $fillable = [
        'workspace_id',
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * RELAÇÕES
     */

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * INTELIGÊNCIA DE RH
     */

    // Calcula apenas os dias úteis (Segunda a Sexta)
    public function getBusinessDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;

        // Alterado de "Carbon $date" para sem tipo ou "CarbonInterface" para evitar o erro 500
        return $this->start_date->diffInDaysFiltered(function (CarbonInterface $date) {
            return !$date->isWeekend();
        }, $this->end_date) + 1;
    }

    // Retorna a cor baseada no tipo
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'ferias'            => 'emerald',
            'doenca'            => 'red',
            'falta_justificada' => 'blue',
            'pessoal'           => 'amber',
            default             => 'zinc'
        };
    }

    // Tradução para a interface
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'ferias'            => 'Férias',
            'doenca'            => 'Doença/Baixa',
            'falta_justificada' => 'Falta Justificada',
            'pessoal'           => 'Assuntos Pessoais',
            default             => 'Outro'
        };
    }
}
