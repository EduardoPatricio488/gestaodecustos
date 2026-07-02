<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StoreBundle extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'price', 'image', 'badge', 'savings_percent', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(StoreProduct::class, 'store_bundle_products', 'bundle_id', 'product_id');
    }

    public function individualTotal(): float
    {
        return $this->products->sum('price');
    }
}
