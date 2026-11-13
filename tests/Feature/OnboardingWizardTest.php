<?php

use App\Livewire\OnboardingWizard;
use App\Livewire\OnboardingSalary;
use App\Livewire\OnboardingIncome;
use App\Livewire\OnboardingInvestment;
use App\Livewire\OnboardingInvestmentImport;
use App\Livewire\InvestmentsHub;
use App\Livewire\OnboardingStatement;
use App\Livewire\OnboardingSubscription;
use App\Models\BankAccount;
use App\Models\BankStatementImport;
use App\Models\BankTransfer;
use App\Models\Category;
use App\Models\Investment;
use App\Models\RecurringIncome;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use Dompdf\Dompdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function onboardingProofPdf(string $text): UploadedFile
{
    $pdf = new Dompdf;
    $pdf->loadHtml('<html><body><p>'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'</p></body></html>');
    $pdf->render();

    return UploadedFile::fake()->createWithContent('comprovativo.pdf', $pdf->output());
}

function onboardingWorkspace(): array
{
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Finanças', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    test()->actingAs($user);

    return [$user, $workspace];
}

test('each onboarding step shows a stable encouragement while its form is updated', function () {
    onboardingWorkspace();

    $wizard = Livewire::test(OnboardingWizard::class);
    for ($step = 1; $step <= 10; $step++) {
        $wizard->set('step', $step);
        expect(preg_match('/<span data-onboarding-encouragement>(.*?)<\/span>/s', $wizard->html(), $matches))->toBe(1);
        expect(trim($matches[1]))->not->toBe('');
        $message = $matches[1];
        $wizard->set('workspaceName', 'Nome de teste');
        expect($wizard->html())->toContain('<span data-onboarding-encouragement>'.$message.'</span>');
    }
});

test('each onboarding step renders the same navigation footer', function () {
    onboardingWorkspace();

    $wizard = Livewire::test(OnboardingWizard::class);
    for ($step = 1; $step <= 10; $step++) {
        $wizard->set('step', $step)->assertSee("Passo $step de 10");
        expect(substr_count($wizard->html(), 'data-onboarding-footer'))->toBe(1);
        if ($step >= 2 && $step <= 8) {
            $wizard->assertSee('Podes saltar este passo e preencher os dados mais tarde.');
        } else {
            $wizard->assertDontSee('Podes saltar este passo e preencher os dados mais tarde.');
        }
    }

    $wizard->set('step', 4)->set('salarySource', 'freelance')
        ->assertSee('Guardar e Continuar');
    expect(substr_count($wizard->html(), 'data-onboarding-footer'))->toBe(1);

    Livewire::test(OnboardingSalary::class)
        ->assertSeeHtml('wire:submit.prevent="saveEmployment"')
        ->assertSeeHtml('wire:click="$parent.previousStep()"')
        ->assertSeeHtml('wire:click="$parent.skipStep()"')
        ->assertSeeHtml('wire:target="saveEmployment,scanEmploymentContract,contractScanPhoto"');
});

test('new onboarding steps can be skipped and revisited without saving records', function () {
    [$user, $workspace] = onboardingWorkspace();

    $wizard = Livewire::test(OnboardingWizard::class)->set('step', 5);
    foreach ([
        5 => 'Adiciona outros rendimentos',
        6 => 'Organiza os teus investimentos',
        7 => 'Importa um extrato bancário',
        8 => 'Organiza as tuas assinaturas',
    ] as $step => $title) {
        $wizard->assertSet('step', $step)->assertSee($title)
            ->assertDontSee('Regista transferências bancárias')
            ->assertSee('Saltar')->call('skipStep');
    }

    $wizard->assertSet('step', 9)->assertSee('Passo 9 de 10')
        ->call('previousStep')->assertSet('step', 8)->assertSee('Organiza as tuas assinaturas')
        ->call('nextStep')->assertSet('step', 9)->call('nextStep')->assertSet('step', 10)
        ->assertSee('Tudo pronto');

    expect(RecurringIncome::where('workspace_id', $workspace->id)->count())->toBe(0)
        ->and(Investment::where('workspace_id', $workspace->id)->count())->toBe(0)
        ->and(BankStatementImport::where('workspace_id', $workspace->id)->count())->toBe(0)
        ->and(BankTransfer::where('workspace_id', $workspace->id)->count())->toBe(0)
        ->and(Subscription::where('workspace_id', $workspace->id)->count())->toBe(0);
});

