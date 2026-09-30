<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Invoice;
use App\Models\Reminder;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Workspace;
use App\Services\SubscriptionCycleService;
use Carbon\Carbon;
use RuntimeException;

class AiToolRegistry
{
    private const WRITE_TOOLS = [
        'create_expense', 'create_income', 'create_goal', 'update_goal_progress',
        'create_subscription', 'create_investment', 'create_reminder',
        'complete_reminder', 'delete_reminder', 'delete_expense',
    ];

    public function definitions(): array
    {
        return [
            $this->tool('get_financial_snapshot', 'Consulta o snapshot financeiro determinístico do workspace atual.', [
                'period' => ['type' => 'string', 'description' => 'Mês YYYY-MM. Vazio = mês atual.'],
            ]),
            $this->tool('list_expenses', 'Consulta despesas reais do workspace atual.', [
                'days' => ['type' => 'integer', 'description' => 'Número de dias a analisar. Default 30.'],
                'category_name' => ['type' => 'string', 'description' => 'Filtro opcional por categoria.'],
            ]),
            $this->tool('list_incomes', 'Consulta rendimentos reais do workspace atual.', [
                'days' => ['type' => 'integer', 'description' => 'Número de dias a analisar. Default 30.'],
            ]),
            $this->tool('list_goals', 'Consulta objetivos de poupança reais do workspace atual.'),
            $this->tool('list_subscriptions', 'Consulta subscrições reais do workspace atual.'),
            $this->tool('list_investments', 'Consulta investimentos reais do workspace atual.'),
            $this->tool('list_categories', 'Consulta categorias reais do workspace atual.'),
            $this->tool('list_business_clients', 'No workspace empresarial, consulta clientes reais e faturação agregada.'),
            $this->tool('list_business_invoices', 'No workspace empresarial, consulta faturas reais, estado e vencimentos.'),
            $this->tool('list_business_suppliers', 'No workspace empresarial, consulta fornecedores reais.'),
            $this->tool('create_expense', 'Prepara uma nova despesa. Requer confirmação antes de executar.', [
                'amount' => ['type' => 'number'],
                'description' => ['type' => 'string'],
                'category_name' => ['type' => 'string'],
                'date' => ['type' => 'string'],
            ], ['amount', 'description']),
            $this->tool('create_income', 'Prepara um novo rendimento. Requer confirmação antes de executar.', [
                'amount' => ['type' => 'number'],
                'description' => ['type' => 'string'],
                'date' => ['type' => 'string'],
            ], ['amount', 'description']),
            $this->tool('create_goal', 'Prepara uma nova meta de poupança. Requer confirmação.', [
                'name' => ['type' => 'string'],
                'target_amount' => ['type' => 'number'],
                'deadline' => ['type' => 'string'],
            ], ['name', 'target_amount']),
            $this->tool('create_subscription', 'Prepara uma nova subscrição. Requer confirmação.', [
                'name' => ['type' => 'string'],
                'amount' => ['type' => 'number'],
                'cycle' => ['type' => 'string', 'enum' => ['monthly', 'yearly']],
            ], ['name', 'amount']),
            $this->tool('create_investment', 'Prepara um novo registo de investimento. Requer confirmação.', [
                'name' => ['type' => 'string'],
                'amount' => ['type' => 'number'],
                'type' => ['type' => 'string'],
            ], ['name', 'amount']),
            $this->tool('create_reminder', 'Prepara um novo lembrete. Requer confirmação.', [
                'title' => ['type' => 'string'],
                'date' => ['type' => 'string'],
                'priority' => ['type' => 'string'],
            ], ['title']),
            $this->tool('complete_reminder', 'Prepara a conclusão de um lembrete. Requer confirmação.', [
                'reminder_id' => ['type' => 'integer'],
            ], ['reminder_id']),
            $this->tool('delete_reminder', 'Prepara a eliminação definitiva de um lembrete. Requer confirmação explícita.', [
                'reminder_id' => ['type' => 'integer'],
            ], ['reminder_id']),
            $this->tool('delete_expense', 'Prepara a eliminação definitiva de uma despesa. Requer confirmação explícita.', [
                'expense_id' => ['type' => 'integer'],
            ], ['expense_id']),
        ];
    }

