<?php

namespace App\Services;

use App\Models\StoreProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StoreRecommendationService
{
    public function crossSell(Collection $cartProducts): Collection
    {
        $types = $cartProducts->pluck('type')->unique();
        $excludeIds = $cartProducts->pluck('id')->all();

        $suggestions = collect();

        if ($types->contains('ia')) {
            $suggestions = $suggestions->merge(
                StoreProduct::where('type', 'widget')->whereNotIn('id', $excludeIds)->orderByDesc('sales_count')->limit(2)->get()
            );
        }

        if ($types->contains('widget')) {
            $suggestions = $suggestions->merge(
                StoreProduct::where('type', 'data')->whereNotIn('id', $excludeIds)->orderByDesc('sales_count')->limit(2)->get()
            );
        }

        $related = $cartProducts->flatMap(fn (StoreProduct $p) => $p->relatedProductsList());

        return $suggestions->merge($related)
            ->unique('id')
            ->whereNotIn('id', $excludeIds)
            ->take(4)
            ->values();
    }

    public function upsell(Collection $cartProducts): Collection
    {
        $avgPrice = $cartProducts->avg('price') ?? 0;

        return StoreProduct::where('price', '>', $avgPrice)
            ->whereNotIn('id', $cartProducts->pluck('id'))
            ->orderByDesc('rating_avg')
            ->limit(3)
            ->get();
    }

    public function aiExplainProduct(StoreProduct $product): string
    {
        $user = Auth::user();

        $profile = match (true) {
            $user && $user->isAnyPremium() => 'utilizador PRO com necessidades avançadas',
            $user && $user->workspaces()->exists() => 'gestor empresarial',
            default => 'utilizador que quer organizar finanças pessoais',
        };

        return match ($product->type) {
            'ia' => "Ideal para {$profile}: esta extensão de IA automatiza análises e poupa horas de trabalho manual.",
            'widget' => "Perfeito para {$profile}: visualiza dados financeiros em tempo real no teu dashboard.",
            'course' => "Recomendado para {$profile}: aprende passo a passo com conteúdo prático e aplicável.",
            'plan' => "Plano pensado para {$profile}: escala as funcionalidades conforme as tuas necessidades.",
            default => "Excelente escolha para {$profile}: complementa o teu ecossistema financeiro.",
        };
    }
}
