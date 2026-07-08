<?php

use App\Livewire\{
    AiInsights, Categories, Dashboard, Expenses, ManageExpense,
    CategoryHub, SubscriptionHub, GoalsHub, YearlyReport, IncomeHub,
    InvestmentsHub, NetWorthHub, FinancialCalendar, ActivityFeed,
    GlobalSearch, SubscriptionPlans, FamilyRanking, DebtHub, SupportHub,
    PersonalCalendar, CategoryFields, ManageFamily, FitnessHub,
    BudgetHub, StatementImportHub, WrappedReport, LockInHub, BancoHub
};
use App\Livewire\Admin\AiMonitor;
use App\Livewire\Business\CollaboratorExpenseHub;
use App\Livewire\Admin\GamificationHub;

use App\Livewire\Admin\ProductivityHub;
use App\Livewire\Store\{HubStore, Checkout, ShoppingCart, WishlistHub, ProductCompare};
use App\Http\Controllers\StoreDownloadController;
use App\Livewire\Admin\CommunicationManager;
use App\Http\Controllers\{
    ExportController, SmartwatchController, MiFitnessImportController,
    StravaController, PushSubscriptionController,
    ClientPortalController
};
use App\Livewire\Admin\{AdminDashboard, UserManagement, SupportManager, GlobalLogs, SiteSettings, StoreHub};
use App\Livewire\Business\{
    BusinessDashboard, InvoicingHub, ClientHub, ProjectHub, InventoryHub,
    SupplierHub, TeamHub, TaxHub, BusinessSettings, BusinessAiHub,
    DocumentVault, TaskHub, TaskTimeline, BusinessPnlHub, BusinessMessenger,
    ProposalHub, BankAccountHub, CompanyCalendar, AbsenceHub,
    CashFlowHub, ExpenseApprovalHub, AtInvoiceHub
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Route, Mail, DB, Auth};
use App\Mail\MonthlyReportMail;
use App\Livewire\ClientPortal;
use App\Livewire\Business\ClientLogin;
use App\Livewire\Admin\AnalyticsHub;
use App\Livewire\Business\BusinessGateway;
use App\Livewire\Admin\RemindersMonitor;
use App\Livewire\Admin\SubscriptionHub as AdminSubscriptionHub;
// --- ÁREAS EXTERNAS (ACESSOS PARA NÃO-UTILIZADORES) ---

Route::prefix('portal')->group(function () {
    // Portal de Fornecedores (Login/Envio de faturas)
    Route::get('/fornecedor', \App\Livewire\Public\SupplierPortal::class)->name('supplier.portal');

    // Portal Bancário (Acesso para auditoria/verificação)
    Route::get('/banco', \App\Livewire\Public\BankPortal::class)->name('bank.portal');
});
Route::get('/portal/fornecedor/dashboard/{token}', \App\Livewire\Public\SupplierDashboard::class)->name('supplier.dashboard');



Route::get('/portal/banco/dashboard/{token}', \App\Livewire\Public\BankDashboard::class)->name('bank.dashboard');




// Área de Recrutamento (Página de candidaturas)
Route::get('/carreiras', \App\Livewire\Public\CareersHub::class)->name('careers.apply');
Route::get('/portal/login', \App\Livewire\Business\ClientLogin::class)->name('client.login');
Route::get('/portal/{token}', \App\Livewire\ClientPortal::class)->name('client.portal');
// --- 1. PÁGINA INICIAL ---
Route::view('/', 'welcome')->name('home');

// Ponto de entrada (Público)
Route::get('/carreiras', \App\Livewire\Public\CareersHub::class)->name('careers.apply');

// Portal de Empresas (Apenas para logados)
Route::middleware(['auth'])->group(function () {
    Route::get('/carreiras/portal', \App\Livewire\Public\CareersPortal::class)->name('careers.portal');
});
Route::get('/api/whatsapp/webhook', [\App\Http\Controllers\Api\WhatsappWebhookController::class, 'verify']);
Route::post('/api/whatsapp/webhook', [\App\Http\Controllers\Api\WhatsappWebhookController::class, 'handle']);

// --- 2. SISTEMA DE VERIFICAÇÃO (APENAS AUTH) ---
Route::middleware('auth')->group(function () {

    Route::get('/verificar-conta', function () {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/verificar-codigo', function (Request $request) {
        $request->validate(['code' => 'required|size:6']);
        $user = Auth::user();

        if ($request->code == $user->verification_code) {
            $user->markEmailAsVerified();
            $user->update(['verification_code' => null]);
            return redirect()->route('dashboard')->with('ok', 'Conta ativada com sucesso!');
        }
        return back()->withErrors(['code' => 'O código de segurança está incorreto.']);
    })->name('verification.verify-code');

    Route::post('/verification-notification', function (Request $request) {
        $user = $request->user();
        $newCode = rand(100000, 999999);
        $user->update(['verification_code' => $newCode]);
        Mail::to($user->email)->send(new \App\Mail\VerifyAccountMail($newCode));
        return back()->with('status', 'Novo código enviado!');
    })->name('verification.send');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});

