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
        'photo_path',
        'active',
        'suspended',
        'terminated_at',
        'resignation_reason',
         'cv_path',
        'resignation_status',
        'portal_token' // <--- ADICIONADO: Agora o sistema já consegue gravar o código!
    ];

    /**
     * Casts para garantir que o Laravel trata os campos corretamente
     */
    protected $casts = [
        'terminated_at' => 'datetime',
        'active' => 'boolean',
        'suspended' => 'boolean',
        'salary' => 'decimal:2',
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

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * INTELIGÊNCIA DE RECURSOS HUMANOS
     */

    // Dias de férias usados no ano atual
    public function getVacationDaysUsedAttribute(): int
    {
        return (int) $this->absences()
            ->where('type', 'ferias')
            ->where('status', 'aprovado')
            ->whereYear('start_date', now()->year)
            ->sum('business_days');
    }

    // Verifica se está ausente hoje
    public function getIsAbsentTodayAttribute(): bool
    {
        return $this->absences()
            ->where('status', 'aprovado')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }

    /**
     * FOTO DO COLABORADOR
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : asset('images/default-employee.png');
    }

    /**
     * Estado atual do colaborador (Texto Dinâmico)
     */
    public function getCurrentStatusText(): string
    {
        if ($this->terminated_at) {
            return "Vínculo Terminado";
        }

        if ($this->resignation_status === 'pending') {
            return "Rescisão Pendente";
        }

        if ($this->suspended) {
            return "Suspenso";
        }

        if (!$this->active) {
            return "Inativo";
        }

        if ($this->is_absent_today) {
            $currentAbsence = $this->absences()
                ->where('status', 'aprovado')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            return "Ausente (" . ($currentAbsence->type ?? 'Férias/Baixa') . ")";
        }

        return "Em funções";
    }
}
