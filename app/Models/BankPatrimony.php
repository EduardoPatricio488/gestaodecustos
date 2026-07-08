<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankPatrimony extends Model
{
    use BelongsToWorkspace;

    protected $table = 'bank_patrimony';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'type',
        'name',
        'value',
        'purchase_price',
        'purchase_date',
        'currency',
        'description',
        'status',
        'is_business',
        'metadata',
    ];

    protected $casts = [
        'value'          => 'float',
        'purchase_price' => 'float',
        'purchase_date'  => 'date',
        'is_business'    => 'boolean',
        'metadata'       => 'array',
    ];

    public static array $types = [
        'real_estate'  => ['label' => 'Imóvel',        'icon' => 'home',         'color' => '#10b981'],
        'vehicle'      => ['label' => 'Veículo',        'icon' => 'truck',        'color' => '#3b82f6'],
        'gold'         => ['label' => 'Ouro',           'icon' => 'star',         'color' => '#f59e0b'],
        'crypto'       => ['label' => 'Criptomoeda',    'icon' => 'cpu-chip',     'color' => '#8b5cf6'],
        'other_asset'  => ['label' => 'Outro Ativo',    'icon' => 'cube',         'color' => '#6b7280'],
        'liability'    => ['label' => 'Passivo',        'icon' => 'minus-circle', 'color' => '#ef4444'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGainLossAttribute(): float
    {
        if (!$this->purchase_price) {
            return 0;
        }
        return $this->value - $this->purchase_price;
    }

    public function getGainLossPctAttribute(): float
    {
        if (!$this->purchase_price || $this->purchase_price <= 0) {
            return 0;
        }
        return (($this->value - $this->purchase_price) / $this->purchase_price) * 100;
    }
}
