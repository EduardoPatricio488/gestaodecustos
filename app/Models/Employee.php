<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'user_id', 'workspace_id', 'name', 'role', 'salary', 'pay_day', 'photo_path', 'active',
        'suspended', 'terminated_at', 'resignation_reason', 'cv_path', 'resignation_status',
        'portal_token', 'invite_expires_at', 'invite_used_at', 'invite_revoked_at',
    ];

    protected $casts = [
        'terminated_at' => 'datetime', 'active' => 'boolean', 'suspended' => 'boolean',
        'salary' => 'decimal:2', 'invite_expires_at' => 'datetime', 'invite_used_at' => 'datetime',
        'invite_revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function absences(): HasMany { return $this->hasMany(Absence::class); }

    public function getVacationDaysUsedAttribute(): int
    {
        return (int) $this->absences()->where('type', 'ferias')->where('status', 'aprovado')->whereYear('start_date', now()->year)->sum('business_days');
    }

    public function getIsAbsentTodayAttribute(): bool
    {
        return $this->absences()->where('status', 'aprovado')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->exists();
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : asset('images/default-employee.png');
    }

    public function getCurrentStatusText(): string
    {
        if ($this->terminated_at) return 'Vínculo Terminado';
        if ($this->resignation_status === 'pending') return 'Rescisão Pendente';
        if ($this->suspended) return 'Suspenso';
        if (! $this->active) return 'Inativo';
        if ($this->is_absent_today) return 'Ausente';
        return 'Em funções';
    }
}
