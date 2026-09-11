<?php

namespace App\Services\AI;

use App\Models\Workspace;

final class AiBriefingService
{
    public function daily(Workspace $workspace): array
    {
        return $this->build($workspace, 'daily');
    }

    public function weekly(Workspace $workspace): array
    {
        return $this->build($workspace, 'weekly');
    }

    public function monthly(Workspace $workspace): array
    {
        return $this->build($workspace, 'monthly');
    }

    private function build(Workspace $workspace, string $frequency): array
    {
        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $items = [];

        if (($snapshot['income'] ?? 0) > 0) {
            $items[] = [
                'type' => 'metric',
                'title' => 'Receitas',
                'value' => $snapshot['income'],
                'currency' => $snapshot['currency'] ?? 'EUR',
            ];
        }

        if (($snapshot['expenses'] ?? 0) > 0) {
            $items[] = [
                'type' => 'metric',
                'title' => 'Despesas',
                'value' => $snapshot['expenses'],
                'currency' => $snapshot['currency'] ?? 'EUR',
            ];
        }

        if (array_key_exists('savings_rate', $snapshot)) {
            $items[] = [
                'type' => 'metric',
                'title' => 'Taxa de poupança',
                'value' => $snapshot['savings_rate'],
                'unit' => '%',
            ];
        }

        if (! empty($snapshot['changes'])) {
            foreach (['expenses_percent' => 'Variação das despesas', 'income_percent' => 'Variação das receitas'] as $key => $label) {
                if (array_key_exists($key, $snapshot['changes'])) {
                    $items[] = ['type' => 'change', 'title' => $label, 'value' => $snapshot['changes'][$key], 'unit' => '%'];
                }
            }
        }

        if (($snapshot['metrics']['overdue_receivables'] ?? 0) > 0) {
            $items[] = [
                'type' => 'risk',
                'title' => 'Recebimentos vencidos',
                'value' => $snapshot['metrics']['overdue_receivables'],
                'currency' => $snapshot['currency'] ?? 'EUR',
            ];
        }

        return [
            'frequency' => $frequency,
            'workspace_id' => $workspace->id,
            'workspace_type' => $workspace->type,
            'period' => $snapshot['period'] ?? now()->format('Y-m'),
            'generated_at' => now()->toIso8601String(),
            'items' => $items,
            'has_sufficient_data' => count($items) > 0,
            'source' => 'finance_pro_backend_snapshot',
        ];
    }
}
