<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentIncome extends Model
{
    protected $fillable = [
        'investment_id', 'user_id', 'workspace_id',
        'reference_date', 'gross_amount', 'tax_amount', 'net_amount',
        'base_rate', 'loyalty_bonus', 'capital_before', 'capital_after', 'type',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'gross_amount'   => 'float',
        'tax_amount'     => 'float',
        'net_amount'     => 'float',
        'base_rate'      => 'float',
        'loyalty_bonus'  => 'float',
        'capital_before' => 'float',
        'capital_after'  => 'float',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
