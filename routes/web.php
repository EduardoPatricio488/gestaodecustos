<?php

use App\Http\Controllers\Api\OfflineExpenseController;
use App\Http\Controllers\Api\WhatsappWebhookController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MiFitnessImportController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SmartwatchController;
use App\Http\Controllers\StoreDownloadController;
use App\Http\Controllers\StravaController;
use App\Livewire\ActivityFeed;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AiMonitor;
use App\Livewire\Admin\AnalyticsHub;
use App\Livewire\Admin\CommunicationManager;
use App\Livewire\Admin\GamificationHub;
use App\Livewire\Admin\GlobalLogs;
use App\Livewire\Admin\ProductivityHub;
use App\Livewire\Admin\RemindersMonitor;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\StoreHub;
use App\Livewire\Admin\SubscriptionHub as AdminSubscriptionHub;
use App\Livewire\Admin\SupportManager;
use App\Livewire\Admin\UserManagement;
use App\Livewire\AiInsights;
use App\Livewire\AnomalyHub;
use App\Livewire\BancoHub;
use App\Livewire\BudgetHub;
use App\Livewire\Business\AbsenceHub;
use App\Livewire\Business\AtInvoiceHub;
use App\Livewire\Business\BankAccountHub;
use App\Livewire\Business\BusinessAiHub;
use App\Livewire\Business\BusinessDashboard;
use App\Livewire\Business\BusinessGateway;
use App\Livewire\Business\BusinessMessenger;
use App\Livewire\Business\BusinessOnboarding;
use App\Livewire\Business\BusinessPnlHub;
use App\Livewire\Business\BusinessSettings;
use App\Livewire\Business\CashFlowHub;
use App\Livewire\Business\ClientHub;
use App\Livewire\Business\ClientLogin;
use App\Livewire\Business\CollaboratorDashboard;
use App\Livewire\Business\CollaboratorExpenseHub;
use App\Livewire\Business\CompanyCalendar;
use App\Livewire\Business\CompanyExpenses;
use App\Livewire\Business\DocumentVault;
use App\Livewire\Business\ExpenseApprovalHub;
use App\Livewire\Business\InventoryHub;
use App\Livewire\Business\InvoicingHub;
use App\Livewire\Business\MyCompanyProfile;
use App\Livewire\Business\ProjectCostsHub;
use App\Livewire\Business\ProjectHub;
use App\Livewire\Business\ProposalHub;
use App\Livewire\Business\RecruitmentHub;
use App\Livewire\Business\SupplierHub;
use App\Livewire\Business\TaskHub;
use App\Livewire\Business\TaskTimeline;
use App\Livewire\Business\TaxHub;
use App\Livewire\Business\TeamHub;
use App\Livewire\Categories;
use App\Livewire\CategoryFields;
use App\Livewire\CategoryHub;
use App\Livewire\ClientPortal;
use App\Livewire\Dashboard;
use App\Livewire\DebtHub;
use App\Livewire\ExpenseForecastHub;
use App\Livewire\Expenses;
use App\Livewire\FamilyRanking;
use App\Livewire\FamilyScenarioHub;
use App\Livewire\FitnessHub;
use App\Livewire\GoalsHub;
use App\Livewire\Hub\RemindersHub;
use App\Livewire\IncomeHub;
use App\Livewire\InflationHub;
use App\Livewire\InvestmentsHub;
use App\Livewire\LockInHub;
use App\Livewire\ManageExpense;
use App\Livewire\ManageFamily;
use App\Livewire\NetWorthHub;
use App\Livewire\PersonalCalendar;
use App\Livewire\Public\BankDashboard;
use App\Livewire\Public\BankPortal;
use App\Livewire\Public\CareersHub;
use App\Livewire\Public\ContactPage;
use App\Livewire\Public\SupplierDashboard;
use App\Livewire\Public\SupplierPortal;
use App\Livewire\RetirementSimulator;
use App\Livewire\Social\SocialHub;
use App\Livewire\Social\SocialProfile;
use App\Livewire\SplitHub;
use App\Livewire\StatementImportHub;
use App\Livewire\Store\Checkout;
use App\Livewire\Store\HubStore;
use App\Livewire\Store\ProductCompare;
use App\Livewire\Store\ProductShow;
use App\Livewire\Store\ShoppingCart;
use App\Livewire\Store\UserInventory;
use App\Livewire\Store\WishlistHub;
use App\Livewire\SubscriptionHub;
use App\Livewire\SubscriptionPlans;
use App\Livewire\SubscriptionScannerHub;
use App\Livewire\SupportHub;
use App\Livewire\WrappedReport;
use App\Livewire\YearlyReport;
use App\Mail\VerifyAccountMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════
// 1. ÁREAS EXTERNAS E PÚBLICAS (Acessíveis por Visitantes)
// ══════════════════════════════════════════════════════════════════

