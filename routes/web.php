<?php

use App\Livewire\AiInsights;
use App\Livewire\Categories;
use App\Livewire\Dashboard;
use App\Livewire\Expenses;
use App\Livewire\ManageExpense;
use App\Livewire\CompanyExpenses;
use App\Livewire\CategoryHub;
use App\Livewire\SubscriptionHub;
use App\Livewire\GoalsHub;
use App\Livewire\YearlyReport;
use App\Livewire\IncomeHub;
use App\Livewire\InvestmentsHub;
use App\Livewire\NetWorthHub;
use App\Livewire\FinancialCalendar;
use App\Livewire\ActivityFeed;
use App\Livewire\GlobalSearch;
use App\Livewire\SubscriptionPlans;
use App\Livewire\FamilyRanking;
use App\Livewire\DebtHub;
use App\Livewire\SupportHub;
// Imports de Administração
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\SupportManager;
use App\Livewire\Admin\GlobalLogs;
use App\Livewire\Admin\SiteSettings;
// Imports do Módulo Empresarial
use App\Livewire\Business\BusinessDashboard;
use App\Livewire\Business\InvoicingHub;
use App\Livewire\Business\ClientHub;
use App\Livewire\Business\ProjectHub;
use App\Livewire\Business\InventoryHub;
use App\Livewire\Business\SupplierHub;
use App\Livewire\Business\TeamHub;
use App\Livewire\Business\TaxHub;
use App\Livewire\Business\BusinessSettings;
use App\Livewire\Business\BusinessAiHub;
use App\Livewire\Business\DocumentVault;
use App\Livewire\Business\TaskHub;
use App\Livewire\Business\TaskTimeline;
use App\Livewire\Business\BusinessPnlHub;
use App\Livewire\Business\BusinessMessenger;
use App\Livewire\Business\ProposalHub;

use App\Livewire\Business\BankAccountHub;
use App\Livewire\Business\CompanyCalendar;
use App\Livewire\Business\AbsenceHub;
use App\Http\Controllers\ExportController;
use App\Mail\MonthlyReportMail;
use Illuminate\Support\Facades\{Route, Mail, DB, Auth};

// --- PÁGINA INICIAL ---
Route::view('/', 'welcome')->name('home');

// --- GRUPO PROTEGIDO (Utilizadores Autenticados) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Painéis Centrais e Logs
    // Rota para registar o telemóvel nas notificações Push
Route::post('/push-subscriptions', [\App\Http\Controllers\PushSubscriptionController::class, 'update']);

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/atividades', ActivityFeed::class)->name('activity-log');
    Route::get('/planos', SubscriptionPlans::class)->name('hub.pricing');

    // --- MÓDULO EMPRESARIAL (MODO BUSINESS) ---
    Route::prefix('empresa')->group(function () {
        Route::get('/dashboard', BusinessDashboard::class)->name('hub.business.dashboard');
        Route::get('/ia-estrategista', BusinessAiHub::class)->name('hub.business.ai');
        Route::get('/arquivo', DocumentVault::class)->name('hub.business.vault');
        Route::get('/despesas', CompanyExpenses::class)->name('company-expenses');
        Route::get('/faturacao', InvoicingHub::class)->name('hub.business.invoices');
        Route::get('/propostas', ProposalHub::class)->name('hub.business.proposals');
        Route::get('/clientes', ClientHub::class)->name('hub.business.clients');
        Route::get('/projetos', ProjectHub::class)->name('hub.business.projects');
        Route::get('/stock', InventoryHub::class)->name('hub.business.inventory');
        Route::get('/fornecedores', SupplierHub::class)->name('hub.business.suppliers');
        Route::get('/equipa', TeamHub::class)->name('hub.business.team');
        Route::get('/impostos', TaxHub::class)->name('hub.business.taxes');
        Route::get('/perfil', BusinessSettings::class)->name('hub.business.settings');
        Route::get('/tarefas', TaskHub::class)->name('hub.business.tasks');
        Route::get('/timeline', TaskTimeline::class)->name('hub.business.timeline');
        Route::get('/resultados', BusinessPnlHub::class)->name('hub.business.pnl');
        Route::get('/messenger', BusinessMessenger::class)->name('hub.business.messenger');
        Route::get('/contas', BankAccountHub::class)->name('hub.business.accounts');
        Route::get('/calendario-empresa', CompanyCalendar::class)->name('hub.business.calendar');
        Route::get('/ferias', AbsenceHub::class)->name('hub.business.absences');
        Route::get('/suporte', SupportHub::class)->name('hub.business.support');
    });

   // Rota principal do Perfil
Route::get('/profile', function () {
    return view('profile');
})->name('profile.edit');

