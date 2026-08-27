<?php

namespace App\Services;

use App\Models\StoreLicense;
use App\Models\StorePurchase;
use Illuminate\Support\Str;

class StoreLicenseService
{
    public function issue(StorePurchase $purchase): StoreLicense
    {
        return StoreLicense::firstOrCreate(
            ['purchase_id' => $purchase->id],
            [
                'user_id' => $purchase->user_id,
                'product_id' => $purchase->product_id,
                'license_key' => $this->generateKey(),
                'activated_at' => now(),
            ]
        );
    }

    public function generateKey(): string
    {
        return strtoupper(implode('-', [
            Str::random(4),
            Str::random(4),
            Str::random(4),
            Str::random(4),
        ]));
    }
}