// --- 3. GRUPO PROTEGIDO (AUTH + VERIFIED) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // --- PAINÉIS CENTRAIS ---
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Sair do modo empresa: troca para o workspace pessoal e redireciona para o dashboard
    Route::get('/sair-empresa', function () {
        $user = auth()->user();
        $personal = $user->workspaces()->where('type', 'personal')->first();
        if ($personal) {
            $user->update(['current_workspace_id' => $personal->id]);
        }
        return redirect()->route('dashboard');
    })->name('hub.business.exit');
    Route::get('/ai', AiInsights::class)->name('ai');
    Route::get('/insights', AiInsights::class)->name('insights');
    Route::get('/empresa/acesso', BusinessGateway::class)->name('hub.business.gateway');
    Route::get('/atividades', ActivityFeed::class)->name('activity-log');
    Route::get('/planos', SubscriptionPlans::class)->name('hub.pricing');
// --- MÓDULO EMPRESARIAL (MODO BUSINESS) ---
    Route::prefix('empresa')->group(function () {

        // 1. DASHBOARD DINÂMICO (Decide entre CEO ou Colaborador)
        Route::get('/dashboard', function() {
            $user = auth()->user();

            // Se for o dono ou admin, vê a dashboard de gestão (CEO)
            if ($user->isOwner() || $user->isAdminRole()) {
                return app()->make(\App\Livewire\Business\BusinessDashboard::class)();
            }

            // Caso contrário, vê o terminal operacional (Colaborador)
            return app()->make(\App\Livewire\Business\CollaboratorDashboard::class)();
        })->name('hub.business.dashboard');
Route::get('/meu-perfil', \App\Livewire\Business\MyCompanyProfile::class)->name('hub.business.my-profile');
        // 2. FERRAMENTAS E HUBS
        Route::get('/ia-estrategista', BusinessAiHub::class)->name('hub.business.ai');
        Route::get('/arquivo', DocumentVault::class)->name('hub.business.vault');

        Route::get('/despesas', \App\Livewire\Business\CompanyExpenses::class)->name('company-expenses');
        Route::get('/faturacao', InvoicingHub::class)->name('hub.business.invoices');
        Route::get('/propostas', ProposalHub::class)->name('hub.business.proposals');
        Route::get('/clientes', ClientHub::class)->name('hub.business.clients');
        Route::get('/projetos', ProjectHub::class)->name('hub.business.projects');
        Route::get('/empresa/analise-custos', \App\Livewire\Business\ProjectCostsHub::class)->name('hub.business.costs');
        Route::get('/stock', InventoryHub::class)->name('hub.business.inventory');
        Route::get('/fornecedores', SupplierHub::class)->name('hub.business.suppliers');
        Route::get('/lembretes', \App\Livewire\Hub\RemindersHub::class)->name('hub.reminders');
        Route::get('/equipa', TeamHub::class)->name('hub.business.team');
        Route::get('/impostos', TaxHub::class)->name('hub.business.taxes');
         Route::get('/minhas-despesas', CollaboratorExpenseHub::class)->name('hub.business.my-expenses');
        Route::get('/perfil', BusinessSettings::class)->name('hub.business.settings');
        Route::get('/tarefas', TaskHub::class)->name('hub.business.tasks');
        Route::get('/timeline', TaskTimeline::class)->name('hub.business.timeline');
        Route::get('/resultados', BusinessPnlHub::class)->name('hub.business.pnl');
        Route::get('/fluxo-caixa', CashFlowHub::class)->name('hub.business.cashflow');
        Route::get('/aprovacoes', ExpenseApprovalHub::class)->name('hub.business.expense-approvals');
        Route::get('/e-fatura', AtInvoiceHub::class)->name('hub.business.at-invoices');
        Route::get('/messenger', BusinessMessenger::class)->name('hub.business.messenger');
        Route::get('/contas', BankAccountHub::class)->name('hub.business.accounts');
        Route::get('/calendario-empresa', CompanyCalendar::class)->name('hub.business.calendar');
        Route::get('/ferias', AbsenceHub::class)->name('hub.business.absences');
        Route::get('/suporte-empresa', SupportHub::class)->name('hub.business.support');
    });

    // ROTA PARA SAIR DO MODO COLABORADOR (Fora do grupo para evitar conflitos de prefixo)
    Route::get('/empresa/sair-modo-colaborador', function () {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $ceo = \App\Models\User::find(session()->pull('impersonator_id'));

        if ($ceo) {
            Auth::login($ceo);
        }

        return redirect()->route('hub.business.dashboard');
    })->name('hub.business.leave-impersonation');

    // CEO sai da vista de colaborador (sem troca de conta, apenas limpa a sessão)
    Route::get('/empresa/sair-vista-colaborador', function () {
        session()->forget('viewing_as_collaborator_id');
        return redirect()->route('hub.business.dashboard');
    })->name('hub.business.stop-viewing-collaborator');




    // --- FINANÇAS PESSOAIS ---
    Route::get('/receitas', IncomeHub::class)->name('hub.incomes');
    Route::get('/investimentos', InvestmentsHub::class)->name('hub.investments');
    Route::get('/patrimonio', NetWorthHub::class)->name('hub.networth');
    Route::get('/calendario', PersonalCalendar::class)->name('hub.calendar');
    Route::get('/assinaturas', SubscriptionHub::class)->name('hub.subscriptions');
    Route::get('/objetivos', GoalsHub::class)->name('hub.goals');
    Route::get('/relatorios', YearlyReport::class)->name('hub.reports');
    Route::get('/ranking', FamilyRanking::class)->name('hub.ranking');
    Route::get('/dividas', DebtHub::class)->name('hub.debts');
    Route::get('/minhas-contas', BankAccountHub::class)->name('hub.personal.accounts');
    Route::get('/banco', BancoHub::class)->name('hub.banco');
    Route::get('/familia/gestao', ManageFamily::class)->name('hub.family.manage');
    Route::get('/orcamento', BudgetHub::class)->name('hub.budget');
    Route::get('/importar-extrato', StatementImportHub::class)->name('hub.import');
    Route::get('/wrapped', WrappedReport::class)->name('hub.wrapped');
    Route::get('/lock-in', LockInHub::class)->name('hub.lockin');

    // --- MARKETPLACE PRO (LOJA & INVENTÁRIO) ---
    Route::get('/loja', HubStore::class)->name('hub.store');
    Route::get('/loja/carrinho', ShoppingCart::class)->name('store.cart');
    Route::get('/loja/checkout', Checkout::class)->name('store.checkout');
    Route::get('/loja/produto/{product}', \App\Livewire\Store\ProductShow::class)->name('store.product.show');
    Route::get('/loja/favoritos', WishlistHub::class)->name('store.wishlist');
    Route::get('/loja/comparar', ProductCompare::class)->name('store.compare');
    Route::get('/loja/download/{purchase}/request', [StoreDownloadController::class, 'requestToken'])->name('store.download.request');
    Route::get('/loja/download/{purchase}', [StoreDownloadController::class, 'download'])->name('store.download');
    Route::get('/inventario', \App\Livewire\Store\UserInventory::class)->name('hub.inventory');

    // --- GESTÃO DE DESPESAS & CATEGORIAS ---
    Route::get('/expenses', Expenses::class)->name('expenses'); // Alias
    Route::get('/despesas-pessoais', Expenses::class)->name('expenses.index');
    Route::get('/expenses/create', ManageExpense::class)->name('expenses.create');
    Route::get('/expenses/{expense}/edit', ManageExpense::class)->name('expenses.edit');
    Route::get('/categories', Categories::class)->name('categories');
    Route::get('/categories/{category}/campos', CategoryFields::class)->name('categories.fields');


    // Hub de categoria (qualquer slug — fixo ou personalizado)
    Route::get('/hub/{slug}', CategoryHub::class)
        ->where('slug', '[a-z0-9\-]+')
        ->name('hub.category');

    // --- SOCIAL & FITNESS ---
    Route::get('/social', \App\Livewire\Social\SocialHub::class)->name('social.hub');
    Route::get('/social/u/{username}', \App\Livewire\Social\SocialProfile::class)->name('social.profile');
    Route::get('/fitness', FitnessHub::class)->name('hub.fitness');

    Route::post('/api/smartwatch-info', [SmartwatchController::class, 'info']);
    Route::post('/api/mifitness/import', [MiFitnessImportController::class, 'import'])->name('mifitness.import');
    Route::get('/fitness/strava/connect', [StravaController::class, 'connect'])->name('strava.connect');
    Route::get('/fitness/strava/callback', [StravaController::class, 'callback'])->name('strava.callback');
    Route::get('/fitness/strava/disconnect', [StravaController::class, 'disconnect'])->name('strava.disconnect');

    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'update']);

    Route::post('/api/offline/expenses/sync', [\App\Http\Controllers\Api\OfflineExpenseController::class, 'sync'])->name('api.offline.sync');

    // --- SUPORTE ---
    Route::get('/suporte', SupportHub::class)->name('support.hub');

    // --- EXPORTAÇÕES ---
    Route::get('/export/dashboard-pdf', [ExportController::class, 'dashboardPdf'])->name('export.dashboard.pdf');
    Route::get('/export/expenses', [ExportController::class, 'expensesPdf'])->name('export.expenses');
    Route::get('/export/empresa', [ExportController::class, 'businessExport'])->name('export.business');

    // --- PERFIL & CONFIGURAÇÕES ---
    Route::view('/profile', 'profile')->name('profile.edit');

    // --- ÁREA DE ADMINISTRAÇÃO ---
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/lembretes', RemindersMonitor::class)->name('admin.reminders');
        Route::get('/faturacao', AdminSubscriptionHub::class)->name('admin.billing');
        Route::get('/produtividade', ProductivityHub::class)->name('admin.productivity');
        Route::get('/ai-monitor', AiMonitor::class)->name('admin.ai');
        Route::get('/gamificacao', GamificationHub::class)->name('admin.gamification');

        Route::get('/comunicacao', CommunicationManager::class)->name('admin.communication');
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');

        // Utilizadores
        Route::get('/utilizadores', UserManagement::class)->name('admin.users');
        Route::get('/utilizadores/{user}', function ($id) { return "Perfil do Utilizador: $id"; })->name('admin.users.show');
