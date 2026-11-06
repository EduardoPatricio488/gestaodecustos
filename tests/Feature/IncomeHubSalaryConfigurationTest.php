<?php

use App\Livewire\IncomeHub;
use App\Livewire\Incomes;
use App\Models\BankAccount;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\SalaryDocument;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Models\Workspace;
use App\Services\PortugueseNetPayCalculator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('employment salary includes holiday and christmas allowances paid in duodecimos', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $mealAccount = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Cartão refeição',
        'type' => 'corrente',
        'balance' => 0,
        'currency' => 'EUR',
    ]);
    $this->actingAs($user);
    $expectedNet = app(PortugueseNetPayCalculator::class)->calculate(1750)['net'];

    Livewire::test(IncomeHub::class)
        ->set('recSalaryGross', 1500)
        ->set('recHolidayAllowanceSameAsSalary', false)
        ->set('recHolidayAllowanceGross', 1200)
        ->set('recChristmasAllowanceSameAsSalary', false)
        ->set('recChristmasAllowanceGross', 1800)
        ->set('recAllowancesInDuodecimos', true)
        ->set('recSalaryDestination', 'bank:'.$mealAccount->id)
        ->set('recBankAccountId', (string) $mealAccount->id)
        ->set('recSalaryReceivedThisMonth', true)
        ->set('recMealDestination', 'bank:'.$mealAccount->id)
        ->assertSet('recAmount', $expectedNet)
        ->set('recDescription', 'Salário')
        ->set('recDay', 25)
        ->call('saveEmployment')
        ->assertHasNoErrors();

    $income = RecurringIncome::where('workspace_id', $workspace->id)->firstOrFail();

    expect($income->amount)->toBe(number_format($expectedNet, 2, '.', ''))
        ->and($income->bank_account_id)->toBe($mealAccount->id)
        ->and($income->metadata['holiday_allowance_same_as_salary'])->toBeFalse()
        ->and($income->metadata['holiday_allowance_gross'])->toBe(1200)
        ->and($income->metadata['christmas_allowance_gross'])->toBe(1800)
        ->and($income->metadata['allowances_in_duodecimos'])->toBeTrue()
        ->and($income->metadata['received_month'])->toBe(now()->format('Y-m'))
        ->and($income->metadata['meal_allowance_destination'])->toBe('bank')
        ->and($income->metadata['meal_bank_account_id'])->toBe($mealAccount->id);
});

test('salary modal hides removed sections and uses named bank options with end date only for fixed-term contracts', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Banco Exemplo',
        'type' => 'corrente',
        'balance' => 0,
        'currency' => 'EUR',
    ]);
    $this->actingAs($user);

    Livewire::test(IncomeHub::class)
        ->call('newEmployment')
        ->assertSee('Banco Exemplo')
        ->assertDontSee('Composição salarial e retenção')
        ->assertDontSee('Benefícios e benefícios adicionais')
        ->assertDontSee('Ano em que começou a beneficiar')
        ->assertDontSee('Data de fim')
        ->set('recIrsJovem', true)
        ->assertSee('Ano de benefício')
        ->assertDontSee('Ano em que começou a beneficiar')
        ->set('recContractType', 'termo_certo')
        ->assertSee('Data de fim');
});

test('changing IRS Jovem benefit year recalculates and displays its exemption separately from withholding', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $component = Livewire::test(IncomeHub::class)
        ->set('recSalaryGross', 1500)
        ->set('recIrsJovem', true)
        ->set('recIrsJovemYear', 1)
        ->assertSet('calculatedIRSJovemRate', 1.0)
        ->assertSet('calculatedIRSJovemExemptIncome', 1500.0)
        ->assertSee('100% de isenção no 1.º ano')
        ->assertSet('calculatedIRS', 0.0)
        ->set('recIrsJovemYear', 2)
        ->set('recSalaryGross', 5000)
        ->assertSet('calculatedIRSJovemRate', 0.75)
        ->assertSet('calculatedIRSJovemExemptIncome', 2110.15)
        ->assertSee('75% de isenção no 2.º ano')
        ->assertSet('calculatedIRS', app(PortugueseNetPayCalculator::class)->withholding(5000 - 2110.15, 'solteiro', 0, false)['irs']);

    expect((float) $component->get('calculatedIRSJovemSavings'))->toBeGreaterThan(0);
});

test('salary modal shows only requested payroll lines and pins monthly net above save action', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(IncomeHub::class)
        ->call('newEmployment')
        ->set('recSalaryGross', 1500)
        ->assertSee('IRS NORMAL (RETENÇÃO ESTIMADA)')
        ->assertSee('Líquido final mensal')
        ->assertDontSee('POUPANÇA ~')
        ->set('recIrsJovem', true)
        ->assertSee('IRS JOVEM (RETENÇÃO ESTIMADA)')
        ->assertDontSee('IRS NORMAL (RETENÇÃO ESTIMADA)')
        ->set('recAllowancesInDuodecimos', true)
        ->assertSee('DUODÉCIMOS (BRUTO)');
});

