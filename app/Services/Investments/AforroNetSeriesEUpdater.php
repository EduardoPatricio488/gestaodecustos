<?php

namespace App\Services\Investments;

use App\Models\Investment;
use App\Models\InvestmentIncome;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AforroNetSeriesEUpdater
{
    private const RATES_URL = 'https://igcp-aforro.primor.me/rates.json';
    private const IRS_RATE = 0.28;

    /**
     * Atualiza apenas capitalizações trimestrais já vencidas.
     * A fonte de taxas é um snapshot comunitário que documenta a reprodução
     * das regras do IGCP; se estiver indisponível ou desatualizado, não altera saldos.
     */
    public function run(): array
    {
        [$rateByMonth, $generatedAt] = $this->loadRates();
        $processed = 0;
        $skipped = 0;

        Investment::query()
            ->where('broker', 'AforroNet')
            ->where('series', 'E')
            ->whereNotNull('subscription_number')
            ->whereNotNull('operation_date')
            ->whereNotNull('capitalization_date')
            ->orderBy('id')
            ->chunkById(100, function ($investments) use ($rateByMonth, &$processed, &$skipped) {
                foreach ($investments as $investment) {
                    $result = $this->capitalizeIfDue($investment, $rateByMonth);
                    $result ? $processed++ : $skipped++;
                }
            });

        Log::info('AforroNet Série E automatic valuation completed', [
            'processed' => $processed,
            'skipped' => $skipped,
            'rates_generated_at' => $generatedAt,
        ]);

        return ['processed' => $processed, 'skipped' => $skipped, 'generated_at' => $generatedAt];
    }

    public function runForWorkspace(Workspace $workspace, User $user): array
    {
        $investments = Investment::query()
            ->where('workspace_id', $workspace->id)
            ->when($workspace->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
            ->where('broker', 'AforroNet')
            ->where('series', 'E')
            ->where('type', 'Divida')
            ->get();
        $current = 0;
        $updated = 0;
        $partial = 0;
        $unavailable = 0;
        $due = [];

        foreach ($investments as $investment) {
            if ($investment->product_type !== 'CA' || ! $investment->subscription_number || ! $investment->operation_date || ! $investment->valuation_date
                || ! $investment->capitalization_date || $investment->quantity <= 0 || $investment->current_price <= 0
                || $investment->operation_date->gt($investment->valuation_date)
                || $investment->valuation_date->gte($investment->capitalization_date)) {
                $unavailable++;
            } elseif ($investment->capitalization_date->startOfDay()->gt(now()->startOfDay())) {
                $current++;
            } else {
                $due[] = $investment;
            }
        }

        if ($due === []) {
            return ['updated' => $updated, 'current' => $current, 'partial' => $partial, 'unavailable' => $unavailable];
        }

        try {
            [$rateByMonth] = $this->loadRates();
        } catch (ConnectionException|RequestException|RuntimeException|InvalidFormatException $exception) {
            Log::warning('AforroNet Série E rates unavailable', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return ['updated' => $updated, 'current' => $current, 'partial' => $partial, 'unavailable' => $unavailable + count($due)];
        }

        foreach ($due as $investment) {
            $changed = false;
            while ($investment->capitalization_date && $investment->capitalization_date->startOfDay()->lte(now()->startOfDay())) {
                $before = $investment->capitalization_date->toDateString();
                $changed = $this->capitalizeIfDue($investment, $rateByMonth) || $changed;
                $investment->refresh();
                if (! $investment->capitalization_date || $investment->capitalization_date->toDateString() <= $before) {
                    break;
                }
            }
            if (! $investment->capitalization_date || $investment->capitalization_date->startOfDay()->lte(now()->startOfDay())) {
                $changed ? $partial++ : $unavailable++;
            } elseif ($changed) {
                $updated++;
            } else {
                $current++;
            }
        }

        return ['updated' => $updated, 'current' => $current, 'partial' => $partial, 'unavailable' => $unavailable];
    }

    private function loadRates(): array
    {
        $response = Http::acceptJson()->timeout(25)->retry(2, 500)->get(self::RATES_URL);
        if (! $response->successful()) {
            throw new RuntimeException('Não foi possível obter o histórico de taxas da Série E.');
        }

        $rates = $response->json();
        $generatedAt = data_get($rates, 'generatedAt');
        $monthlyRates = data_get($rates, 'series.E.monthlyBaseRates');

        if (! is_array($monthlyRates) || ! is_string($generatedAt) || trim($generatedAt) === '') {
            throw new RuntimeException('O ficheiro de taxas não tem o formato esperado.');
        }

        $snapshotDate = Carbon::parse($generatedAt);
        if ($snapshotDate->lt(now()->subDays(60))) {
            throw new RuntimeException('O histórico de taxas está desatualizado; atualização automática suspensa por segurança.');
        }

        return [collect($monthlyRates)->keyBy('month'), $generatedAt];
    }

    private function capitalizeIfDue(Investment $investment, $rateByMonth): bool
    {
        return DB::transaction(function () use ($investment, $rateByMonth) {
            $locked = Investment::query()->lockForUpdate()->find($investment->id);
            if (! $locked || ! $locked->capitalization_date || $locked->capitalization_date->isFuture()) {
                return false;
            }

            $dueDate = Carbon::parse($locked->capitalization_date)->startOfDay();
            $quarterStart = $dueDate->copy()->subMonthsNoOverflow(3);
            $monthKey = $quarterStart->format('Y-m');
            $rateRow = $rateByMonth->get($monthKey);

            // Não inventar uma taxa quando a fonte ainda não publicou o mês.
            if (! $rateRow || ! is_numeric($rateRow['basePct'] ?? null) || (float) $rateRow['basePct'] < 0) {
                return false;
            }

            $alreadyRecorded = InvestmentIncome::query()
                ->where('investment_id', $locked->id)
                ->whereDate('reference_date', $dueDate->toDateString())
                ->where('type', 'CA')
                ->exists();

            if ($alreadyRecorded) {
                $locked->update([
                    'capitalization_date' => $dueDate->copy()->addMonthsNoOverflow(3)->toDateString(),
                ]);
                return false;
            }

            $subscriptionDate = Carbon::parse($locked->operation_date);
            $completedYears = $subscriptionDate->diffInYears($quarterStart);
            $premium = $completedYears >= 5 ? 1.0 : ($completedYears >= 1 ? 0.5 : 0.0);
            $baseRate = (float) $rateRow['basePct'];
            $annualRate = $baseRate + $premium;

            $units = (float) $locked->quantity;
            $capitalBefore = round($units * (float) $locked->current_price, 2);
            if ($units <= 0 || $capitalBefore <= 0) {
                return false;
            }

            $gross = round($capitalBefore * ($annualRate / 100) / 4, 2);
            $tax = round($gross * self::IRS_RATE, 2);
            $net = $gross - $tax;
            $capitalAfter = $capitalBefore + $net;

            InvestmentIncome::create([
                'investment_id' => $locked->id,
                'user_id' => $locked->user_id,
                'workspace_id' => $locked->workspace_id,
                'reference_date' => $dueDate->toDateString(),
                'gross_amount' => $gross,
                'tax_amount' => $tax,
                'net_amount' => $net,
                'base_rate' => $baseRate,
                'loyalty_bonus' => $premium,
                'capital_before' => $capitalBefore,
                'capital_after' => $capitalAfter,
                'type' => 'CA',
            ]);

            $locked->update([
                'current_price' => $capitalAfter / $units,
                'valuation_date' => $dueDate->toDateString(),
                'capitalization_date' => $dueDate->copy()->addMonthsNoOverflow(3)->toDateString(),
                'interest_rate' => $baseRate,
                'loyalty_bonus' => $premium,
            ]);

            return true;
        }, 3);
    }
}
