<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryWidget extends Model
{
    protected $fillable = [
        'category_id', 'type', 'title', 'config', 'enabled', 'order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
