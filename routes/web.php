<?php

use App\Livewire\{
    AiInsights, Categories, Dashboard, Expenses, ManageExpense,
    CategoryHub, SubscriptionHub, GoalsHub, YearlyReport, IncomeHub,
    InvestmentsHub, NetWorthHub, FinancialCalendar, ActivityFeed,
    GlobalSearch, SubscriptionPlans, FamilyRanking, DebtHub, SupportHub,
    PersonalCalendar, CategoryFields, ManageFamily, FitnessHub,
    BudgetHub, StatementImportHub, WrappedReport, LockInHub, BancoHub,
    RetirementSimulator, SplitHub, InflationHub, AnomalyHub, ExpenseForecastHub,
    SubscriptionScannerHub, FamilyScenarioHub
};

// Procure os imports do Admin e adicione esta linha separada:
use App\Livewire\Admin\SubscriptionHub as AdminSubscriptionHub;
use App\Livewire\Admin\{AiMonitor, GamificationHub, ProductivityHub, CommunicationManager, AdminDashboard, UserManagement, SupportManager, GlobalLogs, SiteSettings, StoreHub, AnalyticsHub, RemindersMonitor};
use App\Livewire\Business\{
    BusinessDashboard, InvoicingHub, ClientHub, ProjectHub, InventoryHub,
    SupplierHub, TeamHub, TaxHub, BusinessSettings, BusinessAiHub,
    DocumentVault, TaskHub, TaskTimeline, BusinessPnlHub, BusinessMessenger,
    ProposalHub, BankAccountHub, CompanyCalendar, AbsenceHub,
    CashFlowHub, ExpenseApprovalHub, AtInvoiceHub, CollaboratorExpenseHub,
    BusinessGateway, BusinessOnboarding, ClientLogin
};
use App\Livewire\Store\{HubStore, Checkout, ShoppingCart, WishlistHub, ProductCompare};
use App\Http\Controllers\{
    ExportController, SmartwatchController, MiFitnessImportController,
    StravaController, PushSubscriptionController, ClientPortalController,
    StoreDownloadController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Route, Mail, DB, Auth, Log};

// ══════════════════════════════════════════════════════════════════
// 1. ÁREAS EXTERNAS E PÚBLICAS
// ══════════════════════════════════════════════════════════════════

Route::view('/', 'welcome')->name('home');

// Sincronização Offline
Route::post('/api/offline/sync', function (Request $request) {
    $expenses = $request->input('expenses');
    $user = auth()->user();
    foreach ($expenses as $item) {
        \App\Models\Expense::create([
            'user_id' => $user->id,
            'workspace_id' => $user->current_workspace_id,
            'amount' => $item['amount'],
            'description' => '[OFFLINE] ' . $item['description'],
            'spent_at' => $item['date'],
            'category_id' => 11
        ]);
    }
    return response()->json(['status' => 'success']);
})->middleware(['auth']);

// Portais Públicos (Fornecedores e Bancos)
Route::prefix('portal')->group(function () {
    Route::get('/fornecedor', \App\Livewire\Public\SupplierPortal::class)->name('supplier.portal');
    Route::get('/banco', \App\Livewire\Public\BankPortal::class)->name('bank.portal');
    Route::get('/fornecedor/dashboard/{token}', \App\Livewire\Public\SupplierDashboard::class)->name('supplier.dashboard');
    Route::get('/banco/dashboard/{token}', \App\Livewire\Public\BankDashboard::class)->name('bank.dashboard');
    Route::get('/login', ClientLogin::class)->name('client.login');
    Route::get('/{token}', \App\Livewire\ClientPortal::class)->name('client.portal');
});

Route::get('/carreiras', \App\Livewire\Public\CareersHub::class)->name('careers.apply');

// Webhooks
Route::get('/api/whatsapp/webhook', [\App\Http\Controllers\Api\WhatsappWebhookController::class, 'verify']);
Route::post('/api/whatsapp/webhook', [\App\Http\Controllers\Api\WhatsappWebhookController::class, 'handle']);

