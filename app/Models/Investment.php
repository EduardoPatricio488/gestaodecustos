<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'issuer', 'series', 'interest_rate', 'loyalty_bonus', 'capitalization_date',
        'user_id',
        'workspace_id',
        'name',
        'symbol',
        'isin',
        'product_type',
        'type',
        'quantity',
        'average_price',
        'current_price',
        'exchange',
        'network',
        'provider',
        'broker',
        'operation_date',
        'fees',
    ];

    protected $casts = [
        'operation_date' => 'date',
        'quantity'       => 'float',
        'product_type' => 'string',
        'average_price'  => 'float',
        'current_price'  => 'float',
        'fees'           => 'float',
        'interest_rate'       => 'float',
'loyalty_bonus'       => 'float',
'capitalization_date' => 'date',
    ];
public function incomes()
{
    return $this->hasMany(InvestmentIncome::class);
}
    }