test('income selector opens each official form without opening the background modal', function (string $source) {
    onboardingWorkspace();

    Livewire::test(OnboardingIncome::class)
        ->assertSee('Para que fonte de rendimento é este registo?')
        ->call('openIncomeSource', $source)
        ->assertDispatched('onboarding-modal-show-'.$source)
        ->assertNotDispatched('modal-show-'.$source);
})->with(['emprego', 'renda', 'freelance', 'investimento', 'reforma', 'bolsa', 'outro']);

test('additional income shows validation and saves only when confirmed', function () {
    [$user, $workspace] = onboardingWorkspace();

    Livewire::test(OnboardingIncome::class)
        ->call('openIncomeSource', 'renda')
        ->call('saveRental')->assertHasErrors(['recDescription', 'recRentalGross'])
        ->assertSee('Verifica os dados antes de guardar:')
        ->set('recDescription', 'Renda exemplo')
        ->set('recRentalGross', 500)
        ->call('saveRental')->assertHasNoErrors()
        ->assertDispatched('onboarding-modal-close-renda');

    $income = RecurringIncome::where('workspace_id', $workspace->id)->where('description', 'Renda exemplo')->firstOrFail();
    expect((float) $income->amount)->toBe(500.0);
});

test('additional employment scanner fills the form without saving', function () {
    Storage::fake('local');
    config(['services.openrouter.api_key' => 'fake-test-key']);
    Http::fake(['openrouter.ai/*' => Http::response([
        'choices' => [['message' => ['content' => json_encode(['company' => 'Empresa Teste', 'salary_gross' => 1500])]]],
    ])]);
    onboardingWorkspace();

    Livewire::test(OnboardingIncome::class)->call('openIncomeSource', 'emprego')
        ->set('salaryFormMode', 'scanner')
        ->set('contractScanPhoto', UploadedFile::fake()->image('contrato.jpg'))
        ->call('scanEmploymentContract')
        ->assertSet('recDescription', 'Empresa Teste')->assertSet('recSalaryGross', 1500.0);

    expect(RecurringIncome::count())->toBe(0);
    Http::assertSentCount(1);
});

test('investment form validates and creates an asset for the selected account', function () {
    [$user, $workspace] = onboardingWorkspace();
    $account = BankAccount::create(['workspace_id' => $workspace->id, 'user_id' => $user->id, 'name' => 'Conta', 'type' => 'corrente', 'balance' => 100]);

    Livewire::test(OnboardingInvestment::class)
        ->call('createAsset')->assertDispatched('onboarding-modal-show-add-investment')
        ->call('save')->assertHasErrors(['symbol', 'bank_account_id'])
        ->assertSee('Verifica os dados antes de guardar:')
        ->set('bank_account_id', $account->id)->set('symbol', 'TEST')
        ->set('quantity', 2)->set('average_price', 10)
        ->call('save')->assertHasNoErrors()->assertDispatched('onboarding-modal-close-add-investment');

    expect(Investment::where('workspace_id', $workspace->id)->where('symbol', 'TEST')->count())->toBe(1);
});

test('investment onboarding offers the report importer with both providers', function () {
    onboardingWorkspace();

    Livewire::test(OnboardingInvestment::class)
        ->assertSee('Novo Investimento')
        ->assertSee('Upload de relatórios')
        ->assertSee('enviado ao OpenRouter')
        ->assertSeeLivewire('onboarding-investment-import');

    Livewire::test(OnboardingInvestmentImport::class)
        ->assertSee('XTB')->assertSee('AforroNet')
        ->call('switchProvider', 'aforronet')
        ->assertSee('Ficheiro PDF do extrato AforroNet');
});

