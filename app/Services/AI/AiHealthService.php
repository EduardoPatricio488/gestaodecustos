<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

final class AiHealthService
{
    public function status(): array
    {
        $provider = (string) config('services.openrouter.key') ? 'openrouter' : ((string) config('services.openai.key') ? 'openai' : 'unconfigured');

        if ($provider === 'unconfigured') {
            return ['status' => 'unavailable', 'provider' => null, 'reason' => 'provider_not_configured'];
        }

        return Cache::remember('ai:health:status', now()->addSeconds(30), function () use ($provider) {
            try {
                // Do not perform a model call here. Health checks must be cheap and non-billable.
                return [
                    'status' => 'configured',
                    'provider' => $provider,
                    'checked_at' => now()->toIso8601String(),
                ];
            } catch (Throwable $e) {
                report($e);
                return ['status' => 'degraded', 'provider' => $provider, 'reason' => 'health_check_failed'];
            }
        });
    }

    public function failureCode(Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        return match (true) {
            str_contains($message, 'timeout') => 'timeout',
            str_contains($message, 'rate limit'), str_contains($message, '429') => 'rate_limited',
            str_contains($message, 'quota') => 'quota_exceeded',
            default => 'provider_error',
        };
    }
}
