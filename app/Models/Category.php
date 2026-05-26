<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToWorkspace;

class Category extends Model
{
    use BelongsToWorkspace;

    // Colunas autorizadas
    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'color',
        'icon',
        'budget_limit',
    ];

    /**
     * RELAÇÕES
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