test('salary calculation reacts immediately to duodecimos and meal allowance destination changes', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $component = Livewire::test(IncomeHub::class)
        ->set('recSalaryGross', 1500)
        ->set('recHolidayAllowanceSameAsSalary', true)
        ->set('recChristmasAllowanceSameAsSalary', true)
        ->set('recDuodecimosPercentage', 50)
        ->set('recAllowancesInDuodecimos', false)
        ->assertSet('recAmount', 1166.83)
        ->set('recAllowancesInDuodecimos', true)
        ->assertSet('recDuodecimosPercentage', 100)
        ->assertSet('recAmount', app(PortugueseNetPayCalculator::class)->calculate(1750)['net'])
        ->set('recAllowancesInDuodecimos', false)
        ->assertSet('recDuodecimosPercentage', 0)
        ->set('recSalaryGross', 1500)
        ->set('recMealAllowance', 8.15)
        ->set('recWorkingDays', 20)
        ->set('recMealDestination', 'cash')
        ->assertSet('calculatedSS', 169.4)
        ->set('recMealDestination', 'meal_card')
        ->assertSet('calculatedSS', 165.0);

    expect((float) $component->get('calculatedSA'))->toBe(163.0)
        ->and((float) $component->get('recAmount'))->toBe(
            app(PortugueseNetPayCalculator::class)->calculate(
                gross: 1500,
                mealPerDay: 8.15,
                workingDays: 20,
                mealPayment: 'cartao',
            )['net'],
        );
});

test('income page separates meal card allowance from salary and shows combined total', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'meal_allowance_destination' => 'meal_card',
            'calculated_sa' => 150,
        ],
    ]);

    Livewire::test(Incomes::class)
        ->assertSee('Salário: +1 050,00€')
        ->assertSee('Cartão refeição: +150,00€')
        ->assertSee('Total: +1 200,00€');
});

test('income dashboard projects salary and meal allowance in separate cards', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Empresa Exemplo',
        'amount' => 1300,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'contract_status' => 'active',
            'salary_gross' => 1600,
            'calculated_sa' => 100,
            'working_days' => 20,
        ],
    ]);

    $component = Livewire::test(IncomeHub::class);

    expect((float) $component->viewData('projectedSalaryAnnualTotal'))
        ->toBe((float) $component->viewData('projectedSalaryNetTotal') * 12);
});

test('salary edit modal hides annual payment and withholding settings and the simulation summary', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'annual_payments' => 12,
            'withholding_status' => 'exempt',
            'salary_gross' => 1400,
            'calculated_sa' => 0,
        ],
    ]);

    Livewire::test(IncomeHub::class)
        ->call('editFixed', $salary->id)
        ->assertSee('Editar Salário')
        ->assertDontSee('Pagamentos por ano')
        ->assertDontSee('Estado da retenção')
        ->assertDontSee('Cenário atual')
        ->assertDontSee('Cenário simulado')
        ->assertDontSee('Diferença mensal')
        ->assertDontSee('Simulação com os valores atualmente preenchidos')
        ->assertSet('recAnnualPayments', 12)
        ->assertSet('recWithholdingStatus', 'exempt');
});

test('income dashboard shows this month received total excluding pending salary payments', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário',
        'amount' => 1500,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => ['received_month' => now()->format('Y-m')],
    ]);

    Income::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Serviço extra',
        'amount' => 80,
        'received_at' => now()->toDateString(),
        'source' => 'emprego',
        'currency' => 'EUR',
    ]);

    SalaryPayment::create([
        'recurring_income_id' => $salary->id,
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'payment_month' => now()->month,
        'payment_year' => now()->year,
        'net_received' => 1500,
        'paid_at' => now()->toDateString(),
        'status' => 'paid',
    ]);

    SalaryPayment::create([
        'recurring_income_id' => $salary->id,
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'payment_month' => now()->subMonth()->month,
        'payment_year' => now()->subMonth()->year,
        'net_received' => 1200,
        'status' => 'pending',
    ]);

    Livewire::test(IncomeHub::class)
        ->assertSee('Este mês recebi')
        ->assertSee('1 580,00€')
        ->assertSee(now()->translatedFormat('F Y'));
});

test('income dashboard includes active monthly salary contract when no payment record exists', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário',
        'amount' => 1400,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => ['calculated_sa' => 100],
    ]);
    $mealCard = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Cartão refeição — Salário',
        'amount' => 100,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => ['income_type' => 'meal_card_allowance', 'linked_salary_id' => $salary->id],
    ]);
    $salary->metadata = array_merge($salary->metadata, ['meal_card_income_id' => $mealCard->id]);
    $salary->save();

    Livewire::test(IncomeHub::class)
        ->assertSee('Este mês recebi')
        ->assertSee('1 500,00€');
});

test('income dashboard includes salary registered as received through the salary form', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $component = Livewire::test(IncomeHub::class)
        ->set('recDescription', 'Salário registado')
        ->set('recDay', 25)
        ->set('recSalaryGross', 1500)
        ->set('recSalaryReceivedThisMonth', true)
        ->call('saveEmployment')
        ->assertHasNoErrors();

    $salary = RecurringIncome::where('workspace_id', $workspace->id)->where('source', 'emprego')->firstOrFail();
    $component->assertSet('recSalaryReceivedThisMonth', false);

    $dashboard = Livewire::test(IncomeHub::class);
    expect((float) $dashboard->viewData('thisMonthReceived'))->toBe((float) $salary->amount);
});

test('employment social security uses the cash meal allowance exemption when not paid on a meal card', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(IncomeHub::class)
        ->set('recSalaryGross', 1500)
        ->set('recMealAllowance', 8.15)
        ->set('recWorkingDays', 20)
        ->set('recMealDestination', 'cash')
        ->assertSet('calculatedSS', 169.4)
        ->set('recMealDestination', 'meal_card')
        ->assertSet('calculatedSS', 165.0);
});

