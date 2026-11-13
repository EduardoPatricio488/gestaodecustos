<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\ExpenseForm;
use App\Livewire\Expenses;
use App\Livewire\IncomeHub;
use App\Livewire\InvestmentsHub;
use App\Livewire\BankTransactionsHub;
use App\Livewire\PersonalCalendar;
use App\Livewire\SubscriptionHub;
use App\Livewire\Hub\RemindersHub;
use App\Models\Category;
use App\Models\BankAccount;
use App\Models\DailyMealAllowance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Investment;
use App\Models\InvestmentIncome;
use App\Models\RecurringIncome;
use App\Models\SalaryPayment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Investments\AforroNetSeriesEUpdater;
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

    public function test_subscription_modal_lists_only_the_current_users_bank_accounts_and_hides_investment_accounts(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $otherUser = User::factory()->create();

        BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => 'Conta à ordem',
            'bank_name' => 'Banco de teste',
            'type' => 'corrente',
            'balance' => 500,
            'currency' => 'EUR',
            'include_in_total' => true,
        ]);

        foreach (['XTB', 'AforroNet'] as $investmentAccountName) {
            BankAccount::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'name' => $investmentAccountName,
                'type' => 'investimentos',
                'balance' => 0,
                'currency' => 'EUR',
                'include_in_total' => false,
            ]);
        }

        BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $otherUser->id,
            'name' => 'Conta de outro utilizador',
            'bank_name' => 'Outro banco',
            'type' => 'corrente',
            'balance' => 250,
            'currency' => 'EUR',
            'include_in_total' => true,
        ]);

        Livewire::test(SubscriptionHub::class)
            ->call('openExtraModal')
            ->assertSee('Conta à ordem')
            ->assertDontSee('XTB')
            ->assertDontSee('AforroNet')
            ->assertDontSee('Conta de outro utilizador');
    }

    public function test_subscription_renewal_date_is_calculated_from_start_date_and_billing_cycle(): void
    {
        $this->travelTo(\\Carbon\\Carbon::parse('2026-11-13 12:00:00'));
        $this->signInWithWorkspace();

        $subscription = Livewire::test(SubscriptionHub::class)
            ->set('started_at', '2026-10-01')
            ->set('billing_cycle', 'monthly')
            ->assertSet('renewal_date', '2026-12-01')
            ->set('billing_cycle', 'quarterly')
            ->assertSet('renewal_date', '2027-01-01')
            ->set('billing_cycle', 'semiannual')
            ->assertSet('renewal_date', '2027-04-01')
            ->set('billing_cycle', 'annual')
            ->assertSet('renewal_date', '2027-10-01')
            ->set('started_at', '2026-11-20')
            ->assertSet('renewal_date', '2027-11-20');

        $this->assertSame('2027-11-20', $subscription->get('renewal_date'));
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

    public function test_investment_summary_refreshes_available_prices_and_reports_partial_updates(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $workspace->update(['type' => 'personal']);
        Http::fake(['api.coingecko.com/*' => Http::response(['bitcoin' => ['eur' => 150]]), '*' => Http::response([])]);

        $bitcoin = Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Bitcoin',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 2, 'average_price' => 80, 'current_price' => 100,
        ]);
        Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Manual',
            'type' => 'Outro', 'quantity' => 1, 'average_price' => 10, 'current_price' => 10,
        ]);
        $otherUser = User::factory()->create();
        $otherInvestment = Investment::withoutEvents(fn () => Investment::create([
            'user_id' => $otherUser->id, 'workspace_id' => $workspace->id, 'name' => 'Outro utilizador',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 1, 'average_price' => 70, 'current_price' => 90,
        ]));

        $dashboard = Livewire::test(Dashboard::class);
        preg_match('/<section[^>]*id="dashboard-investimentos".*?<\/section>/s', $dashboard->html(), $section);
        $this->assertNotEmpty($section);
        $this->assertStringContainsString('wire:click="refreshInvestmentPrices"', $section[0]);
        $this->assertStringContainsString('Atualizar preços', $section[0]);

        $dashboard->call('refreshInvestmentPrices')
            ->assertSee('1 ativo atualizado.')
            ->assertSee('1 ativo não pôde ser atualizado.')
            ->assertSee('310,00')
            ->assertDispatched('dashboard-investments-updated');
        $this->assertSame(150.0, $bitcoin->fresh()->current_price);
        $this->assertSame(90.0, Investment::withoutGlobalScopes()->findOrFail($otherInvestment->id)->current_price);
    }

    public function test_investment_summary_reports_when_no_quotes_are_available(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $workspace->update(['type' => 'personal']);
        Http::fake(['*' => Http::response([], 503)]);
        $bitcoin = Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Bitcoin',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 1, 'average_price' => 80, 'current_price' => 100,
        ]);

        Livewire::test(Dashboard::class)
            ->call('refreshInvestmentPrices')
            ->assertSee('Nenhuma cotação foi atualizada.')
            ->assertSet('investmentRefreshFailed', true);
        $this->assertSame(100.0, $bitcoin->fresh()->current_price);
    }

    public function test_investment_summary_updates_cached_values_even_if_refresh_fails(): void
    {
        [$user, $workspace] = $this->signInWithWorkspace();
        $investment = Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Ativo',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 1, 'average_price' => 80, 'current_price' => 100,
        ]);
        $this->mock(InvestmentsHub::class)->shouldReceive('refreshPrices')->once()->with(true)->andReturnUsing(function () use ($investment) {
            $investment->update(['current_price' => 150]);

            return false;
        });

        Livewire::test(Dashboard::class)
            ->assertSee('100,00')
            ->call('refreshInvestmentPrices')
            ->assertSee('Não foi possível concluir a atualização.')
            ->assertSee('150,00')
            ->assertDispatched('dashboard-investments-updated');
    }

    public function test_investment_summary_updates_due_certificates_and_keeps_current_certificates_unchanged(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-13 12:00:00'));
        $otherUser = User::factory()->create();
        $otherWorkspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $workspace->update(['type' => 'personal']);
        Http::fake([
            'igcp-aforro.primor.me/*' => Http::response([
                'generatedAt' => now()->toIso8601String(),
                'series' => ['E' => ['monthlyBaseRates' => [['month' => '2026-08', 'basePct' => 2]]]],
            ]),
            'api.coingecko.com/*' => Http::response(['bitcoin' => ['eur' => 150]]),
            '*' => Http::response([]),
        ]);
        Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Bitcoin',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 1, 'average_price' => 80, 'current_price' => 100,
        ]);
        $due = $this->createAforroCertificate($user, $workspace, ['valuation_date' => '2026-10-01', 'capitalization_date' => '2026-11-01']);
        $current = $this->createAforroCertificate($user, $workspace, ['valuation_date' => '2026-10-01', 'capitalization_date' => '2027-01-01']);
        $otherPersonal = Investment::withoutEvents(fn () => $this->createAforroCertificate($otherUser, $workspace, ['capitalization_date' => '2026-11-01']));
        $otherWorkspaceAsset = Investment::withoutEvents(fn () => $this->createAforroCertificate($otherUser, $otherWorkspace, ['capitalization_date' => '2026-11-01']));

        Livewire::test(Dashboard::class)
            ->call('refreshInvestmentPrices')
            ->assertSee('2 ativos atualizados.')
            ->assertSee('1 ativo sem alterações pendentes.')
            ->assertSet('investmentRefreshFailed', false);

        $this->assertSame(1.0036, $due->fresh()->current_price);
        $this->assertSame('2027-02-01', $due->fresh()->capitalization_date->toDateString());
        $this->assertSame(1.0, $current->fresh()->current_price);
        $this->assertSame(1.0, Investment::withoutGlobalScopes()->findOrFail($otherPersonal->id)->current_price);
        $this->assertSame(1.0, Investment::withoutGlobalScopes()->findOrFail($otherWorkspaceAsset->id)->current_price);
        $this->assertSame(1, InvestmentIncome::count());
    }

    public function test_investment_summary_updates_overdue_certificates_only_as_far_as_available_rates_allow(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-13 12:00:00'));
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $certificate = $this->createAforroCertificate($user, $workspace, ['capitalization_date' => '2026-05-01']);
        $rates = [
            ['month' => '2026-02', 'basePct' => 2],
            ['month' => '2026-05', 'basePct' => 2],
        ];
        Http::fake(function () use (&$rates) {
            return Http::response(['generatedAt' => now()->toIso8601String(), 'series' => ['E' => ['monthlyBaseRates' => $rates]]]);
        });

        Livewire::test(Dashboard::class)
            ->call('refreshInvestmentPrices')
            ->assertSee('1 ativo atualizado parcialmente por falta de dados.')
            ->assertSet('investmentRefreshFailed', true);
        $this->assertSame(2, InvestmentIncome::where('investment_id', $certificate->id)->count());
        $this->assertSame('2026-11-01', $certificate->fresh()->capitalization_date->toDateString());

        $rates[] = ['month' => '2026-08', 'basePct' => 2];
        $dashboard = Livewire::test(Dashboard::class)->call('refreshInvestmentPrices');
        $dashboard->assertSee('1 ativo atualizado.')->assertSet('investmentRefreshFailed', false);
        $dashboard->call('refreshInvestmentPrices')->assertSee('1 ativo sem alterações pendentes.');
        $this->assertSame(3, InvestmentIncome::where('investment_id', $certificate->id)->count());
        $this->assertSame('2027-02-01', $certificate->fresh()->capitalization_date->toDateString());
    }

    public function test_investment_summary_reports_stale_rates_without_blocking_other_assets(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-13 12:00:00'));
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $certificate = $this->createAforroCertificate($user, $workspace, ['capitalization_date' => '2026-11-01']);
        $this->createAforroCertificate($user, $workspace, ['capitalization_date' => null]);
        Http::fake([
            'igcp-aforro.primor.me/*' => Http::response([
                'generatedAt' => now()->subDays(61)->toIso8601String(),
                'series' => ['E' => ['monthlyBaseRates' => [['month' => '2026-08', 'basePct' => 2]]]],
            ]),
            'api.coingecko.com/*' => Http::response(['bitcoin' => ['eur' => 150]]),
            '*' => Http::response([]),
        ]);
        Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Bitcoin',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 1, 'average_price' => 80, 'current_price' => 100,
        ]);

        Livewire::test(Dashboard::class)
            ->call('refreshInvestmentPrices')
            ->assertSee('1 ativo atualizado.')
            ->assertSee('2 ativos não puderam ser atualizados.')
            ->assertSet('investmentRefreshFailed', true);
        $this->assertSame(1.0, $certificate->fresh()->current_price);
        $this->assertSame(0, InvestmentIncome::count());
    }

    public function test_investment_summary_refuses_invalid_rates_and_never_uses_the_generic_calculator_for_series_e(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-13 12:00:00'));
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $imported = $this->createAforroCertificate($user, $workspace, ['capitalization_date' => '2026-11-01', 'interest_rate' => 3]);
        $manual = $this->createAforroCertificate($user, $workspace, ['broker' => 'Outro', 'capitalization_date' => '2026-11-01', 'interest_rate' => 3]);
        Http::fake(['*' => Http::response([
            'generatedAt' => now()->toIso8601String(),
            'series' => ['E' => ['monthlyBaseRates' => [['month' => '2026-08', 'basePct' => 'inválido']]]],
        ])]);

        Livewire::test(Dashboard::class)
            ->call('refreshInvestmentPrices')
            ->assertSee('Nenhuma cotação foi atualizada.')
            ->assertSet('investmentRefreshFailed', true);
        $this->assertSame(1.0, $imported->fresh()->current_price);
        $this->assertSame(1.0, $manual->fresh()->current_price);
        $this->assertSame(0, InvestmentIncome::count());
    }

    public function test_investment_summary_reports_unavailable_rate_provider_without_blocking_market_quotes(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-13 12:00:00'));
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $certificate = $this->createAforroCertificate($user, $workspace, ['capitalization_date' => '2026-11-01']);
        Investment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id, 'name' => 'Bitcoin',
            'symbol' => 'BTC', 'type' => 'Cripto', 'quantity' => 1, 'average_price' => 80, 'current_price' => 100,
        ]);
        Http::fake([
            'igcp-aforro.primor.me/*' => Http::response([], 503),
            'api.coingecko.com/*' => Http::response(['bitcoin' => ['eur' => 150]]),
            '*' => Http::response([]),
        ]);

        Livewire::test(Dashboard::class)
            ->call('refreshInvestmentPrices')
            ->assertSee('1 ativo atualizado.')
            ->assertSee('1 ativo não pôde ser atualizado.')
            ->assertSet('investmentRefreshFailed', true);
        $this->assertSame(1.0, $certificate->fresh()->current_price);
        $this->assertSame(0, InvestmentIncome::count());
    }

    public function test_certificates_without_due_capitalization_do_not_request_rates_and_inconsistent_dates_are_rejected(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-13 12:00:00'));
        [$user, $workspace] = $this->signInWithWorkspace(false);
        $this->createAforroCertificate($user, $workspace, [
            'valuation_date' => '2026-10-01', 'capitalization_date' => '2027-01-01',
        ]);
        $inconsistent = $this->createAforroCertificate($user, $workspace, [
            'valuation_date' => '2026-11-02', 'capitalization_date' => '2026-11-01',
        ]);
        Http::fake();

        $result = app(AforroNetSeriesEUpdater::class)->runForWorkspace($workspace, $user);

        $this->assertSame(['updated' => 0, 'current' => 1, 'partial' => 0, 'unavailable' => 1], $result);
        $this->assertSame(1.0, $inconsistent->fresh()->current_price);
        $this->assertSame(0, InvestmentIncome::count());
        Http::assertNothingSent();
    }

    private function createAforroCertificate(User $user, Workspace $workspace, array $overrides = []): Investment
    {
        return Investment::create(array_merge([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Certificado de teste', 'symbol' => 'CAF-E', 'type' => 'Divida',
            'broker' => 'AforroNet', 'series' => 'E', 'product_type' => 'CA',
            'subscription_number' => '12345678', 'operation_date' => '2026-02-01',
            'valuation_date' => '2026-04-01', 'capitalization_date' => '2026-05-01',
            'quantity' => 1000, 'average_price' => 1, 'current_price' => 1,
        ], $overrides));
    }

    private function signInWithWorkspace(bool $fakeHttp = true): array
    {
        if ($fakeHttp) {
            Http::fake(['*' => Http::response([], 200)]);
        }
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