test('onboarding previews an XTB report before synchronizing positions', function () {
    Storage::fake('local');
    config(['services.openrouter.api_key' => null]);
    Http::fake();
    [$user, $workspace] = onboardingWorkspace();
    $report = UploadedFile::fake()->createWithContent('relatorio.csv', "Ticker,Category,Volume,Value,Open Price,Current Price,Instrument/Position\nTEST,STOCK,2,24,10,12,123456\n");

    $importer = Livewire::test(OnboardingInvestmentImport::class)
        ->set('reportFile', $report)
        ->call('importPositions');
    expect(Investment::where('workspace_id', $workspace->id)->count())->toBe(0);

    $importer->call('analyzeReport')->assertHasNoErrors()->assertSet('previewReady', true)
        ->assertSee('Pré-visualização dos investimentos')->assertSee('TEST');
    expect(Investment::where('workspace_id', $workspace->id)->count())->toBe(0);

    $importer->call('importPositions')->assertHasNoErrors()->assertSet('previewReady', false)
        ->assertSee('Posições XTB importadas.');
    $investment = Investment::where('workspace_id', $workspace->id)->where('symbol', 'TEST')->firstOrFail();
    expect((float) $investment->quantity)->toBe(2.0)
        ->and((float) $investment->current_price)->toBe(12.0)
        ->and($investment->broker)->toBe('XTB');

    $importer->set('reportFile', UploadedFile::fake()->createWithContent('relatorio.csv', "Ticker,Category,Volume,Value,Open Price,Current Price,Instrument/Position\nTEST,STOCK,3,45,10,15,123456\n"))
        ->call('analyzeReport')->call('importPositions');
    expect(Investment::where('workspace_id', $workspace->id)->where('broker', 'XTB')->count())->toBe(1)
        ->and((float) $investment->fresh()->quantity)->toBe(3.0);
    Http::assertNothingSent();
});

test('the investments module still exposes its original report importer', function () {
    Http::fake();
    onboardingWorkspace();

    Livewire::test(InvestmentsHub::class)
        ->assertSee('Upload de relatórios')
        ->assertSeeLivewire('xtb-investment-import-hub');
});

test('switching a report invalidates its previous preview', function () {
    Storage::fake('local');
    config(['services.openrouter.api_key' => null]);
    onboardingWorkspace();

    Livewire::test(OnboardingInvestmentImport::class)
        ->set('reportFile', UploadedFile::fake()->createWithContent('primeiro.csv', "Ticker,Category,Volume,Value,Open Price\nTEST,STOCK,2,24,10\n"))
        ->call('analyzeReport')->assertSet('previewReady', true)
        ->set('reportFile', UploadedFile::fake()->createWithContent('segundo.csv', "Ticker,Category,Volume,Value,Open Price\nOUTRO,STOCK,1,20,10\n"))
        ->assertSet('previewReady', false)->assertSet('positions', [])
        ->call('importPositions');

    expect(Investment::count())->toBe(0);
});

test('onboarding previews and imports AforroNet subscriptions after confirmation', function () {
    Storage::fake('local');
    [$user, $workspace] = onboardingWorkspace();
    $pdf = onboardingProofPdf('Data do Extrato 13-11-2026 CAF Serie E '.str_repeat('informacao ', 15).'01-01-2022 123456 1,05000 100 105,00');

    $importer = Livewire::test(OnboardingInvestmentImport::class)
        ->call('switchProvider', 'aforronet')->set('aforroNetFile', $pdf)
        ->call('analyzeAforroNetFile')->assertHasNoErrors()
        ->assertSet('aforroNetPreviewReady', true)->assertSee('Pré-visualização das subscrições');
    expect(Investment::where('workspace_id', $workspace->id)->count())->toBe(0);

    $importer->call('importAforroNetPosition')->assertSet('aforroNetPreviewReady', false)
        ->assertSee('Subscrições AforroNet importadas.');
    $investment = Investment::where('workspace_id', $workspace->id)->where('broker', 'AforroNet')->firstOrFail();
    expect((float) $investment->quantity)->toBe(100.0)
        ->and($investment->subscription_number)->toBe('123456');
});