// ══════════════════════════════════════════════════════════════════
// 2. SISTEMA DE VERIFICAÇÃO E LOGOUT
// ══════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/verificar-conta', function () {
        if (Auth::user()->hasVerifiedEmail()) return redirect()->route('dashboard');
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

    // --- PAINÉIS E SISTEMA ---
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

    // 🔥 ADICIONADO AQUI PARA FIXAR O ERRO
    Route::get('/lembretes', \App\Livewire\Hub\RemindersHub::class)->name('hub.reminders');

    // Despesas Pessoais
    Route::get('/expenses', Expenses::class)->name('expenses');
    Route::get('/despesas-pessoais', Expenses::class)->name('expenses.index');
    Route::get('/expenses/create', ManageExpense::class)->name('expenses.create');
    Route::get('/expenses/{expense}/edit', ManageExpense::class)->name('expenses.edit');

    // Categorias e Hubs Dinâmicos
    Route::get('/categories/{category}/campos', CategoryFields::class)->name('categories.fields');
    Route::get('/hub/{slug}', CategoryHub::class)->where('slug', '[a-z0-9\-]+')->name('hub.category');

    // --- GESTÃO DE FAMÍLIA E ESPAÇOS ---
    Route::get('/familia/gestao', ManageFamily::class)->name('hub.family.manage');
    Route::get('/importar-extrato', StatementImportHub::class)->name('hub.import');

    // Troca de Espaço
    Route::get('/trocar-espaco/{id}', function ($id) {
        $user = auth()->user();
        $ws = $user->workspaces()->findOrFail($id);
        $user->update(['current_workspace_id' => $ws->id]);
        return $ws->type === 'personal' ? redirect()->route('dashboard') : redirect()->route('hub.business.dashboard');
    })->name('workspace.switch');

    // Sair do Modo Empresa
    Route::get('/sair-empresa', function () {
        $user = auth()->user();
        $personal = $user->workspaces()->where('type', 'personal')->first();
        if ($personal) $user->update(['current_workspace_id' => $personal->id]);
        return redirect()->route('dashboard');
    })->name('hub.business.exit');

    // --- MARKETPLACE & SOCIAL (BASE) ---
    Route::get('/loja', HubStore::class)->name('hub.store');
    Route::get('/loja/carrinho', ShoppingCart::class)->name('store.cart');
    Route::get('/loja/checkout', Checkout::class)->name('store.checkout');
    Route::get('/loja/produto/{product}', \App\Livewire\Store\ProductShow::class)->name('store.product.show');
    Route::get('/loja/favoritos', WishlistHub::class)->name('store.wishlist');
    Route::get('/loja/comparar', ProductCompare::class)->name('store.compare');

    Route::get('/social', \App\Livewire\Social\SocialHub::class)->name('social.hub');
    Route::get('/social/u/{username}', \App\Livewire\Social\SocialProfile::class)->name('social.profile');
});

    // ══════════════════════════════════════════════════════════════════
    // 4. MÓDULO PREMIUM (REQUER PLANO PLUS OU SUPERIOR)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['plan:premium'])->group(function () {

        // --- INTELIGÊNCIA ARTIFICIAL ---
        Route::get('/ai', AiInsights::class)->name('ai');
        Route::get('/ia-pilot', AiInsights::class)->name('insights');

        // --- FERRAMENTAS AVANÇADAS ---
        Route::get('/lock-in', LockInHub::class)->name('hub.lockin');
        Route::get('/fitness', FitnessHub::class)->name('hub.fitness');
        Route::get('/inventario', \App\Livewire\Store\UserInventory::class)->name('hub.inventory');

        // --- SIMULADORES E ANÁLISES ---
        Route::get('/reforma', RetirementSimulator::class)->name('hub.retirement');
        Route::get('/inflacao', InflationHub::class)->name('hub.inflation');
        Route::get('/anomalias', AnomalyHub::class)->name('hub.anomalies');
        Route::get('/previsao-despesas', ExpenseForecastHub::class)->name('hub.expense-forecast');
        Route::get('/familia/simulacao', FamilyScenarioHub::class)->name('hub.family.scenario');

        // --- GESTÃO DE ASSINATURAS PRO ---
        Route::get('/assinaturas', SubscriptionHub::class)->name('hub.subscriptions');
        Route::get('/assinaturas/scanner', SubscriptionScannerHub::class)->name('hub.subscriptions.scanner');

        // --- UTILITÁRIOS PREMIUM ---
        Route::get('/dividir', SplitHub::class)->name('hub.split');
        Route::get('/categories', Categories::class)->name('categories'); // Gerir categorias
        Route::get('/investimentos', InvestmentsHub::class)->name('hub.investments');
        Route::get('/patrimonio', NetWorthHub::class)->name('hub.networth');
        Route::get('/relatorios', YearlyReport::class)->name('hub.reports');

        // Integração Fitness API
        Route::get('/fitness/strava/connect', [StravaController::class, 'connect'])->name('strava.connect');
        Route::get('/fitness/strava/callback', [StravaController::class, 'callback'])->name('strava.callback');
        Route::get('/fitness/strava/disconnect', [StravaController::class, 'disconnect'])->name('strava.disconnect');
    });

   // ══════════════════════════════════════════════════════════════════
    // 5. MÓDULO EMPRESARIAL (REQUER PLANO BUSINESS / DIAMOND)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['plan:business'])->group(function () {

        Route::get('/empresa/acesso', BusinessGateway::class)->name('hub.business.gateway');
        Route::get('/empresa/onboarding', BusinessOnboarding::class)->name('hub.business.onboarding');

        Route::prefix('empresa')->group(function () {

            // --- DASHBOARD DINÂMICO (CEO vs COLABORADOR) ---
            Route::get('/dashboard', function() {
                $user = auth()->user();
                // 1. Verificamos se há uma sessão de "Vista de Colaborador" ativa
                if (session()->has('viewing_as_collaborator_id')) {
                    return app()->make(\App\Livewire\Business\CollaboratorDashboard::class)();
                }
                // 2. Se for um colaborador REAL (não é dono nem admin do sistema)
                if (!($user->isOwner() || $user->isAdminRole())) {
                    return app()->make(\App\Livewire\Business\CollaboratorDashboard::class)();
                }
                // 3. Caso contrário, é o CEO na sua vista normal
                return app()->make(\App\Livewire\Business\BusinessDashboard::class)();
            })->name('hub.business.dashboard');

            // --- OPERAÇÕES E FINANCEIRO ---
            Route::get('/ia-estrategista', BusinessAiHub::class)->name('hub.business.ai');
            Route::get('/arquivo', DocumentVault::class)->name('hub.business.vault');
            Route::get('/despesas', \App\Livewire\Business\CompanyExpenses::class)->name('company-expenses');
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
            Route::get('/recrutamento', \App\Livewire\Business\RecruitmentHub::class)->name('hub.business.recruitment');

            // --- ESPAÇO DO COLABORADOR ---
            Route::get('/meu-perfil', \App\Livewire\Business\MyCompanyProfile::class)->name('hub.business.my-profile');
            Route::get('/minhas-despesas', CollaboratorExpenseHub::class)->name('hub.business.my-expenses');
            Route::get('/aprovacoes', ExpenseApprovalHub::class)->name('hub.business.expense-approvals');

            // --- SUPORTE E CONFIGURAÇÕES ---
            Route::get('/suporte-empresa', SupportHub::class)->name('hub.business.support');
            Route::get('/perfil', BusinessSettings::class)->name('hub.business.settings');
        });

        // Troca Rápida de Contexto (Apenas para Business)
        Route::get('/trocar-contexto/{id}', function ($id) {
            $user = auth()->user();
            $ws = $user->workspaces()->findOrFail($id);
            $user->update(['current_workspace_id' => $ws->id]);
            return in_array($ws->type, ['business', 'company'])
                ? redirect()->route('hub.business.dashboard')
                : redirect()->route('dashboard');
        })->name('workspace.switch.fast');
    });

    // --- ROTAS DE SAÍDA DE MODO (SESSÃO) ---
    Route::get('/empresa/sair-modo-colaborador', function () {
        if (!session()->has('impersonator_id')) return redirect()->route('dashboard');
        $ceo = \App\Models\User::find(session()->pull('impersonator_id'));
        if ($ceo) Auth::login($ceo);
        return redirect()->route('hub.business.dashboard');
    })->name('hub.business.leave-impersonation');

    Route::get('/empresa/sair-vista-colaborador', function () {
        session()->forget('viewing_as_collaborator_id');
        return redirect()->route('hub.business.dashboard');
    })->name('hub.business.stop-viewing-collaborator');


    // ══════════════════════════════════════════════════════════════════
    // 6. ÁREA DE ADMINISTRAÇÃO (APENAS EQUIPA INTERNA / SUPER-ADMINS)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['admin'])->prefix('admin')->group(function () {

        // Dashboards de Monitorização
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/estatisticas', AnalyticsHub::class)->name('admin.stats');
        Route::get('/ai-monitor', AiMonitor::class)->name('admin.ai');
        Route::get('/produtividade', ProductivityHub::class)->name('admin.productivity');
        Route::get('/lembretes', RemindersMonitor::class)->name('admin.reminders');

        // Gestão de Ecossistema
        Route::get('/utilizadores', UserManagement::class)->name('admin.users');
        Route::get('/faturacao', AdminSubscriptionHub::class)->name('admin.billing');
        Route::get('/suporte-global', SupportManager::class)->name('admin.support');
        Route::get('/comunicacao', CommunicationManager::class)->name('admin.communication');
        Route::get('/gamificacao', GamificationHub::class)->name('admin.gamification');
        Route::get('/loja', StoreHub::class)->name('admin.store');

        // Segurança e Configuração
        Route::get('/logs', GlobalLogs::class)->name('admin.logs');
        Route::get('/configuracoes', SiteSettings::class)->name('admin.settings');

        // Personificação
        Route::get('/impersonate/{user}', function (\App\Models\User $user) {
            if (!auth()->user()->isAdminRole()) abort(403);
            session()->put('impersonator_id', auth()->id());
            Auth::login($user);
            return redirect()->route('dashboard');
        })->name('admin.impersonate');
    });

    // Parar Personificação (Voltar ao Admin)
    Route::get('/stop-impersonating', function () {
        if (!session()->has('impersonator_id')) return redirect('/');
        $adminId = session()->pull('impersonator_id');
        $admin = \App\Models\User::find($adminId);
        if ($admin) {
            Auth::login($admin);
            return $admin->isAdminRole() ? redirect()->route('admin.users') : redirect()->route('dashboard');
        }
        return redirect()->route('dashboard');
    })->name('admin.stop-impersonating');

    // ══════════════════════════════════════════════════════════════════
    // 7. EXPORTAÇÕES E APIS DE INTEGRAÇÃO
    // ══════════════════════════════════════════════════════════════════

    // Exportação de Documentos
    Route::get('/export/dashboard-pdf', [ExportController::class, 'dashboardPdf'])->name('export.dashboard.pdf');
    Route::get('/export/expenses', [ExportController::class, 'expensesPdf'])->name('export.expenses');
    Route::get('/export/empresa', [ExportController::class, 'businessExport'])->name('export.business');

    // Downloads da Loja
    Route::get('/loja/download/{purchase}/request', [StoreDownloadController::class, 'requestToken'])->name('store.download.request');
    Route::get('/loja/download/{purchase}', [StoreDownloadController::class, 'download'])->name('store.download');

    // Integrações de Saúde / Smartwatch / Offline
    Route::post('/api/smartwatch-info', [SmartwatchController::class, 'info']);
    Route::post('/api/mifitness/import', [MiFitnessImportController::class, 'import'])->name('mifitness.import');
    Route::post('/api/offline/expenses/sync', [\App\Http\Controllers\Api\OfflineExpenseController::class, 'sync'])->name('api.offline.sync');
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'update']);

// 🔥 ESTE FECHA O GRUPO AUTH+VERIFIED (Antiga Linha 353)

// ══════════════════════════════════════════════════════════════════
// 8. SERVIÇOS DE E-MAIL E CONFIGURAÇÕES FINAIS (FORA DO AUTH)
// ══════════════════════════════════════════════════════════════════

// Reenvio de Código de Verificação
Route::post('/email/verification-notification', function (Request $request) {
    $user = $request->user();
    $newCode = rand(100000, 999999);
    $user->update(['verification_code' => $newCode]);
    try {
        Mail::to($user->email)->send(new \App\Mail\VerifyAccountMail($newCode));
        Log::info("Reenvio de e-mail disparado para {$user->email}");
        return back()->with('status', 'verification-link-sent');
    } catch (\Exception $e) {
        Log::error("Erro no reenvio de e-mail: " . $e->getMessage());
        return back()->withErrors(['code' => 'Erro ao conectar ao servidor de e-mail.']);
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
