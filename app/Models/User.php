<?php

namespace App\Models;

use App\Traits\HasGamification; // 1. IMPORTANTE
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use NotificationChannels\WebPush\HasPushSubscriptions;

// 2. ADICIONADO "implements MustVerifyEmail" - ISTO É O QUE TRAVA O DASHBOARD
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    // 3. ORGANIZADO: Traits usados apenas uma vez
    use Billable,
        HasFactory,
        HasGamification,
        HasPushSubscriptions,
        Notifiable,
        PasskeyAuthenticatable,
        TwoFactorAuthenticatable;

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->username)) {
                $user->username = static::generateUniqueUsername($user->name ?? 'user');
            }
        });
    }

    public static function generateUniqueUsername(string $name): string
    {
        $base = Str::slug($name, '');
        $base = $base !== '' ? $base : 'user';
        $candidate = $base;
        $i = 1;
        while (static::where('username', $candidate)->exists()) {
            $candidate = $base.$i;
            $i++;
        }

        return $candidate;
    }

    /** ── RELAÇÕES SOCIAIS ── **/
    public function socialPosts()
    {
        return $this->hasMany(SocialPost::class);
    }

    public function following()
    {
        return $this->hasMany(SocialFollow::class, 'follower_id');
    }

    public function followers()
    {
        return $this->hasMany(SocialFollow::class, 'following_id');
    }

    public function likes()
    {
        return $this->hasMany(SocialLike::class);
    }

    public function socialNotifications(): HasMany
    {
        return $this->hasMany(SocialNotification::class);
    }

    public function isFollowing(int $userId): bool
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    public function sendEmailVerificationNotification()
    {
        // Deixamos vazio para o Laravel não enviar o email automático com o botão.
        // O nosso RegisteredUserController já trata de enviar o código.
    }

    protected $fillable = [
        'name',
        'profile_emoji',
        'profile_color',
        'last_login_at', // <-- ADICIONAR ESTE
        'last_ip',
        'username',

        'social_bio',
        'verification_code', // OK
        'email',
        'password',
        'locale',
        'current_workspace_id',
        'onboarding_completed',
        'is_admin',
        'is_active',
        'role',

        'xp',
        'level',
        'avatar_path',
        'default_post_visibility',
        'is_profile_private',
        'plan', // Adicionado caso precises
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'xp' => 'integer',
            'level' => 'integer',
            'onboarding_completed' => 'boolean',
        ];
    }

    /** ── SISTEMA DE PLANOS: free | pro | business | extras ── **/
    public static function normalizePlan(?string $plan): string
    {
        $plan = strtolower(trim((string) $plan));

        if ($plan === '' || $plan === 'free' || $plan === 'null') {
            return 'free';
        }

        return match ($plan) {
            'plus', 'premium', 'star' => 'pro',
            'diamond', 'diamante', 'company' => 'business',
            default => $plan,
        };
    }

    public static function planLabel(?string $plan): string
    {
        $slug = self::normalizePlan($plan);

        return match ($slug) {
            'free' => 'Free',
            'pro' => 'Pro',
            'business' => 'Business',
            default => SubscriptionPlan::where('slug', $slug)->value('name') ?? Str::headline($slug),
        };
    }

    public function currentPlanSlug(): string
    {
        return self::normalizePlan($this->plan ?: $this->currentWorkspace?->plan);
    }

    public function isPro(): bool
    {
        return $this->currentPlanSlug() === 'pro';
    }

    public function isBusinessPlan(): bool
    {
        $slug = $this->currentPlanSlug();
        if ($slug === 'business') {
            return true;
        }

        $catalog = SubscriptionPlan::where('slug', $slug)->first();

        return $catalog?->hasFeature('business_mode') ?? false;
    }

    public function isPaidPlan(): bool
    {
        return $this->currentPlanSlug() !== 'free';
    }

    public function isAnyPremium(): bool
    {
        return $this->isPaidPlan();
    }

    public function isStar(): bool
    {
        return $this->isPro();
    }

    public function isDiamond(): bool
    {
        return $this->isBusinessPlan();
    }

    public function isOnline()
    {
        if (! isset($this->last_seen_at)) {
            return false;
        }

        return $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /** ── PERMISSÕES WORKSPACE ── **/
    public function currentRole(): string
    {
        if (! $this->current_workspace_id) {
            return 'admin';
        }

        $workspace = $this->workspaces()
            ->where('workspaces.id', $this->current_workspace_id)
            ->first();

        return $workspace?->pivot?->role ?? 'member';
    }

    public function isOwner(): bool
    {
        return $this->currentRole() === 'admin';
    }

    public function isEditor(): bool
    {
        return in_array($this->currentRole(), ['admin', 'editor']);
    }

    public function isViewer(): bool
    {
        return $this->currentRole() === 'viewer';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? \Storage::url($this->avatar_path) : null;
    }

    public function chatConversations(): BelongsToMany
    {
        return $this->belongsToMany(ChatConversation::class, 'chat_conversation_user')
            ->withPivot('last_read_at')->withTimestamps();
    }

    public function isActive(): bool
    {
        // Se a tua coluna na base de dados se chamar 'is_active'
        return (bool) $this->is_active;
    }

    /**
     * Verifica os cargos do utilizador.
     */
    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    /**
     * Atalhos de cargos
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    public function isAnalyst(): bool
    {
        return $this->role === 'analyst';
    }

    public function isAdminRole(): bool
    {
        return in_array($this->role, ['admin', 'moderator', 'analyst']);
    }

    public function logActivity($action, $type = 'geral')
    {
        \DB::table('activity_logs')->insert([
            'user_id' => $this->id,
            'action' => $action,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected $casts = [
        // ... outros casts que já tenhas (email_verified_at, password, etc)
        'badges' => 'collection',
        'is_active' => 'boolean',
    ];

    public function initials(): string
    {
        return Str::of($this->name)->explode(' ')->take(2)->map(fn ($word) => Str::substr($word, 0, 1))->implode('');
    }

    /** ── RELAÇÕES WORKSPACE ── **/
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_user')->withPivot('role')->withTimestamps();
    }

    public function currentWorkspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    /** ── RELAÇÕES GERAIS ── **/
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function recurringIncomes(): HasMany
    {
        return $this->hasMany(RecurringIncome::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function storePurchases(): HasMany
    {
        return $this->hasMany(StorePurchase::class);
    }

    public function storeWishlists(): HasMany
    {
        return $this->hasMany(StoreWishlist::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class)->withTimestamps();
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    /** ── RELAÇÕES EMPRESARIAIS ── **/
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BusinessDocument::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BusinessMessage::class);
    }
}
