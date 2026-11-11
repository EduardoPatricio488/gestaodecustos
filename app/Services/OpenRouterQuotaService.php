<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenRouterQuotaService
{
    public function dailyQuota(bool $forceRefresh = false): array
    {
        $apiKey = config('services.openrouter.api_key');
        $empty = [
            'available' => false,
            'used' => null,
            'limit' => null,
            'remaining' => null,
            'percent' => null,
            'resets_at' => now('UTC')->endOfDay()->toIso8601String(),
            'error' => null,
        ];

        if (blank($apiKey)) {
            $empty['error'] = 'Chave OpenRouter não configurada.';
            return $empty;
        }

        $cacheKey = 'openrouter.daily_quota.'.hash('sha256', $apiKey);
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addSeconds(15), function () use ($apiKey, $empty) {
            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->connectTimeout(4)
                    ->timeout(8)
                    ->get('https://openrouter.ai/api/v1/key');

                if (! $response->successful()) {
                    $empty['error'] = 'Não foi possível consultar a quota no OpenRouter.';
                    return $empty;
                }

                $quota = $response->json('data.free_model_daily_requests');
                if (! is_array($quota) || ! isset($quota['used'], $quota['limit'], $quota['remaining'])) {
                    $empty['error'] = 'O OpenRouter não devolveu os dados da quota diária.';
                    return $empty;
                }

                $limit = max(0, (int) $quota['limit']);
                $used = max(0, (int) $quota['used']);
                $remaining = max(0, (int) $quota['remaining']);

                return [
                    'available' => true,
                    'used' => $used,
                    'limit' => $limit,
                    'remaining' => $remaining,
                    'percent' => $limit > 0 ? min(100, (int) round(($used / $limit) * 100)) : 0,
                    'resets_at' => now('UTC')->endOfDay()->toIso8601String(),
                    'error' => null,
                ];
            } catch (\Throwable $exception) {
                report($exception);
                $empty['error'] = 'Não foi possível consultar a quota diária neste momento.';
                return $empty;
            }
        });
    }
}