test('meal card allowance is saved as a separate recurring income linked to its salary', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $component = Livewire::test(IncomeHub::class)
        ->set('recDescription', 'Salário Empresa')
        ->set('recDay', 25)
        ->set('recSalaryGross', 1500)
        ->set('recMealAllowance', 7.5)
        ->set('recWorkingDays', 22)
        ->set('recMealDestination', 'meal_card')
        ->call('saveEmployment')
        ->assertHasNoErrors();

    $salary = RecurringIncome::where('workspace_id', $workspace->id)
        ->whereNull('metadata->income_type')
        ->firstOrFail();
    $mealCardIncome = RecurringIncome::where('workspace_id', $workspace->id)
        ->where('metadata->income_type', 'meal_card_allowance')
        ->firstOrFail();

    expect((float) $salary->amount)->toBe(1166.83)
        ->and((float) $mealCardIncome->amount)->toBe(165.0)
        ->and($salary->metadata['meal_card_income_id'])->toBe($mealCardIncome->id)
        ->and($mealCardIncome->metadata['linked_salary_id'])->toBe($salary->id);

    Livewire::test(Incomes::class)
        ->assertSee('Salário Empresa')
        ->assertSee('Subsídio de alimentação (cartão) — Salário Empresa')
        ->assertSee('1.331,83€');

    Livewire::test(IncomeHub::class)
        ->assertSee('18 150,62€');

    $component->call('editFixed', $salary->id)
        ->set('recMealAllowance', 8)
        ->call('saveEmployment')
        ->assertHasNoErrors();

    expect(RecurringIncome::where('workspace_id', $workspace->id)->count())->toBe(2)
        ->and((float) $mealCardIncome->fresh()->amount)->toBe(176.0);
});

test('salary can be kept without an increase and annual raises are cleared', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário mensal',
        'amount' => 1500,
        'day_of_month' => 1,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'annual_raise' => ['enabled' => true, 'type' => 'fixed', 'value' => 50, 'next_date' => now()->addYear()->toDateString()],
        ],
    ]);

    Livewire::test(IncomeHub::class)
        ->call('openRaiseModal', $salary->id)
        ->set('raiseMode', 'none')
        ->call('applyRaise')
        ->assertHasNoErrors()
        ->assertDispatched('toast', text: 'Salário mantido sem aumento.');

    expect((float) $salary->fresh()->amount)->toBe(1500.0)
        ->and($salary->fresh()->metadata)->not->toHaveKey('annual_raise');
});

test('raise value validation is shown in Portuguese', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário mensal',
        'amount' => 1500,
        'day_of_month' => 1,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
    ]);

    Livewire::test(IncomeHub::class)
        ->call('openRaiseModal', $salary->id)
        ->set('raiseValue', '')
        ->call('applyRaise')
        ->assertHasErrors(['raiseValue' => 'required'])
        ->assertSee('Indica o valor do aumento.');
});

test('annual percentage raise is configured and applied repeatedly to a recurring income', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $income = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Renda mensal',
        'amount' => 1000,
        'day_of_month' => 1,
        'is_active' => true,
        'source' => 'outro',
        'frequency' => 'mensal',
    ]);
    $firstRaiseDate = now()->addDay()->toDateString();

    Livewire::test(IncomeHub::class)
        ->call('openRaiseModal', $income->id)
        ->set('raiseMode', 'annual_percentage')
        ->set('raiseValue', 5)
        ->set('raiseNextDate', $firstRaiseDate)
        ->call('applyRaise')
        ->assertHasNoErrors();

    expect($income->fresh()->metadata['annual_raise'])->toMatchArray([
        'enabled' => true,
        'type' => 'percentage',
        'value' => 5,
        'next_date' => $firstRaiseDate,
    ]);

    $this->travelTo(now()->addDays(2));
    $this->artisan('incomes:apply-annual-raises')->assertSuccessful();
    expect((float) $income->fresh()->amount)->toBe(1050.0)
        ->and($income->fresh()->metadata['annual_raise']['next_date'])->toBe(
            \Illuminate\Support\Carbon::parse($firstRaiseDate)->addYear()->toDateString()
        );

    $this->travelTo(now()->addYear()->addDay());
    $this->artisan('incomes:apply-annual-raises')->assertSuccessful();
    expect((float) $income->fresh()->amount)->toBe(1102.5);
});

test('annual fixed raise updates salary gross and recalculates net pay', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $income = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário',
        'amount' => 1166.83,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'salary_gross' => 1500,
            'meal_allowance' => 0,
            'working_days' => 22,
            'meal_allowance_destination' => 'same',
            'calculated_sa' => 0,
            'calculated_ss' => 165,
            'calculated_irs' => 168.17,
            'annual_raise' => [
                'enabled' => true,
                'type' => 'fixed',
                'value' => 100,
                'next_date' => now()->toDateString(),
            ],
        ],
    ]);

    $this->artisan('incomes:apply-annual-raises')->assertSuccessful();

    expect((float) $income->fresh()->metadata['salary_gross'])->toBe(1600.0)
        ->and((float) $income->fresh()->amount)->toBe(app(PortugueseNetPayCalculator::class)->calculate(1600)['net'])
        ->and((float) $income->fresh()->metadata['calculated_ss'])->toBe(176.0);
});