Route::view('/', 'welcome')->name('home');
Route::view('/termos', 'pages.legal.terms')->name('legal.terms');
Route::view('/privacidade', 'pages.legal.privacy')->name('legal.privacy');
Route::get('/contacto', ContactPage::class)->name('public.contact');

// Sincronização Offline (Protegido por auth básico)
Route::post('/api/offline/sync', function (Request $request) {
    $expenses = $request->input('expenses');
    $user = auth()->user();
    foreach ($expenses as $item) {
        Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $user->current_workspace_id,
            'amount' => $item['amount'],
            'description' => '[OFFLINE] '.$item['description'],
            'spent_at' => $item['date'],
            'category_id' => 11,
        ]);
    }

    return response()->json(['status' => 'success']);
})->middleware(['auth']);

// Portais Públicos de Negócio
Route::prefix('portal')->group(function () {
    Route::get('/fornecedor', SupplierPortal::class)->name('supplier.portal');
    Route::get('/banco', BankPortal::class)->name('bank.portal');
    Route::get('/fornecedor/dashboard/{token}', SupplierDashboard::class)->name('supplier.dashboard');
    Route::get('/banco/dashboard/{token}', BankDashboard::class)->name('bank.dashboard');
    Route::get('/login', ClientLogin::class)->name('client.login');
    Route::get('/{token}', ClientPortal::class)->name('client.portal');
});

Route::get('/carreiras', CareersHub::class)->name('careers.apply');

// Webhooks de Integração
Route::get('/api/whatsapp/webhook', [WhatsappWebhookController::class, 'verify']);
Route::post('/api/whatsapp/webhook', [WhatsappWebhookController::class, 'handle']);

// ══════════════════════════════════════════════════════════════════
// 2. SISTEMA DE VERIFICAÇÃO E LOGOUT
// ══════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/verificar-conta', function () {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    })->name('verificar.conta');

    Route::post('/verificar-codigo', function (Request $request) {
        $request->validate(['code' => 'required|size:6']);
        $user = Auth::user();
        if ($request->code == $user->verification_code) {
            $user->markEmailAsVerified();
            $user->update(['verification_code' => null]);

            return redirect()->route('dashboard')->with('ok', 'Conta ativada!');
        }

        return back()->withErrors(['code' => 'Código incorreto.']);
    })->name('verification.verify-code');

    Route::post('/logout', function () {
        Auth::logout();

        return redirect('/');
    })->name('logout');
});