Route::get('/estatisticas', AnalyticsHub::class)->name('admin.stats');
        // Suporte e Comunicação
        Route::get('/suporte-global', SupportManager::class)->name('admin.support');
       Route::get('/comunicacao', CommunicationManager::class)->name('admin.communication');

        // Logs e Configurações
        Route::get('/logs', GlobalLogs::class)->name('admin.logs');
        Route::get('/loja', StoreHub::class)->name('admin.store');
        Route::get('/configuracoes', SiteSettings::class)->name('admin.settings');

        // Personificação
        Route::get('/impersonate/{user}', function (\App\Models\User $user) {
            // Verifica usando o novo sistema de roles ou o is_admin antigo
            if (!auth()->user()->hasRole(['super-admin', 'admin']) && !auth()->user()->is_admin) abort(403);

            session()->put('impersonator_id', auth()->id());
            Auth::login($user);
            return redirect()->route('dashboard');
        })->name('admin.impersonate');
    });

Route::get('/trocar-espaco/{id}', function ($id) {
    $user = auth()->user();
    $ws = $user->workspaces()->findOrFail($id);
    $user->update(['current_workspace_id' => $ws->id]);

    return $ws->type === 'personal'
        ? redirect()->route('dashboard')
        : redirect()->route('hub.business.dashboard');
})->name('workspace.switch.fast');
    // Parar Personificação
   Route::get('/stop-impersonating', function () {
    if (!session()->has('impersonator_id')) return redirect('/');

    $adminId = session()->pull('impersonator_id');
    $admin = \App\Models\User::find($adminId);

    if ($admin) {
        Auth::login($admin);

        // Se ele era Admin, volta para a lista de utilizadores
        if ($admin->isAdminRole()) {
            return redirect()->route('admin.users');
        }
    }

    return redirect()->route('dashboard');
})->name('admin.stop-impersonating');
Route::get('/empresa/dashboard', function() {
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
    // --- TESTE DE EMAIL ---
    Route::get('/test-email', function() {
        $user = auth()->user();
        $lastMonth = now()->subMonth();
        $data = [
            'spent' => (float) ($user->expenses()->whereMonth('spent_at', $lastMonth->month)->sum('amount') ?? 0),
            'earned' => (float) (($user->incomes()->whereMonth('received_at', $lastMonth->month)->sum('amount') ?? 0) + ($user->recurringIncomes()->sum('amount') ?? 0)),
            'categoryStats' => $user->expenses()->whereMonth('spent_at', $lastMonth->month)
                ->select('categories.name', DB::raw('SUM(expenses.amount) as total'))
                ->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->groupBy('categories.name')->get(),
            'monthName' => $lastMonth->translatedFormat('F'),
            'year' => $lastMonth->year
        ];
        try {
            Mail::to($user->email)->send(new MonthlyReportMail($user, $data));
            return "Email enviado!";
        } catch (\Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    });
});


require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
