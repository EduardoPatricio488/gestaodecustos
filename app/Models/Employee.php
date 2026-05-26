<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'role',
        'salary',
        'pay_day',
        'status'
    ];

    /**
     * RELAÇÕES
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    // NOVA RELAÇÃO: Histórico de Ausências e Férias
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * INTELIGÊNCIA DE RECURSOS HUMANOS
     */

    // Calcula quantos dias de férias gozou no ano atual
    public function getVacationDaysUsedAttribute(): int
    {
        return (int) $this->absences()
            ->where('type', 'ferias')
            ->where('status', 'aprovado')
            ->whereYear('start_date', now()->year)
            ->get()
            ->sum('business_days'); // Usa o cálculo de dias úteis que criámos no Modelo Absence
    }

    // Verifica se o colaborador está de férias ou baixa HOJE
    public function getIsAbsentTodayAttribute(): bool
    {
        return $this->absences()
            ->where('status', 'aprovado')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }

    // Retorna o estado atual do colaborador (Ativo ou Ausente)
    public function getCurrentStatusText(): string
    {
        if ($this->is_absent_today) {
            $currentAbsence = $this->absences()
                ->where('status', 'aprovado')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            return "Ausente (" . $currentAbsence->type_text . ")";
        }

        return "Em funções";
    }
}