test('investment report importer rejects users without write access', function () {
    [$user, $workspace] = onboardingWorkspace();
    $workspace->users()->updateExistingPivot($user->id, ['role' => 'viewer']);

    Livewire::test(OnboardingInvestmentImport::class)->assertForbidden();
    expect(Investment::count())->toBe(0);
});

test('investment report importer rejects a workspace change before confirmation', function () {
    Storage::fake('local');
    config(['services.openrouter.api_key' => null]);
    [$user, $workspace] = onboardingWorkspace();
    $user->forceFill(['current_workspace_id' => null])->save();
    $other = Workspace::create(['name' => 'Outro', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();

    $importer = Livewire::test(OnboardingInvestmentImport::class)
        ->set('reportFile', UploadedFile::fake()->createWithContent('relatorio.csv', "Ticker,Category,Volume,Value,Open Price\nTEST,STOCK,2,24,10\n"))
        ->call('analyzeReport')->assertSet('previewReady', true);
    $user->forceFill(['current_workspace_id' => $other->id])->save();
    $importer->call('importPositions')->assertForbidden();

    expect(Investment::withoutGlobalScopes()->count())->toBe(0);
});

test('statement import requires preview and explicit confirmation', function () {
    Storage::fake('local');
    [$user, $workspace] = onboardingWorkspace();
    $file = UploadedFile::fake()->createWithContent('extrato.csv', "Data;Descricao;Valor\n01/11/2026;Cafe;-20.00\n");

    $component = Livewire::test(OnboardingStatement::class)
        ->call('openImportForm')->set('statementFile', $file)
        ->call('importStatement')->assertDispatched('toast');
    expect(BankStatementImport::where('workspace_id', $workspace->id)->count())->toBe(0);

    $component->call('generatePreview')->assertHasNoErrors()->assertSet('previewReady', true)
        ->assertSet('preview.transactions_total', 1);
    expect(BankStatementImport::where('workspace_id', $workspace->id)->count())->toBe(0);
    $component->call('importStatement')->assertHasNoErrors()->assertSet('previewReady', false);
    expect(BankStatementImport::where('workspace_id', $workspace->id)->count())->toBe(1);
});

test('changing the statement file clears its previous preview', function () {
    Storage::fake('local');
    onboardingWorkspace();

    Livewire::test(OnboardingStatement::class)->call('openImportForm')
        ->set('statementFile', UploadedFile::fake()->createWithContent('primeiro.csv', "Data;Descricao;Valor\n01/11/2026;Cafe;-20.00\n"))
        ->call('generatePreview')->assertSet('previewReady', true)
        ->set('statementFile', UploadedFile::fake()->createWithContent('segundo.csv', "Data;Descricao;Valor\n02/11/2026;Loja;-30.00\n"))
        ->assertSet('previewReady', false)->assertSet('selectedSignatures', []);
});

test('subscription saves once when explicitly confirmed', function () {
    [$user, $workspace] = onboardingWorkspace();
    $component = Livewire::test(OnboardingSubscription::class)->call('openExtraModal');
    $category = Category::where('workspace_id', $workspace->id)->where('name', 'Transporte')->firstOrFail();
    $component->call('save')->assertHasErrors(['name', 'amount', 'category_id', 'billing_day'])
        ->assertSee('Verifica os dados antes de guardar:')
        ->set('name', 'Serviço exemplo')->set('amount', 10)->set('category_id', $category->id)
        ->set('billing_day', 12)->call('save')->assertHasNoErrors()->assertSet('showExtraModal', false);
    expect(Subscription::where('workspace_id', $workspace->id)->where('name', 'Serviço exemplo')->count())->toBe(1);
});

test('onboarding subscription dialog is layered above its backdrop', function () {
    onboardingWorkspace();

    Livewire::test(OnboardingSubscription::class)->call('openExtraModal')
        ->assertSeeHtml('class="fixed inset-0 z-[69] bg-zinc-950/70"')
        ->assertSeeHtml('class="fixed inset-0 z-[70]  flex items-center justify-center')
        ->assertSeeHtml('<div @click.stop x-show="open"')
        ->call('closeExtraModal')->assertSet('showExtraModal', false);
});

test('subscription modal keeps the monthly transport pass form functional', function () {
    [$user, $workspace] = onboardingWorkspace();

    Livewire::test(OnboardingSubscription::class)->call('openExtraModal')
        ->set('transportPassMode', true)
        ->assertSee('Passe mensal de transporte')
        ->assertSee('Pago pela empresa?')
        ->set('transportPassName', 'Passe exemplo')
        ->set('transportPassAmount', 40)
        ->set('transportPassStartedAt', '2026-11-01')
        ->call('save')->assertHasNoErrors()->assertSet('showExtraModal', false);

    expect(Subscription::where('workspace_id', $workspace->id)->where('name', 'Passe exemplo')->count())->toBe(1);
});

test('optional forms reject saving after the active workspace changes', function () {
    [$user, $workspace] = onboardingWorkspace();
    $user->forceFill(['current_workspace_id' => null])->save();
    $other = Workspace::create(['name' => 'Outro', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $component = Livewire::test(OnboardingSubscription::class)->call('openExtraModal');
    $user->forceFill(['current_workspace_id' => $other->id])->save();

    $component->call('save')->assertForbidden();
    expect(Subscription::count())->toBe(0);
});

test('viewers can skip optional steps but cannot mount their write forms', function () {
    [$user, $workspace] = onboardingWorkspace();
    $workspace->users()->updateExistingPivot($user->id, ['role' => 'viewer']);

    Livewire::test(OnboardingWizard::class)->set('step', 8)->assertStatus(200)
        ->assertSee('Não existe um espaço financeiro ativo com permissão')
        ->call('skipStep')->assertSet('step', 9);
    Livewire::test(OnboardingSubscription::class)->assertForbidden();
    expect(Subscription::count())->toBe(0);
});

test('welcome continues to workspace, then bank accounts and salary', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->assertSee('Podes saltar alguns passos')
        ->assertSee('Área empresarial')
        ->assertSee('Gere o teu negócio com o plano Business.')
        ->assertSee('Começar configuração')
        ->call('nextStep')
        ->assertSet('step', 2)
        ->assertSee('O teu espaço financeiro')
        ->assertSee('Passo 2 de 10')
        ->call('skipStep')
        ->assertSet('step', 3)
        ->assertSee('Onde guardas o teu dinheiro?')
        ->assertSee('Passo 3 de 10')
        ->call('previousStep')
        ->assertSet('step', 2)
        ->assertSee('O teu espaço financeiro')
        ->call('skipStep')
        ->call('skipStep')
        ->assertSet('step', 4)
        ->assertSee('Quais são os dados do teu salário?')
        ->assertSee('Passo 4 de 10');
});

test('workspace step names the existing space without changing its type or access', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Original', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 2)
        ->assertSet('workspaceName', 'Original')
        ->assertSee('As sugestões alteram apenas o nome.')
        ->set('workspaceName', 'Família')
        ->call('nextStep')
        ->assertSet('step', 3);

    expect($workspace->fresh()->name)->toBe('Família')
        ->and($workspace->fresh()->type)->toBe('personal')
        ->and($workspace->users()->count())->toBe(1);
});

test('skipping the workspace step keeps the existing name', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Original', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 2)
        ->set('workspaceName', 'Nome não guardado')
        ->call('skipStep')
        ->assertSet('step', 3);

    expect($workspace->fresh()->name)->toBe('Original');
});

test('onboarding estimates net pay from the 2026 withholding tables', function () {
    $user = User::factory()->create([
        'name' => 'Eduardo Silva',
        'onboarding_completed' => false,
    ]);

    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 4)
        ->set('salaryGross', 1500)
        ->assertSet('calculatedIRS', 168.17)
        ->assertSet('calculatedSS', 165.0)
        ->assertSet('salaryAmount', 1166.83);
});