test('salary bonus calculates net from its editable IRS and social security percentages', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $bonusDate = now()->toDateString();
    $calculator = app(PortugueseNetPayCalculator::class);
    $salaryNet = $calculator->calculate(1500)['net'];
    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário Empresa',
        'amount' => $salaryNet,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'salary_gross' => 1500,
            'meal_allowance' => 0,
            'working_days' => 22,
        ],
    ]);
    $component = Livewire::test(IncomeHub::class)
        ->call('editSalaryBonus', $salary->id)
        ->assertSet('showSalaryBonusModal', true)
        ->set('recBonusGross', 500)
        ->set('recBonusDeductIrs', true)
        ->set('recBonusIrsPercentage', 10)
        ->set('recBonusDeductSocialSecurity', true)
        ->set('recBonusSocialSecurityPercentage', 11)
        ->assertSet('recBonusIrsAmount', 50)
        ->assertSet('recBonusSocialSecurityAmount', 55)
        ->assertSet('recBonusNet', 395)
        ->set('recBonusDate', $bonusDate)
        ->call('saveSalaryBonus')
        ->assertHasNoErrors();

    $bonusIncome = Income::where('workspace_id', $workspace->id)->firstOrFail();

    expect($bonusIncome->description)->toBe('Prémio — Salário Empresa')
        ->and((float) $bonusIncome->amount)->toBe(395.0)
        ->and($bonusIncome->salary_recurring_income_id)->toBe($salary->id)
        ->and($bonusIncome->salary_income_type)->toBe('prize')
        ->and($bonusIncome->received_at->toDateString())->toBe($bonusDate)
        ->and($component->viewData('extraIncomes')->contains('id', $bonusIncome->id))->toBeTrue()
        ->and((float) $salary->fresh()->amount)->toBe((float) $salaryNet)
        ->and($salary->fresh()->metadata['bonus'])->toMatchArray([
            'type' => 'one_off',
            'gross' => 500.0,
            'net' => 395.0,
            'deduct_irs' => true,
            'irs_percentage' => 10.0,
            'irs_amount' => 50.0,
            'deduct_social_security' => true,
            'social_security_percentage' => 11.0,
            'social_security_amount' => 55.0,
            'income_id' => $bonusIncome->id,
        ]);
});

test('salary bonus can be saved without deductions and later edited with different settings', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $calculator = app(PortugueseNetPayCalculator::class);
    $salaryNet = $calculator->calculate(1500)['net'];
    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário com prémio',
        'amount' => $salaryNet,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'salary_gross' => 1500,
            'meal_allowance' => 0,
            'working_days' => 22,
        ],
    ]);

    $component = Livewire::test(IncomeHub::class)
        ->call('editSalaryBonus', $salary->id)
        ->set('recBonusGross', 500)
        ->set('recBonusDeductIrs', false)
        ->set('recBonusDeductSocialSecurity', false)
        ->assertSet('recBonusNet', 500)
        ->call('saveSalaryBonus')
        ->assertHasNoErrors();

    $salary->refresh();
    $bonusIncome = Income::where('workspace_id', $workspace->id)->firstOrFail();
    expect((float) $salary->amount)->toBe((float) $salaryNet)
        ->and((float) $bonusIncome->amount)->toBe(500.0)
        ->and($salary->metadata['bonus']['deduct_irs'])->toBeFalse()
        ->and($salary->metadata['bonus']['deduct_social_security'])->toBeFalse();

    $component->call('editSalaryBonus', $salary->id)
        ->set('recBonusDeductIrs', true)
        ->set('recBonusIrsPercentage', 10)
        ->set('recBonusDeductSocialSecurity', true)
        ->set('recBonusSocialSecurityPercentage', 8)
        ->assertSet('recBonusNet', 410)
        ->call('saveSalaryBonus')
        ->assertHasNoErrors();

    expect(Income::where('workspace_id', $workspace->id)->count())->toBe(1)
        ->and((float) $bonusIncome->fresh()->amount)->toBe(410.0)
        ->and($salary->fresh()->metadata['bonus']['deduct_irs'])->toBeTrue()
        ->and((float) $salary->fresh()->metadata['bonus']['irs_percentage'])->toBe(10.0)
        ->and((float) $salary->fresh()->metadata['bonus']['social_security_percentage'])->toBe(8.0);

    $component->call('editSalaryBonus', $salary->id)
        ->set('recBonusGross', 0)
        ->call('saveSalaryBonus')
        ->assertHasNoErrors();

    expect(Income::where('workspace_id', $workspace->id)->count())->toBe(0)
        ->and($salary->fresh()->metadata['bonus'])->toBeNull()
        ->and((float) $salary->fresh()->amount)->toBe((float) $salaryNet);
});

test('one-off salary bonus is included in the one-year net estimate', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $calculator = app(PortugueseNetPayCalculator::class);
    $salaryNet = $calculator->calculate(1500)['net'];
    $bonusNet = 395.0;
    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário com prémio anual',
        'amount' => $salaryNet,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'salary_gross' => 1500,
            'meal_allowance' => 0,
            'working_days' => 22,
        ],
    ]);
    $component = Livewire::test(IncomeHub::class)
        ->call('editSalaryBonus', $salary->id)
        ->set('recBonusGross', 500)
        ->set('recBonusDeductIrs', true)
        ->set('recBonusIrsPercentage', 10)
        ->set('recBonusDeductSocialSecurity', true)
        ->set('recBonusSocialSecurityPercentage', 11)
        ->call('saveSalaryBonus')
        ->assertHasNoErrors();

    $salary->refresh();
    $incomeCard = $component->viewData('fixedIncomes')->firstWhere('id', $salary->id);
    $expectedAnnualNet = round(($salaryNet * 12) + ($salaryNet * 2) + $bonusNet, 2);

    expect((float) $salary->amount)->toBe($salaryNet)
        ->and((float) $incomeCard->annual_net_estimate)->toBe($expectedAnnualNet)
        ->and(Income::where('workspace_id', $workspace->id)->count())->toBe(1);
});

