<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryField extends Model
{
    protected $fillable = [
        'category_id', 'label', 'key', 'type', 'options', 'required', 'order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