// ══════════════════════════════════════════════════════════════════
// 3. GRUPO PROTEGIDO (ÁREA LOGADA - ACESSO BÁSICO / FREE)
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARDS E SISTEMA ---
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/atividades', ActivityFeed::class)->name('activity-log');
    Route::get('/planos', SubscriptionPlans::class)->name('hub.pricing');
    Route::get('/suporte', SupportHub::class)->name('support.hub');
    Route::view('/profile', 'profile')->name('profile.edit');
    Route::get('/ranking', FamilyRanking::class)->name('hub.ranking');

    // --- GESTÃO FINANCEIRA BASE ---
    Route::get('/receitas', IncomeHub::class)->name('hub.incomes');
    Route::get('/dividas', DebtHub::class)->name('hub.debts');
    Route::get('/objetivos', GoalsHub::class)->name('hub.goals');
    Route::get('/orcamento', BudgetHub::class)->name('hub.budget');
    Route::get('/wrapped', WrappedReport::class)->name('hub.wrapped');
    Route::get('/banco', BancoHub::class)->name('hub.banco');
    Route::get('/calendario', PersonalCalendar::class)->name('hub.calendar');
    Route::get('/lembretes', RemindersHub::class)->name('hub.reminders');

    // Despesas Pessoais
    Route::get('/expenses', Expenses::class)->name('expenses');
    Route::get('/despesas-pessoais', Expenses::class)->name('expenses.index');
    Route::get('/expenses/create', ManageExpense::class)->name('expenses.create');
    Route::get('/expenses/{expense}/edit', ManageExpense::class)->name('expenses.edit');

    // Categorias e Hubs Dinâmicos
    Route::get('/categorias', Categories::class)->name('categories');
    Route::get('/categories/{category}/campos', CategoryFields::class)->name('categories.fields');
    Route::get('/hub/{slug}', CategoryHub::class)->where('slug', '[a-z0-9\-]+')->name('hub.category');

    // --- GESTÃO DE FAMÍLIA E ESPAÇOS ---
    Route::get('/familia/gestao', ManageFamily::class)->name('hub.family.manage');
    Route::get('/importar-extrato', StatementImportHub::class)->name('hub.import');

    // Troca de Workspace / Espaço
    Route::get('/trocar-espaco/{id}', function ($id) {
        $user = auth()->user();
        $ws = $user->workspaces()->findOrFail($id);
        $user->update(['current_workspace_id' => $ws->id]);

        return $ws->type === 'personal'
            ? redirect()->route('dashboard')
            : redirect()->route('hub.business.dashboard');
    })->name('workspace.switch');

    // Sair do Modo Empresa
    Route::get('/sair-empresa', function () {
        $user = auth()->user();
        $personal = $user->workspaces()->where('type', 'personal')->first();
        if ($personal) {
            $user->update(['current_workspace_id' => $personal->id]);
        }

        return redirect()->route('dashboard');
    })->name('hub.business.exit');

    // --- MARKETPLACE (LOJA) ---
    Route::get('/loja', HubStore::class)->name('hub.store');
    Route::get('/loja/carrinho', ShoppingCart::class)->name('store.cart');
    Route::get('/loja/checkout', Checkout::class)->name('store.checkout');
    Route::get('/loja/produto/{product}', ProductShow::class)->name('store.product.show');
    Route::get('/loja/favoritos', WishlistHub::class)->name('store.wishlist');
    Route::get('/loja/comparar', ProductCompare::class)->name('store.compare');

    // --- SOCIAL (FINANCE CONNECT) ---
    Route::get('/social', SocialHub::class)->name('social.hub');
    Route::get('/social/u/{username}', SocialProfile::class)->name('social.profile');
});

// ══════════════════════════════════════════════════════════════════
// 4. MÓDULO PREMIUM (REQUER PLANO PLUS OU SUPERIOR)
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified', 'plan:premium'])->group(function () {

    // --- INTELIGÊNCIA ARTIFICIAL ---
    Route::get('/ai', AiInsights::class)->name('ai');
    Route::get('/ia-pilot', AiInsights::class)->name('insights');

    // --- FERRAMENTAS AVANÇADAS ---
    Route::get('/lock-in', LockInHub::class)->name('hub.lockin');
    Route::get('/fitness', FitnessHub::class)->name('hub.fitness');
    Route::get('/inventario', UserInventory::class)->name('hub.inventory');

    // --- SIMULADORES E ANÁLISES ---
    Route::get('/reforma', RetirementSimulator::class)->name('hub.retirement');
    Route::get('/inflacao', InflationHub::class)->name('hub.inflation');
    Route::get('/anomalias', AnomalyHub::class)->name('hub.anomalies');
    Route::get('/previsao-despesas', ExpenseForecastHub::class)->name('hub.expense-forecast');
    Route::get('/familia/simulacao', FamilyScenarioHub::class)->name('hub.family.scenario');

    // --- GESTÃO PRO ---
    Route::get('/assinaturas', SubscriptionHub::class)->name('hub.subscriptions');
    Route::get('/assinaturas/scanner', SubscriptionScannerHub::class)->name('hub.subscriptions.scanner');
    Route::get('/dividir', SplitHub::class)->name('hub.split');
    Route::get('/investimentos', InvestmentsHub::class)->name('hub.investments');
    Route::get('/patrimonio', NetWorthHub::class)->name('hub.networth');
    Route::get('/relatorios', YearlyReport::class)->name('hub.reports');

    // Integrações Fitness API
    Route::get('/fitness/strava/connect', [StravaController::class, 'connect'])->name('strava.connect');
    Route::get('/fitness/strava/callback', [StravaController::class, 'callback'])->name('strava.callback');
    Route::get('/fitness/strava/disconnect', [StravaController::class, 'disconnect'])->name('strava.disconnect');
});