test('onboarding creates the configured bank account before income setup', function () {
    $user = User::factory()->create([
        'onboarding_completed' => false,
    ]);
    $workspace = Workspace::create([
        'name' => 'As Minhas Finanças',
        'owner_id' => $user->id,
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();

    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('bankAccounts.0.name', 'Conta principal')
        ->set('bankAccounts.0.bank_name', 'Banco Teste')
        ->set('bankAccounts.0.balance', 1250.50)
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 4);

    $this->assertDatabaseHas('bank_accounts', [
        'user_id' => $user->id,
        'name' => 'Conta principal',
        'bank_name' => 'Banco Teste',
        'balance' => 1250.50,
    ]);
});

test('onboarding saves all bank fields and does not duplicate accounts when returning', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Finanças', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('bankAccounts.0.name', 'Investimento')
        ->set('bankAccounts.0.type', 'investimento')
        ->set('bankAccounts.0.balance', 250.50)
        ->set('bankAccounts.0.currency', 'USD')
        ->set('bankAccounts.0.account_number', '12345678')
        ->set('bankAccounts.0.iban', 'PT50001800001234567890154')
        ->set('bankAccounts.0.holder_name', 'Pessoa Teste')
        ->set('bankAccounts.0.swift', 'TOTAPTPL')
        ->set('bankAccounts.0.color', '#112233')
        ->set('bankAccounts.0.alert_below', 0)
        ->set('bankAccounts.0.is_business', true)
        ->set('bankAccounts.0.include_in_total', false)
        ->call('addBankAccount')
        ->set('bankAccounts.1.name', 'Carteira')
        ->set('bankAccounts.1.type', 'cash')
        ->set('bankAccounts.1.balance', 30)
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 4)
        ->call('previousStep')
        ->set('bankAccounts.0.balance', 275)
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 4);

    $this->assertDatabaseCount('bank_accounts', 2);
    $this->assertDatabaseHas('bank_accounts', [
        'workspace_id' => $workspace->id, 'user_id' => $user->id,
        'name' => 'Investimento', 'type' => 'investimento', 'balance' => 275,
        'currency' => 'USD', 'account_number' => '12345678', 'iban' => 'PT50001800001234567890154',
        'holder_name' => 'Pessoa Teste', 'swift' => 'TOTAPTPL', 'color' => '#112233',
        'alert_below' => 0, 'is_business' => true, 'include_in_total' => false,
    ]);
});

