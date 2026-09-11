<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'name', 'sku', 'stock_quantity', 'min_stock_alert', 'unit_cost', 'unit_price'];

    protected $casts = [
        'stock_quantity' => 'integer',
        'min_stock_alert' => 'integer',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_alert;
    }

    public function getInventoryValueAttribute(): float
    {
        return (float) ($this->stock_quantity * $this->unit_cost);
    }
}
