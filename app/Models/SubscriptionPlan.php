<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    public const CORE_SLUGS = ['pro', 'business'];

    public const FEATURE_LABELS = [
        'ia_access' => 'IA Pilot',
        'business_mode' => 'Área Empresa',
        'inventory' => 'Inventário',
        'advanced_reports' => 'Wrapped Pro',
        'ads_free' => 'Sem Ads',
        'priority_support' => 'Suporte VIP',
    ];

    protected $fillable = ['name', 'slug', 'price', 'color', 'description', 'features', 'stripe_price_id', 'is_active'];

    protected $casts = [
        'features' => 'array',
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    public function featureKeys(): array
    {
        return array_values(array_filter((array) $this->features));
    }

    public function hasFeature(string $key): bool
    {
        return in_array($key, $this->featureKeys(), true);
    }

    public function isCore(): bool
    {
        return in_array($this->slug, self::CORE_SLUGS, true);
    }

    public function resolvedStripePriceId(): ?string
    {
        $stored = trim((string) $this->stripe_price_id);

        if ($stored !== '') {
            $configKey = str_starts_with(strtoupper($stored), 'STRIPE_PRICE_')
                ? strtolower(substr($stored, strlen('STRIPE_PRICE_')))
                : $stored;
            $fromConfig = config("services.stripe.prices.{$configKey}");

            if ($fromConfig) {
                return (string) $fromConfig;
            }
        }

        $fromConfig = config("services.stripe.prices.{$this->slug}");

        if ($fromConfig) {
            return (string) $fromConfig;
        }

        return $stored !== '' ? $stored : null;
    }

    public function subscriberCount(): int
    {
        return User::where('plan', $this->slug)->count();
    }
}
