<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Investment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FinanceBot extends Component
{
    public $isOpen = false;
    public $messages = [];
    public $userInput = '';

    // Estado de conversa multi-passo (ex: a perguntar um valor, ou os dados de um ticket)
    public $awaiting = null;
    public $context = [];

    public function mount()
    {
        $this->messages[] = $this->botMessage(
            "Olá " . $this->firstName() . "! Sou o teu Finance Pilot 🤖. Posso registar gastos e receitas, mostrar objetivos, investimentos, assinaturas e orçamentos, abrir tickets de suporte e levar-te a qualquer página do site. O que precisas?",
            $this->mainMenuOptions()
        );
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    // ===========================================================
    // ENTRADA DE TEXTO LIVRE
    // ===========================================================
    public function sendMessage()
    {
        $text = trim($this->userInput);

        if ($text === '') {
            return;
        }

        $this->messages[] = $this->userMessage($text);
        $this->userInput = '';
        $this->dispatch('message-sent');

        if ($this->awaiting) {
            $this->handleAwaitingInput($text);
        } else {
            $this->respondTo($text);
        }

        $this->dispatch('message-sent');
    }

    private function handleAwaitingInput(string $text): void
    {
        $normalized = Str::lower(trim($text));

        if (in_array($normalized, ['cancelar', 'cancel', 'sair', 'esquece'])) {
            $this->awaiting = null;
            $this->context = [];
            $this->messages[] = $this->botMessage('Ok, cancelado. Em que mais posso ajudar?', $this->mainMenuOptions());
            return;
        }

        switch ($this->awaiting) {
            case 'goal_amount':
                $this->processGoalContribution($text);
                break;

            case 'ticket_subject':
                if (strlen($text) < 5) {
                    $this->messages[] = $this->botMessage('O assunto deve ter pelo menos 5 caracteres. Tenta novamente (ou escreve "cancelar").');
                    return;
                }
                $this->context['subject'] = $text;
                $this->awaiting = 'ticket_message';
                $this->messages[] = $this->botMessage('Entendido. Agora descreve o problema com mais detalhe (mín. 10 caracteres).');
                break;

            case 'ticket_message':
                if (strlen($text) < 10) {
                    $this->messages[] = $this->botMessage('Preciso de um pouco mais de detalhe (mín. 10 caracteres). Tenta novamente.');
                    return;
                }
                $this->context['message'] = $text;
                $this->awaiting = null;
                $this->messages[] = $this->botMessage('Qual a urgência deste pedido?', [
                    ['label' => '🔴 Alta', 'action' => 'support:priority:high'],
                    ['label' => '🟡 Normal', 'action' => 'support:priority:normal'],
                    ['label' => '🟢 Baixa', 'action' => 'support:priority:low'],
                ]);
                break;

            default:
                $this->awaiting = null;
                $this->respondTo($text);
        }
    }

    // ===========================================================
    // AÇÕES (BOTÕES)
    // ===========================================================
    public function handleAction($action)
    {
        // --- Ações dinâmicas (com parâmetro embutido) ---
        if (Str::startsWith($action, 'goal:pick:')) {
            $this->pickGoal((int) Str::after($action, 'goal:pick:'));
            $this->dispatch('message-sent');
            return;
        }

        if (Str::startsWith($action, 'workspace:switch:')) {
            $this->switchWorkspaceFromChat((int) Str::after($action, 'workspace:switch:'));
            $this->dispatch('message-sent');
            return;
        }

        if (Str::startsWith($action, 'support:priority:')) {
            $this->createSupportTicket(Str::after($action, 'support:priority:'));
            $this->dispatch('message-sent');
            return;
        }

        if (Str::startsWith($action, 'nav:')) {
            $route = $this->resolveNavRoute(Str::after($action, 'nav:'));
            if ($route) {
                $this->isOpen = false;
                return redirect()->route($route);
            }
        }

        // --- Ações estáticas ---
        if ($action === 'startExpense') { $this->startExpense(); }
        elseif ($action === 'startIncome') { $this->startIncome(); }
        elseif ($action === 'triggerManual') { return $this->goToExpenseForm(); }
        elseif ($action === 'triggerScanner') { return $this->goToScanner(); }
        elseif ($action === 'triggerIncomeManual') { return $this->goToIncomeForm(); }
        elseif ($action === 'showDailySummary') { $this->showDailySummary(); }
        elseif ($action === 'getAiAdvice') { $this->getAiAdvice(); }
        elseif ($action === 'menu:root') { $this->messages[] = $this->botMessage('Em que mais posso ajudar?', $this->mainMenuOptions()); }
        elseif ($action === 'menu:money') { $this->moneyMenu(); }
        elseif ($action === 'menu:goals') { $this->goalsMenuRoot(); }
        elseif ($action === 'menu:invest') { $this->investMenuRoot(); }
        elseif ($action === 'menu:support') { $this->supportMenuRoot(); }
        elseif ($action === 'menu:nav') { $this->navMenuRoot(); }
        elseif ($action === 'menu:nav:business') { $this->navMenuBusiness(); }
        elseif ($action === 'menu:workspaces') { $this->showWorkspaces(); }
        elseif ($action === 'menu:hubs') { $this->categoryHubsMenu(); }
        elseif ($action === 'goal:list') { $this->showGoalsList(); }
        elseif ($action === 'goal:contribute:start') { $this->startGoalContribution(); }
        elseif ($action === 'budget:list') { $this->showBudgets(); }
        elseif ($action === 'portfolio:view') { $this->showPortfolio(); }
        elseif ($action === 'subs:list') { $this->showSubscriptions(); }
        elseif ($action === 'support:new:start') { $this->startSupportTicket(); }

        $this->dispatch('message-sent');
    }

    // ===========================================================
    // NAVEGAÇÃO PARA FORA DO CHAT
    // ===========================================================
    private function goToExpenseForm()
    {
        $this->isOpen = false;
        return redirect()->route('expenses.create');
    }

    private function goToScanner()
    {
        $this->isOpen = false;
        return redirect()->route('hub.outras', ['open_scanner' => 1]);
    }

    private function goToIncomeForm()
    {
        $this->isOpen = false;
        return redirect()->route('hub.incomes', ['open' => 'add-extra-income']);
    }

    /**
     * Mapa de rotas pessoais navegáveis a partir do chat.
     */
    private function personalNavMap(): array
    {
        return [
            'dashboard'      => ['label' => '🏠 Dashboard', 'route' => 'dashboard'],
            'expenses'       => ['label' => '💸 Despesas', 'route' => 'expenses'],
            'incomes'        => ['label' => '💰 Receitas', 'route' => 'hub.incomes'],
            'goals'          => ['label' => '🎯 Objetivos', 'route' => 'hub.goals'],
            'investments'    => ['label' => '📈 Investimentos', 'route' => 'hub.investments'],
            'subscriptions'  => ['label' => '🔁 Assinaturas', 'route' => 'hub.subscriptions'],
            'networth'       => ['label' => '🏦 Património', 'route' => 'hub.networth'],
            'calendar'       => ['label' => '📅 Calendário', 'route' => 'hub.calendar'],
            'reports'        => ['label' => '📑 Relatórios', 'route' => 'hub.reports'],
            'ranking'        => ['label' => '🏆 Ranking Família', 'route' => 'hub.ranking'],
            'debts'          => ['label' => '📉 Dívidas', 'route' => 'hub.debts'],
            'categories'     => ['label' => '🗂️ Categorias', 'route' => 'categories'],
            'accounts'       => ['label' => '🏛️ Contas', 'route' => 'hub.personal.accounts'],
            'support'        => ['label' => '🆘 Suporte', 'route' => 'support.hub'],
            'social'         => ['label' => '🌐 Finance Connect', 'route' => 'social.hub'],
            'ai'             => ['label' => '🤖 IA & Insights', 'route' => 'ai'],
        ];
    }

    /**
     * Mapa de rotas empresariais navegáveis a partir do chat.
     */
    private function businessNavMap(): array
    {
        return [
            'b_dashboard' => ['label' => '🏢 Dashboard Empresa', 'route' => 'hub.business.dashboard'],
            'b_invoices'  => ['label' => '🧾 Faturação', 'route' => 'hub.business.invoices'],
            'b_proposals' => ['label' => '📃 Propostas', 'route' => 'hub.business.proposals'],
            'b_expenses'  => ['label' => '💸 Despesas Empresa', 'route' => 'company-expenses'],
            'b_clients'   => ['label' => '🤝 Clientes', 'route' => 'hub.business.clients'],
            'b_projects'  => ['label' => '📁 Projetos', 'route' => 'hub.business.projects'],
            'b_inventory' => ['label' => '📦 Stock', 'route' => 'hub.business.inventory'],
            'b_suppliers' => ['label' => '🚚 Fornecedores', 'route' => 'hub.business.suppliers'],
            'b_team'      => ['label' => '👥 Equipa', 'route' => 'hub.business.team'],
            'b_tasks'     => ['label' => '✅ Tarefas', 'route' => 'hub.business.tasks'],
            'b_timeline'  => ['label' => '🗓️ Timeline', 'route' => 'hub.business.timeline'],
            'b_pnl'       => ['label' => '📊 Resultados', 'route' => 'hub.business.pnl'],
            'b_taxes'     => ['label' => '🧮 Impostos', 'route' => 'hub.business.taxes'],
            'b_accounts'  => ['label' => '🏛️ Contas Empresa', 'route' => 'hub.business.accounts'],
            'b_calendar'  => ['label' => '📅 Calendário Empresa', 'route' => 'hub.business.calendar'],
            'b_absences'  => ['label' => '🌴 Férias', 'route' => 'hub.business.absences'],
            'b_support'   => ['label' => '🆘 Suporte Empresa', 'route' => 'hub.business.support'],
            'b_ai'        => ['label' => '🤖 IA Estrategista', 'route' => 'hub.business.ai'],
            'b_vault'     => ['label' => '🗄️ Arquivo', 'route' => 'hub.business.vault'],
            'b_messenger' => ['label' => '💬 Messenger', 'route' => 'hub.business.messenger'],
            'b_settings'  => ['label' => '⚙️ Definições', 'route' => 'hub.business.settings'],
        ];
    }

    private function resolveNavRoute(string $key): ?string
    {
        if (Str::startsWith($key, 'hub_')) {
            return 'hub.' . Str::after($key, 'hub_');
        }

        $map = array_merge($this->personalNavMap(), $this->businessNavMap());
        return $map[$key]['route'] ?? null;
    }

    // ===========================================================
    // MENUS
    // ===========================================================
    private function mainMenuOptions(): array
    {
        return [
            ['label' => '💸 Dinheiro (Gastos/Receitas)', 'action' => 'menu:money'],
            ['label' => '🎯 Objetivos & Orçamentos', 'action' => 'menu:goals'],
            ['label' => '📈 Investimentos & Assinaturas', 'action' => 'menu:invest'],
            ['label' => '🆘 Suporte', 'action' => 'menu:support'],
            ['label' => '🧭 Ir Para...', 'action' => 'menu:nav'],
            ['label' => '🔄 Trocar de Espaço', 'action' => 'menu:workspaces'],
            ['label' => '📊 Resumo de Hoje', 'action' => 'showDailySummary'],
            ['label' => '🤖 Sugestão da IA', 'action' => 'getAiAdvice'],
        ];
    }

    private function moneyMenu(): void
    {
        $this->messages[] = $this->botMessage('O que queres fazer?', [
            ['label' => '💸 Registar Gasto', 'action' => 'startExpense'],
            ['label' => '💰 Registar Receita', 'action' => 'startIncome'],
            ['label' => '📋 Ver Despesas', 'action' => 'nav:expenses'],
            ['label' => '📋 Ver Receitas', 'action' => 'nav:incomes'],
            ['label' => '📊 Ver Orçamentos', 'action' => 'budget:list'],
            ['label' => '🗂️ Hubs de Categoria', 'action' => 'menu:hubs'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    private function categoryHubsMenu(): void
    {
        $hubs = [
            'carro'       => '🚗 Carro',
            'casa'        => '🏠 Casa',
            'alimentacao' => '🛒 Alimentação',
            'transporte'  => '🚌 Transporte',
            'saude'       => '❤️ Saúde',
            'educacao'    => '🎓 Educação',
            'tecnologia'  => '💻 Tecnologia',
            'emprestimos' => '🏦 Empréstimos',
            'seguros'     => '🛡️ Seguros',
            'outras'      => '🏷️ Outras',
        ];

        $options = collect($hubs)
            ->map(fn ($label, $slug) => ['label' => $label, 'action' => "nav:hub_{$slug}"])
            ->values()
            ->toArray();

        $options[] = ['label' => '⬅️ Voltar', 'action' => 'menu:money'];

        $this->messages[] = $this->botMessage('Que categoria queres ver?', $options);
    }

    private function goalsMenuRoot(): void
    {
        $this->messages[] = $this->botMessage('Objetivos e Orçamentos — o que precisas?', [
            ['label' => '🎯 Ver Objetivos', 'action' => 'goal:list'],
            ['label' => '➕ Adicionar a um Objetivo', 'action' => 'goal:contribute:start'],
            ['label' => '📊 Ver Orçamentos', 'action' => 'budget:list'],
            ['label' => '🧭 Ir para Objetivos', 'action' => 'nav:goals'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    private function investMenuRoot(): void
    {
        $this->messages[] = $this->botMessage('Investimentos e Assinaturas:', [
            ['label' => '📈 Ver Carteira', 'action' => 'portfolio:view'],
            ['label' => '🔁 Ver Assinaturas', 'action' => 'subs:list'],
            ['label' => '🧭 Ir para Investimentos', 'action' => 'nav:investments'],
            ['label' => '🧭 Ir para Assinaturas', 'action' => 'nav:subscriptions'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    private function supportMenuRoot(): void
    {
        $isBusinessMode = request()->routeIs('hub.business.*');

        $this->messages[] = $this->botMessage('Como posso ajudar com o suporte?', [
            ['label' => '🎟️ Abrir Ticket', 'action' => 'support:new:start'],
            ['label' => '📋 Ver os Meus Tickets', 'action' => $isBusinessMode ? 'nav:b_support' : 'nav:support'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    private function navMenuRoot(): void
    {
        $options = collect($this->personalNavMap())
            ->map(fn ($v, $k) => ['label' => $v['label'], 'action' => "nav:{$k}"])
            ->values()
            ->toArray();

        if (Auth::user()->currentWorkspace && Auth::user()->currentWorkspace->type === 'company') {
            $options[] = ['label' => '🏢 Ver Páginas da Empresa', 'action' => 'menu:nav:business'];
        }

        $options[] = ['label' => '⬅️ Menu', 'action' => 'menu:root'];

        $this->messages[] = $this->botMessage('Para onde queres ir?', $options);
    }

    private function navMenuBusiness(): void
    {
        $options = collect($this->businessNavMap())
            ->map(fn ($v, $k) => ['label' => $v['label'], 'action' => "nav:{$k}"])
            ->values()
            ->toArray();

        $options[] = ['label' => '⬅️ Voltar', 'action' => 'menu:nav'];

        $this->messages[] = $this->botMessage('Páginas da Empresa:', $options);
    }

    private function showWorkspaces(): void
    {
        $workspaces = Auth::user()->workspaces;
        $current = Auth::user()->current_workspace_id;

        $options = $workspaces->map(function ($ws) use ($current) {
            $flag = $ws->id === $current ? '✅ ' : '';
            return ['label' => $flag . $ws->name, 'action' => "workspace:switch:{$ws->id}"];
        })->toArray();

        $options[] = ['label' => '⬅️ Menu', 'action' => 'menu:root'];

        $this->messages[] = $this->botMessage('Em qual espaço queres trabalhar?', $options);
    }

    private function switchWorkspaceFromChat(int $id): void
    {
        if (Auth::user()->workspaces()->where('workspaces.id', $id)->exists()) {
            Auth::user()->update(['current_workspace_id' => $id]);
            $this->messages[] = $this->botMessage('Espaço alterado com sucesso! 🔄', $this->mainMenuOptions());
        } else {
            $this->messages[] = $this->botMessage('Não tens acesso a esse espaço.', $this->mainMenuOptions());
        }
    }

    // ===========================================================
    // GASTOS / RECEITAS
    // ===========================================================
    private function startExpense(): void
    {
        $this->messages[] = $this->userMessage('Quero registar um gasto.');
        $this->messages[] = $this->botMessage(
            'Com certeza! Podes usar o Scanner IA para ser mais rápido (foto da fatura) ou preencher manualmente.',
            [
                ['label' => '📸 Abrir Scanner IA', 'action' => 'triggerScanner'],
                ['label' => '✍️ Registo Manual', 'action' => 'triggerManual'],
            ]
        );
    }

    private function startIncome(): void
    {
        $this->messages[] = $this->userMessage('Quero registar uma receita.');
        $this->messages[] = $this->botMessage(
            'Boa! Vamos lá registar essa receita.',
            [
                ['label' => '✍️ Registo Manual', 'action' => 'triggerIncomeManual'],
                ['label' => '📋 Ver Receitas', 'action' => 'nav:incomes'],
            ]
        );
    }

    private function showDailySummary(): void
    {
        $workspaceId = Auth::user()->current_workspace_id;

        $spentToday = (float) Expense::where('workspace_id', $workspaceId)
            ->whereDate('spent_at', today())
            ->sum('amount');

        $earnedToday = (float) Income::where('workspace_id', $workspaceId)
            ->whereDate('received_at', today())
            ->sum('amount');

        $this->messages[] = $this->userMessage('Como está o meu dia?');

        if ($spentToday <= 0 && $earnedToday <= 0) {
            $content = "Ainda não há movimentos registados hoje neste espaço. 🎉";
        } else {
            $content = "Hoje já há **" . number_format($spentToday, 2, ',', '.') . "€** em despesas";
            $content .= $earnedToday > 0
                ? " e **" . number_format($earnedToday, 2, ',', '.') . "€** em receitas."
                : '.';
            $content .= " Queres ver os detalhes?";
        }

        $this->messages[] = $this->botMessage($content, [
            ['label' => '📋 Ver Histórico', 'action' => 'nav:expenses'],
            ['label' => '📊 Ver Orçamentos', 'action' => 'budget:list'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    private function getAiAdvice(): void
    {
        $workspaceId = Auth::user()->current_workspace_id;
        $monthStart = now()->startOfMonth();

        $topCategory = Expense::query()
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.workspace_id', $workspaceId)
            ->whereBetween('expenses.spent_at', [$monthStart, now()])
            ->selectRaw('categories.name, SUM(expenses.amount) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->first();

        $monthlySubs = (float) Subscription::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get()
            ->sum(fn ($s) => Str::lower((string) $s->cycle) === 'yearly' ? $s->amount / 12 : $s->amount);

        $this->messages[] = $this->userMessage('Dá-me uma sugestão.');

        $content = $topCategory
            ? "Este mês a categoria com mais gastos foi **{$topCategory->name}**, com " . number_format($topCategory->total, 2, ',', '.') . "€. Que tal definir um orçamento para ela?"
            : "Ainda não há despesas suficientes este mês para uma análise. Começa por registar os teus gastos! 💪";

        if ($monthlySubs > 0) {
            $content .= "\n\nAlém disso, as tuas assinaturas ativas custam cerca de **" . number_format($monthlySubs, 2, ',', '.') . "€/mês**. Vale a pena rever se ainda usas todas.";
        }

        $this->messages[] = $this->botMessage($content, [
            ['label' => '🗂️ Gerir Categorias', 'action' => 'nav:categories'],
            ['label' => '🔁 Ver Assinaturas', 'action' => 'subs:list'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    // ===========================================================
    // ORÇAMENTOS
    // ===========================================================
    private function showBudgets(): void
    {
        $workspaceId = Auth::user()->current_workspace_id;
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $categories = Category::where('workspace_id', $workspaceId)->where('budget_limit', '>', 0)->get();

        if ($categories->isEmpty()) {
            $this->messages[] = $this->botMessage('Ainda não definiste orçamentos em nenhuma categoria.', [
                ['label' => '🗂️ Ir para Categorias', 'action' => 'nav:categories'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]);
            return;
        }

        $lines = $categories->map(function ($cat) use ($workspaceId, $monthStart, $monthEnd) {
            $spent = (float) Expense::where('workspace_id', $workspaceId)
                ->where('category_id', $cat->id)
                ->whereBetween('spent_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $pct = $cat->budget_limit > 0 ? round(($spent / $cat->budget_limit) * 100) : 0;
            $emoji = $pct >= 100 ? '🔴' : ($pct >= 80 ? '🟠' : '🟢');

            return "{$emoji} **{$cat->name}** — " . number_format($spent, 2, ',', '.') . "€ / " . number_format($cat->budget_limit, 2, ',', '.') . "€ ({$pct}%)";
        })->implode("\n");

        $this->messages[] = $this->botMessage($lines, [
            ['label' => '🗂️ Gerir Categorias', 'action' => 'nav:categories'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    // ===========================================================
    // OBJETIVOS
    // ===========================================================
    private function showGoalsList(): void
    {
        $goals = Goal::where('workspace_id', Auth::user()->current_workspace_id)->orderBy('deadline')->get();

        if ($goals->isEmpty()) {
            $this->messages[] = $this->botMessage('Ainda não tens objetivos definidos.', [
                ['label' => '🎯 Criar Objetivo', 'action' => 'nav:goals'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]);
            return;
        }

        $lines = $goals->map(function ($g) {
            $pct = $g->target_amount > 0 ? min(100, round(($g->current_amount / $g->target_amount) * 100)) : 0;
            $deadline = $g->deadline ? ' · até ' . $g->deadline->translatedFormat('M Y') : '';
            return "🎯 **{$g->name}** — {$pct}% (" . number_format($g->current_amount, 2, ',', '.') . "€ / " . number_format($g->target_amount, 2, ',', '.') . "€){$deadline}";
        })->implode("\n");

        $this->messages[] = $this->botMessage($lines, [
            ['label' => '➕ Adicionar a um Objetivo', 'action' => 'goal:contribute:start'],
            ['label' => '🧭 Ir para Objetivos', 'action' => 'nav:goals'],
            ['label' => '⬅️ Menu', 'action' => 'menu:root'],
        ]);
    }

    private function startGoalContribution(): void
    {
        $goals = Goal::where('workspace_id', Auth::user()->current_workspace_id)->get();

        if ($goals->isEmpty()) {
            $this->messages[] = $this->botMessage('Ainda não tens objetivos. Cria um primeiro!', [
                ['label' => '🧭 Ir para Objetivos', 'action' => 'nav:goals'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]);
            return;
        }

        $options = $goals->map(fn ($g) => ['label' => "🎯 {$g->name}", 'action' => "goal:pick:{$g->id}"])->toArray();
        $options[] = ['label' => '⬅️ Voltar', 'action' => 'goal:list'];

        $this->messages[] = $this->botMessage('A qual objetivo queres adicionar dinheiro?', $options);
    }

    private function pickGoal(int $id): void
    {
        $goal = Goal::where('workspace_id', Auth::user()->current_workspace_id)->find($id);

        if (!$goal) {
            $this->messages[] = $this->botMessage('Esse objetivo já não existe.', $this->mainMenuOptions());
            return;
        }

        $this->context = ['goal_id' => $id];
        $this->awaiting = 'goal_amount';
        $this->messages[] = $this->botMessage("Quanto queres adicionar ao objetivo **{$goal->name}**? (escreve só o valor, ex: 50)");
    }

    private function processGoalContribution(string $text): void
    {
        preg_match('/(\d+[.,]?\d*)/', $text, $m);
        $amount = isset($m[1]) ? (float) str_replace(',', '.', $m[1]) : null;

        if (!$amount || $amount <= 0) {
            $this->messages[] = $this->botMessage('Não percebi o valor. Escreve só o número, ex: 50 (ou "cancelar").');
            return;
        }

        $goalId = $this->context['goal_id'] ?? null;
        $goal = Goal::where('workspace_id', Auth::user()->current_workspace_id)->find($goalId);

        if (!$goal) {
            $this->awaiting = null;
            $this->context = [];
            $this->messages[] = $this->botMessage('Esse objetivo já não existe.', $this->mainMenuOptions());
            return;
        }

        $goal->current_amount = (float) $goal->current_amount + $amount;
        $goal->save();

        $this->awaiting = null;
        $this->context = [];

        $pct = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0;

        $this->messages[] = $this->botMessage(
            "Adicionei **" . number_format($amount, 2, ',', '.') . "€** ao objetivo **{$goal->name}**. Está a {$pct}% (" . number_format($goal->current_amount, 2, ',', '.') . "€ de " . number_format($goal->target_amount, 2, ',', '.') . "€). 🎉",
            [
                ['label' => '🎯 Ver Objetivos', 'action' => 'goal:list'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]
        );
    }

    // ===========================================================
    // INVESTIMENTOS
    // ===========================================================
    private function showPortfolio(): void
    {
        $workspaceId = Auth::user()->current_workspace_id;
        $investments = Investment::where('workspace_id', $workspaceId)->get();

        if ($investments->isEmpty()) {
            $this->messages[] = $this->botMessage('Ainda não tens investimentos registados.', [
                ['label' => '🧭 Ir para Investimentos', 'action' => 'nav:investments'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]);
            return;
        }

        $market = Cache::get('market_prices_crypto', []);
        $total = 0;

        $lines = $investments->map(function ($inv) use (&$total, $market) {
            $symbol = Str::lower((string) $inv->symbol);
            $price = match ($symbol) {
                'btc' => $market['bitcoin']['eur'] ?? $inv->current_price,
                'eth' => $market['ethereum']['eur'] ?? $inv->current_price,
                'sol' => $market['solana']['eur'] ?? $inv->current_price,
                default => $inv->current_price,
            };

            $value = (float) $inv->quantity * (float) $price;
            $total += $value;

            return "💼 **{$inv->name}** (" . ($inv->symbol ?: $inv->product_type) . ") — " . number_format($value, 2, ',', '.') . "€";
        })->implode("\n");

        $this->messages[] = $this->botMessage(
            "📈 Valor total da carteira: **" . number_format($total, 2, ',', '.') . "€**\n\n{$lines}",
            [
                ['label' => '🧭 Ir para Investimentos', 'action' => 'nav:investments'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]
        );
    }

    // ===========================================================
    // ASSINATURAS
    // ===========================================================
    private function showSubscriptions(): void
    {
        $workspaceId = Auth::user()->current_workspace_id;
        $subs = Subscription::where('workspace_id', $workspaceId)->where('is_active', true)->get();

        if ($subs->isEmpty()) {
            $this->messages[] = $this->botMessage('Não tens assinaturas ativas registadas.', [
                ['label' => '🧭 Ir para Assinaturas', 'action' => 'nav:subscriptions'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]);
            return;
        }

        $monthlyTotal = $subs->sum(fn ($s) => Str::lower((string) $s->cycle) === 'yearly' ? $s->amount / 12 : $s->amount);

        $lines = $subs->map(function ($s) {
            $cycleLabel = Str::lower((string) $s->cycle) === 'yearly' ? '/ano' : '/mês';
            return "🔁 **{$s->name}** — " . number_format($s->amount, 2, ',', '.') . "€ {$cycleLabel}";
        })->implode("\n");

        $this->messages[] = $this->botMessage(
            "💳 Total mensal estimado: **" . number_format($monthlyTotal, 2, ',', '.') . "€**\n\n{$lines}",
            [
                ['label' => '🧭 Ir para Assinaturas', 'action' => 'nav:subscriptions'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]
        );
    }

    // ===========================================================
    // SUPORTE (TICKETS)
    // ===========================================================
    private function startSupportTicket(): void
    {
        $this->context = [];
        $this->awaiting = 'ticket_subject';
        $this->messages[] = $this->botMessage('Vamos abrir um ticket de suporte. Qual é o assunto? (escreve "cancelar" para desistir)');
    }

    private function createSupportTicket(string $priority): void
    {
        $isBusinessMode = request()->routeIs('hub.business.*');

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'workspace_id' => $isBusinessMode ? Auth::user()->current_workspace_id : null,
            'subject' => $this->context['subject'] ?? 'Pedido via Chat',
            'priority' => $priority,
            'status' => 'open',
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $this->context['message'] ?? '',
            'is_admin_reply' => false,
        ]);

        $this->context = [];

        $this->messages[] = $this->botMessage(
            "Ticket #{$ticket->id} criado com sucesso! A nossa equipa vai responder em breve. 🎟️",
            [
                ['label' => '📋 Ver os Meus Tickets', 'action' => $isBusinessMode ? 'nav:b_support' : 'nav:support'],
                ['label' => '⬅️ Menu', 'action' => 'menu:root'],
            ]
        );
    }

    // ===========================================================
    // INTERPRETAÇÃO DE TEXTO LIVRE
    // ===========================================================
    private function respondTo(string $text): void
    {
        $normalized = Str::lower($text);

        if (Str::contains($normalized, ['gastei', 'paguei', 'comprei', 'despesa', 'gasto'])) {
            preg_match('/(\d+[.,]?\d*)/', $normalized, $m);
            $amount = isset($m[1]) ? (float) str_replace(',', '.', $m[1]) : null;

            $this->messages[] = $this->botMessage(
                $amount
                    ? "Detetei um gasto de aproximadamente " . number_format($amount, 2, ',', '.') . "€. Queres que o registe?"
                    : "Parece que queres registar um gasto. Vamos a isso!",
                [
                    ['label' => '✍️ Registo Manual', 'action' => 'triggerManual'],
                    ['label' => '📸 Scanner IA', 'action' => 'triggerScanner'],
                ]
            );
            return;
        }

        if (Str::contains($normalized, ['recebi', 'ganhei', 'receita', 'salário', 'salario'])) {
            $this->messages[] = $this->botMessage(
                "Vamos registar essa receita!",
                [['label' => '✍️ Registo Manual', 'action' => 'triggerIncomeManual']]
            );
            return;
        }

        if (Str::contains($normalized, ['objetivo', 'poupança', 'poupanca'])) {
            $this->showGoalsList();
            return;
        }

        if (Str::contains($normalized, ['investimento', 'carteira', 'portfolio', 'portfólio'])) {
            $this->showPortfolio();
            return;
        }

        if (Str::contains($normalized, ['assinatura', 'subscri'])) {
            $this->showSubscriptions();
            return;
        }

        if (Str::contains($normalized, ['orçamento', 'orcamento', 'budget'])) {
            $this->showBudgets();
            return;
        }

        if (Str::contains($normalized, ['suporte', 'ticket', 'reclama', 'problema técnico', 'problema tecnico'])) {
            $this->supportMenuRoot();
            return;
        }

        if (Str::contains($normalized, ['resumo', 'hoje'])) {
            $this->showDailySummary();
            return;
        }

        if (Str::contains($normalized, ['sugestão', 'sugestao', 'conselho', 'dica'])) {
            $this->getAiAdvice();
            return;
        }

        if (Str::contains($normalized, ['menu', 'opções', 'opcoes', 'ajuda'])) {
            $this->messages[] = $this->botMessage('Aqui tens as opções:', $this->mainMenuOptions());
            return;
        }

        $this->messages[] = $this->botMessage(
            "Ainda estou a aprender a responder a tudo 🙂 Por agora posso ajudar-te com isto:",
            $this->mainMenuOptions()
        );
    }

    // ===========================================================
    // HELPERS
    // ===========================================================
    private function userMessage(string $content): array
    {
        return ['id' => (string) Str::uuid(), 'role' => 'user', 'content' => $content];
    }

    private function botMessage(string $content, array $options = []): array
    {
        $message = ['id' => (string) Str::uuid(), 'role' => 'bot', 'content' => $content];

        if (!empty($options)) {
            $message['options'] = $options;
        }

        return $message;
    }

    private function firstName(): string
    {
        $name = Auth::user()->name;
        return explode(' ', $name)[0] ?? $name;
    }

    public function render()
    {
        return view('livewire.finance-bot');
    }
}
