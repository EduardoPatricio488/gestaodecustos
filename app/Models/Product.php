<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['workspace_id', 'name', 'sku', 'stock_quantity', 'min_stock_alert', 'unit_cost', 'unit_price'];

    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }

    /**
     * LÓGICA DE INVENTÁRIO
     */

    // Verifica se o stock está em nível crítico
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_alert;
    }

    // Calcula o valor total do stock em armazém (preço de venda)
    public function getInventoryValueAttribute(): float
    {
        return (float) ($this->stock_quantity * $this->unit_price);
    }
}