// ══════════════════════════════════════════════════════════════════
// 5. MÓDULO EMPRESARIAL (REQUER PLANO BUSINESS / DIAMOND)
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified', 'plan:business'])->group(function () {

    Route::get('/empresa/acesso', BusinessGateway::class)->name('hub.business.gateway');
    Route::get('/empresa/onboarding', BusinessOnboarding::class)->name('hub.business.onboarding');

    Route::prefix('empresa')->group(function () {

        // --- DASHBOARD DINÂMICO (CEO vs COLABORADOR) ---
        Route::get('/dashboard', function () {
            $user = auth()->user();
            if (session()->has('viewing_as_collaborator_id')) {
                return app()->make(CollaboratorDashboard::class)();
            }
            if (! ($user->isOwner() || $user->isAdminRole())) {
                return app()->make(CollaboratorDashboard::class)();
            }

            return app()->make(BusinessDashboard::class)();
        })->name('hub.business.dashboard');

        // --- OPERAÇÕES E FINANCEIRO ---
        Route::get('/analise-custos', ProjectCostsHub::class)->name('hub.business.costs');
        Route::get('/ia-estrategista', BusinessAiHub::class)->name('hub.business.ai');
        Route::get('/arquivo', DocumentVault::class)->name('hub.business.vault');
        Route::get('/despesas', CompanyExpenses::class)->name('company-expenses');
        Route::get('/faturacao', InvoicingHub::class)->name('hub.business.invoices');
        Route::get('/propostas', ProposalHub::class)->name('hub.business.proposals');
        Route::get('/clientes', ClientHub::class)->name('hub.business.clients');
        Route::get('/projetos', ProjectHub::class)->name('hub.business.projects');
        Route::get('/stock', InventoryHub::class)->name('hub.business.inventory');
        Route::get('/fornecedores', SupplierHub::class)->name('hub.business.suppliers');
        Route::get('/contas', BankAccountHub::class)->name('hub.business.accounts');
        Route::get('/fluxo-caixa', CashFlowHub::class)->name('hub.business.cashflow');
        Route::get('/resultados', BusinessPnlHub::class)->name('hub.business.pnl');
        Route::get('/impostos', TaxHub::class)->name('hub.business.taxes');
        Route::get('/e-fatura', AtInvoiceHub::class)->name('hub.business.at-invoices');

        // --- GESTÃO DE EQUIPA E TAREFAS ---
        Route::get('/equipa', TeamHub::class)->name('hub.business.team');
        Route::get('/tarefas', TaskHub::class)->name('hub.business.tasks');
        Route::get('/timeline', TaskTimeline::class)->name('hub.business.timeline');
        Route::get('/messenger', BusinessMessenger::class)->name('hub.business.messenger');
        Route::get('/calendario-empresa', CompanyCalendar::class)->name('hub.business.calendar');
        Route::get('/ferias', AbsenceHub::class)->name('hub.business.absences');
        Route::get('/recrutamento', RecruitmentHub::class)->name('hub.business.recruitment');

        // --- ESPAÇO DO COLABORADOR ---
        Route::get('/meu-perfil', MyCompanyProfile::class)->name('hub.business.my-profile');
        Route::get('/minhas-despesas', CollaboratorExpenseHub::class)->name('hub.business.my-expenses');
        Route::get('/aprovacoes', ExpenseApprovalHub::class)->name('hub.business.expense-approvals');

        // --- SUPORTE E CONFIGURAÇÕES ---
        Route::get('/suporte-empresa', SupportHub::class)->name('hub.business.support');
        Route::get('/perfil', BusinessSettings::class)->name('hub.business.settings');
    });

    // Troca Rápida de Contexto
    Route::get('/trocar-contexto/{id}', function ($id) {
        $user = auth()->user();
        $ws = $user->workspaces()->findOrFail($id);
        $user->update(['current_workspace_id' => $ws->id]);

        return in_array($ws->type, ['business', 'company'])
            ? redirect()->route('hub.business.dashboard')
            : redirect()->route('dashboard');
    })->name('workspace.switch.fast');
});

