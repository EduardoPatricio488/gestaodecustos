<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        return Cache::remember($cacheKey, now()->addSeconds(60), function () use ($apiKey, $empty) {
            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->connectTimeout(4)
                    ->timeout(8)
                    ->get('https://openrouter.ai/api/v1/key');
            } catch (ConnectionException $exception) {
                Log::notice('OpenRouter quota request failed to connect.', ['exception' => $exception::class]);
                $empty['error'] = 'Não foi possível ligar ao OpenRouter. Verifica a ligação e tenta novamente.';
                return $empty;
            }

            if (! $response->successful()) {
                Log::notice('OpenRouter quota request failed.', ['status' => $response->status()]);
                $empty['error'] = match ($response->status()) {
                    401, 403 => 'O OpenRouter recusou a chave configurada. Verifica a chave e as respetivas permissões.',
                    429 => 'O OpenRouter limitou temporariamente a consulta da quota. Aguarda e tenta novamente.',
                    default => 'Não foi possível consultar a quota no OpenRouter.',
                };
                return $empty;
            }

            $quota = $response->json('data.free_model_daily_requests');
            $validCounts = is_array($quota);
            foreach (['used', 'limit', 'remaining'] as $field) {
                $value = is_array($quota) ? ($quota[$field] ?? null) : null;
                if ((! is_int($value) && ! is_string($value))
                    || filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
                    $validCounts = false;
                    break;
                }
            }
            if (! $validCounts) {
                Log::notice('OpenRouter quota response did not contain valid daily request counts.');
                $empty['error'] = 'O OpenRouter não devolveu os dados da quota diária.';
                return $empty;
            }

            $limit = (int) $quota['limit'];
            $used = (int) $quota['used'];
            $remaining = (int) $quota['remaining'];

            return [
                'available' => true,
                'used' => $used,
                'limit' => $limit,
                'remaining' => $remaining,
                'percent' => $limit > 0 ? min(100, (int) round(($used / $limit) * 100)) : 0,
                'resets_at' => now('UTC')->endOfDay()->toIso8601String(),
                'error' => null,
            ];
        });
    }
}