test('onboarding imports a PDF into the selected account only', function () {
    Storage::fake('local');
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Finanças', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->call('addBankAccount')
        ->set('bankAccounts.1.entry_mode', 'proof')
        ->set('ibanProofs.1', onboardingProofPdf('TITULAR PESSOA TESTE IBAN PT50 0018 0000 1234 5678 9015 4 BIC TOTAPTPL'))
        ->assertHasNoErrors()
        ->assertSet('bankAccounts.0.iban', '')
        ->assertSet('bankAccounts.1.iban', 'PT50001800001234567890154')
        ->assertSet('bankAccounts.1.bank_name', 'Santander')
        ->assertSet('bankAccounts.1.holder_name', 'PESSOA TESTE')
        ->assertSet('bankAccounts.1.swift', 'TOTAPTPL')
        ->assertSet('bankAccounts.1.entry_mode', 'manual')
        ->assertSee('Comprovativo lido. Confirma os dados');
});

test('onboarding reports an unreadable proof without replacing account data', function () {
    Storage::fake('local');
    $user = User::factory()->create(['onboarding_completed' => false]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('bankAccounts.0.entry_mode', 'proof')
        ->set('bankAccounts.0.iban', 'PT50001800001234567890154')
        ->set('ibanProofs.0', onboardingProofPdf('Documento sem dados bancários'))
        ->assertHasErrors(['ibanProofs.0'])
        ->assertSet('bankAccounts.0.iban', 'PT50001800001234567890154')
        ->assertSee('Não foi possível encontrar um IBAN português no documento.');
});

test('onboarding rejects oversized proofs', function () {
    Storage::fake('local');
    $user = User::factory()->create(['onboarding_completed' => false]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->call('addBankAccount')
        ->set('bankAccounts.1.entry_mode', 'proof')
        ->set('ibanProofs.1', UploadedFile::fake()->create('grande.pdf', 5121, 'application/pdf'))
        ->assertHasErrors(['ibanProofs.1'])
        ->assertSee('O ficheiro não pode ultrapassar 5 MB.');
});

test('onboarding preserves the remaining account when another is removed', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->call('addBankAccount')
        ->set('bankAccounts.1.name', 'Segunda conta')
        ->call('removeBankAccount', 0)
        ->assertSet('bankAccounts.0.name', 'Segunda conta')
        ->assertSet('ibanProofs.0', null);
});

test('onboarding requires account details or lets the user skip the step', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->call('nextStep')
        ->assertHasErrors(['bankAccounts.0.name', 'bankAccounts.0.balance'])
        ->assertSet('step', 3)
        ->call('skipStep')
        ->assertSet('step', 4);

    $this->assertDatabaseCount('bank_accounts', 0);
});