test('extra income added from a salary contract is linked and counted for the full year', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário Empresa',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => ['salary_gross' => 1500],
    ]);
    $receivedDate = now()->startOfYear()->addMonths(2)->toDateString();

    $component = Livewire::test(IncomeHub::class)
        ->call('openExtraModal', $salary->id)
        ->assertSet('extraSalaryId', $salary->id)
        ->set('description', 'Serviço técnico')
        ->set('serviceUnitAmount', 125)
        ->set('serviceQuantity', 2)
        ->assertSet('amount', 250)
        ->set('received_at', $receivedDate)
        ->call('saveExtra')
        ->assertHasNoErrors();

    $extra = Income::where('description', 'Serviço técnico')->firstOrFail();
    $component->call('openSalaryIncomeHistory', $salary->id)
        ->assertSet('salaryIncomeHistoryId', $salary->id)
        ->assertSee('Serviço técnico')
        ->assertSee('Serviço extra')
        ->assertSee('2 × 125,00')
        ->assertSee('250,00');

    expect($extra->salary_recurring_income_id)->toBe($salary->id)
        ->and($extra->salary_income_type)->toBe('service')
        ->and((float) $extra->service_unit_amount)->toBe(125.0)
        ->and($extra->service_quantity)->toBe(2)
        ->and((float) $extra->amount)->toBe(250.0)
        ->and($extra->added_to_salary)->toBeTrue()
        ->and($component->viewData('salaryExtraIncomes')->contains('id', $extra->id))->toBeTrue();
});

test('salary service is added to the next month payment and its performed quantity is editable', function () {
    \Illuminate\Support\Carbon::setTestNow('2026-11-05 10:00:00');
    try {
        $user = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Finanças pessoais',
            'owner_id' => $user->id,
            'type' => 'personal',
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => 'Conta salário',
            'type' => 'corrente',
            'balance' => 100,
            'currency' => 'EUR',
        ]);

        $salary = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'description' => 'Salário Empresa',
            'amount' => 1200,
            'bank_account_id' => $account->id,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => [],
        ]);

        $component = Livewire::test(IncomeHub::class)
            ->call('openExtraModal', $salary->id)
            ->set('description', 'Serviço deste mês')
            ->set('serviceUnitAmount', 125)
            ->set('serviceQuantity', 2)
            ->call('saveExtra')
            ->assertSee('Serviços extra a receber no próximo salário')
            ->assertHasNoErrors();

        $extra = Income::where('description', 'Serviço deste mês')->firstOrFail();
        expect($extra->added_to_salary)->toBeTrue()
            ->and((float) $salary->fresh()->amount)->toBe(1200.0)
            ->and((float) $component->viewData('projectedSalaryNetTotal'))->toBe(1200.0);

        Livewire::test(IncomeHub::class)
            ->call('openSalaryIncomeHistory', $salary->id)
            ->call('editSalaryExtraIncome', $extra->id)
            ->assertSet('serviceQuantity', 2)
            ->set('serviceQuantity', 3)
            ->call('saveExtra')
            ->assertHasNoErrors();

        expect((int) $extra->fresh()->service_quantity)->toBe(3)
            ->and((float) $extra->fresh()->amount)->toBe(375.0);

        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 1.')->assertExitCode(0);
        $novemberPayment = $salary->salaryPayments()->where('payment_month', 11)->firstOrFail();
        expect($novemberPayment->status)->toBe('pending')
            ->and((float) $novemberPayment->net_received)->toBe(1200.0)
            ->and((float) $account->fresh()->balance)->toBe(100.0);

        \Illuminate\Support\Carbon::setTestNow('2026-12-25 08:45:00');
        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 1.')->assertExitCode(0);
        $decemberPayment = $salary->salaryPayments()->where('payment_month', 12)->firstOrFail();
        expect($decemberPayment->status)->toBe('paid')
            ->and((float) $decemberPayment->other_complements)->toBe(375.0)
            ->and((float) $decemberPayment->net_received)->toBe(1575.0)
            ->and((float) $decemberPayment->bank_balance_amount)->toBe(1575.0)
            ->and((float) $account->fresh()->balance)->toBe(1675.0);

        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 0.')->assertExitCode(0);
        expect((float) $account->fresh()->balance)->toBe(1675.0);
    } finally {
        \Illuminate\Support\Carbon::setTestNow();
    }
});

test('salary contract history includes all years and excludes entries linked to other salaries', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Contrato A',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [],
    ]);
    $otherSalary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Contrato B',
        'amount' => 1300,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [],
    ]);

    foreach ([
        ['description' => 'Entrada deste ano', 'amount' => 125, 'date' => now()->startOfYear()->addMonth()->toDateString(), 'salary' => $salary],
        ['description' => 'Prémio do ano passado', 'amount' => 75, 'date' => now()->subYear()->toDateString(), 'salary' => $salary],
        ['description' => 'Entrada de outro contrato', 'amount' => 300, 'date' => now()->toDateString(), 'salary' => $otherSalary],
    ] as $entry) {
        Income::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'salary_recurring_income_id' => $entry['salary']->id,
            'description' => $entry['description'],
            'amount' => $entry['amount'],
            'currency' => 'EUR',
            'received_at' => $entry['date'],
            'type' => 'Extra',
            'source' => 'emprego',
            'frequency' => 'pontual',
        ]);
    }

    Livewire::test(IncomeHub::class)
        ->assertSee('Receitas extra e prémios · '.now()->year)
        ->assertSee('Serviços extra')
        ->assertSee('Adicionar serviço')
        ->assertSee('Prémios')
        ->assertSee('Configurar prémio')
        ->assertSee('125,00')
        ->call('openSalaryIncomeHistory', $salary->id)
        ->assertSee('Entrada deste ano')
        ->assertSee('Prémio do ano passado')
        ->assertDontSee('Entrada de outro contrato');
});

