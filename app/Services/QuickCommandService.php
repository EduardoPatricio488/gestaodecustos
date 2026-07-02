<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class QuickCommandService
{
    public function parse(string $input, User $user): array
    {
        $text = trim(mb_strtolower($input));

        if (preg_match('/(?:adiciona|regista|gasto|despesa)\s+(?:de\s+)?(\d+[,.]?\d*)\s*€?\s*(?:em|em\s+)?(.+)/iu', $text, $m)) {
            return $this->addExpense($user, $m[1], $m[2]);
        }

        if (preg_match('/resumo\s+(?:semanal|da\s+semana)/iu', $text)) {
            return $this->weeklySummary($user);
        }

        if (preg_match('/quanto\s+(?:gastei|gasto)/iu', $text)) {
            return $this->monthlySpent($user);
        }

        if (preg_match('/saldo/iu', $text)) {
            return $this->monthlyBalance($user);
        }

        return [
            'success' => false,
            'message' => 'Comandos: "Adiciona despesa 15€ almoço", "Resumo semanal", "Quanto gastei?", "Saldo"',
        ];
    }

    private function addExpense(User $user, string $amountRaw, string $description): array
    {
        $amount = (float) str_replace(',', '.', $amountRaw);
        $workspace = $user->currentWorkspace;

        if (! $workspace) {
            return ['success' => false, 'message' => 'Nenhum workspace ativo.'];
        }

        $category = $this->guessCategory($description, $workspace->id);

        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category?->id,
            'title' => ucfirst(trim($description)),
            'amount' => $amount,
            'description' => trim($description),
            'spent_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => "Despesa de ".number_format($amount, 2, ',', '.')."€ registada em ".($category?->name ?? 'Geral').'.',
            'action' => 'expense_created',
        ];
    }

    private function weeklySummary(User $user): array
    {
        $workspace = $user->currentWorkspace;
        $spent = (float) Expense::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('spent_at', '>=', now()->subDays(7))
            ->sum('amount');

        return [
            'success' => true,
            'message' => 'Esta semana gastaste '.number_format($spent, 2, ',', '.').'€.',
            'action' => 'summary',
        ];
    }

    private function monthlySpent(User $user): array
    {
        $workspace = $user->currentWorkspace;
        $spent = (float) Expense::where('workspace_id', $workspace->id)
            ->where('spent_at', '>=', now()->startOfMonth())
            ->sum('amount');

        return [
            'success' => true,
            'message' => 'Este mês gastaste '.number_format($spent, 2, ',', '.').'€ no total.',
            'action' => 'summary',
        ];
    }

    private function monthlyBalance(User $user): array
    {
        $workspace = $user->currentWorkspace;
        $start = now()->startOfMonth();
        $spent = (float) Expense::where('workspace_id', $workspace->id)->where('spent_at', '>=', $start)->sum('amount');
        $earned = (float) \App\Models\Income::where('workspace_id', $workspace->id)->where('received_at', '>=', $start)->sum('amount');
        $net = $earned - $spent;

        return [
            'success' => true,
            'message' => 'Saldo do mês: '.number_format($net, 2, ',', '.').'€ (receitas '.number_format($earned, 2, ',', '.').'€ − despesas '.number_format($spent, 2, ',', '.').'€).',
            'action' => 'summary',
        ];
    }

    private function guessCategory(string $desc, int $workspaceId): ?Category
    {
        $keywords = [
            'alimentação' => ['almoço', 'jantar', 'comida', 'supermercado', 'restaur'],
            'transporte' => ['uber', 'bolt', 'gasolina', 'metro'],
            'entretenimento' => ['cinema', 'netflix', 'jogo'],
        ];

        foreach ($keywords as $catName => $words) {
            foreach ($words as $word) {
                if (str_contains($desc, $word)) {
                    return Category::where('workspace_id', $workspaceId)
                        ->where(fn ($q) => $q->where('slug', $catName)->orWhere('name', 'like', "%{$catName}%"))
                        ->first();
                }
            }
        }

        return Category::where('workspace_id', $workspaceId)->first();
    }
}
