<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Workspace extends Model
{
    protected $fillable = [
        'name',
        'type',
        'owner_id',
        'invite_code',
        'legal_name',
        'logo_path',
        'tax_number',
        'industry',
        'currency',
        'initial_capital',
         'audit_token',
          'recruitment_extra_info',
        'business_email',
        'plan',
        'plan_expires_at',
        'address',
         'recruitment_active',
    'recruitment_description',
    'recruitment_announcement',
    'recruitment_vacancies',
    'recruitment_extra_info',
        'fiscal_year_start'
    ];

    /**
     * VALORES PADRÃO (Para evitar empresas "fantasmas" sem tipo)
     */
    protected $attributes = [
        'type' => 'business',
        'currency' => 'EUR'
    ];

    /**
     * IDENTIDADE VISUAL
     */
    public function generateInviteCode()
    {
        if (!$this->invite_code) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name), 0, 3));
            $random = strtoupper(bin2hex(random_bytes(3))); // Gera algo como 'A1B2C3'

            $this->invite_code = $prefix . '-' . $random;
            $this->save();
        }

        return $this->invite_code;
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path
            ? Storage::url($this->logo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=10b981&background=ecfdf5&bold=true';
    }

    /**
     * RELAÇÕES DE ESTRUTURA E UTILIZADORES
     */
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class, 'workspace_user')->withPivot('role')->withTimestamps(); }

    /**
     * RELAÇÕES DE NEGÓCIO E OPERAÇÕES
     */
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function incomes(): HasMany { return $this->hasMany(Income::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function categories(): HasMany { return $this->hasMany(Category::class); }
    public function clients(): HasMany { return $this->hasMany(Client::class); }
    public function suppliers(): HasMany { return $this->hasMany(Supplier::class); }
    public function projects(): HasMany { return $this->hasMany(Project::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function documents(): HasMany { return $this->hasMany(BusinessDocument::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function messages(): HasMany { return $this->hasMany(BusinessMessage::class); }
    public function proposals(): HasMany { return $this->hasMany(Proposal::class); }
    public function bankAccounts(): HasMany { return $this->hasMany(BankAccount::class); }


    /**
     * GESTÃO DE FÉRIAS E AUSÊNCIAS
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * TRADUÇÃO E INTERFACE
     */
    public function getTypeText(): string
    {
        return match($this->type) {
            'personal' => 'Conta Individual',
            'couple'   => 'Conta Partilhada (Casal)',
            'family'   => 'Conta Familiar',
            'business' => 'Gestão Empresarial', // Ajustado para coincidir com o valor da DB
            'company'  => 'Gestão Empresarial',
            default    => 'Outro'
        };
    }

    /**
     * MÉTRICAS DE GESTÃO EMPRESARIAL
     */
    public function getBurnRate(): float
    {
        $threeMonthsAgo = now()->subMonths(3);
        $totalSpent = $this->expenses()
            ->where('is_company', true)
            ->where('spent_at', '>=', $threeMonthsAgo)
            ->sum('amount');

        return (float) ($totalSpent / 3);
    }

    public function getLiquidezAtual(): float
    {
        if ($this->bankAccounts()->exists()) {
            return (float) $this->bankAccounts->sum('current_balance');
        }
        $revenue = $this->invoices()->where('status', 'paga')->sum('total_amount');
        $spent = $this->expenses()->where('is_company', true)->sum('amount');
        $payroll = $this->employees()->sum('salary');
        return (float) ($this->initial_capital + $revenue - $spent - $payroll);
    }

    public function money($amount)
    {
        $symbols = [
            'EUR' => '€',
            'USD' => '$',
            'BRL' => 'R$',
            'GBP' => '£',
            'CHF' => 'CHF',
            'JPY' => '¥'
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        if (in_array($this->currency, ['USD', 'BRL'])) {
            return $symbol . ' ' . number_format($amount, 2, ',', ' ');
        }

        return number_format($amount, 2, ',', ' ') . ' ' . $symbol;
    }

    public function getRunway(): string
    {
        $burnRate = $this->getBurnRate();
        $liquidez = $this->getLiquidezAtual();
        if ($burnRate <= 0) return '∞';
        if ($liquidez <= 0) return '0 meses';
        $months = $liquidez / $burnRate;
        return number_format($months, 1) . ' meses';
    }

    /**
     * SCORE DE SAÚDE FINANCEIRA
     */
    public function calculateScore(): int
    {
        $monthStart = now()->startOfMonth();
        $spent = Expense::where('workspace_id', $this->id)->where('spent_at', '>=', $monthStart)->sum('amount') ?: 0;
        $earned = Income::where('workspace_id', $this->id)->where('received_at', '>=', $monthStart)->sum('amount') ?: 0;
        $budget = Category::where('workspace_id', $this->id)->sum('budget_limit') ?: 0;
        $net = (float)$earned - (float)$spent;
        $savingsRate = $earned > 0 ? ($net / $earned) * 100 : 0;
        $budgetAdherence = $budget > 0 ? (1 - (min($spent, $budget) / $budget)) * 100 : 100;
        $score = ($savingsRate * 0.7) + ($budgetAdherence * 0.3) + 20;
        return (int) max(0, min(100, $score));
    }
}
