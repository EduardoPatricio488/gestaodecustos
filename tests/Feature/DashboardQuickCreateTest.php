<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\ExpenseForm;
use App\Livewire\Expenses;
use App\Livewire\IncomeHub;
use App\Livewire\BankTransactionsHub;
use App\Livewire\PersonalCalendar;
use App\Livewire\SubscriptionHub;
use App\Livewire\Hub\RemindersHub;
use App\Models\Category;
use App\Models\BankAccount;
use App\Models\DailyMealAllowance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\SalaryPayment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardQuickCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_quick_create_opens_subscription_modal_without_navigation(): void
    {
        $this->signInWithWorkspace();

        Livewire::test(SubscriptionHub::class, ['dashboardMode' => true])
            ->dispatch('dashboard-subscription-create')
            ->assertSet('showExtraModal', true);
    }

    public function test_calendar_scheduled_record_modal_offers_the_existing_create_flows(): void
    {
        $this->signInWithWorkspace();

        Livewire::test(PersonalCalendar::class)
            ->assertSee('Nova Despesa')
            ->assertSee('Nova Receita extra')
            ->assertSee('Novo Bónus')
            ->assertSee('Nova Assinatura')
            ->assertSee('Transferência bancária')
            ->assertSee('Novo lembrete')
            ->assertSee('calendar-expense-create')
            ->assertSee('calendar-bonus-create')
            ->assertSee('calendar-bank-transfer-create')
            ->assertSee('calendar-reminder-create')
            ->assertDontSee("window.location.href='/expenses/create");
    }

    public function test_calendar_create_flows_use_the_selected_date(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $date = '2026-11-13';
        $category = $this->createCategory($user, $workspace, 'Alimentação');
        $salary = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Contrato de trabalho',
            'amount' => 1500,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => ['salary_gross' => 1500],
        ]);

        Livewire::test(Expenses::class, ['dashboardMode' => true])
            ->dispatch('calendar-expense-create', date: $date)
            ->assertSet('calendarCreateDate', $date)
            ->assertDispatched('open-expense-category-picker')
            ->call('selectCategory', $category->id)
            ->assertDispatched('dashboard-expense-category-selected', categoryId: $category->id, date: $date);

        Livewire::test(ExpenseForm::class, ['dashboardMode' => true])
            ->dispatch('dashboard-expense-category-selected', categoryId: $category->id, date: $date)
            ->assertSet('spent_at', $date)
            ->assertSet('modalOpen', true);

        Livewire::test(IncomeHub::class, ['dashboardMode' => true])
            ->dispatch('dashboard-income-create', date: $date)
            ->assertSet('received_at', $date)
            ->assertSet('showExtraModal', true);

        Livewire::test(SubscriptionHub::class, ['dashboardMode' => true])
            ->dispatch('dashboard-subscription-create', date: $date)
            ->assertSet('started_at', $date)
            ->assertSet('billing_day', 13)
            ->assertSet('showExtraModal', true);

        Livewire::test(IncomeHub::class, ['dashboardMode' => true])
            ->dispatch('calendar-bonus-create', date: $date)
            ->assertSet('showCalendarBonusContractPicker', true)
            ->assertSee('Escolher contrato para o prémio')
            ->call('openCalendarBonusForContract', $salary->id)
            ->assertSet('showSalaryBonusModal', true)
            ->assertSet('recBonusDate', $date)
            ->assertSee('Prémio do contrato');

        Livewire::test(BankTransactionsHub::class, ['dashboardMode' => true])
            ->dispatch('calendar-bank-transfer-create', date: $date)
            ->assertSet('showEditModal', true)
            ->assertSet('editDate', $date);

        Livewire::test(RemindersHub::class, ['dashboardMode' => true])
            ->dispatch('calendar-reminder-create', date: $date)
            ->assertSet('openModal', true)
            ->assertSet('remind_at', $date.'T09:00');
    }

    public function test_dashboard_quick_create_saves_subscription_and_offers_its_page(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $category = $this->createCategory($user, $workspace, 'Streaming');

        Livewire::test(SubscriptionHub::class, ['dashboardMode' => true])
            ->set('name', 'Video Teste')
            ->set('amount', '9.99')
            ->set('category_id', $category->id)
            ->set('billing_day', 12)
            ->set('billing_cycle', 'monthly')
            ->call('save')
            ->assertSet('showExtraModal', false)
            ->assertDispatched('toast')
            ->assertDispatched('dashboard-quick-create-saved');

        $subscription = Subscription::where('workspace_id', $workspace->id)->firstOrFail();
        $this->assertSame('Video Teste', $subscription->name);
        $this->assertSame($category->id, $subscription->category_id);
    }

    public function test_dashboard_quick_create_saves_extra_income_and_offers_its_page(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();

        Livewire::test(IncomeHub::class, ['dashboardMode' => true])
            ->dispatch('dashboard-income-create')
            ->assertSet('showExtraModal', true)
            ->assertSee('Receita Extra')
            ->assertSee('Associar ao contrato')
            ->assertSee('Onde entra este valor?')
            ->set('description', 'Trabalho extra')
            ->set('amount', '120')
            ->set('received_at', '2026-11-12')
            ->set('tax_estimate', '15')
            ->set('notes', 'Cliente de teste')
            ->call('saveExtra')
            ->assertSet('showExtraModal', false)
            ->assertDispatched('dashboard-record-created')
            ->assertDispatched('toast')
            ->assertDispatched('dashboard-quick-create-saved');

        $income = Income::where('workspace_id', $workspace->id)->firstOrFail();
        $this->assertSame('Trabalho extra', $income->description);
        $this->assertSame('Extra', $income->type);
        $this->assertSame(15.0, (float) $income->tax_estimate);
        $this->assertNull($income->bank_account_id);
    }

    public function test_dashboard_expense_modal_stays_on_dashboard_and_offers_category_page(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $category = $this->createCategory($user, $workspace, 'Alimentação');

        Livewire::test(ExpenseForm::class, ['dashboardMode' => true])
            ->call('openDashboardCreate')
            ->set('category_id', $category->id)
            ->set('amount', '18.50')
            ->set('description', 'Almoço')
            ->set('spent_at', '2026-11-12')
            ->call('save')
            ->assertSet('modalOpen', false)
            ->assertDispatched('dashboard-record-created')
            ->assertDispatched('toast');

        $expense = Expense::where('workspace_id', $workspace->id)->firstOrFail();
        $this->assertSame('Almoço', $expense->description);
        $this->assertSame($category->id, $expense->category_id);
    }

    public function test_dashboard_expense_picker_shows_scanner_and_opens_manual_form_without_navigation(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $category = $this->createCategory($user, $workspace, 'Alimentação');

        Livewire::test(Expenses::class, ['dashboardMode' => true])
            ->assertSee('Scanner de fatura')
            ->assertSee('Escolher categoria')
            ->assertSee($category->name)
            ->assertDontSee('Todas as Despesas')
            ->call('selectCategory', $category->id)
            ->assertDispatched('dashboard-expense-category-selected');

        Livewire::test(ExpenseForm::class, ['dashboardMode' => true])
            ->dispatch('dashboard-expense-category-selected', categoryId: $category->id)
            ->assertSet('category_id', $category->id)
            ->assertSet('modalOpen', true)
            ->assertDispatched('dashboard-expense-category-form-opened');
    }

    public function test_expense_scanner_shows_remaining_time_while_analysis_is_queued(): void
    {
        $this->signInWithWorkspace();

        Livewire::test(Expenses::class, ['dashboardMode' => true])
            ->set('isScanningInvoice', true)
            ->set('invoiceScanId', (string) \Illuminate\Support\Str::uuid())
            ->set('invoiceScanStartedAt', now()->timestamp)
            ->assertSee('Tempo máximo de espera:')
            ->assertSee('formatScanTime(scanTimeoutRemaining)')
            ->assertDontSee('A análise foi submetida e está a aguardar a resposta.');
    }

    public function test_dashboard_expense_category_picker_matches_sidebar_visibility(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $this->createCategory($user, $workspace, 'Categoria visível');
        $hiddenCategory = $this->createCategory($user, $workspace, 'Categoria escondida');
        $hiddenCategory->update(['hidden_from_sidebar' => true]);
        $otherCategory = $this->createCategory($user, $workspace, 'Outros');
        $otherCategory->update(['hidden_from_sidebar' => true]);

        Livewire::test(Expenses::class, ['dashboardMode' => true])
            ->assertSeeInOrder(['Categoria visível', 'Outros'])
            ->assertDontSee('Categoria escondida');
    }

    public function test_dashboard_meal_card_summary_deducts_and_lists_meal_card_expenses(): void
    {
        $this->travelTo(now()->startOfMonth()->addDays(10));
        [$user, $workspace] = $this->signInWithWorkspace();
        $category = $this->createCategory($user, $workspace, 'Alimentação');
        $cardValueEnteredAt = now()->startOfMonth()->addDays(3);

        $contract = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Contrato de trabalho',
            'amount' => 1900,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => [
                'contract_status' => 'active',
                'start_date' => now()->startOfMonth()->toDateString(),
                'meal_allowance_destination' => 'meal_card',
                'calculated_sa' => 230.12,
                'meal_allowance' => 10.46,
                'working_days' => 22,
            ],
        ]);

        $mealCardAllowance = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Subsídio de alimentação (cartão)',
            'amount' => 230.12,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => [
                'income_type' => 'meal_card_allowance',
                'linked_salary_id' => $contract->id,
            ],
        ]);
        $mealCardAllowance->forceFill([
            'created_at' => $cardValueEnteredAt,
            'updated_at' => $cardValueEnteredAt,
        ])->saveQuietly();
        $contract->metadata = array_merge($contract->metadata, ['meal_card_income_id' => $mealCardAllowance->id]);
        $contract->save();

        SalaryPayment::create([
            'recurring_income_id' => $contract->id,
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'payment_month' => now()->month,
            'payment_year' => now()->year,
            'meal_allowance' => 230.12,
            'paid_at' => now()->subDay()->toDateString(),
            'status' => 'paid',
        ]);

        DailyMealAllowance::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'recurring_income_id' => $contract->id,
            'allowance_date' => now()->toDateString(),
            'expected_amount' => 10.46,
            'received_amount' => 0,
            'notes' => 'Não recebido',
        ]);

        $olderMealCardExpense = Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 99,
            'description' => 'Despesa anterior ao cartão',
            'spent_at' => $cardValueEnteredAt->toDateString(),
            'metadata' => ['payment_source' => 'meal_card'],
        ]);

        $expense = Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 12.50,
            'description' => 'Compra no cartão',
            'spent_at' => now()->toDateString(),
            'metadata' => ['payment_source' => 'meal_card'],
        ]);

        $bankAccount = BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => 'Conta corrente',
            'bank_name' => 'Banco de teste',
            'type' => 'corrente',
            'balance' => 500,
            'currency' => 'EUR',
            'include_in_total' => true,
        ]);
        $bankAccount->forceFill([
            'created_at' => $cardValueEnteredAt,
            'updated_at' => $cardValueEnteredAt,
        ])->saveQuietly();
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => 20,
            'description' => 'Despesa bancária anterior',
            'spent_at' => $cardValueEnteredAt->toDateString(),
        ]);
        $bankExpense = Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => 30,
            'description' => 'Despesa bancária posterior',
            'spent_at' => now()->toDateString(),
        ]);

        Livewire::test(Dashboard::class)
            ->assertSee('Saldo estimado')
            ->assertSee('Saldo estimado no cartão')
            ->assertSee('207,16 €')
            ->assertSee('470,00 €')
            ->assertSee('Histórico do subsídio de alimentação')
            ->assertDontSee('Histórico de pagamentos salariais')
            ->assertDontSee('Despesas pagas com o cartão')
            ->assertSee('Compra no cartão')
            ->assertDontSee(route('hub.category', ['slug' => 'alimentacao', 'open_expense' => $olderMealCardExpense->id]))
            ->assertSee('Pago')
            ->assertSee('+ 230,12 €')
            ->assertSee('12,50 €')
            ->assertSee(route('hub.category', ['slug' => 'alimentacao', 'open_expense' => $expense->id]))
            ->assertSee(route('hub.category', ['slug' => 'alimentacao', 'open_expense' => $bankExpense->id]));
    }

    private function signInWithWorkspace(): array
    {
        Http::fake(['*' => Http::response([], 200)]);
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        return [$user, $workspace];
    }

    private function createCategory(User $user, Workspace $workspace, string $name): Category
    {
        return Category::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => $name,
            'slug' => str($name)->slug(),
        ]);
    }
}
