<?php

use App\Livewire\FinanceBot;
use App\Models\Expense;
use App\Models\Reminder;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BancoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function personalIsolationFixture(): array
{
    $owner = User::factory()->create(['name' => 'Utilizador A']);
    $attacker = User::factory()->create(['name' => 'Utilizador B']);

    $workspace = Workspace::factory()->create([
        'name' => 'Workspace Pessoal A',
        'type' => 'personal',
        'owner_id' => $owner->id,
    ]);

    $workspace->users()->attach($attacker->id, ['role' => 'member']);

    $owner->update(['current_workspace_id' => $workspace->id]);
    $attacker->update(['current_workspace_id' => $workspace->id]);

    $categoryA = DB::table('categories')->where('workspace_id', $workspace->id)->where('user_id', $owner->id)->first()
        ?? DB::table('categories')->where('workspace_id', $workspace->id)->first();

    $categoryB = DB::table('categories')->insertGetId([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'name' => 'Categoria B',
        'slug' => 'categoria-b',
        'is_fixed' => false,
        'order' => 99,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $expenseA = DB::table('expenses')->insertGetId([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'category_id' => $categoryA->id,
        'amount' => 10,
        'description' => 'Despesa A',
        'spent_at' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $expenseB = Expense::create([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'category_id' => $categoryB,
        'amount' => 999,
        'description' => 'SEGREDO B',
        'spent_at' => now()->toDateString(),
    ])->id;

    $incomeA = DB::table('incomes')->insertGetId([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'description' => 'Rendimento A',
        'amount' => 20,
        'received_at' => now()->toDateString(),
        'type' => 'Outros',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $incomeB = DB::table('incomes')->insertGetId([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'description' => 'SEGREDO RENDIMENTO B',
        'amount' => 888,
        'received_at' => now()->toDateString(),
        'type' => 'Outros',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $accountA = DB::table('bank_accounts')->insertGetId([
        'workspace_id' => $workspace->id,
        'user_id' => $owner->id,
        'name' => 'Conta A',
        'type' => 'corrente',
        'balance' => 100,
        'currency' => 'EUR',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $accountB = DB::table('bank_accounts')->insertGetId([
        'workspace_id' => $workspace->id,
        'user_id' => $attacker->id,
        'name' => 'CONTA SECRETA B',
        'type' => 'corrente',
        'balance' => 5000,
        'currency' => 'EUR',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $investmentA = DB::table('investments')->insertGetId([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'name' => 'Investimento A',
        'type' => 'Acao',
        'quantity' => 1,
        'average_price' => 100,
        'current_price' => 110,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $investmentB = DB::table('investments')->insertGetId([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'name' => 'SEGREDO INVESTIMENTO B',
        'type' => 'Acao',
        'quantity' => 1,
        'average_price' => 900,
        'current_price' => 900,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $goalA = DB::table('goals')->insertGetId([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'name' => 'Meta A',
        'target_amount' => 1000,
        'current_amount' => 100,
        'deadline' => now()->addMonth()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $goalB = DB::table('goals')->insertGetId([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'name' => 'SEGREDO META B',
        'target_amount' => 9000,
        'current_amount' => 900,
        'deadline' => now()->addMonth()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $debtA = DB::table('debts')->insertGetId([
        'workspace_id' => $workspace->id,
        'user_id' => $owner->id,
        'type' => 'owe',
        'person_name' => 'Credor A',
        'amount' => 50,
        'is_paid' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $debtB = DB::table('debts')->insertGetId([
        'workspace_id' => $workspace->id,
        'user_id' => $attacker->id,
        'type' => 'owe',
        'person_name' => 'SEGREDO CREDOR B',
        'amount' => 700,
        'is_paid' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $subscriptionA = DB::table('subscriptions')->insertGetId([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'category_id' => $categoryA->id,
        'name' => 'Subscrição A',
        'amount' => 5,
        'billing_day' => 1,
        'cycle' => 'monthly',
        'status' => 'active',
        'is_active' => true,
        'started_at' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $subscriptionB = DB::table('subscriptions')->insertGetId([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'category_id' => $categoryB,
        'name' => 'SEGREDO SUBSCRIÇÃO B',
        'amount' => 700,
        'billing_day' => 1,
        'cycle' => 'monthly',
        'status' => 'active',
        'is_active' => true,
        'started_at' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reminderA = DB::table('reminders')->insertGetId([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'title' => 'Lembrete A',
        'remind_at' => now()->addDay(),
        'priority' => 'medium',
        'frequency' => 'once',
        'is_completed' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reminderB = Reminder::create([
        'user_id' => $attacker->id,
        'workspace_id' => $workspace->id,
        'title' => 'SEGREDO LEMBRETE B',
        'remind_at' => now()->addDay(),
        'priority' => 'high',
        'frequency' => 'once',
        'is_completed' => false,
    ])->id;

    return compact(
        'owner',
        'attacker',
        'workspace',
        'expenseA',
        'expenseB',
        'incomeA',
        'incomeB',
        'accountA',
        'accountB',
        'investmentA',
        'investmentB',
        'goalA',
        'goalB',
        'debtA',
        'debtB',
        'subscriptionA',
        'subscriptionB',
        'reminderA',
        'reminderB'
    );
}

function financeBotTool(string $tool, array $args = []): array
{
    $method = new \ReflectionMethod(FinanceBot::class, 'executeTool');
    $method->setAccessible(true);

    return $method->invoke(app(FinanceBot::class), $tool, $args);
}

test('a personal workspace never exposes another users records through FinanceBot', function () {
    $data = personalIsolationFixture();

    $this->actingAs($data['owner']);

    expect(financeBotTool('list_expenses', ['days' => 30])['items'])->toHaveCount(1)
        ->and(financeBotTool('list_expenses', ['days' => 30])['items'][0]['description'])->toBe('Despesa A')
        ->and(financeBotTool('list_incomes', ['days' => 30])['items'][0]['description'])->toBe('Rendimento A')
        ->and(financeBotTool('list_investments')['items'][0]['name'])->toBe('Investimento A')
        ->and(financeBotTool('list_subscriptions')['items'][0]['name'])->toBe('Subscrição A')
        ->and(financeBotTool('list_goals')['items'][0]['name'])->toBe('Meta A')
        ->and(financeBotTool('list_reminders')['items'][0]['title'])->toBe('Lembrete A');
});

test('a personal workspace never exposes another users bank or debt records through BancoService', function () {
    $data = personalIsolationFixture();

    $this->actingAs($data['owner']);

    $service = new BancoService($data['workspace']->id, $data['owner']->id);

    expect($service->getAccounts()->pluck('id')->all())->toBe([$data['accountA']])
        ->and($service->getInvestments()->pluck('id')->all())->toBe([$data['investmentA']])
        ->and($service->getGoals()->pluck('id')->all())->toBe([$data['goalA']])
        ->and($service->getDebts()->pluck('id')->all())->toBe([$data['debtA']]);
});

test('FinanceBot cannot delete or complete another users records by id', function () {
    $data = personalIsolationFixture();

    // Create the attacker records after the fixture so this test controls the exact
    // records being targeted, without Eloquent ownership/global-scope side effects.
    $expenseB = DB::table('expenses')->insertGetId([
        'user_id' => $data['attacker']->id,
        'workspace_id' => $data['workspace']->id,
        'category_id' => DB::table('categories')
            ->where('workspace_id', $data['workspace']->id)
            ->where('user_id', $data['attacker']->id)
            ->value('id'),
        'amount' => 999,
        'description' => 'SEGREDO B - DELETE TEST',
        'spent_at' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reminderB = DB::table('reminders')->insertGetId([
        'user_id' => $data['attacker']->id,
        'workspace_id' => $data['workspace']->id,
        'title' => 'SEGREDO LEMBRETE B - DELETE TEST',
        'remind_at' => now()->addDay(),
        'priority' => 'high',
        'frequency' => 'once',
        'is_completed' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($data['owner']);

    expect(DB::table('expenses')->whereKey($expenseB)->exists())->toBeTrue()
        ->and(financeBotTool('delete_expense', ['expense_id' => $expenseB])['error'])->toBe('Despesa não encontrada.')
        ->and(DB::table('expenses')->whereKey($expenseB)->exists())->toBeTrue()
        ->and(financeBotTool('complete_reminder', ['reminder_id' => $reminderB])['error'])->toBe('Lembrete não encontrado.')
        ->and(DB::table('reminders')->whereKey($reminderB)->value('is_completed'))->toBeFalse()
        ->and(financeBotTool('delete_reminder', ['reminder_id' => $reminderB])['error'])->toBe('Lembrete não encontrado.')
        ->and(DB::table('expenses')->whereKey($expenseB)->exists())->toBeTrue()
        ->and(DB::table('reminders')->whereKey($reminderB)->exists())->toBeTrue();
});

test('FinanceBot financial summary only includes the authenticated users income and expenses', function () {
    $data = personalIsolationFixture();

    $this->actingAs($data['owner']);

    $summary = financeBotTool('get_financial_summary', ['days' => 30]);

    expect($summary['earned'])->toBe(20.0)
        ->and($summary['spent'])->toBe(10.0)
        ->and($summary['by_category'])->not->toHaveKey('Categoria B')
        ->and(array_values($summary['by_category']))->not->toContain(999.0);
});

test('personal workspace isolation still works when an attacker belongs to the same workspace', function () {
    $data = personalIsolationFixture();

    $this->actingAs($data['attacker']);

    expect(financeBotTool('list_expenses', ['days' => 30])['items'][0]['description'])->toBe('SEGREDO B')
        ->and(financeBotTool('list_incomes', ['days' => 30])['items'][0]['description'])->toBe('SEGREDO RENDIMENTO B')
        ->and(financeBotTool('list_investments')['items'][0]['name'])->toBe('SEGREDO INVESTIMENTO B')
        ->and(financeBotTool('list_subscriptions')['items'][0]['name'])->toBe('SEGREDO SUBSCRIÇÃO B')
        ->and(financeBotTool('list_goals')['items'][0]['name'])->toBe('SEGREDO META B')
        ->and(financeBotTool('list_reminders')['items'][0]['title'])->toBe('SEGREDO LEMBRETE B');
});


test('personal finance pages do not render another users records from the same workspace', function () {
    $data = personalIsolationFixture();

    $this->actingAs($data['owner']);

    foreach ([
        route('expenses.index'),
        route('hub.incomes'),
        route('hub.debts'),
        route('hub.goals'),
        route('hub.banco'),
        route('hub.investments'),
        route('hub.subscriptions'),
        route('hub.networth'),
        route('hub.reminders'),
    ] as $url) {
        $response = $this->get($url)->assertOk();

        $response->assertDontSee('SEGREDO B')
            ->assertDontSee('SEGREDO RENDIMENTO B')
            ->assertDontSee('SEGREDO INVESTIMENTO B')
            ->assertDontSee('SEGREDO META B')
            ->assertDontSee('SEGREDO SUBSCRIÇÃO B')
            ->assertDontSee('SEGREDO LEMBRETE B')
            ->assertDontSee('CONTA SECRETA B')
            ->assertDontSee('SEGREDO CREDOR B');
    }
});
