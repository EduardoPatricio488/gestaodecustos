<?php

namespace Tests\Feature;

use App\Livewire\PersonalCalendar;
use App\Livewire\BankTransactionsHub;
use App\Models\CalendarVacation;
use App\Models\BankAccount;
use App\Models\BankTransfer;
use App\Models\Category;
use App\Models\DailyMealAllowance;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Reminder;
use App\Models\RecurringIncome;
use App\Models\SalaryPayment;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Smalot\PdfParser\Parser;
use Tests\TestCase;

class PersonalCalendarMealAllowanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_date_returns_to_today_across_year_boundaries(): void
    {
        $this->travelTo(Carbon::parse('2027-01-15'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Livewire::test(PersonalCalendar::class)
            ->assertDontSeeHtml('wire:click="goToToday"')
            ->assertDontSeeHtml('aria-label="Mês anterior"')
            ->assertDontSeeHtml('aria-label="Mês seguinte"')
            ->assertDontSee('Mês atual')
            ->assertSeeHtml('wire:click="resetSummaryDate"')
            ->assertDontSeeHtml('id="calendar-search"')
            ->assertSeeHtml('aria-label="Filtros"')
            ->assertDontSee('Próximos pagamentos · 15 dias')
            ->assertDontSee('Previsto vs. confirmado · mês')
            ->set('summaryDayInput', 10)
            ->set('summaryMonthInput', 12)
            ->set('summaryYearInput', 2026)
            ->call('applySummaryDate')
            ->assertSet('month', 12)
            ->assertSet('year', 2026)
            ->call('resetSummaryDate')
            ->assertSet('month', 1)
            ->assertSet('year', 2027)
            ->set('summaryDayInput', 10)
            ->set('summaryMonthInput', 2)
            ->set('summaryYearInput', 2027)
            ->call('applySummaryDate')
            ->assertSet('month', 2)
            ->call('resetSummaryDate')
            ->assertSet('month', 1)
            ->assertSet('year', 2027);
    }

    public function test_search_filters_calendar_records_without_changing_financial_totals(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $category = Category::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Geral',
            'slug' => 'geral',
        ]);
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 12,
            'description' => 'Almoço',
            'spent_at' => '2026-11-12',
        ]);
        BankTransfer::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'recipient' => 'Loja local',
            'direction' => 'out',
            'amount' => 25,
            'currency' => 'EUR',
            'description' => 'Transferência',
            'status' => 'completed',
            'transferred_at' => '2026-11-13',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->set('search', 'LOJA')
            ->call('openDayDetail', '2026-11-13')
            ->assertSee('Transferência')
            ->assertSee('Loja local');
        $this->assertSame(12.0, $calendar->instance()->monthSummary['outflow']);
        $this->assertFalse($calendar->instance()->visibleDayEvents->has('2026-11-12'));

        $calendar->set('search', 'almoço');
        $this->assertTrue($calendar->instance()->visibleDayEvents->has('2026-11-12'));
        $this->assertFalse($calendar->instance()->visibleDayEvents->has('2026-11-13'));
    }

    public function test_symbol_filters_distinguish_meal_statuses_and_other_calendar_indicators(): void
    {
        $this->travelTo(Carbon::parse('2026-11-13 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);
        foreach (['2026-11-11' => 5, '2026-11-12' => 0] as $date => $amount) {
            DailyMealAllowance::create([
                'recurring_income_id' => $contract->id, 'user_id' => $user->id,
                'workspace_id' => $workspace->id, 'allowance_date' => $date,
                'expected_amount' => 10.46, 'received_amount' => $amount,
            ]);
        }
        CalendarVacation::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'start_date' => '2026-11-16', 'end_date' => '2026-11-16',
        ]);
        Reminder::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'title' => 'Lembrete de teste', 'remind_at' => '2026-11-13 09:00:00',
        ]);
        Income::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'amount' => 100, 'description' => 'Receita de teste', 'received_at' => '2026-11-13',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('wire:click="toggleFilter(\'meal_allowance_partial\')"')
            ->assertSeeHtml('wire:click="toggleFilter(\'holidays\')"')
            ->assertSeeHtml('wire:click="toggleFilter(\'vacations\')"');
        $summary = $calendar->instance()->monthSummary;

        $calendar->call('toggleFilter', 'meal_allowance_partial');
        $events = $calendar->instance()->visibleDayEvents->flatten(1);
        $this->assertCount(1, $events);
        $this->assertSame('2026-11-11', $events->first()['date']);
        $this->assertSame('partial', $events->first()['status']);
        $calendar->assertSeeHtml('aria-pressed="true"');

        $calendar->call('toggleFilter', 'meal_allowance_missed');
        $this->assertEqualsCanonicalizing(
            ['partial', 'missed'],
            $calendar->instance()->visibleDayEvents->flatten(1)->pluck('status')->all(),
        );
        $calendar->call('toggleFilter', 'meal_allowance_partial')->call('toggleFilter', 'meal_allowance_missed');
        $this->assertGreaterThan(2, $calendar->instance()->visibleDayEvents->flatten(1)->count());

        foreach ([
            'meal_allowance' => ['meal_allowance', 'expected'],
            'paydays' => ['payday', null],
            'work_milestones' => ['work_milestone', null],
            'vacations' => ['vacation', null],
            'holidays' => ['public_holiday', null],
        ] as $filter => [$subtype, $status]) {
            $calendar->call('toggleFilter', $filter);
            $events = $calendar->instance()->visibleDayEvents->flatten(1);
            $this->assertNotEmpty($events, $filter);
            $this->assertTrue($events->every(fn ($event) => ($event['subtype'] ?? null) === $subtype), $filter);
            if ($status) {
                $this->assertTrue($events->contains('status', $status));
                $this->assertFalse($events->contains('status', 'partial'));
                $this->assertFalse($events->contains('status', 'missed'));
            }
            $calendar->call('toggleFilter', $filter);
        }

        $calendar->call('toggleFilter', 'reminders');
        $this->assertSame(['Lembrete de teste'], $calendar->instance()->visibleDayEvents->flatten(1)->pluck('label')->all());
        $calendar->call('toggleFilter', 'reminders')->call('toggleFilter', 'incomes');
        $events = $calendar->instance()->visibleDayEvents->flatten(1);
        $this->assertTrue($events->contains('label', 'Receita de teste'));
        $this->assertFalse($events->contains(fn ($event) => in_array($event['subtype'] ?? null, ['meal_allowance', 'payday'], true)));
        $this->assertSame($summary, $calendar->instance()->monthSummary);
        $this->assertMatchesRegularExpression('~wire:click="openDayDetail\(\'2026-11-01\'\)"[^>]*data-day-type="holiday"~', $calendar->html());
    }

    public function test_expense_comparison_uses_previous_month_and_current_workspace(): void
    {
        $this->travelTo(Carbon::parse('2027-01-15'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        foreach (['2026-12-01' => 40, '2027-01-31' => 55] as $date => $amount) {
            Expense::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'amount' => $amount,
                'description' => 'Despesa',
                'spent_at' => $date,
            ]);
        }

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSee('+15,00 € face ao mês anterior');
        $this->assertSame(['previous' => 40.0, 'difference' => 15.0], $calendar->instance()->expenseComparison);
    }

    public function test_calendar_clock_schedules_refresh_at_six_and_midnight(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 17:59:30'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(30000, $calendar->instance()->refreshCalendarClock());

        $this->travelTo(Carbon::parse('2026-11-12 18:00:00'));
        $this->assertSame(21600000, $calendar->instance()->refreshCalendarClock());
    }

    public function test_month_widgets_include_only_events_up_to_today(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        foreach ([
            ['2026-10-30', 40, 4, 7],
            ['2026-11-11', 10, 1, 4],
            ['2026-11-12', 20.20, 2.50, 5],
            ['2026-11-13', 30, 3, 6],
            ['2026-12-01', 50, 5, 8],
        ] as [$date, $income, $expense, $debt]) {
            Income::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'amount' => $income, 'description' => 'Receita', 'received_at' => $date,
            ]);
            Expense::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'amount' => $expense, 'description' => 'Despesa', 'spent_at' => $date,
            ]);
            Debt::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'type' => 'owe', 'person_name' => 'Pessoa', 'amount' => $debt,
                'due_at' => $date, 'is_paid' => false,
            ]);
        }

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('aria-label="Escolher data dos totais: 12/11/2026"')
            ->assertDontSee('Totais do mês selecionado até')
            ->assertSee('Total até hoje')
            ->assertSee('Entradas menos saídas do mês')
            ->assertSee('Despesas, assinaturas e parcelas incluídas.')
            ->assertSee('30,20€')
            ->assertSee('12,50€')
            ->assertSee('17,70€')
            ->assertSee('Média diária do balanço')
            ->assertSee('1,48€')
            ->assertSee('12 dias decorridos')
            ->assertDontSee('Risco hoje');
        $summary = $calendar->instance()->monthSummary;
        $this->assertSame([30.2, 12.5, 17.7], [$summary['income'], $summary['outflow'], $summary['net_total']]);
        $this->assertSame([12, 1.48], [$summary['elapsed_days'], $summary['daily_average']]);

        $calendar->call('prevMonth')->assertSee('0,94€')->assertSee('31 dias decorridos');
        $summary = $calendar->instance()->monthSummary;
        $this->assertSame([40.0, 11.0, 29.0], [$summary['income'], $summary['outflow'], $summary['net_total']]);
        $this->assertSame([31, 0.94], [$summary['elapsed_days'], $summary['daily_average']]);

        $calendar->call('nextMonth')->call('nextMonth')
            ->assertSee('O mês selecionado ainda não começou.')
            ->assertSee('Sem dias decorridos neste mês.');
        $summary = $calendar->instance()->monthSummary;
        $this->assertSame([0.0, 0.0, 0.0], [$summary['income'], $summary['outflow'], $summary['net_total']]);
        $this->assertSame([0, null], [$summary['elapsed_days'], $summary['daily_average']]);

        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'amount' => 50, 'description' => 'Despesa adicional', 'spent_at' => '2026-11-12',
        ]);
        $calendar = Livewire::test(PersonalCalendar::class)->assertSee('-32,30€')->assertSee('-2,69€');
        $this->assertSame(-32.3, $calendar->instance()->monthSummary['net_total']);
        $this->assertSame(-2.69, $calendar->instance()->monthSummary['daily_average']);
    }

    public function test_summary_date_filters_totals_and_synchronizes_calendar_month(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 12:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        foreach ([
            ['2026-11-11', 10],
            ['2026-11-12', 20],
            ['2026-11-13', 30],
            ['2026-11-14', 40],
            ['2026-12-02', 50],
            ['2027-01-10', 100],
        ] as [$date, $amount]) {
            Income::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'amount' => $amount, 'description' => 'Receita', 'received_at' => $date,
            ]);
        }
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'amount' => 5, 'description' => 'Despesa', 'spent_at' => '2026-11-13',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('aria-controls="calendar-summary-date"')
            ->assertSee('Voltar a hoje')
            ->assertSeeHtml('id="summary-day"')
            ->assertSeeHtml('id="summary-month"')
            ->assertSeeHtml('id="summary-year"')
            ->assertDontSee('Ver totais')
            ->set('summaryDayInput', 11)
            ->call('applySummaryDate')
            ->assertSeeHtml('aria-label="Escolher data dos totais: 11/11/2026"')
            ->assertSee('Total até à data');
        $summary = $calendar->instance()->monthSummary;
        $this->assertSame([10.0, 0.0, 10.0, 11, 0.91], [
            $summary['income'], $summary['outflow'], $summary['net_total'], $summary['elapsed_days'], $summary['daily_average'],
        ]);

        $calendar->set('summaryDayInput', 12)->call('applySummaryDate');
        $this->assertSame(10.0, $calendar->instance()->monthSummary['income']);
        $calendar->set('summaryDayInput', 13)
            ->call('applySummaryDate')
            ->assertSee('Inclui valores previstos até à data selecionada.');
        $summary = $calendar->instance()->monthSummary;
        $this->assertSame([60.0, 5.0, 55.0, 13, 4.23], [
            $summary['income'], $summary['outflow'], $summary['net_total'], $summary['elapsed_days'], $summary['daily_average'],
        ]);

        $calendar->set('summaryDayInput', 2)->set('summaryMonthInput', 12)->call('applySummaryDate')
            ->assertSet('month', 12)
            ->assertSet('year', 2026)
            ->assertSeeHtml('aria-label="Escolher data dos totais: 02/12/2026"');
        $this->assertSame([50.0, 2, 25.0], [
            $calendar->instance()->monthSummary['income'],
            $calendar->instance()->monthSummary['elapsed_days'],
            $calendar->instance()->monthSummary['daily_average'],
        ]);
        $calendar->call('prevMonth')->assertSet('month', 11)->assertSet('summaryDayInput', 2)->assertSet('summaryMonthInput', 11);
        $calendar->call('nextMonth')->assertSet('summaryDayInput', 2)->assertSet('summaryMonthInput', 12);
        $calendar->set('summaryDayInput', 31)->set('summaryMonthInput', 1)->set('summaryYearInput', 2027)->call('applySummaryDate')
            ->assertSet('year', 2027)->assertSet('month', 1)
            ->call('nextMonth')->assertSet('summaryDayInput', 28)->assertSet('summaryMonthInput', 2);

        $calendar->set('summaryDayInput', 30)->call('applySummaryDate')
            ->assertHasErrors(['summaryDayInput'])
            ->assertSee('Este dia não existe no mês e ano selecionados.')
            ->assertSet('month', 2)
            ->assertSeeHtml('aria-label="Escolher data dos totais: 28/02/2027"');
        $calendar->call('resetSummaryDate')
            ->assertHasNoErrors()
            ->assertSet('month', 11)
            ->assertSet('year', 2026)
            ->assertSet('summaryDayInput', 12)
            ->assertSet('summaryMonthInput', 11)
            ->assertSet('summaryYearInput', 2026)
            ->assertSeeHtml('aria-label="Escolher data dos totais: 12/11/2026"');
        $this->assertSame(10.0, $calendar->instance()->monthSummary['income']);

        $this->travelTo(Carbon::parse('2026-11-13 00:00:00'));
        $calendar->call('refreshCalendarClock')
            ->assertSet('summaryDayInput', 13)
            ->assertSet('summaryMonthInput', 11)
            ->assertSet('summaryYearInput', 2026)
            ->assertSeeHtml('aria-label="Escolher data dos totais: 13/11/2026"');
    }

    public function test_daily_average_counts_empty_days_and_leap_months(): void
    {
        $this->travelTo(Carbon::parse('2024-03-01 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        Income::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'amount' => 29, 'description' => 'Receita', 'received_at' => '2024-02-15',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSee('0,00€')
            ->assertSee('1 dia decorrido');
        $this->assertSame([1, 0.0], [
            $calendar->instance()->monthSummary['elapsed_days'],
            $calendar->instance()->monthSummary['daily_average'],
        ]);
        $calendar->call('prevMonth')->assertSee('1,00€')->assertSee('29 dias decorridos');
        $this->assertSame([29, 1.0], [
            $calendar->instance()->monthSummary['elapsed_days'],
            $calendar->instance()->monthSummary['daily_average'],
        ]);
    }

    public function test_today_entries_are_counted_at_six_without_delaying_outflows(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 00:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->update(['metadata' => array_merge($contract->metadata, ['start_date' => '2026-11-12'])]);
        foreach ([['2026-11-11', 10], ['2026-11-12', 20], ['2026-11-13', 30]] as [$date, $amount]) {
            Income::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'description' => 'Receita', 'amount' => $amount, 'received_at' => $date,
            ]);
        }
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa', 'amount' => 5, 'spent_at' => '2026-11-12',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSee('Entradas de hoje contam a partir das 18h.')
            ->call('openDayDetail', '2026-11-12')
            ->assertSee('À espera');
        $summary = $calendar->instance()->monthSummary;
        $forecast = $calendar->instance()->monthlyFinanceInsights;
        $this->assertSame([10.0, 5.0, 5.0], [$summary['income'], $summary['outflow'], $summary['net_total']]);
        $this->assertSame(0.42, $summary['daily_average']);

        $this->travelTo(Carbon::parse('2026-11-12 17:59:59'));
        $calendar->call('refreshCalendarClock');
        $this->assertSame(10.0, $calendar->instance()->monthSummary['income']);

        $this->travelTo(Carbon::parse('2026-11-12 18:00:00'));
        $calendar->call('refreshCalendarClock');
        $summary = $calendar->instance()->monthSummary;
        $this->assertSame([120.91, 5.0, 115.91], [$summary['income'], $summary['outflow'], $summary['net_total']]);
        $this->assertSame(9.66, $summary['daily_average']);
        $this->assertSame($forecast, $calendar->instance()->monthlyFinanceInsights);
        $calendar->assertSee('Pago');
    }

    public function test_account_balance_and_risk_projection_still_use_movements_up_to_today(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id, 'currency' => 'EUR']);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Conta', 'balance' => 100, 'include_in_total' => true,
        ]);
        foreach ([['2026-11-11', 20], ['2026-11-12', 5], ['2026-11-13', 400]] as [$date, $amount]) {
            Income::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'bank_account_id' => $account->id, 'amount' => $amount,
                'description' => 'Receita', 'received_at' => $date,
            ]);
        }
        foreach ([['2026-11-11', 25], ['2026-11-12', 30], ['2026-11-13', 500]] as [$date, $amount]) {
            Expense::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'bank_account_id' => $account->id, 'amount' => $amount,
                'description' => 'Despesa', 'spent_at' => $date,
            ]);
        }

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(70.0, $calendar->instance()->currentAccountBalance['balance']);
        $this->assertSame(-30.0, $calendar->instance()->cashProjection['final_balance']);
        $calendar->call('prevMonth');
        $this->assertSame(70.0, $calendar->instance()->currentAccountBalance['balance']);
        $calendar->call('nextMonth')->call('nextMonth');
        $this->assertSame(70.0, $calendar->instance()->currentAccountBalance['balance']);

        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'bank_account_id' => $account->id, 'amount' => 100,
            'description' => 'Despesa de hoje', 'spent_at' => '2026-11-12',
        ]);
        $this->assertSame(-30.0, Livewire::test(PersonalCalendar::class)->instance()->currentAccountBalance['balance']);
    }

    public function test_risk_projection_starts_from_included_account_balance_and_carries_into_next_month(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $category = Category::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Assinaturas', 'slug' => 'assinaturas',
        ]);
        $account = BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Incluída', 'balance' => 100, 'include_in_total' => true,
        ]);
        BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Excluída', 'balance' => 500, 'include_in_total' => false,
        ]);
        Income::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'bank_account_id' => $account->id, 'amount' => 20,
            'description' => 'Receita', 'received_at' => '2026-11-10',
        ]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'bank_account_id' => $account->id, 'amount' => 30,
            'description' => 'Despesa registada', 'spent_at' => '2026-11-11',
        ]);
        Subscription::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'category_id' => $category->id, 'name' => 'Assinatura', 'amount' => 120, 'billing_day' => 13,
            'is_active' => true, 'paid_by_company' => false,
        ]);

        $calendar = Livewire::test(PersonalCalendar::class);
        $projection = $calendar->instance()->cashProjection;
        $this->assertFalse($projection['risk_days']->has('2026-11-12'));
        $this->assertSame(-30.0, $projection['risk_days']->get('2026-11-13')['projected_balance']);
        $this->assertSame(-30.0, $projection['final_balance']);
        $calendar->call('nextMonth');
        $projection = $calendar->instance()->cashProjection;
        $this->assertSame(-30.0, $projection['risk_days']->get('2026-12-01')['projected_balance']);
        $this->assertSame(-150.0, $projection['risk_days']->get('2026-12-13')['projected_balance']);
        $calendar->call('prevMonth')->call('prevMonth');
        $this->assertFalse($calendar->instance()->cashProjection['available']);
    }

    public function test_risk_projection_does_not_add_different_account_currencies(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id, 'currency' => 'EUR']);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Outra moeda', 'balance' => 100, 'currency' => 'USD', 'include_in_total' => true,
        ]);

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame('currency', $calendar->instance()->cashProjection['reason']);
    }

    public function test_risk_projection_handles_debts_only_for_included_accounts(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Incluída', 'balance' => 100, 'include_in_total' => true,
        ]);
        $excluded = BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Excluída', 'balance' => 0, 'include_in_total' => false,
        ]);
        foreach ([
            [$account->id, 'owe', 70, '2026-11-13'],
            [$account->id, 'owed', 20, '2026-11-14'],
            [$excluded->id, 'owe', 1000, '2026-11-15'],
        ] as [$accountId, $type, $amount, $date]) {
            Debt::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'bank_account_id' => $accountId, 'type' => $type,
                'person_name' => 'Pessoa', 'amount' => $amount, 'due_at' => $date, 'is_paid' => false,
            ]);
        }

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(50.0, $calendar->instance()->cashProjection['final_balance']);
        $this->assertTrue($calendar->instance()->cashRiskDays->isEmpty());
        $this->assertSame(20.0, $calendar->instance()->paymentSummary['receivable']);
        $this->assertSame(1070.0, $calendar->instance()->paymentSummary['payable']);
    }

    public function test_risk_projection_adds_salary_on_payment_day_only_if_not_already_paid(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Conta', 'balance' => 50, 'include_in_total' => true,
        ]);
        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->update(['bank_account_id' => $account->id]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'amount' => 120, 'description' => 'Despesa futura', 'spent_at' => '2026-11-13',
        ]);
        $projection = Livewire::test(PersonalCalendar::class)->instance()->cashProjection;
        $this->assertSame(-70.0, $projection['risk_days']->get('2026-11-24')['projected_balance']);
        $this->assertFalse($projection['risk_days']->has('2026-11-25'));
        $this->assertSame(1699.88, $projection['final_balance']);

        $payment = SalaryPayment::create([
            'recurring_income_id' => $contract->id, 'user_id' => $user->id,
            'workspace_id' => $workspace->id, 'bank_account_id' => $account->id,
            'payment_month' => 11, 'payment_year' => 2026,
            'meal_allowance' => 100, 'net_received' => 1600,
            'status' => 'pending', 'bank_balance_amount' => 0,
        ]);
        $projection = Livewire::test(PersonalCalendar::class)->instance()->cashProjection;
        $this->assertSame(1430.0, $projection['final_balance']);

        $payment->update([
            'meal_allowance' => 230.12, 'net_received' => 2000,
            'paid_at' => '2026-11-12', 'status' => 'paid', 'bank_balance_amount' => 1769.88,
        ]);
        $account->increment('balance', 1769.88);
        $projection = Livewire::test(PersonalCalendar::class)->instance()->cashProjection;
        $this->assertSame(1699.88, $projection['final_balance']);
    }

    public function test_pending_summary_and_upcoming_payments_use_confirmed_amounts_across_months(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $category = Category::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Assinaturas', 'slug' => 'assinaturas',
        ]);
        foreach (['2026-11-20' => 100, '2026-11-30' => 50] as $date => $amount) {
            Income::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'amount' => $amount, 'description' => 'Receita', 'received_at' => $date,
            ]);
        }
        foreach ([
            ['2026-11-26', 40, 10, 'pendente'],
            ['2026-11-20', 20, 20, 'paid'],
            ['2026-12-02', 15, 0, 'pendente'],
            ['2026-12-10', 99, 0, 'pendente'],
        ] as [$date, $amount, $paid, $status]) {
            Expense::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'amount' => $amount, 'amount_paid' => $paid, 'status' => $status,
                'description' => 'Despesa '.$date, 'spent_at' => $date,
            ]);
        }
        $subscription = Subscription::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'category_id' => $category->id, 'name' => 'Subscrição', 'amount' => 30, 'billing_day' => 27,
            'is_active' => true, 'paid_by_company' => false,
        ]);
        SubscriptionPayment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'subscription_id' => $subscription->id, 'billing_date' => '2026-11-27',
            'amount' => 12, 'paid_at' => '2026-11-24',
        ]);
        $cancelled = Subscription::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'category_id' => $category->id, 'name' => 'Cancelada', 'amount' => 9,
            'billing_day' => 22, 'is_active' => false, 'paid_by_company' => false,
        ]);
        SubscriptionPayment::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'subscription_id' => $cancelled->id, 'billing_date' => '2026-11-22',
            'amount' => 9, 'paid_at' => '2026-11-22',
        ]);
        $settledExpense = Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'amount' => 8, 'description' => 'Dívida liquidada', 'spent_at' => '2026-11-22',
        ]);
        Debt::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'type' => 'owe', 'person_name' => 'Pessoa', 'amount' => 8,
            'is_paid' => true, 'expense_id' => $settledExpense->id,
        ]);

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame([
            'received' => 100.0, 'receivable' => 50.0, 'paid' => 59.0, 'payable' => 48.0,
        ], $calendar->instance()->paymentSummary);
        $upcoming = $calendar->instance()->upcomingPayments;
        $this->assertSame(['2026-11-26', '2026-11-27', '2026-12-02'], $upcoming->pluck('date')->all());
        $this->assertSame([30.0, 18.0, 15.0], $upcoming->pluck('outstanding_amount')->all());
        $calendar->set('search', 'outra coisa')->call('openUpcomingPayment', '2026-12-02')
            ->assertSet('month', 12)
            ->assertSet('search', '')
            ->assertSee('Despesa 2026-12-02');
    }

    public function test_upcoming_subscriptions_respect_billing_cycles_and_unknown_dates(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $category = Category::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Assinaturas', 'slug' => 'assinaturas',
        ]);
        foreach ([
            ['Anual', 'annual', '2026-12-02'],
            ['Trimestral', 'quarterly', '2027-01-03'],
            ['Sem data', 'annual', null],
            ['Renovação futura', 'monthly', '2027-01-02'],
        ] as [$name, $cycle, $renewal]) {
            Subscription::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'category_id' => $category->id, 'name' => $name, 'amount' => 10,
                'billing_day' => 2, 'cycle' => $cycle, 'renewal_date' => $renewal,
                'is_active' => true, 'paid_by_company' => false,
            ]);
        }

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(['Anual'], $calendar->instance()->upcomingPayments->pluck('label')->all());
        $this->assertSame(['2026-12-02'], $calendar->instance()->upcomingPayments->pluck('date')->all());
    }

    public function test_payment_summary_does_not_count_unconfirmed_salary_or_meal_allowance_as_received(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(0.0, $calendar->instance()->paymentSummary['received']);
        $this->assertGreaterThan(0, $calendar->instance()->paymentSummary['receivable']);

        SalaryPayment::create([
            'recurring_income_id' => $contract->id, 'user_id' => $user->id,
            'workspace_id' => $workspace->id, 'payment_month' => 11, 'payment_year' => 2026,
            'meal_allowance' => 230.12, 'net_received' => 2000,
            'paid_at' => '2026-11-12', 'status' => 'paid',
        ]);
        DailyMealAllowance::create([
            'recurring_income_id' => $contract->id, 'user_id' => $user->id,
            'workspace_id' => $workspace->id, 'allowance_date' => '2026-11-12',
            'expected_amount' => 10.46, 'received_amount' => 5,
        ]);
        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(1774.88, $calendar->instance()->paymentSummary['received']);
        $this->assertGreaterThanOrEqual(5.46, $calendar->instance()->paymentSummary['receivable']);
    }

    public function test_meal_allowance_is_in_pending_summary_before_the_daily_cutoff(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 17:59:59'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);
        $before = Livewire::test(PersonalCalendar::class)->instance()->paymentSummary;

        $this->travelTo(Carbon::parse('2026-11-12 18:00:00'));
        $after = Livewire::test(PersonalCalendar::class)->instance()->paymentSummary;
        $this->assertSame($before['receivable'], $after['receivable']);
        $this->assertSame(0.0, $after['received']);
    }

    public function test_salary_paid_before_its_reference_month_is_not_projected_again(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Conta', 'balance' => 1769.88, 'include_in_total' => true,
        ]);
        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->update(['bank_account_id' => $account->id]);
        SalaryPayment::create([
            'recurring_income_id' => $contract->id, 'user_id' => $user->id,
            'workspace_id' => $workspace->id, 'bank_account_id' => $account->id,
            'payment_month' => 11, 'payment_year' => 2026,
            'meal_allowance' => 230.12, 'net_received' => 2000,
            'paid_at' => '2026-10-31', 'status' => 'paid', 'bank_balance_amount' => 1769.88,
        ]);

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertSame(1769.88, $calendar->instance()->cashProjection['final_balance']);
        $this->assertSame(1769.88, $calendar->instance()->paymentSummary['received']);
    }

    public function test_calendar_generates_and_edits_daily_meal_allowance_records(): void
    {
        $this->travelTo(now()->startOfMonth()->addDays(10)->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $contract = $this->createMealAllowanceContract($user, $workspace);
        $date = now()->startOfMonth()->addDays(10)->toDateString();
        SalaryPayment::create([
            'recurring_income_id' => $contract->id,
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'payment_month' => now()->month,
            'payment_year' => now()->year,
            'meal_allowance' => 230.12,
            'net_received' => 2000,
            'paid_at' => $date,
            'status' => 'paid',
        ]);
        $category = Category::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Alimentação',
            'slug' => 'alimentacao',
        ]);
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 6,
            'description' => 'Croissant Nutela',
            'spent_at' => $date,
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertDontSeeHtml('title="Croissant Nutela"')
            ->assertDontSeeHtml('title="Receitas do dia"')
            ->assertDontSeeHtml('title="Despesas do dia"')
            ->assertSeeHtml('title="Total do dia"')
            ->call('openDayDetail', $date)
            ->assertSet('showDayDetailModal', true)
            ->assertSee('Subsídio de alimentação')
            ->assertSee('Pago')
            ->assertSee('+10,46€')
            ->assertSee('Salário recebido (valor diário)')
            ->assertSee('+80,45€')
            ->assertSee('Receitas')
            ->assertSee('Despesas')
            ->assertSee('90,91€')
            ->assertSee('−6,00€')
            ->assertSee('Total do dia')
            ->assertSee('+84,91€')
            ->assertSeeHtml('text-emerald-600 dark:text-emerald-400')
            ->assertDontSee('Subsídio de alimentação diário')
            ->assertSee('Entradas previstas no mês')
            ->assertSee('Saídas previstas no mês')
            ->assertSee('Saldo previsto do mês')
            ->assertSee('Balanço do ano')
            ->assertDontSee('Maior saída do mês')
            ->assertSee('6,00 €')
            ->assertSee('Croissant Nutela')
            ->assertDontSee('Início do trabalho')
            ->assertDontSee('Fim do trabalho')
            ->assertDontSee('Valor recebido')
            ->assertSee('Editar')
            ->call('beginEditMealAllowance', $contract->id, $date)
            ->assertSee('Valor recebido')
            ->assertSee('Guardar dia')
            ->assertDontSeeHtml('wire:model="eventEditForm.label"')
            ->assertDontSeeHtml('wire:model="eventEditForm.date"')
            ->assertDontSeeHtml('wire:model="eventEditForm.amount"')
            ->assertDontSeeHtml('wire:click="saveEvent(')
            ->set('mealAllowanceForms.'.$contract->id.'.received_amount', '0')
            ->set('mealAllowanceForms.'.$contract->id.'.notes', 'Não recebido neste dia')
            ->call('saveMealAllowanceDay', $contract->id, $date)
            ->assertSee('Subsídio não recebido')
            ->assertSee('Não recebido neste dia')
            ->assertDontSee('Valor recebido')
            ->assertSee('+0,00€')
            ->assertSee('Total do dia')
            ->assertSee('+74,45€')
            ->assertDispatched('toast');

        $this->assertMatchesRegularExpression(
            '~data-calendar-event-type="salary"[^>]*aria-label="Subsídio de alimentação"[^>]*class="[^"]*text-red-600 dark:text-red-400~',
            $calendar->html()
        );

        $record = DailyMealAllowance::where('workspace_id', $workspace->id)
            ->where('recurring_income_id', $contract->id)
            ->whereDate('allowance_date', $date)
            ->firstOrFail();

        $this->assertSame(10.46, (float) $record->expected_amount);
        $this->assertSame(0.0, (float) $record->received_amount);
        $this->assertNull($record->work_started_at);
        $this->assertNull($record->work_ended_at);
        $this->assertSame('Não recebido neste dia', $record->notes);
    }

    public function test_today_total_stops_being_pending_at_six_but_future_totals_remain_pending(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(17, 59, 59));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class);
        $html = $calendar->html();

        foreach ([
            '2026-11-11' => ['Total do dia', '90,91'],
            '2026-11-12' => ['Total previsto (pendente)', '90,91'],
            '2026-11-13' => ['Total previsto (pendente)', '90,91'],
        ] as $date => [$title, $amount]) {
            $this->assertMatchesRegularExpression(
                '~wire:click="openDayDetail\(\''.preg_quote($date, '~').'\'\)"(?:(?!wire:click="openDayDetail).)*?title="'.preg_quote($title, '~').'"(?:(?!wire:click="openDayDetail).)*?\+'.preg_quote($amount, '~').'€~s',
                $html
            );
        }

        $calendar->call('openDayDetail', '2026-11-11')
            ->assertSeeHtml('>Total do dia</p>')
            ->call('openDayDetail', '2026-11-12')
            ->assertSeeHtml('>Total previsto</p>')
            ->assertSee('+90,91€')
            ->call('openDayDetail', '2026-11-13')
            ->assertSeeHtml('>Total previsto</p>')
            ->assertSee('+90,91€');

        $this->travelTo(Carbon::parse('2026-11-12')->setTime(18, 0));
        $calendar = Livewire::test(PersonalCalendar::class);
        $html = $calendar->html();

        foreach ([
            '2026-11-12' => 'Total do dia',
            '2026-11-13' => 'Total previsto (pendente)',
        ] as $date => $title) {
            $this->assertMatchesRegularExpression(
                '~wire:click="openDayDetail\(\''.preg_quote($date, '~').'\'\)"(?:(?!wire:click="openDayDetail).)*?title="'.preg_quote($title, '~').'"(?:(?!wire:click="openDayDetail).)*?\+90,91€~s',
                $html
            );
        }

        $calendar->call('openDayDetail', '2026-11-12')
            ->assertSeeHtml('>Total do dia</p>')
            ->assertSee('Pago')
            ->assertSee('+90,91€')
            ->call('openDayDetail', '2026-11-13')
            ->assertSeeHtml('>Total previsto</p>');
    }

    public function test_meal_allowances_have_a_separate_icon_and_are_grouped_together(): void
    {
        $this->travelTo(Carbon::parse('2026-11-13 19:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);
        $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('aria-label="Subsídio de alimentação: 2 registos"')
            ->call('openDayDetail', '2026-11-13');

        $events = $calendar->instance()->selectedDayEvents;
        $this->assertSame('cake', $events->firstWhere('subtype', 'meal_allowance')['calendar_icon']);
        $this->assertSame('banknotes', $events->firstWhere('subtype', 'salary_projection')['calendar_icon']);
        $this->assertSame(2, substr_count($calendar->html(), 'aria-label="Ícone: Subsídio de alimentação"'));
        $calendar->assertSeeHtml('aria-label="Ícone: Receita prevista"');
    }

    public function test_calendar_shows_an_icon_for_each_record_and_transfer_details_on_the_day(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $category = Category::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Geral',
            'slug' => 'geral',
        ]);
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 8,
            'description' => 'Almoço de teste',
            'spent_at' => '2026-11-13',
        ]);
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 4,
            'description' => 'Café de teste',
            'spent_at' => '2026-11-13',
        ]);
        Income::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Receita de teste',
            'amount' => 100,
            'received_at' => '2026-11-13',
        ]);
        Debt::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'type' => 'owe',
            'person_name' => 'Pessoa de teste',
            'description' => 'Parcela de teste',
            'amount' => 20,
            'due_at' => '2026-11-13',
            'is_paid' => false,
        ]);
        Subscription::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'name' => 'Assinatura de teste',
            'amount' => 12,
            'billing_day' => 13,
            'is_active' => true,
        ]);
        Reminder::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'title' => 'Lembrete de teste',
            'remind_at' => '2026-11-13 09:00:00',
        ]);
        $transfer = BankTransfer::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'recipient' => 'Destinatário de teste',
            'direction' => 'out',
            'amount' => 25,
            'currency' => 'EUR',
            'description' => 'Transferência de teste',
            'status' => 'completed',
            'transferred_at' => '2026-11-13 12:00:00',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('data-calendar-event-type="expense"')
            ->assertSeeHtml('data-calendar-event-type="income"')
            ->assertSeeHtml('data-calendar-event-type="debt"')
            ->assertSeeHtml('data-calendar-event-type="subscription"')
            ->assertSeeHtml('data-calendar-event-type="reminder"')
            ->assertSeeHtml('data-calendar-event-type="transfer"')
            ->assertSeeHtml('data-calendar-event-count="2"')
            ->assertSeeHtml('aria-label="Despesa: 2 registos"')
            ->assertDontSeeHtml('class="min-h-screen space-y-8 pb-16"')
            ->assertDontSeeHtml('class="max-w-7xl mx-auto space-y-10 pb-20"')
            ->call('openDayDetail', '2026-11-13')
            ->assertSee('Almoço de teste')
            ->assertSee('Café de teste')
            ->assertSee('Receita de teste')
            ->assertSee('Parcela de teste')
            ->assertSee('Assinatura de teste')
            ->assertSee('Lembrete de teste')
            ->assertSee('Transferência de teste')
            ->assertSee('Destinatário de teste')
            ->assertSee('25,00€')
            ->assertSee('Editar');
        $this->assertSame(2, substr_count($calendar->html(), 'aria-label="Ícone: Despesa"'));
        foreach (['Receita', 'Parcela', 'Assinatura', 'Lembrete', 'Transferência'] as $label) {
            $calendar->assertSeeHtml('aria-label="Ícone: '.$label.'"');
        }
        foreach ([
            'income' => 'text-emerald-600 dark:text-emerald-400',
            'expense' => 'text-red-600 dark:text-red-400',
            'debt' => 'text-amber-600 dark:text-amber-400',
            'subscription' => 'text-purple-600 dark:text-purple-400',
            'transfer' => 'text-blue-600 dark:text-blue-400',
            'reminder' => 'text-indigo-600 dark:text-indigo-400',
        ] as $type => $color) {
            $this->assertMatchesRegularExpression(
                '~data-calendar-event-type="'.preg_quote($type, '~').'"[^>]*class="[^"]*'.preg_quote($color, '~').'~',
                $calendar->html()
            );
        }
        $dayEvents = $calendar->instance()->dayEvents->get('2026-11-13');
        $this->assertSame('text-red-500', $dayEvents->firstWhere('type', 'expense')['color']);
        $this->assertSame('text-emerald-500', $dayEvents->firstWhere('type', 'income')['color']);
        $this->assertSame('text-purple-500', $dayEvents->firstWhere('type', 'subscription')['color']);
        $this->assertSame('text-indigo-500', $dayEvents->firstWhere('type', 'reminder')['color']);
        $this->assertSame('text-blue-500', $dayEvents->firstWhere('type', 'transfer')['color']);

        Livewire::test(BankTransactionsHub::class, ['dashboardMode' => true])
            ->dispatch('calendar-bank-transfer-edit', transferId: $transfer->id)
            ->assertSet('showEditModal', true)
            ->assertSet('editingMovementId', $transfer->id)
            ->assertSet('editDescription', 'Transferência de teste');
    }

    public function test_risk_days_use_a_detail_instead_of_red_cell_highlighting(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        BankAccount::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Conta',
            'balance' => 0,
            'include_in_total' => true,
        ]);

        $category = Category::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Geral',
            'slug' => 'geral',
        ]);
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 10,
            'description' => 'Despesa de teste',
            'spent_at' => '2026-11-13',
        ]);

        Livewire::test(PersonalCalendar::class)
            ->assertDontSeeHtml('bg-red-50/70')
            ->assertDontSeeHtml('ring-red-200')
            ->call('openDayDetail', '2026-11-13')
            ->assertSee('Dia de risco')
            ->assertSee('Saldo projetado acumulado negativo')
            ->assertSee('10,00€')
            ->assertSeeHtml('bg-indigo-50 dark:bg-indigo-950/30');
    }

    public function test_monthly_forecast_includes_future_income_and_all_known_outflows(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 17:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->metadata = array_merge($contract->metadata, ['start_date' => '2026-11-11']);
        $contract->save();
        Income::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Receita recebida',
            'amount' => 25,
            'received_at' => '2026-11-12',
        ]);
        Income::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Receita futura',
            'amount' => 500,
            'received_at' => '2026-11-13',
        ]);
        RecurringIncome::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Receita recorrente', 'amount' => 100,
            'day_of_month' => 25, 'is_active' => true, 'source' => 'outro', 'frequency' => 'mensal',
        ]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa futura', 'amount' => 40, 'spent_at' => '2026-11-13',
        ]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa anterior', 'amount' => 10, 'spent_at' => '2026-10-13',
        ]);
        $category = Category::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Assinaturas', 'slug' => 'assinaturas',
        ]);
        foreach ([['Mensal', 'monthly', 50], ['Data desconhecida', 'annual', 999]] as [$name, $cycle, $amount]) {
            Subscription::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'category_id' => $category->id, 'name' => $name, 'amount' => $amount,
                'billing_day' => 27, 'cycle' => $cycle, 'is_active' => true, 'paid_by_company' => false,
            ]);
        }
        foreach ([['owe', 60], ['owed', 70]] as [$type, $amount]) {
            Debt::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'type' => $type, 'person_name' => 'Pessoa', 'amount' => $amount,
                'due_at' => '2026-11-28', 'is_paid' => false,
            ]);
        }

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSee('Previsão para todo o mês de novembro')
            ->assertSee('Assinaturas sem ciclo ou data definidos não entram na previsão.')
            ->assertSee('1.967,74 €')
            ->assertSee('150,00 €')
            ->assertSee('1.817,74 €')
            ->assertSee('+30,00 € face ao mês anterior');
        $summary = $calendar->instance()->monthlyFinanceInsights;
        $this->assertSame(1967.74, $summary['received']);
        $this->assertSame(150.0, $summary['spent']);
        $this->assertSame(1817.74, $summary['balance']);
        $this->assertSame(60.0, $summary['largest_expense']['amount']);

        SalaryPayment::create([
            'recurring_income_id' => $contract->id, 'user_id' => $user->id,
            'workspace_id' => $workspace->id, 'payment_month' => 11, 'payment_year' => 2026,
            'meal_allowance' => 230.12, 'net_received' => 2000,
            'paid_at' => '2026-11-12', 'status' => 'paid',
        ]);
        $summary = Livewire::test(PersonalCalendar::class)->instance()->monthlyFinanceInsights;
        $this->assertSame(2611.32, $summary['received']);
        $this->assertSame(2461.32, $summary['balance']);

        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa maior', 'amount' => 3000, 'spent_at' => '2026-11-30',
        ]);
        $calendar = Livewire::test(PersonalCalendar::class)->assertSee('-538,68 €');
        $this->assertSame(-538.68, $calendar->instance()->monthlyFinanceInsights['balance']);
        $this->assertSame('Despesa maior', $calendar->instance()->monthlyFinanceInsights['largest_expense']['label']);
    }

    public function test_annual_balance_covers_the_selected_year_and_includes_forecasts(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 17:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        foreach ([['2026-01-01', 100], ['2026-12-31', 200], ['2027-01-01', 20]] as [$date, $amount]) {
            Income::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'description' => 'Receita', 'amount' => $amount, 'received_at' => $date,
            ]);
        }
        foreach ([['2026-01-02', 10], ['2026-12-30', 400]] as [$date, $amount]) {
            Expense::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'description' => 'Despesa', 'amount' => $amount, 'spent_at' => $date,
            ]);
        }
        foreach ([['owed', 20], ['owe', 50]] as [$type, $amount]) {
            Debt::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'type' => $type, 'person_name' => 'Pessoa', 'amount' => $amount,
                'due_at' => '2026-12-15', 'is_paid' => false,
            ]);
        }
        $category = Category::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'name' => 'Assinaturas', 'slug' => 'assinaturas',
        ]);
        Subscription::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'category_id' => $category->id, 'name' => 'Mensal', 'amount' => 5,
            'billing_day' => 1, 'cycle' => 'monthly', 'started_at' => '2026-01-01',
            'is_active' => true, 'paid_by_company' => false,
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSee('Balanço do ano 2026')
            ->assertSee('-200,00 €')
            ->assertDontSee('Maior saída do mês')
            ->call('toggleFilter', 'holidays');
        $this->assertSame(-200.0, $calendar->instance()->annualFinanceInsights['balance']);

        $calendar->set('summaryYearInput', 2027)->call('applySummaryDate')
            ->assertSee('Balanço do ano 2027')
            ->assertSee('-40,00 €');
        $this->assertSame(-40.0, $calendar->instance()->annualFinanceInsights['balance']);
    }

    public function test_monthly_forecast_uses_the_selected_future_month(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 17:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        Income::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Receita futura', 'amount' => 100, 'received_at' => '2026-12-20',
        ]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa futura', 'amount' => 30, 'spent_at' => '2026-12-21',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)->call('nextMonth');
        $summary = $calendar->instance()->monthlyFinanceInsights;
        $this->assertSame([100.0, 30.0, 70.0], [$summary['received'], $summary['spent'], $summary['balance']]);
        $this->assertSame(0.0, $calendar->instance()->monthSummary['income']);
    }

    public function test_calendar_marks_the_salary_payment_day(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->day_of_month = 1;
        $contract->metadata = array_merge($contract->metadata, ['start_date' => '2026-11-11']);
        $contract->save();

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('aria-label="Dia de pagamento"')
            ->assertSeeHtml('aria-label="Feriado"')
            ->assertDontSee('Dia habitual de pagamento · Contrato de trabalho')
            ->call('openDayDetail', '2026-11-01')
            ->assertSee('Dia habitual de pagamento · Contrato de trabalho')
            ->assertSee('Salário líquido mensal previsto')
            ->assertSee('1.769,88 €')
            ->assertSee('Subsídio de alimentação mensal previsto')
            ->assertSee('230,12 €');

        $payday = $calendar->instance()->selectedDayEvents->firstWhere('subtype', 'payday');
        $this->assertArrayNotHasKey('amount', $payday);
        $this->assertFalse($calendar->instance()->selectedDaySummary['has_financial_records']);
    }

    public function test_payment_day_separates_meal_allowance_included_in_salary(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->amount = 2000;
        $contract->metadata = array_merge($contract->metadata, ['meal_allowance_destination' => 'same']);
        $contract->save();

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-11-25')
            ->assertSee('Salário líquido mensal previsto')
            ->assertSee('1.769,88 €')
            ->assertSee('Subsídio de alimentação mensal previsto')
            ->assertSee('230,12 €');

        $payday = $calendar->instance()->selectedDayEvents->firstWhere('subtype', 'payday');
        $this->assertSame(1769.88, $payday['payday_net_salary']);
        $this->assertSame(230.12, $payday['payday_meal_allowance']);
        $this->assertArrayNotHasKey('amount', $payday);
    }

    public function test_work_contract_start_and_end_dates_show_calendar_notices(): void
    {
        $this->travelTo(Carbon::parse('2026-11-13')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->metadata = array_merge($contract->metadata, [
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);
        $contract->save();

        Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('aria-label="Marco de trabalho"')
            ->assertDontSee('Primeiro dia de trabalho');

        $startDayCalendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', now()->startOfMonth()->toDateString())
            ->assertSee('Primeiro dia de trabalho')
            ->assertDontSee('Início do trabalho')
            ->assertDontSee('Fim do trabalho');

        $startDayMilestones = $startDayCalendar->instance()->dayEvents
            ->get(now()->startOfMonth()->toDateString())
            ->where('subtype', 'work_milestone');
        $this->assertCount(1, $startDayMilestones);

        Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', now()->endOfMonth()->toDateString())
            ->assertSee('Último dia de trabalho');
    }

    public function test_calendar_marks_working_non_working_and_holiday_days(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('data-day-type="working"')
            ->assertSeeHtml('data-day-type="non-working"')
            ->assertSeeHtml('data-day-type="holiday"')
            ->assertDontSeeHtml('aria-controls="calendar-symbols"')
            ->assertDontSee('Número de registos')
            ->assertSee('Receita ou salário')
            ->assertSee('Subsídio de alimentação')
            ->assertSee('Subsídio parcial')
            ->assertSee('Dias úteis')
            ->assertSee('Dias não úteis')
            ->assertSee('Feriados')
            ->assertSeeInOrder(['Registar férias', 'Dias úteis'])
            ->assertDontSee('Não útil');
    }

    public function test_calendar_highlights_only_today_in_the_current_month(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 23:59:59'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $calendar = Livewire::test(PersonalCalendar::class);
        $this->assertMatchesRegularExpression(
            '~wire:click="openDayDetail\(\'2026-11-12\'\)"[^>]*aria-current="date"[^>]*>(?:(?!wire:click="openDayDetail).)*?class="[^"]*bg-sky-600 text-base[^"]*"[^>]*>\s*12\s*</span>~s',
            $calendar->html()
        );
        $calendar->call('nextMonth')->assertDontSeeHtml('aria-current="date"');
        $calendar->call('goToToday')->assertSeeHtml('aria-current="date"');
        $this->travelTo(Carbon::parse('2026-11-13 00:00:00'));
        $calendar->call('refreshCalendarClock');
        $this->assertMatchesRegularExpression(
            '~wire:click="openDayDetail\(\'2026-11-13\'\)"[^>]*aria-current="date"[^>]*>(?:(?!wire:click="openDayDetail).)*?class="[^"]*bg-sky-600 text-base[^"]*"[^>]*>\s*13\s*</span>~s',
            $calendar->html()
        );
        $this->assertDoesNotMatchRegularExpression('~aria-current="date"[^>]*\bring-~', $calendar->html());
        $this->assertSame(1, substr_count($calendar->html(), 'aria-current="date"'));
    }

    public function test_pdf_exports_all_records_from_the_selected_month_despite_filters(): void
    {
        $this->travelTo(Carbon::parse('2026-12-10 12:00:00'));
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id, 'type' => 'personal']);
        $otherWorkspace = Workspace::factory()->create(['owner_id' => $otherUser->id, 'type' => 'personal']);
        $otherUser->forceFill(['current_workspace_id' => $otherWorkspace->id])->save();
        $this->actingAs($otherUser);
        Expense::create([
            'user_id' => $otherUser->id, 'workspace_id' => $otherWorkspace->id,
            'description' => 'Despesa outro workspace', 'amount' => 40, 'spent_at' => '2026-11-12',
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Income::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Receita novembro', 'amount' => 100, 'received_at' => '2026-11-10',
        ]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa novembro', 'amount' => 25, 'spent_at' => '2026-11-11',
        ]);
        Expense::create([
            'user_id' => $user->id, 'workspace_id' => $workspace->id,
            'description' => 'Despesa dezembro', 'amount' => 30, 'spent_at' => '2026-12-10',
        ]);
        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('prevMonth')
            ->set('search', 'Receita')
            ->call('toggleFilter', 'incomes')
            ->assertSee('Exportar PDF');
        $response = $calendar->instance()->exportPdf();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Calendario_2026-11.pdf', $response->headers->get('Content-Disposition'));
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $text = (new Parser)->parseContent($content)->getText();
        $this->assertStringContainsString('O mês num olhar', $text);
        $this->assertMatchesRegularExpression('/REGISTOS\s+3\s+DIAS COM REGISTOS\s+3/u', $text);
        $this->assertStringContainsString('Receita novembro', $text);
        $this->assertStringContainsString('Despesa novembro', $text);
        $this->assertStringContainsString('10/11/2026', $text);
        $this->assertStringContainsString('11/11/2026', $text);
        $this->assertStringNotContainsString('Despesa dezembro', $text);
        $this->assertStringNotContainsString('Despesa outro workspace', $text);
        $calendar->call('exportPdf')->assertFileDownloaded('Calendario_2026-11.pdf', contentType: 'application/pdf');
    }

    public function test_calendar_pdf_preserves_records_across_multiple_pages(): void
    {
        $this->travelTo(Carbon::parse('2026-11-13 12:00:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id, 'type' => 'personal']);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        for ($number = 1; $number <= 65; $number++) {
            Expense::create([
                'user_id' => $user->id, 'workspace_id' => $workspace->id,
                'description' => sprintf('Despesa de paginação %02d', $number),
                'amount' => $number, 'spent_at' => '2026-11-13',
            ]);
        }

        $response = Livewire::test(PersonalCalendar::class)->instance()->exportPdf();
        ob_start();
        $response->sendContent();
        $document = (new Parser)->parseContent(ob_get_clean());
        $text = $document->getText();

        $this->assertGreaterThan(1, count($document->getPages()));
        for ($number = 1; $number <= 65; $number++) {
            $this->assertStringContainsString(sprintf('Despesa de paginação %02d', $number), $text);
        }
    }

    public function test_calendar_pdf_requires_business_export_permission(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id, 'type' => 'business']);
        $workspace->users()->attach($member->id, ['role' => 'viewer']);
        $member->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($member);

        $calendar = new PersonalCalendar;
        $calendar->mount();
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $calendar->exportPdf();
    }

    public function test_calendar_pdf_requires_workspace_membership(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id, 'type' => 'personal']);
        $member->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($member);

        $calendar = new PersonalCalendar;
        $calendar->mount();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $calendar->exportPdf();
    }

    public function test_recurring_salary_uses_a_fixed_22_workday_month(): void
    {
        $this->travelTo(Carbon::parse('2026-11-17')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);

        $salaryEvents = Livewire::test(PersonalCalendar::class)
            ->instance()
            ->dayEvents
            ->flatten(1)
            ->where('subtype', 'salary_projection');

        $this->assertCount(21, $salaryEvents);
        $this->assertSame(80.45, (float) $salaryEvents->firstWhere('date', '2026-11-02')['amount']);
        $this->assertSame(1689.45, round((float) $salaryEvents->sum('amount'), 2));
        $this->assertFalse($salaryEvents->contains(fn (array $event): bool => $event['date'] === '2026-11-01'));
    }

    public function test_salary_entries_start_on_the_employment_start_date(): void
    {
        $this->travelTo(Carbon::parse('2026-11-17')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $projectionContract = $this->createMealAllowanceContract($user, $workspace);
        $projectionContract->metadata = array_merge($projectionContract->metadata, ['start_date' => '2026-11-12']);
        $projectionContract->save();

        $paidContract = $this->createMealAllowanceContract($user, $workspace);
        $paidContract->description = 'Segundo contrato';
        $paidContract->metadata = array_merge($paidContract->metadata, ['start_date' => '2026-11-12']);
        $paidContract->save();
        SalaryPayment::create([
            'recurring_income_id' => $paidContract->id,
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'payment_month' => 11,
            'payment_year' => 2026,
            'meal_allowance' => 230.12,
            'net_received' => 2000,
            'paid_at' => '2026-11-25',
            'status' => 'paid',
        ]);

        $events = Livewire::test(PersonalCalendar::class)
            ->instance()
            ->dayEvents
            ->flatten(1);
        $salaryProjectionEvents = $events->where('subtype', 'salary_projection');
        $paidSalaryEvents = $events->where('subtype', 'salary_payment');

        $this->assertCount(13, $salaryProjectionEvents);
        $this->assertCount(13, $paidSalaryEvents);
        $this->assertSame('2026-11-12', $salaryProjectionEvents->min('date'));
        $this->assertSame('2026-11-12', $paidSalaryEvents->min('date'));
        $this->assertSame(80.45, (float) $salaryProjectionEvents->firstWhere('date', '2026-11-12')['amount']);
        $this->assertSame(1045.85, round((float) $salaryProjectionEvents->sum('amount'), 2));
        $this->assertSame(1045.85, round((float) $paidSalaryEvents->sum('amount'), 2));
        $this->assertFalse($salaryProjectionEvents->contains(fn (array $event): bool => $event['date'] < '2026-11-12'));
        $this->assertFalse($paidSalaryEvents->contains(fn (array $event): bool => $event['date'] < '2026-11-12'));
    }

    public function test_vacation_period_hides_daily_allowance_events_and_can_be_removed(): void
    {
        $this->travelTo(Carbon::parse('2026-11-17')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->assertSee('Registar férias')
            ->call('openVacationModal')
            ->set('vacationForm.start_date', '2026-11-12')
            ->set('vacationForm.end_date', '2026-11-15')
            ->set('vacationForm.notes', 'Viagem')
            ->assertSee('Dias úteis')
            ->assertSee('Inclui 2 dias de fim de semana.')
            ->assertSee('20,92 €')
            ->call('saveVacationPeriod')
            ->assertHasNoErrors()
            ->call('openDayDetail', '2026-11-12')
            ->assertSee('Férias');

        $eventsOnVacationDay = $calendar->instance()->selectedDayEvents;
        $this->assertContains('Férias', $eventsOnVacationDay->pluck('label')->all());
        $this->assertNotContains('Subsídio de alimentação', $eventsOnVacationDay->pluck('label')->all());
        $eventsOnFollowingWorkday = $calendar->instance()->dayEvents->get('2026-11-16', collect());
        $this->assertContains(
            'Subsídio de alimentação',
            $eventsOnFollowingWorkday->pluck('label')->all(),
            json_encode($eventsOnFollowingWorkday->all()),
        );

        $vacationSummary = $calendar->instance()->vacationSummary;
        $this->assertSame(4, $vacationSummary['calendar_days']);
        $this->assertSame(2, $vacationSummary['working_days']);
        $this->assertSame(2, $vacationSummary['weekend_days']);
        $this->assertSame(20.92, $vacationSummary['foregone_allowance']);

        $vacation = CalendarVacation::where('workspace_id', $workspace->id)->firstOrFail();
        $this->assertSame('2026-11-12', $vacation->start_date->toDateString());
        $this->assertSame('2026-11-15', $vacation->end_date->toDateString());
        $this->assertSame('Viagem', $vacation->notes);

        $calendar->call('deleteVacationPeriod', $vacation->id)
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('calendar_vacations', ['id' => $vacation->id]);
    }

    public function test_holidays_are_shown_and_skip_meal_allowance_but_keep_salary(): void
    {
        $this->travelTo(Carbon::parse('2026-05-14')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);

        $mafraCalendar = Livewire::test(PersonalCalendar::class)
            ->assertSeeHtml('aria-label="Feriado"')
            ->assertDontSee('Quinta-feira da Ascensão')
            ->call('openDayDetail', '2026-05-14')
            ->assertSee('Quinta-feira da Ascensão');
        $mafraHolidayEvents = $mafraCalendar->instance()->selectedDayEvents;

        $this->assertContains('Quinta-feira da Ascensão', $mafraHolidayEvents->pluck('label')->all());
        $this->assertContains('Salário diário estimado · Contrato de trabalho', $mafraHolidayEvents->pluck('label')->all());
        $this->assertNotContains('Subsídio de alimentação', $mafraHolidayEvents->pluck('label')->all());

        $this->travelTo(Carbon::parse('2026-06-13')->setTime(19, 0));
        Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-06-13')
            ->assertSee('Santo António');

        $this->travelTo(Carbon::parse('2026-12-01')->setTime(19, 0));
        Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-12-01')
            ->assertSee('Restauração da Independência');
    }

    public function test_public_holidays_are_not_counted_as_vacation_workdays_or_lost_allowance(): void
    {
        $this->travelTo(Carbon::parse('2026-12-01')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openVacationModal')
            ->set('vacationForm.start_date', '2026-12-01')
            ->set('vacationForm.end_date', '2026-12-03')
            ->assertSee('Inclui 1 feriado; não conta como dia útil nem gera subsídio de alimentação.')
            ->assertSee('20,92 €');
        $summary = $calendar->instance()->vacationSummary;

        $this->assertSame(3, $summary['calendar_days']);
        $this->assertSame(2, $summary['working_days']);
        $this->assertSame(0, $summary['weekend_days']);
        $this->assertSame(1, $summary['holiday_days']);
        $this->assertSame(20.92, $summary['foregone_allowance']);
    }

    public function test_daily_salary_projection_is_pending_until_six_and_on_future_days(): void
    {
        $this->travelTo(Carbon::parse('2026-11-13 17:59:59'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);
        $contract->update(['metadata' => array_merge($contract->metadata, ['meal_allowance_destination' => 'same'])]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('toggleFilter', 'incomes')
            ->call('openDayDetail', '2026-11-12')
            ->assertSee('Salário diário estimado')
            ->assertDontSee('À espera')
            ->call('openDayDetail', '2026-11-13')
            ->assertSee('À espera')
            ->call('openDayDetail', '2026-11-16')
            ->assertSee('À espera');

        $response = $calendar->instance()->exportPdf();
        ob_start();
        $response->sendContent();
        $text = (new Parser)->parseContent(ob_get_clean())->getText();
        $afterPastHeading = explode('12/11/2026', $text, 2)[1];
        $pastDay = explode('13/11/2026', $afterPastHeading, 2)[0];
        $today = explode('16/11/2026', explode('13/11/2026', $afterPastHeading, 2)[1], 2)[0];
        $this->assertStringContainsString('Salário diário estimado', $pastDay);
        $this->assertStringNotContainsString('À espera', $pastDay);
        $this->assertStringContainsString('Salário diário estimado', $today);
        $this->assertStringContainsString('À espera', $today);

        $this->travelTo(Carbon::parse('2026-11-13 18:00:00'));
        $calendar->call('refreshCalendarClock')
            ->call('openDayDetail', '2026-11-13')
            ->assertSee('Salário diário estimado')
            ->assertDontSee('À espera')
            ->call('openDayDetail', '2026-11-16')
            ->assertSee('À espera');
    }

    public function test_daily_meal_allowance_is_pending_from_midnight_until_six(): void
    {
        $weekday = Carbon::parse('2026-11-13');
        $this->travelTo($weekday->copy()->setTime(0, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', $weekday->toDateString())
            ->assertSee('Salário diário estimado')
            ->assertSee('+80,45€')
            ->assertSee('Subsídio de alimentação')
            ->assertSee('À espera')
            ->assertSee('+10,46€');
        foreach (['80,45', '10,46'] as $amount) {
            $this->assertMatchesRegularExpression(
                '~class="text-sm font-black shrink-0 text-orange-600 dark:text-orange-400">\s*\+'.preg_quote($amount, '~').'€~u',
                $calendar->html(),
            );
        }
        $this->assertSame(2, substr_count($calendar->html(), 'tracking-wider text-orange-600 dark:text-orange-400">'));
        $this->assertSame('expected', $calendar->instance()->selectedDayEvents->firstWhere('subtype', 'meal_allowance')['status']);

        $calendar->call('closeDayDetail')
            ->call('openDayDetail', '2026-11-14')
            ->assertSee('Sem registos neste dia')
            ->call('closeDayDetail')
            ->call('openDayDetail', '2026-11-16')
            ->assertSee('Subsídio de alimentação')
            ->assertSee('À espera')
            ->assertSee('+10,46€');

        $this->assertSame(0, DailyMealAllowance::count());

        $this->travelTo($weekday->copy()->setTime(17, 59, 59));
        Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', $weekday->toDateString())
            ->assertSee('À espera');

        $this->travelTo($weekday->copy()->setTime(18, 0));
        $paidCalendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', $weekday->toDateString())
            ->assertSee('Subsídio de alimentação')
            ->assertSee('Pago')
            ->assertSee('+10,46€');
        foreach (['80,45', '10,46'] as $amount) {
            $this->assertMatchesRegularExpression(
                '~class="text-sm font-black shrink-0 text-emerald-600">\s*\+'.preg_quote($amount, '~').'€~u',
                $paidCalendar->html(),
            );
        }
        $paidCalendar
            ->call('closeDayDetail')
            ->call('openDayDetail', '2026-11-16')
            ->assertSee('À espera');
    }

    public function test_today_meal_allowance_can_be_edited_before_six(): void
    {
        $this->travelTo(Carbon::parse('2026-11-13 00:01:00'));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);

        Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-11-13')
            ->call('beginEditMealAllowance', $contract->id, '2026-11-13')
            ->set('mealAllowanceForms.'.$contract->id.'.received_amount', '5')
            ->call('saveMealAllowanceDay', $contract->id, '2026-11-13')
            ->assertSee('Parcial')
            ->assertSee('+5,00€');

        $this->travelTo(Carbon::parse('2026-11-13 18:00:00'));
        $calendar = Livewire::test(PersonalCalendar::class)->call('openDayDetail', '2026-11-13');
        $event = $calendar->instance()->selectedDayEvents->firstWhere('subtype', 'meal_allowance');
        $this->assertSame('partial', $event['status']);
        $this->assertSame(5.0, $event['amount']);
    }

    public function test_calendar_edits_expense_in_the_day_modal(): void
    {
        $this->travelTo(Carbon::parse('2026-11-17')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $category = Category::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Alimentação',
            'slug' => 'alimentacao',
        ]);
        $expense = Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'amount' => 6,
            'description' => 'Almoço',
            'spent_at' => '2026-11-12',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-11-12');
        $eventKey = $calendar->instance()->selectedDayEvents->firstWhere('expense_id', $expense->id)['event_key'];

        $calendar->call('beginEditEvent', $eventKey)
            ->set('eventEditForm.label', 'Almoço de trabalho')
            ->set('eventEditForm.date', '2026-11-13')
            ->set('eventEditForm.amount', '8.50')
            ->call('saveEvent', $eventKey)
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $expense->refresh();
        $this->assertSame('Almoço de trabalho', $expense->description);
        $this->assertSame('2026-11-13', $expense->spent_at->toDateString());
        $this->assertSame(8.5, (float) $expense->amount);
        $this->assertSame('2026-11-13', $calendar->instance()->selectedDayKey);
    }

    public function test_automatic_salary_occurrence_can_be_overridden_for_one_day(): void
    {
        $this->travelTo(Carbon::parse('2026-11-17')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $contract = $this->createMealAllowanceContract($user, $workspace);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-11-02');
        $event = $calendar->instance()->selectedDayEvents->firstWhere('subtype', 'salary_projection');
        $eventKey = $event['event_key'];

        $calendar->call('beginEditEvent', $eventKey)
            ->assertHasNoErrors();
        $calendar->set('eventEditForm.label', 'Salário corrigido')
            ->set('eventEditForm.date', '2026-11-03')
            ->set('eventEditForm.amount', '90.00')
            ->assertSet('eventEditForm.date', '2026-11-03')
            ->call('saveEvent', $eventKey)
            ->assertSet('selectedDayKey', '2026-11-03')
            ->assertHasNoErrors();

        $targetEvents = $calendar->instance()->selectedDayEvents;
        $overriddenEvent = $targetEvents->firstWhere('event_key', $eventKey);
        $this->assertSame('Salário corrigido', $overriddenEvent['label']);
        $this->assertSame('2026-11-03', $overriddenEvent['date']);
        $this->assertSame(90.0, (float) $overriddenEvent['amount']);
        $this->assertSame(1769.88, (float) $contract->fresh()->amount);
        $this->assertDatabaseHas('calendar_event_overrides', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'event_key' => $eventKey,
        ]);

        $calendar->call('closeDayDetail')
            ->call('openDayDetail', '2026-11-03')
            ->call('beginEditEvent', $eventKey)
            ->assertSet('eventEditForm.amount', 90.0)
            ->call('cancelEditEvent')
            ->call('closeDayDetail')
            ->call('openDayDetail', '2026-11-02');

        $this->assertFalse(
            $calendar->instance()->selectedDayEvents->contains(fn (array $candidate): bool => $candidate['event_key'] === $eventKey),
        );
    }

    public function test_generated_holiday_can_be_overridden_in_the_day_modal(): void
    {
        $this->travelTo(Carbon::parse('2026-12-01')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-12-01');
        $event = $calendar->instance()->selectedDayEvents->firstWhere('subtype', 'public_holiday');
        $eventKey = $event['event_key'];

        $calendar->call('beginEditEvent', $eventKey)
            ->set('eventEditForm.label', 'Feriado personalizado')
            ->set('eventEditForm.date', '2027-01-02')
            ->call('saveEvent', $eventKey)
            ->assertHasNoErrors()
            ->assertSet('selectedDayKey', '2027-01-02')
            ->assertSet('month', 1)
            ->assertSet('year', 2027);

        $this->assertSame(
            'Feriado personalizado',
            $calendar->instance()->selectedDayEvents->firstWhere('event_key', $eventKey)['label'],
        );
        $calendar->call('closeDayDetail')
            ->call('prevMonth')
            ->call('openDayDetail', '2026-12-01');
        $this->assertFalse(
            $calendar->instance()->selectedDayEvents->contains(fn (array $candidate): bool => $candidate['event_key'] === $eventKey),
        );
    }

    public function test_vacation_period_can_be_edited_from_its_day_modal(): void
    {
        $this->travelTo(Carbon::parse('2026-11-17')->setTime(19, 0));
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $vacation = CalendarVacation::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'start_date' => '2026-11-12',
            'end_date' => '2026-11-14',
            'notes' => 'Viagem',
        ]);

        $calendar = Livewire::test(PersonalCalendar::class)
            ->call('openDayDetail', '2026-11-12');
        $eventKey = $calendar->instance()->selectedDayEvents->firstWhere('vacation_id', $vacation->id)['event_key'];

        $calendar->call('beginEditEvent', $eventKey)
            ->set('eventEditForm.start_date', '2026-11-13')
            ->set('eventEditForm.end_date', '2026-11-16')
            ->set('eventEditForm.notes', 'Férias atualizadas')
            ->call('saveEvent', $eventKey)
            ->assertHasNoErrors();

        $vacation->refresh();
        $this->assertSame('2026-11-13', $vacation->start_date->toDateString());
        $this->assertSame('2026-11-16', $vacation->end_date->toDateString());
        $this->assertSame('Férias atualizadas', $vacation->notes);
    }

    private function createMealAllowanceContract(User $user, Workspace $workspace): RecurringIncome
    {
        $contract = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Contrato de trabalho',
            'amount' => 1769.88,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => [
                'contract_status' => 'active',
                'start_date' => now()->startOfMonth()->toDateString(),
                'meal_allowance_destination' => 'meal_card',
                'meal_allowance' => 10.46,
                'calculated_sa' => 230.12,
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
                'start_date' => now()->startOfMonth()->toDateString(),
            ],
        ]);
        $contract->metadata = array_merge($contract->metadata, ['meal_card_income_id' => $mealCardAllowance->id]);
        $contract->save();

        return $contract;
    }
}