test('received salary bonus contributes to the current month only when received this month and once to annual totals', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Contrato de trabalho',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [],
    ]);

    foreach ([
        ['description' => 'Prémio recebido noutro mês', 'amount' => 100, 'received_at' => now()->startOfYear()->addMonth()->toDateString()],
        ['description' => 'Prémio recebido este mês', 'amount' => 70, 'received_at' => now()->toDateString()],
        ['description' => 'Prémio do ano anterior', 'amount' => 50, 'received_at' => now()->subYear()->toDateString()],
    ] as $bonus) {
        Income::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'salary_recurring_income_id' => $salary->id,
            'description' => $bonus['description'],
            'amount' => $bonus['amount'],
            'currency' => 'EUR',
            'received_at' => $bonus['received_at'],
            'type' => 'Extra',
            'source' => 'emprego',
            'frequency' => 'pontual',
        ]);
    }

    $component = Livewire::test(IncomeHub::class);
    $contractExtras = $component->viewData('salaryExtraIncomes')
        ->where('salary_recurring_income_id', $salary->id);
    $receivedThisMonth = $component->viewData('extraIncomes')
        ->where('salary_recurring_income_id', $salary->id);

    expect($receivedThisMonth->sum(fn ($income) => (float) $income->amount))->toBe(70.0)
        ->and($contractExtras->filter(fn ($income) => $income->received_at->year === now()->year)
            ->sum(fn ($income) => (float) $income->amount))->toBe(170.0)
        ->and((float) $component->viewData('totalMonthly'))->toBe(1270.0);
});

test('existing unlinked extra can be associated with an employment contract', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Contrato existente',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [],
    ]);
    $extra = Income::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Prémio antigo',
        'amount' => 180,
        'currency' => 'EUR',
        'received_at' => now()->toDateString(),
        'type' => 'Extra',
        'source' => 'emprego',
        'frequency' => 'pontual',
    ]);

    Livewire::test(IncomeHub::class)
        ->assertSee('Outras entradas extra')
        ->call('associateExtraWithSalary', $extra->id, (string) $salary->id)
        ->assertHasNoErrors();

    expect($extra->fresh()->salary_recurring_income_id)->toBe($salary->id);
});

test('salary contract exposes a dedicated bonus button that opens the bonus section', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário',
        'amount' => 1200,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'salary_gross' => 1500,
            'meal_allowance' => 0,
        ],
    ]);

    Livewire::test(IncomeHub::class)
        ->assertSee('Prémio')
        ->assertSeeInOrder(['Rendimentos Fixos', 'Rendimentos por Categorias', 'Contratos de Trabalho (Salários)'])
        ->call('editSalaryBonus', $salary->id)
        ->assertDispatched('modal-show-salary-bonus')
        ->assertSee('Prémio do contrato')
        ->assertSee('Valor do prémio')
        ->assertSee('Descontar IRS')
        ->assertSee('Descontar Segurança Social')
        ->assertSet('editingFixedId', null)
        ->assertSet('salaryBonusId', $salary->id)
        ->assertSet('showSalaryBonusModal', true)
        ->assertSet('recBonusType', 'none');
});

test('salary contract saves professional tax and benefit details and exposes its detail tabs', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(IncomeHub::class)
        ->call('newEmployment')
        ->set('recDescription', 'Empresa Teste')
        ->set('recPosition', 'Analista')
        ->set('recDepartment', 'Financeiro')
        ->set('recSalaryGross', 1800)
        ->set('recDay', 25)
        ->set('recStartDate', '2026-01-01')
        ->set('recAnnualPayments', 14)
        ->set('recDuodecimosPercentage', 50)
        ->set('recOtherComplements', 100)
        ->set('recTaxRegion', 'madeira')
        ->set('recHealthInsurance', 'Plano de saúde')
        ->set('recExpenseAllowance', 75)
        ->set('recAdditionalBenefits', 'Passe de transporte')
        ->set('recNotes', 'Revisão anual em janeiro')
        ->call('saveEmployment')
        ->assertHasNoErrors();

    $salary = RecurringIncome::where('workspace_id', $workspace->id)->firstOrFail();
    expect($salary->metadata)->toMatchArray([
        'position' => 'Analista',
        'department' => 'Financeiro',
        'tax_region' => 'madeira',
        'annual_payments' => 14,
        'duodecimos_percentage' => 50,
        'health_insurance' => 'Plano de saúde',
        'expense_allowance' => 75,
        'additional_benefits' => 'Passe de transporte',
    ])->and($salary->notes)->toBe('Revisão anual em janeiro');

    Livewire::test(IncomeHub::class)
        ->call('openSalaryDetails', $salary->id)
        ->assertSee('Visão geral')
        ->assertSee('Salário')
        ->call('setSalaryDetailsTab', 'professional')
        ->assertSet('salaryDetailsTab', 'professional')
        ->call('setSalaryDetailsTab', 'benefits')
        ->assertSet('salaryDetailsTab', 'benefits');
});

test('salary contract history button opens all monthly payments for editing', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário da MIND',
        'amount' => 1300,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => ['salary_gross' => 1600],
    ]);
    foreach ([9, 10] as $month) {
        SalaryPayment::create([
            'recurring_income_id' => $salary->id,
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'payment_month' => $month,
            'payment_year' => 2026,
            'salary_base_gross' => 1600,
            'allowances_duodecimos' => 0,
            'other_complements' => 0,
            'social_security' => 176,
            'irs' => 0,
            'other_deductions' => 0,
            'meal_allowance' => 0,
            'net_received' => 1300,
            'bank_balance_amount' => 0,
            'status' => 'paid',
            'is_automatic' => true,
        ]);
    }

    Livewire::test(IncomeHub::class)
        ->assertSee('Ver histórico de salários')
        ->call('openSalaryPaymentHistory', $salary->id)
        ->assertSet('salaryDetailsTab', 'payments')
        ->assertSee('setSalaryDetailsTab')
        ->assertSee('setembro 2026')
        ->assertSee('outubro 2026')
        ->assertSee('Editar valores');
});

test('salary payments cannot be added manually', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);
    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário de teste',
        'amount' => 1300,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => ['salary_gross' => 1600],
    ]);

    Livewire::test(IncomeHub::class)
        ->call('openSalaryPaymentForm')
        ->call('saveSalaryPayment')
        ->assertDispatched('toast', variant: 'error', text: 'Não é possível adicionar pagamentos. São criados automaticamente.');

    expect($salary->salaryPayments()->count())->toBe(0);
});

