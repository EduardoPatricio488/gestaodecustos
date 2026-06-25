<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToWorkspace;

class Category extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'slug',
        'color',
        'order',
        'icon',
        'budget_limit',
        'is_fixed',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(\App\Models\CategoryField::class)->orderBy('order');
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(CategoryWidget::class)->orderBy('order');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
