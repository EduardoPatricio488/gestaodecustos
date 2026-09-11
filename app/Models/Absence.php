<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\DomainException;

class Absence extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'employee_id', 'type', 'start_date', 'end_date', 'status', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Absence $absence): void {
            if (! $absence->employee_id || ! $absence->workspace_id) {
                return;
            }

            $employee = Employee::withoutGlobalScopes()->find($absence->employee_id);
            if (! $employee || (int) $employee->workspace_id !== (int) $absence->workspace_id) {
                throw new DomainException('O colaborador não pertence à empresa selecionada.');
            }

            if ($absence->start_date && $absence->end_date && $absence->end_date->lt($absence->start_date)) {
                throw new DomainException('A data de fim não pode ser anterior à data de início.');
            }
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getBusinessDaysAttribute(): int
    {
        if (! $this->start_date || ! $this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDaysFiltered(function (CarbonInterface $date) {
            return ! $date->isWeekend();
        }, $this->end_date) + 1;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'ferias' => 'emerald', 'doenca' => 'red', 'falta_justificada' => 'blue', 'pessoal' => 'amber', default => 'zinc'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match ($this->type) {
            'ferias' => 'Férias', 'doenca' => 'Doença/Baixa', 'falta_justificada' => 'Falta Justificada', 'pessoal' => 'Assuntos Pessoais', default => 'Outro'
        };
    }
}
