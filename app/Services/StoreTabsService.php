<?php

namespace App\Services;

use App\Models\SiteSetting;

class StoreTabsService
{
    public const DEFAULT_TABS = [
        ['key' => 'all', 'label' => 'Todos', 'visible' => true, 'order' => 0],
        ['key' => 'ia', 'label' => 'IA', 'visible' => true, 'order' => 1],
        ['key' => 'widget', 'label' => 'Widgets', 'visible' => true, 'order' => 2],
        ['key' => 'automation', 'label' => 'Automação', 'visible' => true, 'order' => 3],
        ['key' => 'data', 'label' => 'Dados PRO', 'visible' => true, 'order' => 4],
        ['key' => 'course', 'label' => 'Cursos', 'visible' => true, 'order' => 5],
        ['key' => 'guide', 'label' => 'Guias', 'visible' => true, 'order' => 6],
        ['key' => 'pack', 'label' => 'Packs', 'visible' => true, 'order' => 7],
        ['key' => 'plan', 'label' => 'Planos', 'visible' => true, 'order' => 8],
    ];

    public function all(): array
    {
        $raw = SiteSetting::get('store_tabs');

        if (! $raw) {
            return self::DEFAULT_TABS;
        }

        $tabs = json_decode($raw, true);

        if (! is_array($tabs) || $tabs === []) {
            return self::DEFAULT_TABS;
        }

        return collect($tabs)->sortBy('order')->values()->all();
    }

    public function visible(): array
    {
        return collect($this->all())
            ->filter(fn (array $tab) => (bool) ($tab['visible'] ?? true))
            ->values()
            ->all();
    }

    public function save(array $tabs): void
    {
        $normalized = collect($tabs)
            ->values()
            ->map(fn (array $tab, int $index) => [
                'key' => $tab['key'],
                'label' => $tab['label'],
                'visible' => (bool) ($tab['visible'] ?? true),
                'order' => (int) ($tab['order'] ?? $index),
            ])
            ->sortBy('order')
            ->values()
            ->all();

        SiteSetting::set('store_tabs', json_encode($normalized));
        app(StoreCatalogService::class)->clearCache();
    }

    public function reset(): void
    {
        SiteSetting::set('store_tabs', json_encode(self::DEFAULT_TABS));
        app(StoreCatalogService::class)->clearCache();
    }
}