    public function requiresConfirmation(string $tool): bool
    {
        return in_array($tool, self::WRITE_TOOLS, true);
    }

    public function preview(User $user, Workspace $workspace, string $tool, array $args): array
    {
        $this->authorizeScope($user, $workspace);

        return match ($tool) {
            'create_expense' => [
                'action' => 'create_expense',
                'title' => 'Criar despesa',
                'summary' => sprintf('%s %s — %s', $this->money($workspace, (float) ($args['amount'] ?? 0)), $args['description'] ?? 'Despesa', $args['category_name'] ?? 'Sem categoria'),
                'details' => [
                    'valor' => $this->money($workspace, (float) ($args['amount'] ?? 0)),
                    'descrição' => $args['description'] ?? 'Despesa',
                    'categoria' => $args['category_name'] ?? 'Sem categoria',
                    'data' => $this->resolveDate($args['date'] ?? null)->toDateString(),
                    'workspace' => $workspace->name,
                ],
            ],
            'create_income' => [
                'action' => 'create_income',
                'title' => 'Criar rendimento',
                'summary' => sprintf('%s — %s', $this->money($workspace, (float) ($args['amount'] ?? 0)), $args['description'] ?? 'Rendimento'),
                'details' => [
                    'valor' => $this->money($workspace, (float) ($args['amount'] ?? 0)),
                    'descrição' => $args['description'] ?? 'Rendimento',
                    'data' => $this->resolveDate($args['date'] ?? null)->toDateString(),
                    'workspace' => $workspace->name,
                ],
            ],
            'create_goal' => [
                'action' => 'create_goal',
                'title' => 'Criar objetivo',
                'summary' => sprintf('%s — %s', $args['name'] ?? 'Meta', $this->money($workspace, (float) ($args['target_amount'] ?? 0))),
                'details' => $args + ['workspace' => $workspace->name],
            ],
            'create_subscription' => [
                'action' => 'create_subscription',
                'title' => 'Criar subscrição',
                'summary' => sprintf('%s — %s/%s', $args['name'] ?? 'Subscrição', $this->money($workspace, (float) ($args['amount'] ?? 0)), $args['cycle'] ?? 'monthly'),
                'details' => $args + ['workspace' => $workspace->name],
            ],
            'create_investment' => [
                'action' => 'create_investment',
                'title' => 'Registar investimento',
                'summary' => sprintf('%s — %s', $args['name'] ?? 'Investimento', $this->money($workspace, (float) ($args['amount'] ?? 0))),
                'details' => $args + ['workspace' => $workspace->name],
            ],
            'create_reminder' => [
                'action' => 'create_reminder',
                'title' => 'Criar lembrete',
                'summary' => (string) ($args['title'] ?? 'Lembrete'),
                'details' => $args + ['workspace' => $workspace->name],
            ],
            'complete_reminder', 'delete_reminder', 'delete_expense' => $this->previewExistingRecordAction($user, $workspace, $tool, $args),
            default => throw new RuntimeException('Tool de escrita não suportada para preview.'),
        };
    }

