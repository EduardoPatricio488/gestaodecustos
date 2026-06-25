<?php

namespace App\Services;

use App\Models\ConnectedDevice;
use Illuminate\Support\Facades\{Auth, Http, Log};

class StravaService
{
    private const BASE_URL  = 'https://www.strava.com/api/v3';
    private const TOKEN_URL = 'https://www.strava.com/oauth/token';

    // ─── OAuth ────────────────────────────────────────────────────────────────

    public function authUrl(): string
    {
        $params = http_build_query([
            'client_id'     => config('services.strava.client_id'),
            'redirect_uri'  => url(config('services.strava.redirect_uri')),
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope'         => 'read,activity:read_all,profile:read_all',
        ]);

        return "https://www.strava.com/oauth/authorize?{$params}";
    }

    public function exchangeCode(string $code): array
    {
        $response = Http::post(self::TOKEN_URL, [
            'client_id'     => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ]);

        return $response->json();
    }

    // ─── Token ────────────────────────────────────────────────────────────────

    public function getDevice(): ?ConnectedDevice
    {
        return ConnectedDevice::where('user_id', Auth::id())
            ->where('provider', 'strava')
            ->where('is_active', true)
            ->first();
    }

    public function refreshTokenIfNeeded(ConnectedDevice $device): ConnectedDevice
    {
        if ($device->token_expires_at && $device->token_expires_at->isFuture()) {
            return $device;
        }

        $response = Http::post(self::TOKEN_URL, [
            'client_id'     => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'grant_type'    => 'refresh_token',
            'refresh_token' => $device->refresh_token,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $device->update([
                'access_token'     => $data['access_token'],
                'refresh_token'    => $data['refresh_token'],
                'token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);
        }

        return $device->fresh();
    }

    // ─── API ──────────────────────────────────────────────────────────────────

    public function athlete(ConnectedDevice $device): array
    {
        $device = $this->refreshTokenIfNeeded($device);

        $response = Http::withToken($device->access_token)
            ->get(self::BASE_URL . '/athlete');

        return $response->successful() ? $response->json() : [];
    }

    public function athleteStats(ConnectedDevice $device, int $athleteId): array
    {
        $device = $this->refreshTokenIfNeeded($device);

        $response = Http::withToken($device->access_token)
            ->get(self::BASE_URL . "/athletes/{$athleteId}/stats");

        return $response->successful() ? $response->json() : [];
    }

    public function activities(ConnectedDevice $device, int $perPage = 20, int $page = 1): array
    {
        $device = $this->refreshTokenIfNeeded($device);

        $response = Http::withToken($device->access_token)
            ->get(self::BASE_URL . '/athlete/activities', [
                'per_page' => $perPage,
                'page'     => $page,
            ]);

        return $response->successful() ? $response->json() : [];
    }

    public function activity(ConnectedDevice $device, int $id): array
    {
        $device = $this->refreshTokenIfNeeded($device);

        $response = Http::withToken($device->access_token)
            ->get(self::BASE_URL . "/activities/{$id}");

        return $response->successful() ? $response->json() : [];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function formatPace(float $metersPerSecond): string
    {
        if ($metersPerSecond <= 0) return '--';
        $secsPerKm = 1000 / $metersPerSecond;
        $mins = (int) floor($secsPerKm / 60);
        $secs = (int) ($secsPerKm % 60);
        return sprintf('%d:%02d', $mins, $secs);
    }

    public function formatDuration(int $seconds): string
    {
        $h = (int) floor($seconds / 3600);
        $m = (int) floor(($seconds % 3600) / 60);
        $s = $seconds % 60;

        if ($h > 0) return sprintf('%dh %02dm', $h, $m);
        return sprintf('%dm %02ds', $m, $s);
    }

    public function sportTypeIcon(string $type): string
    {
        $map = [
            'run'           => '🏃',
            'virtualrun'    => '🏃',
            'trailrun'      => '🏃',
            'ride'          => '🚴',
            'virtualride'   => '🚴',
            'ebikeride'     => '🚴',
            'swim'          => '🏊',
            'walk'          => '🚶',
            'hike'          => '🚶',
            'yoga'          => '🧘',
            'workout'       => '🏋',
            'weighttraining'=> '🏋',
        ];
        return $map[strtolower($type)] ?? '⚡';
    }

    public function sportTypePt(string $type): string
    {
        $map = [
            'run'            => 'Corrida',
            'virtualrun'     => 'Corrida',
            'trailrun'       => 'Corrida',
            'ride'           => 'Ciclismo',
            'virtualride'    => 'Ciclismo',
            'ebikeride'      => 'Ciclismo',
            'swim'           => 'Natacao',
            'walk'           => 'Caminhada',
            'hike'           => 'Caminhada',
            'yoga'           => 'Yoga',
            'workout'        => 'Ginasio',
            'weighttraining' => 'Ginasio',
        ];
        return $map[strtolower($type)] ?? ucfirst($type);
    }
}