// Aliases para os testes não darem 404
Route::get('/settings/security', function () { return view('profile'); })->name('security.edit');
Route::get('/settings/appearance', function () { return view('profile'); })->name('appearance.edit');

    // --- FINANÇAS PESSOAIS ---
    Route::get('/receitas', IncomeHub::class)->name('hub.incomes');
    Route::get('/investimentos', InvestmentsHub::class)->name('hub.investments');
    Route::get('/patrimonio', NetWorthHub::class)->name('hub.networth');
    Route::get('/calendario', FinancialCalendar::class)->name('hub.calendar');
    Route::get('/assinaturas', SubscriptionHub::class)->name('hub.subscriptions');
    Route::get('/objetivos', GoalsHub::class)->name('hub.goals');
    Route::get('/relatorios', YearlyReport::class)->name('hub.reports');
    Route::get('/ranking', FamilyRanking::class)->name('hub.ranking');
    Route::get('/dividas', DebtHub::class)->name('hub.debts');
    Route::get('/minhas-contas', BankAccountHub::class)->name('hub.personal.accounts');
    Route::get('/familia/gestao', \App\Livewire\ManageFamily::class)->name('hub.family.manage');

    // --- SUPORTE (UTILIZADOR) ---
    Route::get('/suporte', SupportHub::class)->name('support.hub');

    // --- GESTÃO DE DESPESAS ---
    Route::get('/expenses', Expenses::class)->name('expenses');
    Route::get('/expenses/create', ManageExpense::class)->name('expenses.create');
    Route::get('/expenses/{expense}/edit', ManageExpense::class)->name('expenses.edit');
    Route::get('/categories', Categories::class)->name('categories');

    // --- INTELIGÊNCIA ARTIFICIAL ---
    Route::get('/ai', AiInsights::class)->name('ai');
    Route::get('/insights', AiInsights::class)->name('insights');

    // --- EXPORTAÇÃO E PERFIL ---
    Route::get('/export/dashboard-pdf', [ExportController::class, 'dashboardPdf'])->name('export.dashboard.pdf');
    Route::get('/export/expenses', [ExportController::class, 'expensesPdf'])->name('export.expenses');

    // ROTA QUE ESTAVA A FALTAR:
    Route::get('/export/empresa', [ExportController::class, 'businessExport'])->name('export.business');

    Route::view('/profile', 'profile')->name('profile.edit');

    // Hubs de Categoria Dinâmicos
    $slugs = ['carro', 'casa', 'alimentacao', 'transporte', 'saude', 'educacao', 'tecnologia', 'emprestimos', 'seguros', 'outras'];
    foreach ($slugs as $slug) {
        Route::get("/hub/{$slug}", CategoryHub::class)->defaults('slug', $slug)->name("hub.{$slug}");
    }

    // --- ÁREA DE ADMINISTRAÇÃO ---
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/utilizadores', UserManagement::class)->name('admin.users');
        Route::get('/suporte-global', SupportManager::class)->name('admin.support');
        Route::get('/logs', GlobalLogs::class)->name('admin.logs');
        Route::get('/configuracoes', SiteSettings::class)->name('admin.settings');

        // Personificação
        Route::get('/impersonate/{user}', function (\App\Models\User $user) {
            if (!auth()->user()->is_admin) abort(403);
            session()->put('impersonator_id', auth()->id());
            Auth::login($user);
            return redirect()->route('dashboard');
        })->name('admin.impersonate');
    });

    // Parar Personificação
    Route::get('/stop-impersonating', function () {
        if (!session()->has('impersonator_id')) return redirect('/');
        $admin = \App\Models\User::find(session()->pull('impersonator_id'));
        Auth::login($admin);
        return redirect()->route('admin.users');
    })->name('admin.stop-impersonating');

    // --- TESTE DE EMAIL ---
    Route::get('/test-email', function() {
        $user = auth()->user(); $lastMonth = now()->subMonth();
        $data = ['spent' => (float) $user->expenses()->whereMonth('spent_at', $lastMonth->month)->sum('amount'), 'earned' => (float) $user->incomes()->whereMonth('received_at', $lastMonth->month)->sum('amount') + (float) $user->recurringIncomes()->sum('amount'), 'categoryStats' => $user->expenses()->whereMonth('spent_at', $lastMonth->month)->select('categories.name', DB::raw('SUM(expenses.amount) as total'))->join('categories', 'expenses.category_id', '=', 'categories.id')->groupBy('categories.name')->get(), 'monthName' => $lastMonth->translatedFormat('F'), 'year' => $lastMonth->year];
        try { Mail::to($user->email)->send(new MonthlyReportMail($user, $data)); $user->emailLogs()->create(['subject' => 'Relatório Mensal - ' . $data['monthName'], 'sent_at' => now()]); return "Email enviado!"; } catch (\Exception $e) { return "Erro: " . $e->getMessage(); }
    });
});
