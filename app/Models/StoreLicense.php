<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreLicense extends Model
{
    protected $fillable = [
        'purchase_id', 'user_id', 'product_id', 'license_key', 'download_count', 'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(StorePurchase::class, 'purchase_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class, 'product_id');
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(StoreDownloadLog::class, 'license_id');
    }
}
