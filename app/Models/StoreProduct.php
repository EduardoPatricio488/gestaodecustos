<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreProduct extends Model
{
    protected $fillable = [
        'title', 'slug', 'type', 'description', 'long_content', 'price', 'image', 'badge', 'features',
        'video_url', 'screenshots', 'roadmap', 'faq', 'learning_outcomes', 'course_modules', 'related_products',
        'sales_count', 'rating_avg', 'rating_count', 'is_featured', 'download_path', 'points_reward',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'screenshots' => 'array',
        'roadmap' => 'array',
        'faq' => 'array',
        'learning_outcomes' => 'array',
        'course_modules' => 'array',
        'related_products' => 'array',
        'rating_avg' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(StorePurchase::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(StoreReview::class, 'product_id');
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_wishlists')->withTimestamps();
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(StoreBundle::class, 'store_bundle_products', 'product_id', 'bundle_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->type) {
            'ia' => 'Extensão IA',
            'widget' => 'Widget',
            'automation' => 'Automação',
            'data' => 'Dados PRO',
            'course' => 'Curso PDF + Vídeos',
            'guide' => 'Guia PDF',
            'pack' => 'Pack Digital',
            'plan' => 'Plano',
            default => ucfirst($this->type),
        };
    }

    public function getHighlightAttribute(): string
    {
        return match ($this->type) {
            'plan' => 'Subscrição mensal • Cancela quando quiseres',
            'course' => 'Curso completo • Vídeos YouTube + PDFs por módulo',
            'guide' => 'Guia PDF • Leitura online e download',
            default => 'Download imediato • Acesso vitalício',
        };
    }

    public function relatedProductsList()
    {
        if (empty($this->related_products)) {
            return collect();
        }

        return static::whereIn('id', $this->related_products)->get();
    }
}
