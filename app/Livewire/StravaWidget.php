<?php

namespace App\Livewire;

use App\Models\ConnectedDevice;
use App\Models\FitnessActivity;
use App\Services\StravaService;
use Illuminate\Support\Facades\{Auth, Cache, Log};
use Livewire\Component;

class StravaWidget extends Component
{
    public bool $isLoading     = false;
    public bool $isSyncing     = false;
    public int  $syncPage      = 1;
    public int  $syncImported  = 0;
    public string $activeTab   = 'atividades'; // atividades | estatisticas | perfil

    public function mount(): void {}

    // ─── AÇÕES ────────────────────────────────────────────────────────────────

    public function refresh(): void
    {
        $this->isLoading = true;
        $device = $this->getDevice();
        if ($device) {
            Cache::forget("strava:activities:{$device->id}");
            Cache::forget("strava:stats:{$device->id}");
            Cache::forget("strava:athlete:{$device->id}");
        }
        $this->isLoading = false;
        $this->dispatch('toast', text: '🔄 Dados Strava actualizados!');
    }

    public function syncToHub(): void
    {
        $device  = $this->getDevice();
        if (!$device) return;

        $this->isSyncing     = true;
        $this->syncImported  = 0;
        $strava  = app(StravaService::class);
        $user    = Auth::user();
        $wsId    = $user->currentWorkspace->id;
        $userId  = $user->id;

        try {
            $activities = $strava->activities($device, 50, 1);

            foreach ($activities as $act) {
                $date     = substr($act['start_date_local'] ?? '', 0, 10);
                $type     = $this->mapType($act['sport_type'] ?? $act['type'] ?? '');
                $duration = (int)round(($act['moving_time'] ?? 0) / 60);

                if ($duration < 1) continue;

                $exists = FitnessActivity::where('user_id', $userId)
                    ->where('activity_date', $date)
                    ->where('type', $type)
                    ->where('duration_minutes', $duration)
                    ->exists();

                if ($exists) continue;

                FitnessActivity::create([
                    'user_id'          => $userId,
                    'workspace_id'     => $wsId,
                    'type'             => $type,
                    'activity_date'    => $date,
                    'distance_km'      => round(($act['distance'] ?? 0) / 1000, 2) ?: null,
                    'duration_minutes' => $duration,
                    'calories'         => ($act['calories'] ?? null) ?: null,
                    'hr_avg'           => ($act['average_heartrate'] ?? null) ?: null,
                    'hr_max'           => ($act['max_heartrate'] ?? null) ?: null,
                    'steps'            => null,
                    'cadence'          => $act['average_cadence'] ?? null,
                    'pace'             => $strava->formatPace($act['average_speed'] ?? 0) !== '--'
                                            ? $strava->formatPace($act['average_speed'] ?? 0)
                                            : null,
                    'training_load'    => ($act['suffer_score'] ?? null) ?: null,
                ]);

                $this->syncImported++;
            }

            $device->update(['last_synced_at' => now()]);
            Cache::forget("fitness:stats:{$wsId}:{$userId}");
            $this->dispatch('toast', text: "✅ {$this->syncImported} atividades importadas do Strava!");
        } catch (\Throwable $e) {
            Log::error('Strava sync error: ' . $e->getMessage());
            $this->dispatch('toast', text: '⚠️ Erro ao sincronizar com o Strava.');
        }

        $this->isSyncing = false;
    }

    // ─── HELPERS ──────────────────────────────────────────────────────────────

    private function getDevice(): ?ConnectedDevice
    {
        return ConnectedDevice::where('user_id', Auth::id())
            ->where('provider', 'strava')
            ->where('is_active', true)
            ->first();
    }

    private function mapType(string $type): string
    {
        return match(strtolower($type)) {
            'run', 'virtualrun', 'trailrun'    => 'corrida',
            'ride', 'virtualride', 'ebikeride' => 'ciclismo',
            'swim'                             => 'natacao',
            'walk', 'hike'                     => 'caminhada',
            'yoga'                             => 'yoga',
            default                            => 'ginasio',
        };
    }

    // ─── RENDER ───────────────────────────────────────────────────────────────

    public function render()
    {
        $device   = $this->getDevice();
        $strava   = app(StravaService::class);
        $athlete  = [];
        $stats    = [];
        $activities = [];

        if ($device) {
            try {
                $athlete = Cache::remember("strava:athlete:{$device->id}", 300, fn() =>
                    $strava->athlete($device)
                );

                if (!empty($athlete['id'])) {
                    $stats = Cache::remember("strava:stats:{$device->id}", 300, fn() =>
                        $strava->athleteStats($device, $athlete['id'])
                    );
                }

                $activities = Cache::remember("strava:activities:{$device->id}", 120, fn() =>
                    $strava->activities($device, 15)
                );
            } catch (\Throwable $e) {
                Log::error('Strava render error: ' . $e->getMessage());
            }
        }

        return view('livewire.strava-widget', compact('device', 'athlete', 'stats', 'activities', 'strava'));
    }
}
