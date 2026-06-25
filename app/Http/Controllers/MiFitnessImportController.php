<?php

namespace App\Http\Controllers;

use App\Models\ConnectedDevice;
use App\Models\FitnessActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;

class MiFitnessImportController extends Controller
{
    /**
     * Importa ficheiro TCX ou CSV exportado da app Mi Fitness / Zepp Life.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:xml,tcx,csv,txt'],
        ]);

        $file      = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $content   = file_get_contents($file->getRealPath());
        $imported  = 0;
        $errors    = [];

        try {
            if (in_array($extension, ['xml', 'tcx'])) {
                $imported = $this->parseTcx($content);
            } elseif ($extension === 'csv') {
                $imported = $this->parseCsv($content);
            } else {
                return response()->json(['error' => 'Formato não suportado. Usa TCX ou CSV.'], 422);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro ao processar ficheiro: ' . $e->getMessage()], 422);
        }

        return response()->json([
            'success'  => true,
            'imported' => $imported,
            'message'  => "Importados {$imported} treinos com sucesso.",
        ]);
    }

    // ─── TCX (Training Center XML) ────────────────────────────────────────────
    private function parseTcx(string $xml): int
    {
        $imported  = 0;
        $userId    = Auth::id();
        $workspaceId = Auth::user()->current_workspace_id ?? null;

        libxml_use_internal_errors(true);
        $doc = new SimpleXMLElement($xml, LIBXML_NOCDATA);
        $doc->registerXPathNamespace('ns', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
        $doc->registerXPathNamespace('ns3', 'http://www.garmin.com/xmlschemas/ActivityExtension/v2');

        $activities = $doc->xpath('//ns:Activity') ?: $doc->xpath('//Activity') ?: [];

        foreach ($activities as $act) {
            $attrs = $act->attributes();
            $sport = strtolower((string)($attrs['Sport'] ?? 'other'));

            $type = match(true) {
                str_contains($sport, 'run')    => 'corrida',
                str_contains($sport, 'cycl')   => 'ciclismo',
                str_contains($sport, 'swim')   => 'natacao',
                str_contains($sport, 'walk')   => 'caminhada',
                str_contains($sport, 'hike')   => 'caminhada',
                default                        => 'ginasio',
            };

            // Data
            $dateStr = (string)($act->Id ?? $act->xpath('.//ns:Id')[0] ?? '');
            $date    = $dateStr ? now()->parse($dateStr)->format('Y-m-d') : now()->format('Y-m-d');

            // Lap totals
            $laps          = $act->xpath('.//ns:Lap') ?: $act->xpath('.//Lap') ?: [];
            $totalMeters   = 0;
            $totalSeconds  = 0;
            $totalCalories = 0;
            $maxHr         = 0;
            $avgHrSum      = 0;
            $lapCount      = 0;

            foreach ($laps as $lap) {
                $totalMeters   += (float)($lap->DistanceMeters ?? 0);
                $totalSeconds  += (float)($lap->TotalTimeSeconds ?? 0);
                $totalCalories += (int)($lap->Calories ?? 0);
                $lapMaxHr       = (int)($lap->xpath('.//ns:MaximumHeartRateBpm/ns:Value')[0] ?? $lap->xpath('.//MaximumHeartRateBpm/Value')[0] ?? 0);
                $lapAvgHr       = (int)($lap->xpath('.//ns:AverageHeartRateBpm/ns:Value')[0] ?? $lap->xpath('.//AverageHeartRateBpm/Value')[0] ?? 0);

                if ($lapMaxHr > $maxHr) $maxHr = $lapMaxHr;
                if ($lapAvgHr > 0) { $avgHrSum += $lapAvgHr; $lapCount++; }
            }

            $distanceKm  = round($totalMeters / 1000, 2);
            $durationMin = (int)round($totalSeconds / 60);
            $avgHr       = $lapCount > 0 ? (int)round($avgHrSum / $lapCount) : null;

            // Evitar duplicados (mesma data + tipo + duração)
            $exists = FitnessActivity::where('user_id', $userId)
                ->where('activity_date', $date)
                ->where('type', $type)
                ->where('duration_minutes', $durationMin)
                ->exists();

            if ($exists) continue;

            FitnessActivity::create([
                'user_id'          => $userId,
                'workspace_id'     => $workspaceId,
                'type'             => $type,
                'activity_date'    => $date,
                'distance_km'      => $distanceKm > 0 ? $distanceKm : null,
                'duration_minutes' => $durationMin,
                'calories'         => $totalCalories ?: null,
                'hr_avg'           => $avgHr,
                'hr_max'           => $maxHr ?: null,
            ]);

            $imported++;
        }

        return $imported;
    }

    // ─── CSV (Mi Fitness Export) ──────────────────────────────────────────────
    private function parseCsv(string $csv): int
    {
        $imported    = 0;
        $userId      = Auth::id();
        $workspaceId = Auth::user()->current_workspace_id ?? null;
        $lines       = array_filter(explode("\n", trim($csv)));
        $headers     = null;

        foreach ($lines as $i => $line) {
            $row = str_getcsv($line);

            if ($i === 0) {
                $headers = array_map('strtolower', array_map('trim', $row));
                continue;
            }

            if (!$headers || count($row) < 3) continue;
            $data = array_combine($headers, array_pad($row, count($headers), ''));

            $sport = strtolower(trim($data['sport'] ?? $data['type'] ?? $data['activity'] ?? ''));
            $type  = match(true) {
                str_contains($sport, 'run')    => 'corrida',
                str_contains($sport, 'cycl')   => 'ciclismo',
                str_contains($sport, 'swim')   => 'natacao',
                str_contains($sport, 'walk')   => 'caminhada',
                default                        => 'ginasio',
            };

            $dateRaw = trim($data['date'] ?? $data['start time'] ?? $data['start_time'] ?? '');
            $date    = $dateRaw ? now()->parse($dateRaw)->format('Y-m-d') : now()->format('Y-m-d');

            $duration  = (int)round((float)($data['duration (min)'] ?? $data['duration'] ?? $data['duration_min'] ?? 0));
            $distance  = round((float)($data['distance (km)'] ?? $data['distance'] ?? $data['distance_km'] ?? 0), 2);
            $calories  = (int)($data['calories (kcal)'] ?? $data['calories'] ?? $data['calorie'] ?? 0);
            $avgHr     = (int)($data['avg heart rate'] ?? $data['avg_hr'] ?? $data['heart_rate_avg'] ?? 0);
            $steps     = (int)($data['steps'] ?? 0);

            if ($duration < 1) continue;

            $exists = FitnessActivity::where('user_id', $userId)
                ->where('activity_date', $date)
                ->where('type', $type)
                ->where('duration_minutes', $duration)
                ->exists();

            if ($exists) continue;

            FitnessActivity::create([
                'user_id'          => $userId,
                'workspace_id'     => $workspaceId,
                'type'             => $type,
                'activity_date'    => $date,
                'distance_km'      => $distance > 0 ? $distance : null,
                'duration_minutes' => $duration,
                'calories'         => $calories ?: null,
                'hr_avg'           => $avgHr ?: null,
                'steps'            => $steps ?: null,
            ]);

            $imported++;
        }

        return $imported;
    }
}