test('salary payment is generated pending before due date and automatically paid on the due date', function () {
    \Illuminate\Support\Carbon::setTestNow('2026-11-05 08:45:00');
    try {
        $user = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Finanças pessoais',
            'owner_id' => $user->id,
            'type' => 'personal',
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => 'Conta salário automática',
            'type' => 'corrente',
            'balance' => 100,
            'currency' => 'EUR',
        ]);
        $salary = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'bank_account_id' => $account->id,
            'description' => 'Salário automático',
            'amount' => 1324,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => ['salary_gross' => 1600],
        ]);

        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 1.')->assertExitCode(0);
        $payment = $salary->salaryPayments()->firstOrFail();
        expect($payment->is_automatic)->toBeTrue()
            ->and($payment->status)->toBe('pending')
            ->and($payment->paid_at)->toBeNull()
            ->and((float) $account->fresh()->balance)->toBe(100.0);

        $details = Livewire::test(IncomeHub::class)
            ->call('openSalaryDetails', $salary->id)
            ->call('setSalaryDetailsTab', 'payments')
            ->assertSee('Automático')
            ->assertSee('Pendente')
            ->assertDontSee('Adicionar pagamento')
            ->assertDontSee('Marcar pago')
            ->call('openSalaryPaymentForm', $payment->id)
            ->set('salaryPaymentForm.net_received', 1400)
            ->call('saveSalaryPayment')
            ->assertHasNoErrors();

        \Illuminate\Support\Carbon::setTestNow('2026-11-25 08:45:00');
        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 1.')->assertExitCode(0);

        expect($payment->fresh()->status)->toBe('paid')
            ->and($payment->fresh()->paid_at->toDateString())->toBe('2026-11-25')
            ->and((float) $payment->fresh()->net_received)->toBe(1400.0)
            ->and((float) $payment->fresh()->bank_balance_amount)->toBe(1400.0)
            ->and((float) $account->fresh()->balance)->toBe(1500.0);
        $details->call('setSalaryDetailsTab', 'overview')
            ->call('setSalaryDetailsTab', 'payments')
            ->assertSee('Pago');
    } finally {
        \Illuminate\Support\Carbon::setTestNow();
    }
});

test('opening salary history catches up an overdue automatic payment', function () {
    \Illuminate\Support\Carbon::setTestNow('2026-11-01 08:45:00');
    try {
        $user = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Finanças pessoais',
            'owner_id' => $user->id,
            'type' => 'personal',
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => 'Conta salário automática',
            'type' => 'corrente',
            'balance' => 100,
            'currency' => 'EUR',
        ]);
        $salary = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'bank_account_id' => $account->id,
            'description' => 'Salário automático',
            'amount' => 1324,
            'day_of_month' => 2,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => ['salary_gross' => 1600, 'start_date' => '2026-11-01'],
        ]);

        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 1.')->assertExitCode(0);
        $payment = $salary->salaryPayments()->firstOrFail();
        expect($payment->status)->toBe('pending')
            ->and((float) $account->fresh()->balance)->toBe(100.0);

        $salary->update(['metadata' => ['salary_gross' => 1600, 'start_date' => '2026-11-11']]);
        \Illuminate\Support\Carbon::setTestNow('2026-11-05 23:15:00');
        Livewire::test(IncomeHub::class)
            ->call('openSalaryDetails', $salary->id)
            ->call('setSalaryDetailsTab', 'payments')
            ->assertSee('Pago')
            ->assertDontSee('Pendente');

        expect($payment->fresh()->status)->toBe('paid')
            ->and($payment->fresh()->paid_at->toDateString())->toBe('2026-11-02')
            ->and((float) $account->fresh()->balance)->toBe(1424.0);

        Livewire::test(IncomeHub::class)->call('openSalaryDetails', $salary->id);
        expect((float) $account->fresh()->balance)->toBe(1424.0);
    } finally {
        \Illuminate\Support\Carbon::setTestNow();
    }
});

test('monthly salary payments are created once automatically and balance remains editable', function () {
    \Illuminate\Support\Carbon::setTestNow('2026-11-25 08:45:00');
    try {
        $user = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Finanças pessoais',
            'owner_id' => $user->id,
            'type' => 'personal',
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);
        $account = BankAccount::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'name' => 'Conta salário',
            'type' => 'corrente',
            'balance' => 100,
            'currency' => 'EUR',
        ]);
        $salary = RecurringIncome::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'bank_account_id' => $account->id,
            'description' => 'Salário Empresa',
            'amount' => 1324,
            'day_of_month' => 25,
            'is_active' => true,
            'source' => 'emprego',
            'frequency' => 'mensal',
            'metadata' => [
                'salary_gross' => 1600,
                'calculated_ss' => 176,
                'calculated_irs' => 100,
                'calculated_sa' => 100,
                'meal_allowance' => 5,
                'working_days' => 20,
            ],
        ]);

        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 1.')->assertExitCode(0);

        $payment = $salary->salaryPayments()->firstOrFail();
        expect($payment->status)->toBe('paid')
            ->and($payment->is_automatic)->toBeTrue()
            ->and((float) $payment->salary_base_gross)->toBe(1600.0)
            ->and((float) $payment->bank_balance_amount)->toBe(1324.0)
            ->and((float) $account->fresh()->balance)->toBe(1424.0)
            ->and((float) $account->fresh()->current_balance)->toBe(1424.0);

        $this->artisan('salary:process-monthly-payments')->expectsOutput('Pagamentos salariais criados: 0.')->assertExitCode(0);
        expect($salary->salaryPayments()->count())->toBe(1);

        Livewire::test(IncomeHub::class)
            ->call('openSalaryDetails', $salary->id)
            ->call('setSalaryDetailsTab', 'payments')
            ->assertSee('Automático')
            ->call('openSalaryPaymentForm', $payment->id)
            ->set('salaryPaymentForm.net_received', 1500)
            ->call('saveSalaryPayment')
            ->assertHasNoErrors();

        expect((float) $account->fresh()->balance)->toBe(1600.0)
            ->and((float) $account->fresh()->current_balance)->toBe(1600.0)
            ->and((float) $payment->fresh()->bank_balance_amount)->toBe(1500.0);
    } finally {
        \Illuminate\Support\Carbon::setTestNow();
    }
});

