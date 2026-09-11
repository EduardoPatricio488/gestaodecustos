<?php

namespace App\Services\AI;

use App\Models\BankAccount;
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
use App\Services\BusinessFinancialMetrics;
use App\Services\SubscriptionCycleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            'complete_reminder', 'delete_reminder', 'delete_expense' => $this->previewExistingRecordAction($workspace, $tool, $args),
            default => throw new RuntimeException('Tool de escrita não suportada para preview.'),
        };
    }

    public function execute(User $user, Workspace $workspace, string $tool, array $args): array
    {
        $this->authorizeScope($user, $workspace);

        return match ($tool) {
            'get_financial_snapshot' => $this->snapshot($workspace, $args),
            'list_expenses' => $this->expenses($workspace, $args),
            'list_incomes' => $this->incomes($workspace, $args),
            'list_goals' => $this->goals($workspace),
            'list_subscriptions' => $this->subscriptions($workspace),
            'list_investments' => $this->investments($workspace),
            'list_categories' => $this->categories($workspace),
            'list_business_clients' => $this->businessClients($workspace),
            'list_business_invoices' => $this->businessInvoices($workspace),
            'list_business_suppliers' => $this->businessSuppliers($workspace),
            'create_expense' => $this->createExpense($user, $workspace, $args),
            'create_income' => $this->createIncome($user, $workspace, $args),
            'create_goal' => $this->createGoal($user, $workspace, $args),
            'create_subscription' => $this->createSubscription($user, $workspace, $args),
            'create_investment' => $this->createInvestment($user, $workspace, $args),
            'create_reminder' => $this->createReminder($user, $workspace, $args),
            'complete_reminder' => $this->completeReminder($workspace, $args),
            'delete_reminder' => $this->deleteReminder($workspace, $args),
            'delete_expense' => $this->deleteExpense($workspace, $args),
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

    private function snapshot(Workspace $workspace, array $args): array
    {
        $period = ! empty($args['period']) ? Carbon::createFromFormat('Y-m', $args['period'])->startOfMonth() : now()->startOfMonth();
        return app(FinancialIntelligenceService::class)->snapshot($workspace, $period);
    }

    private function expenses(Workspace $workspace, array $args): array
    {
        $days = min(3660, max(1, (int) ($args['days'] ?? 30)));
        $query = $workspace->expenses()->where('spent_at', '>=', now()->subDays($days))->with('category');
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

    private function incomes(Workspace $workspace, array $args): array
    {
        $days = min(3660, max(1, (int) ($args['days'] ?? 30)));
        $items = $workspace->incomes()->where('received_at', '>=', now()->subDays($days))->orderByDesc('received_at')->limit(100)->get();
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

    private function goals(Workspace $workspace): array
    {
        $items = Goal::where('workspace_id', $workspace->id)->get();
        return ['count' => $items->count(), 'items' => $items->map(fn ($goal) => [
            'id' => $goal->id,
            'name' => $goal->name,
            'current' => (float) $goal->current_amount,
            'target' => (float) $goal->target_amount,
            'progress_percent' => $goal->target_amount > 0 ? round(((float) $goal->current_amount / (float) $goal->target_amount) * 100, 1) : 0,
            'deadline' => $goal->deadline?->toDateString(),
        ])->values()->all()];
    }

    private function subscriptions(Workspace $workspace): array
    {
        $items = Subscription::where('workspace_id', $workspace->id)->where('is_active', true)->get();
        return [
            'count' => $items->count(),
            'monthly_cost' => round((float) $items->sum(fn ($item) => SubscriptionCycleService::toMonthly((float) $item->amount, $item->cycle)), 2),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'amount' => (float) $item->amount,
                'cycle' => $item->cycle,
                'renewal_date' => $item->renewal_date?->toDateString(),
            ])->values()->all(),
        ];
    }

    private function investments(Workspace $workspace): array
    {
        $items = Investment::where('workspace_id', $workspace->id)->get();
        return [
            'count' => $items->count(),
            'total_value' => round((float) $items->sum(fn ($item) => (float) $item->quantity * (float) $item->current_price), 2),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type,
                'quantity' => (float) $item->quantity,
                'average_price' => (float) $item->average_price,
                'current_price' => (float) $item->current_price,
                'current_value' => round((float) $item->quantity * (float) $item->current_price, 2),
            ])->values()->all(),
        ];
    }

    private function categories(Workspace $workspace): array
    {
        $items = Category::where('workspace_id', $workspace->id)->orderBy('name')->pluck('name');
        return ['count' => $items->count(), 'items' => $items->values()->all()];
    }

    private function businessClients(Workspace $workspace): array
    {
        $this->assertBusiness($workspace);
        $items = Client::where('workspace_id', $workspace->id)->get();
        return ['count' => $items->count(), 'items' => $items->map(fn ($client) => [
            'id' => $client->id,
            'name' => $client->name,
            'status' => $client->status,
            'revenue_paid' => round((float) $client->total_revenue, 2),
            'pending_debt' => round((float) $client->pending_debt, 2),
        ])->values()->all()];
    }

    private function businessInvoices(Workspace $workspace): array
    {
        $this->assertBusiness($workspace);
        $items = Invoice::where('workspace_id', $workspace->id)->orderByDesc('created_at')->limit(100)->get();
        return ['count' => $items->count(), 'items' => $items->map(fn ($invoice) => [
            'id' => $invoice->id,
            'number' => $invoice->invoice_number,
            'client' => $invoice->client_name,
            'total' => (float) $invoice->total_amount_converted,
            'status' => $invoice->status,
            'due_date' => $invoice->due_date?->toDateString(),
            'paid_at' => $invoice->paid_at?->toIso8601String(),
        ])->values()->all()];
    }

    private function businessSuppliers(Workspace $workspace): array
    {
        $this->assertBusiness($workspace);
        $items = Supplier::where('workspace_id', $workspace->id)->get(['id', 'name', 'email', 'phone', 'status']);
        return ['count' => $items->count(), 'items' => $items->toArray()];
    }

    private function createExpense(User $user, Workspace $workspace, array $args): array
    {
        $amount = (float) ($args['amount'] ?? 0);
        if ($amount <= 0 || empty($args['description'])) throw new RuntimeException('Valor e descrição são obrigatórios.');
        $category = $this->resolveCategory($user, $workspace, $args['category_name'] ?? null);
        $expense = Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category?->id,
            'description' => trim((string) $args['description']),
            'amount' => $amount,
            'spent_at' => $this->resolveDate($args['date'] ?? null),
            'is_company' => in_array($workspace->type, ['business', 'company'], true),
        ]);
        return ['success' => true, 'id' => $expense->id, 'amount' => (float) $expense->amount, 'description' => $expense->description];
    }

    private function createIncome(User $user, Workspace $workspace, array $args): array
    {
        $amount = (float) ($args['amount'] ?? 0);
        if ($amount <= 0 || empty($args['description'])) throw new RuntimeException('Valor e descrição são obrigatórios.');
        $income = Income::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => trim((string) $args['description']),
            'amount' => $amount,
            'received_at' => $this->resolveDate($args['date'] ?? null),
        ]);
        return ['success' => true, 'id' => $income->id, 'amount' => (float) $income->amount, 'description' => $income->description];
    }

    private function createGoal(User $user, Workspace $workspace, array $args): array
    {
        $target = (float) ($args['target_amount'] ?? 0);
        if ($target <= 0 || empty($args['name'])) throw new RuntimeException('Nome e valor objetivo são obrigatórios.');
        $goal = Goal::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => trim((string) $args['name']),
            'target_amount' => $target,
            'current_amount' => 0,
            'deadline' => ! empty($args['deadline']) ? $this->resolveDate($args['deadline']) : null,
        ]);
        return ['success' => true, 'id' => $goal->id, 'name' => $goal->name, 'target' => (float) $goal->target_amount];
    }

    private function createSubscription(User $user, Workspace $workspace, array $args): array
    {
        $amount = (float) ($args['amount'] ?? 0);
        if ($amount <= 0 || empty($args['name'])) throw new RuntimeException('Nome e valor são obrigatórios.');
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => trim((string) $args['name']),
            'amount' => $amount,
            'cycle' => ($args['cycle'] ?? 'monthly') === 'yearly' ? 'yearly' : 'monthly',
            'billing_day' => min(28, max(1, now()->day)),
            'is_active' => true,
            'status' => 'active',
            'started_at' => now(),
        ]);
        return ['success' => true, 'id' => $subscription->id, 'name' => $subscription->name];
    }

    private function createInvestment(User $user, Workspace $workspace, array $args): array
    {
        $amount = (float) ($args['amount'] ?? 0);
        if ($amount <= 0 || empty($args['name'])) throw new RuntimeException('Nome e valor são obrigatórios.');
        $investment = Investment::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => trim((string) $args['name']),
            'type' => $args['type'] ?? 'Outro',
            'product_type' => 'Ativo',
            'quantity' => 1,
            'average_price' => $amount,
            'current_price' => $amount,
            'operation_date' => now(),
        ]);
        return ['success' => true, 'id' => $investment->id, 'name' => $investment->name];
    }

    private function createReminder(User $user, Workspace $workspace, array $args): array
    {
        if (empty($args['title'])) throw new RuntimeException('Título obrigatório.');
        $reminder = Reminder::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'title' => trim((string) $args['title']),
            'remind_at' => $this->resolveDate($args['date'] ?? null),
            'priority' => in_array($args['priority'] ?? '', ['low', 'medium', 'high'], true) ? $args['priority'] : 'medium',
        ]);
        return ['success' => true, 'id' => $reminder->id, 'title' => $reminder->title];
    }

    private function completeReminder(Workspace $workspace, array $args): array
    {
        $reminder = Reminder::where('workspace_id', $workspace->id)->find($args['reminder_id'] ?? null);
        if (! $reminder) throw new RuntimeException('Lembrete não encontrado.');
        $reminder->update(['is_completed' => true, 'completed_at' => now()]);
        return ['success' => true, 'id' => $reminder->id];
    }

    private function deleteReminder(Workspace $workspace, array $args): array
    {
        $reminder = Reminder::where('workspace_id', $workspace->id)->find($args['reminder_id'] ?? null);
        if (! $reminder) throw new RuntimeException('Lembrete não encontrado.');
        $id = $reminder->id;
        $reminder->delete();
        return ['success' => true, 'id' => $id];
    }

    private function deleteExpense(Workspace $workspace, array $args): array
    {
        $expense = Expense::where('workspace_id', $workspace->id)->find($args['expense_id'] ?? null);
        if (! $expense) throw new RuntimeException('Despesa não encontrada.');
        $id = $expense->id;
        $expense->delete();
        return ['success' => true, 'id' => $id];
    }

    private function previewExistingRecordAction(Workspace $workspace, string $tool, array $args): array
    {
        if ($tool === 'delete_expense') {
            $record = Expense::where('workspace_id', $workspace->id)->find($args['expense_id'] ?? null);
            if (! $record) throw new RuntimeException('Despesa não encontrada.');
            return ['action' => $tool, 'title' => 'Apagar despesa', 'summary' => $record->description.' — '.$this->money($workspace, (float) $record->amount), 'details' => ['id' => $record->id, 'descrição' => $record->description, 'valor' => $this->money($workspace, (float) $record->amount), 'data' => $record->spent_at?->toDateString()]];
        }
        $record = Reminder::where('workspace_id', $workspace->id)->find($args['reminder_id'] ?? null);
        if (! $record) throw new RuntimeException('Lembrete não encontrado.');
        return ['action' => $tool, 'title' => $tool === 'delete_reminder' ? 'Apagar lembrete' : 'Concluir lembrete', 'summary' => $record->title, 'details' => ['id' => $record->id, 'título' => $record->title, 'data' => $record->remind_at?->toDateString()]];
    }

    private function resolveCategory(User $user, Workspace $workspace, ?string $name): ?Category
    {
        $name = trim((string) $name);
        if ($name === '') return null;
        return Category::where('workspace_id', $workspace->id)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first()
            ?? Category::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => ucfirst($name)]);
    }

    private function resolveDate(?string $value): Carbon
    {
        $value = trim((string) $value);
        if ($value === '') return now();
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) return Carbon::parse($value);
        $lower = mb_strtolower($value);
        if (str_contains($lower, 'ontem')) return now()->subDay();
        if (str_contains($lower, 'anteontem')) return now()->subDays(2);
        if (preg_match('/há\s+(\d+)\s+dias/', $lower, $match)) return now()->subDays((int) $match[1]);
        return now();
    }

    private function money(Workspace $workspace, float $amount): string
    {
        return $workspace->money($amount);
    }

    private function assertBusiness(Workspace $workspace): void
    {
        if (! in_array($workspace->type, ['business', 'company'], true)) throw new RuntimeException('Esta ferramenta só está disponível num workspace empresarial.');
    }

    private function tool(string $name, string $description, array $properties = [], array $required = []): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $name,
                'description' => $description,
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties ?: new \stdClass(),
                    'required' => $required,
                    'additionalProperties' => false,
                ],
            ],
        ];
    }
}
