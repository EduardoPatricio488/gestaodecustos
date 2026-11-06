<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPortfolioAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'analysis',
        'snapshot',
        'model',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];
}
