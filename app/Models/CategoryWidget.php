<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryWidget extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
