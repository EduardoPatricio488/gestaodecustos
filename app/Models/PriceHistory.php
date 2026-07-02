<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    use BelongsToWorkspace;

    protected $table = 'price_history';

    protected $fillable = [
        'workspace_id', 'category_id', 'expense_id',
        'unit_price', 'unit_type', 'merchant', 'recorded_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
        'recorded_at' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