test('onboarding reports when no workspace is available to save accounts', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('bankAccounts.0.name', 'Conta')
        ->set('bankAccounts.0.balance', 100)
        ->call('nextStep')
        ->assertHasErrors(['bankAccounts'])
        ->assertSet('step', 3)
        ->assertSee('Não existe um espaço financeiro acessível para guardar as contas.');
});

test('onboarding respects bank permissions for a business workspace', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::create(['name' => 'Empresa', 'owner_id' => $owner->id, 'type' => 'business']);
    $viewer = User::factory()->create(['current_workspace_id' => $workspace->id, 'onboarding_completed' => false]);
    $workspace->users()->attach($viewer->id, ['role' => 'viewer']);

    Livewire::actingAs($viewer)
        ->test(OnboardingWizard::class)
        ->set('step', 3)
        ->set('bankAccounts.0.name', 'Conta não autorizada')
        ->set('bankAccounts.0.balance', 100)
        ->call('nextStep')
        ->assertForbidden();

    $this->assertDatabaseMissing('bank_accounts', ['workspace_id' => $workspace->id, 'name' => 'Conta não autorizada']);
});

test('onboarding links the selected bank account to the recurring income', function () {
    $user = User::factory()->create([
        'onboarding_completed' => false,
    ]);
    $workspace = Workspace::create([
        'name' => 'As Minhas Finanças',
        'owner_id' => $user->id,
    ]);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $account = BankAccount::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'name' => 'Conta principal',
        'type' => 'corrente',
        'balance' => 0,
        'currency' => 'EUR',
        'status' => 'active',
    ]);

    $this->actingAs($user);

    Livewire::test(OnboardingSalary::class)
        ->set('recDescription', 'Empresa Teste')
        ->set('recSalaryGross', 1500)
        ->set('recSalaryDestination', 'bank:'.$account->id)
        ->set('recPosition', 'Técnico')
        ->set('recIrsJovem', true)
        ->set('recAllowancesInDuodecimos', true)
        ->call('saveEmployment')
        ->assertHasNoErrors()
        ->assertDispatched('onboarding-salary-saved');

    $income = RecurringIncome::where('workspace_id', $workspace->id)->firstOrFail();
    expect($income->bank_account_id)->toBe($account->id)
        ->and($income->metadata['salary_gross'])->toBe(1500)
        ->and($income->metadata['position'])->toBe('Técnico')
        ->and($income->metadata['irs_jovem'])->toBeTrue()
        ->and($income->metadata['allowances_in_duodecimos'])->toBeTrue();

    Livewire::test(OnboardingWizard::class)
        ->set('step', 4)
        ->assertSeeLivewire(OnboardingSalary::class)
        ->dispatch('onboarding-salary-saved', salaryId: $income->id)
        ->assertSet('step', 5)
        ->call('previousStep')
        ->assertSet('savedSalaryId', $income->id)
        ->assertSee('Editar Salário');

    Livewire::test(OnboardingSalary::class, ['salaryId' => $income->id])
        ->assertSet('recPosition', 'Técnico')
        ->set('recPosition', 'Gestor')
        ->call('saveEmployment')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('recurring_incomes', 1);
    expect($income->fresh()->metadata['position'])->toBe('Gestor');
});

