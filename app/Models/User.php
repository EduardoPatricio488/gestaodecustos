<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasGamification;
use NotificationChannels\WebPush\HasPushSubscriptions; // <--- 1. ADICIONADO O IMPORT

class User extends Authenticatable implements PasskeyUser
{
    // 2. ADICIONADO O TRAIT HasPushSubscriptions NA LISTA ABAIXO
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasGamification, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'current_workspace_id',
        'is_admin', // Admin do Site Global
        'is_active',
        'xp',
        'level'
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'xp' => 'integer',
            'level' => 'integer',
        ];
    }

    /**
     * SISTEMA DE PERMISSÕES DO WORKSPACE
     * Retorna o cargo do utilizador no espaço de trabalho ativo.
     */
    public function currentRole()
    {
        $workspace = $this->workspaces()->where('workspace_id', $this->current_workspace_id)->first();
        return $workspace ? $workspace->pivot->role : 'viewer';
    }

    public function isOwner(): bool {
        return $this->currentRole() === 'admin';
    }

    public function isEditor(): bool {
        return in_array($this->currentRole(), ['admin', 'editor']);
    }

    public function isViewer(): bool {
        return $this->currentRole() === 'viewer';
    }

    /**
     * INICIAIS DO UTILIZADOR
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * RELAÇÕES DE WORKSPACE
     */
    public function workspaces(): BelongsToMany {
        return $this->belongsToMany(Workspace::class, 'workspace_user')->withPivot('role')->withTimestamps();
    }

    public function currentWorkspace(): BelongsTo {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    /**
     * RELAÇÕES GERAIS
     */
    public function categories(): HasMany { return $this->hasMany(Category::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function incomes(): HasMany { return $this->hasMany(Income::class); }
    public function recurringIncomes(): HasMany { return $this->hasMany(RecurringIncome::class); }
    public function goals(): HasMany { return $this->hasMany(Goal::class); }
    public function investments(): HasMany { return $this->hasMany(Investment::class); }
    public function emailLogs(): HasMany { return $this->hasMany(EmailLog::class); }
    public function subscriptions(): HasMany { return $this->hasMany(Subscription::class); }
    public function badges(): BelongsToMany { return $this->belongsToMany(Badge::class)->withTimestamps(); }

    public function appNotifications(): HasMany { return $this->hasMany(AppNotification::class); }

    // RELAÇÕES EMPRESARIAIS
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function suppliers(): HasMany { return $this->hasMany(Supplier::class); }
    public function clients(): HasMany { return $this->hasMany(Client::class); }
    public function documents(): HasMany { return $this->hasMany(BusinessDocument::class); }
    public function proposals(): HasMany { return $this->hasMany(Proposal::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function messages(): HasMany { return $this->hasMany(BusinessMessage::class); }
}
