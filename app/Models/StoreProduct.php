<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreProduct extends Model
{
    protected $fillable = [
        'name', 'title', 'slug', 'type', 'audience', 'description', 'long_content', 'price', 'image', 'badge', 'features',
        'objectives', 'compatibility', 'integration_type', 'integration_route', 'integration_label', 'integration_location',
        'entitlement_key', 'delivery_type', 'sort_order', 'video_url', 'screenshots', 'roadmap', 'faq', 'learning_outcomes',
        'course_modules', 'related_products', 'sales_count', 'rating_avg', 'rating_count', 'is_featured', 'requires_business_plan',
        'download_path', 'points_reward',
    ];

    protected $casts = [
        'price' => 'decimal:2', 'features' => 'array', 'objectives' => 'array', 'compatibility' => 'array',
        'screenshots' => 'array', 'roadmap' => 'array', 'faq' => 'array', 'learning_outcomes' => 'array',
        'course_modules' => 'array', 'related_products' => 'array', 'rating_avg' => 'decimal:2',
        'is_featured' => 'boolean', 'requires_business_plan' => 'boolean',
    ];

    public function purchases(): HasMany { return $this->hasMany(StorePurchase::class, 'product_id'); }
    public function reviews(): HasMany { return $this->hasMany(StoreReview::class, 'product_id'); }
    public function entitlements(): HasMany { return $this->hasMany(StoreProductEntitlement::class, 'product_id'); }
    public function wishlistedBy(): BelongsToMany { return $this->belongsToMany(User::class, 'store_wishlists')->withTimestamps(); }
    public function bundles(): BelongsToMany { return $this->belongsToMany(StoreBundle::class, 'store_bundle_products', 'product_id', 'bundle_id'); }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->type) {
            'ia' => 'Extensão IA', 'widget' => 'Widget', 'automation' => 'Automação', 'data' => 'Dados PRO',
            'course' => 'Curso', 'guide' => 'Guia', 'template' => 'Template', 'pack' => 'Pack Digital',
            'plan' => 'Plano', default => ucfirst($this->type),
        };
    }

    public function getHighlightAttribute(): string
    {
        return match ($this->type) {
            'plan' => 'Subscrição mensal • Cancela quando quiseres',
            'course' => 'Curso completo • Conteúdo por módulos',
            'guide' => 'Guia • Leitura online e download',
            'template' => 'Template • Pronto a utilizar',
            'pack' => 'Pack • Vários recursos num só produto',
            default => $this->delivery_type === 'feature' ? 'Ativação direta dentro do Finance Pro AI' : 'Acesso imediato após a compra',
        };
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->audience ?? 'both') {
            'personal' => 'Uso pessoal', 'business' => 'Negócios', default => 'Pessoal + Negócios',
        };
    }

    public function getDeliveryLabelAttribute(): string
    {
        return match ($this->delivery_type ?? 'feature') {
            'download' => 'Download imediato', 'course' => 'Conteúdo online', 'hybrid' => 'Funcionalidade + conteúdo',
            default => 'Ativação na aplicação',
        };
    }

    public function relatedProductsList()
    {
        if (empty($this->related_products)) return collect();
        return static::whereIn('id', $this->related_products)->get();
    }
}