test('annual salary estimate includes the remaining half of partially paid duodecimos', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $calculator = app(PortugueseNetPayCalculator::class);
    $monthlyNet = $calculator->calculate(1625)['net'];
    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário com duodécimos parciais',
        'amount' => $monthlyNet,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'salary_gross' => 1500,
            'annual_payments' => 14,
            'duodecimos_percentage' => 50,
            'allowances_in_duodecimos' => true,
            'monthly_duodecimos' => 125,
            'holiday_allowance_same_as_salary' => true,
            'christmas_allowance_same_as_salary' => true,
            'meal_allowance' => 0,
        ],
    ]);

    $incomeCard = Livewire::test(IncomeHub::class)
        ->viewData('fixedIncomes')
        ->firstWhere('id', $salary->id);

    expect((float) $incomeCard->annual_net_estimate)
        ->toBe(round(($monthlyNet * 12) + ($calculator->calculate(750)['net'] * 2), 2));
});

test('annual salary estimate includes meal allowance for eleven months in cash or separate card', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    $monthlySalaryNet = app(PortugueseNetPayCalculator::class)->calculate(1500)['net'];
    $mealAllowance = 140.0;
    $cashSalary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário com alimentação em dinheiro',
        'amount' => $monthlySalaryNet + $mealAllowance,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'annual_payments' => 12,
            'calculated_sa' => $mealAllowance,
            'salary_gross' => 1500,
        ],
    ]);
    $mealCardSalary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário com cartão de alimentação',
        'amount' => $monthlySalaryNet,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
        'metadata' => [
            'annual_payments' => 12,
            'calculated_sa' => $mealAllowance,
            'meal_card_income_id' => 123,
            'salary_gross' => 1500,
        ],
    ]);

    $incomeCards = Livewire::test(IncomeHub::class)->viewData('fixedIncomes');

    expect((float) $incomeCards->firstWhere('id', $cashSalary->id)->annual_net_estimate)
        ->toBe(round(($monthlySalaryNet + $mealAllowance) * 12 - $mealAllowance, 2))
        ->and((float) $incomeCards->firstWhere('id', $mealCardSalary->id)->annual_net_estimate)
        ->toBe(round($monthlySalaryNet * 12 + $mealAllowance * 11, 2));
});

test('salary documents are private and can be previewed downloaded and deleted', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Finanças pessoais',
        'owner_id' => $user->id,
        'type' => 'personal',
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);
    $salary = RecurringIncome::create([
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário com documento',
        'amount' => 1300,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
    ]);

    $component = Livewire::test(IncomeHub::class)
        ->call('openSalaryDetails', $salary->id)
        ->set('salaryDocumentType', 'payslip')
        ->set('salaryDocumentFile', UploadedFile::fake()->createWithContent('recibo.pdf', '%PDF-1.4 test'))
        ->call('uploadSalaryDocument')
        ->assertHasNoErrors();
    $document = $salary->salaryDocuments()->firstOrFail();
    Storage::disk('local')->assertExists($document->file_path);
    $this->get(route('salary-documents.preview', $document))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    $component->call('downloadSalaryDocument', $document->id)
        ->assertFileDownloaded('recibo.pdf')
        ->call('deleteSalaryDocument', $document->id)
        ->assertHasNoErrors();
    expect($salary->salaryDocuments()->count())->toBe(0);
    Storage::disk('local')->assertMissing($document->file_path);
});

test('salary documents and contract details are isolated between workspace users', function () {
    Storage::fake('local');
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Empresa partilhada',
        'owner_id' => $owner->id,
        'type' => 'company',
    ]);
    $workspace->users()->attach($member->id, ['role' => 'member']);
    $owner->forceFill(['current_workspace_id' => $workspace->id])->save();
    $member->forceFill(['current_workspace_id' => $workspace->id])->save();
    $salary = RecurringIncome::create([
        'user_id' => $member->id,
        'workspace_id' => $workspace->id,
        'description' => 'Salário privado',
        'amount' => 1300,
        'day_of_month' => 25,
        'is_active' => true,
        'source' => 'emprego',
        'frequency' => 'mensal',
    ]);
    $filePath = 'salary-documents/'.$workspace->id.'/'.$member->id.'/'.$salary->id.'/receipt.pdf';
    Storage::disk('local')->put($filePath, '%PDF-1.4 private');
    $document = SalaryDocument::create([
        'recurring_income_id' => $salary->id,
        'user_id' => $member->id,
        'workspace_id' => $workspace->id,
        'document_type' => 'payslip',
        'original_name' => 'recibo.pdf',
        'file_path' => $filePath,
        'mime_type' => 'application/pdf',
        'file_size' => 16,
    ]);

    $this->actingAs($owner)
        ->get(route('salary-documents.preview', $document))
        ->assertNotFound();

    Livewire::test(IncomeHub::class)->assertDontSee('Salário privado');
    expect(fn () => Livewire::test(IncomeHub::class)->call('openSalaryDetails', $salary->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
