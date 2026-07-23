<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyBudgetPermission extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'category_id',


        // Campos de Mesada e Limites
        'allowance_limit',
        'spending_limit',
        'allowance_frequency',
        'can_view_all',
        'can_edit',
        // Restrições de Módulos Premium
        'restrict_business',
        'restrict_store',
        'restrict_fitness',
        // Restrições de Finanças
        'restrict_budget',
        'restrict_import',
        'restrict_incomes',
        'restrict_debts',
        'restrict_investments',
        'restrict_subs',
        'restrict_networth',
        'restrict_bank',
        // Restrições de Ferramentas
        'restrict_calendar',
        'restrict_reminders',
        'restrict_goals',
        'restrict_wrapped'
    ];

    protected $casts = [
        'can_view_all' => 'boolean',
        'can_edit' => 'boolean',
        'allowance_limit' => 'decimal:2',
        'spending_limit' => 'decimal:2', // Adicionado cast para dinheiro
        // Casts para as permissões serem lidas como true/false
        'restrict_business' => 'boolean',
        'restrict_store' => 'boolean',

        'restrict_fitness' => 'boolean',
        'restrict_budget' => 'boolean',
        'restrict_import' => 'boolean',
        'restrict_incomes' => 'boolean',
        'restrict_debts' => 'boolean',
        'restrict_investments' => 'boolean',
        'restrict_subs' => 'boolean',
        'restrict_networth' => 'boolean',
        'restrict_bank' => 'boolean',
        'restrict_calendar' => 'boolean',
        'restrict_reminders' => 'boolean',
        'restrict_goals' => 'boolean',
        'restrict_wrapped' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