test('onboarding scanner fills the shared salary form without saving until confirmation', function () {
    Storage::fake('local');
    config(['services.openrouter.api_key' => 'fake-test-key']);
    Http::fake(['openrouter.ai/*' => Http::response([
        'choices' => [['message' => ['content' => json_encode([
            'company' => 'Empresa Teste', 'position' => 'Técnico', 'salary_gross' => 1500,
            'meal_allowance' => 8, 'working_days' => 20, 'allowances_in_duodecimos' => true,
        ])]]],
    ])]);
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Finanças', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingSalary::class)
        ->assertSee('Configurar Salário')
        ->assertSee('Scanner do contrato IA')
        ->set('salaryFormMode', 'scanner')
        ->assertSee('enviados ao fornecedor de IA (OpenRouter)')
        ->set('contractScanPhoto', UploadedFile::fake()->image('contrato.jpg'))
        ->call('scanEmploymentContract')
        ->assertSet('salaryFormMode', 'manual')
        ->assertSet('recDescription', 'Empresa Teste')
        ->assertSet('recPosition', 'Técnico')
        ->assertSet('recSalaryGross', 1500.0)
        ->assertSet('recAllowancesInDuodecimos', true)
        ->assertHasNoErrors();

    $this->assertDatabaseCount('recurring_incomes', 0);
    Http::assertSentCount(1);
});

test('onboarding scanner reports provider failure without replacing entered salary data', function () {
    Storage::fake('local');
    config(['services.openrouter.api_key' => 'fake-test-key']);
    Http::fake(['openrouter.ai/*' => Http::response(['error' => ['message' => 'Serviço indisponível']], 503)]);
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Finanças', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingSalary::class)
        ->set('recDescription', 'Empresa Teste')
        ->set('recSalaryGross', 1500)
        ->set('salaryFormMode', 'scanner')
        ->set('contractScanPhoto', UploadedFile::fake()->image('contrato.jpg'))
        ->call('scanEmploymentContract')
        ->assertSet('recDescription', 'Empresa Teste')
        ->assertSet('recSalaryGross', 1500)
        ->assertSet('salaryFormMode', 'scanner')
        ->assertDispatched('toast', variant: 'error');

    $this->assertDatabaseCount('recurring_incomes', 0);
});

test('onboarding salary shows understandable validation errors', function () {
    $user = User::factory()->create(['onboarding_completed' => false]);
    $workspace = Workspace::create(['name' => 'Finanças', 'owner_id' => $user->id, 'type' => 'personal']);
    $user->forceFill(['current_workspace_id' => $workspace->id])->save();
    $this->actingAs($user);

    Livewire::test(OnboardingSalary::class)
        ->call('saveEmployment')
        ->assertHasErrors(['recDescription', 'recAmount'])
        ->assertSee('O campo nome da empresa é obrigatório.')
        ->assertSee('O campo rendimento líquido deve ser pelo menos 1.');
});

test('onboarding salary enforces business permissions', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::create(['name' => 'Empresa', 'owner_id' => $owner->id, 'type' => 'business']);
    $viewer = User::factory()->create(['current_workspace_id' => $workspace->id, 'onboarding_completed' => false]);
    $workspace->users()->attach($viewer->id, ['role' => 'viewer']);

    Livewire::actingAs($viewer)
        ->test(OnboardingSalary::class)
        ->set('recDescription', 'Não autorizado')
        ->set('recSalaryGross', 1500)
        ->call('saveEmployment')
        ->assertForbidden();

    $this->assertDatabaseCount('recurring_incomes', 0);
});