    public function execute(User $user, Workspace $workspace, string $tool, array $args): array
    {
        $this->authorizeScope($user, $workspace);

        return match ($tool) {
            'get_financial_snapshot' => $this->snapshot($user, $workspace, $args),
            'list_expenses' => $this->expenses($user, $workspace, $args),
            'list_incomes' => $this->incomes($user, $workspace, $args),
            'list_goals' => $this->goals($user, $workspace),
            'list_subscriptions' => $this->subscriptions($user, $workspace),
            'list_investments' => $this->investments($user, $workspace),
            'list_categories' => $this->categories($user, $workspace),
            'list_business_clients' => $this->businessClients($workspace),
            'list_business_invoices' => $this->businessInvoices($workspace),
            'list_business_suppliers' => $this->businessSuppliers($workspace),
            'create_expense' => $this->createExpense($user, $workspace, $args),
            'create_income' => $this->createIncome($user, $workspace, $args),
            'create_goal' => $this->createGoal($user, $workspace, $args),
            'create_subscription' => $this->createSubscription($user, $workspace, $args),
            'create_investment' => $this->createInvestment($user, $workspace, $args),
            'create_reminder' => $this->createReminder($user, $workspace, $args),
            'complete_reminder' => $this->completeReminder($user, $workspace, $args),
            'delete_reminder' => $this->deleteReminder($user, $workspace, $args),
            'delete_expense' => $this->deleteExpense($user, $workspace, $args),
            default => throw new RuntimeException("Ferramenta desconhecida: {$tool}"),
        };
    }

    private function authorizeScope(User $user, Workspace $workspace): void
    {
        $member = $user->workspaces()->whereKey($workspace->id)->first();
        if (! $member) {
            throw new RuntimeException('Workspace não autorizado.');
        }

        if (in_array($workspace->type, ['business', 'company'], true) && ! in_array($member->pivot->role, ['owner', 'admin', 'editor'], true)) {
            throw new RuntimeException('O teu papel não tem acesso suficiente para esta operação.');
        }
    }

    private function snapshot(User $user, Workspace $workspace, array $args): array
    {
        $period = ! empty($args['period']) ? Carbon::createFromFormat('Y-m', $args['period'])->startOfMonth() : now()->startOfMonth();

        return app(FinancialIntelligenceService::class)->snapshot($workspace, $period);
    }

    private function expenses(User $user, Workspace $workspace, array $args): array
    {
        $days = min(3660, max(1, (int) ($args['days'] ?? 30)));
        $query = $workspace->expenses()->where('spent_at', '>=', now()->subDays($days))
            ->when($workspace->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->with('category');
        if (! empty($args['category_name'])) {
            $needle = mb_strtolower((string) $args['category_name']);
            $query->whereHas('category', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"]));
        }
        $items = $query->orderByDesc('spent_at')->limit(100)->get();

        return [
            'period_days' => $days,
            'count' => $items->count(),
            'total' => round((float) $items->sum('amount_converted'), 2),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'amount' => (float) $item->amount_converted,
                'category' => $item->category?->name,
                'date' => $item->spent_at?->toDateString(),
            ])->values()->all(),
        ];
    }

    private function incomes(User $user, Workspace $workspace, array $args): array
    {
        $days = min(3660, max(1, (int) ($args['days'] ?? 30)));
        $items = $workspace->incomes()->where('received_at', '>=', now()->subDays($days))
            ->when($workspace->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('received_at')->limit(100)->get();

        return [
            'period_days' => $days,
            'count' => $items->count(),
            'total' => round((float) $items->sum('amount_converted'), 2),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'amount' => (float) $item->amount_converted,
                'date' => $item->received_at?->toDateString(),
            ])->values()->all(),
        ];
    }

    private function goals(User $user, Workspace $workspace): array
    {
        $items = Goal::where('workspace_id', $workspace->id)->when($workspace->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->get();

        return ['count' => $items->count(), 'items' => $items->map(fn ($goal) => [
            'id' => $goal->id,
            'name' => $goal->name,
            'current' => (float) $goal->current_amount,
            'target' => (float) $goal->target_amount,
            'progress_percent' => $goal->target_amount > 0 ? round(((float) $goal->current_amount / (float) $goal->target_amount) * 100, 1) : 0,
            'deadline' => $goal->deadline?->toDateString(),
        ])->values()->all()];
    }
