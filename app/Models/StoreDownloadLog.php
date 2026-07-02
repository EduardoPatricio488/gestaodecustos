<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreDownloadLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'license_id', 'ip_address', 'user_agent', 'token_hash', 'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(StoreLicense::class, 'license_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