// --- ROTAS DE SESSÃO ESPECIAL ---
Route::get('/empresa/sair-modo-colaborador', function () {
    if (! session()->has('impersonator_id')) {
        return redirect()->route('dashboard');
    }
    $ceo = User::find(session()->pull('impersonator_id'));
    if ($ceo) {
        Auth::login($ceo);
    }

    return redirect()->route('hub.business.dashboard');
})->name('hub.business.leave-impersonation');

Route::get('/empresa/sair-vista-colaborador', function () {
    session()->forget('viewing_as_collaborator_id');

    return redirect()->route('hub.business.dashboard');
})->name('hub.business.stop-viewing-collaborator');

// ══════════════════════════════════════════════════════════════════
// 6. ÁREA DE ADMINISTRAÇÃO (APENAS EQUIPA INTERNA)
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Dashboards e Monitorização
    Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/estatisticas', AnalyticsHub::class)->name('admin.stats');
    Route::get('/ai-monitor', AiMonitor::class)->name('admin.ai');
    Route::get('/produtividade', ProductivityHub::class)->name('admin.productivity');
    Route::get('/lembretes', RemindersMonitor::class)->name('admin.reminders');

    // Gestão de Utilizadores e Faturação
    Route::get('/utilizadores', UserManagement::class)->name('admin.users');
    Route::get('/faturacao', AdminSubscriptionHub::class)->name('admin.billing');
    Route::get('/suporte-global', SupportManager::class)->name('admin.support');
    Route::get('/comunicacao', CommunicationManager::class)->name('admin.communication');
    Route::get('/gamificacao', GamificationHub::class)->name('admin.gamification');
    Route::get('/loja', StoreHub::class)->name('admin.store');

    // Configurações e Logs
    Route::get('/logs', GlobalLogs::class)->name('admin.logs');
    Route::get('/configuracoes', SiteSettings::class)->name('admin.settings');

    // Personificação (CEO entra na conta do Colaborador)
    Route::get('/impersonate/{user}', function (User $user) {
        if (! auth()->user()->isAdminRole()) {
            abort(403);
        }
        session()->put('impersonator_id', auth()->id());
        Auth::login($user);

        return redirect()->route('dashboard');
    })->name('admin.impersonate');
});

// Sair da Personificação
Route::get('/stop-impersonating', function () {
    if (! session()->has('impersonator_id')) {
        return redirect('/');
    }
    $admin = User::find(session()->pull('impersonator_id'));
    if ($admin) {
        Auth::login($admin);

        return $admin->isAdminRole() ? redirect()->route('admin.users') : redirect()->route('dashboard');
    }

    return redirect()->route('dashboard');
})->name('admin.stop-impersonating');
// ══════════════════════════════════════════════════════════════════
// 7. EXPORTAÇÕES E APIS EXTERNAS
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {
    Route::get('/export/dashboard-pdf', [ExportController::class, 'dashboardPdf'])->name('export.dashboard.pdf');
    Route::get('/export/expenses', [ExportController::class, 'expensesPdf'])->name('export.expenses');
    Route::get('/export/empresa', [ExportController::class, 'businessExport'])->name('export.business');
    Route::get('/loja/download/{purchase}/request', [StoreDownloadController::class, 'requestToken'])->name('store.download.request');
    Route::get('/loja/download/{purchase}', [StoreDownloadController::class, 'download'])->name('store.download');

    Route::post('/api/smartwatch-info', [SmartwatchController::class, 'info']);
    Route::post('/api/mifitness/import', [MiFitnessImportController::class, 'import'])->name('mifitness.import');
    Route::post('/api/offline/expenses/sync', [OfflineExpenseController::class, 'sync'])->name('api.offline.sync');
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'update']);
});

// ══════════════════════════════════════════════════════════════════
// 8. SERVIÇOS DE E-MAIL E CONFIGURAÇÕES FINAIS
// ══════════════════════════════════════════════════════════════════
Route::post('/email/verification-notification', function (Request $request) {
    $user = $request->user();
    $newCode = rand(100000, 999999);
    $user->update(['verification_code' => $newCode]);
    try {
        Mail::to($user->email)->send(new VerifyAccountMail($newCode));

        return back()->with('status', 'verification-link-sent');
    } catch (Exception $e) {
        return back()->withErrors(['code' => 'Erro de conexão ao servidor de e-mail.']);
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
