<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{
    Expense,
    Income,
    Category,
    Goal,
    Investment,
    Subscription,
    Reminder
};
use Illuminate\Support\Facades\{
    Auth,
    Http,
    DB
};
use Illuminate\Support\Str;

class FinanceBot extends Component
{
    public $isOpen = false;
    public $messages = [];
    public $flow = null;
    public $flowData = [];
    public $userInput = '';
    public $isTyping = false;

   public function mount()
{
    $this->messages = session()->get('financebot_messages', []);

    if (empty($this->messages)) {
        $this->messages[] = $this->botMessage(
            "Olá, **" . $this->firstName() .
            "**! Sou o teu Pilot. Tenho acesso aos teus dados e posso registar tudo por ti. Como posso ajudar?"
        );
    }
}
private function saveMessages()
{
    session()->put('financebot_messages', $this->messages);
}
    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        $this->dispatch('message-sent');
    }

    /* ============================================================
     *  ENDPOINTS INTERNOS — ACESSO A DADOS DO UTILIZADOR
     * ============================================================ */

    public function getUserProfile()
    {
        return Auth::user();
    }

    public function getFamilyMembers()
    {
        return Auth::user()
            ->familyMembers()
            ->get();
    }

    public function getFitnessData()
    {
        return FitnessRecord::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getHealthRecords()
    {
        return HealthRecord::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();
    }

    /* ============================================================
     *  MÉTODOS PRIVADOS — RESUMOS E CONSULTAS RÁPIDAS
     * ============================================================ */

    private function getFitnessSummary()
    {
        return FitnessRecord::where('user_id', Auth::id())
            ->latest()
            ->first();
    }

    private function getHealthSummary()
    {
        return HealthRecord::where('user_id', Auth::id())
            ->latest()
            ->first();
    }

    private function getUserProfileData()
    {
        return Auth::user();
    }

    private function getFamilyData()
    {
        return Auth::user()
            ->familyMembers()
            ->get();
    }

















public function sendMessage()
{
    $text = trim($this->userInput);
    if ($text === '') return;

    $lower = mb_strtolower($text);

    // NOVO CHAT
    if (in_array($lower, ['novo chat', 'reset', 'começar de novo', 'novo'])) {
        $this->handleAction('system:new_chat');
        return;
    }

    // Adicionar mensagem do utilizador (APENAS UMA VEZ)
    $this->messages[] = $this->userMessage($text);
    $this->saveMessages();

    $this->userInput = '';
    $this->isTyping  = true;
    $this->dispatch('message-sent');

    // NLP PRINCIPAL
    if ($this->flow === null) {

        $intent = $this->detectIntent($text);

        // LISTAR LEMBRETES
        if ($intent === 'list_reminders') {

            $reminders = Reminder::where('workspace_id', Auth::user()->current_workspace_id)
                ->orderBy('remind_at')
                ->get();

            if ($reminders->isEmpty()) {
                $this->messages[] = $this->botMessage("Não tens lembretes ativos.");
            } else {
                $list = $reminders->map(fn($r) =>
                    "• {$r->title} — {$r->remind_at->format('d/m H:i')}"
                )->implode("\n");

                $this->messages[] = $this->botMessage("Aqui estão os teus lembretes:\n\n$list");
            }

            $this->isTyping = false;
            $this->dispatch('message-sent');
            return;
        }

        // EXTRAÇÃO DE VALOR
        $amount = null;
        if (preg_match('/(\d+[.,]?\d*)/', $text, $m)) {
            $amount = (float) str_replace(',', '.', $m[1]);
        }

        // CATEGORIA
        $category = $this->detectCategoryByName($text);

        // DESCRIÇÃO
        $desc = trim(
            str_replace([$m[1] ?? '', $category['name'] ?? ''], '', $text)
        );

        // DESPESA direta
if ($intent === 'expense' && $amount && $category['id']) {

    Expense::create([
        'user_id'      => Auth::id(),
        'workspace_id' => Auth::user()->current_workspace_id,
        'description'  => ucfirst($desc),
        'amount'       => $amount,
        'category_id'  => $category['id'],
        'spent_at'     => now(),
    ]);

    $this->messages[] = $this->botMessage(
        "Registei **{$amount}€** em **{$desc}** na categoria **{$category['name']}**. 💸"
    );

    $this->isTyping = false;
    $this->dispatch('message-sent');
    return;
}

        // RENDIMENTO DIRETO
        if ($intent === 'income' && $amount) {

            Income::create([
                'user_id'      => Auth::id(),
                'workspace_id' => Auth::user()->current_workspace_id,
                'description'  => $desc ?: 'Rendimento',
                'amount'       => $amount,
                'received_at'  => now(),
            ]);

            $this->messages[] = $this->botMessage(
                "Registei rendimento de **{$amount}€** como **" . ($desc ?: 'Rendimento') . "**."
            );

            $this->isTyping = false;
            $this->dispatch('message-sent');
            return;
        }

        // INVESTIMENTO DIRETO
        if ($intent === 'investment' && $amount) {

            Investment::create([
                'user_id'        => Auth::id(),
                'workspace_id'   => Auth::user()->current_workspace_id,
                'asset'          => $desc ?: 'Investimento',
                'amount'         => $amount,
                'current_value'  => $amount,
                'invested_at'    => now(),
            ]);

            $this->messages[] = $this->botMessage(
                "Registei investimento de **{$amount}€** em **" . ($desc ?: 'Investimento') . "**. 📈"
            );

            $this->isTyping = false;
            $this->dispatch('message-sent');
            return;
        }

        // SUBSCRIÇÃO DIRETA
        if ($intent === 'subscription' && $amount) {

            $cycle = str_contains($lower, 'anual') ? 'yearly' : 'monthly';

            Subscription::create([
                'user_id'        => Auth::id(),
                'workspace_id'   => Auth::user()->current_workspace_id,
                'name'           => $desc ?: 'Subscrição',
                'amount'         => $amount,
                'cycle'          => $cycle,
                'next_charge_at' => now()->addMonth(),
                'active'         => true,
            ]);

            $this->messages[] = $this->botMessage(
                "Registei subscrição de **{$amount}€ / {$cycle}** como **" . ($desc ?: 'Subscrição') . "**. 📅"
            );

            $this->isTyping = false;
            $this->dispatch('message-sent');
            return;
        }

        // LEMBRETE DIRETO
        if ($intent === 'reminder') {

            $date = $this->parseNaturalDate($text);

            preg_match('/(\d{1,2})h/', $text, $h);
            $hour = $h[1] ?? 9;

            $finalDate = $date->setTime($hour, 0);

            Reminder::create([
                'user_id'      => Auth::id(),
                'workspace_id' => Auth::user()->current_workspace_id,
                'title'        => $text,
                'remind_at'    => $finalDate,
            ]);

            $this->messages[] = $this->botMessage(
                "Lembrete criado para **" . $finalDate->format('d/m H:i') . "**. ⏰"
            );

            $this->isTyping = false;
            $this->dispatch('message-sent');
            return;
        }

        // FLUXOS GUIADOS
        if ($this->flow === null) {
            if (str_contains($lower, 'gastei') || str_contains($lower, 'despesa')) {
                $this->handleAction('flow:add_expense');
                $this->isTyping = false;
                return;
            }

            if (str_contains($lower, 'recebi') || str_contains($lower, 'rendimento')) {
                $this->handleAction('flow:add_income');
                $this->isTyping = false;
                return;
            }

            if (str_contains($lower, 'invest')) {
                $this->handleAction('flow:add_invest');
                $this->isTyping = false;
                return;
            }

            if (str_contains($lower, 'subscri') || str_contains($lower, 'mensalidade')) {
                $this->handleAction('flow:add_sub');
                $this->isTyping = false;
                return;
            }

            if (str_contains($lower, 'meta') || str_contains($lower, 'objetivo')) {
                $this->handleAction('flow:add_goal');
                $this->isTyping = false;
                return;
            }
        }
    }

    // FLUXO ATIVO
    if ($this->flow) {
        $this->processFlow($text);
        return;
    }

    // FALLBACK → IA
    $this->askAi($text);
    $this->isTyping = false;
}

























private function processFlow($text)
{
    $lower = mb_strtolower($text);

    // -----------------------------------------
    // AUTOCOMPLETE INTELIGENTE — "20 café"
    // -----------------------------------------
    if ($this->flow === null && preg_match('/(\d+)[^\d]+(.+)/', $lower, $m)) {
        $this->flowData['amount'] = (float)$m[1];
        $this->flowData['desc']   = ucfirst(trim($m[2]));
        $this->flow               = 'add_expense_confirm';

        $this->messages[] = $this->botMessageWithOptions(
            "Confirmas despesa de **{$m[1]}€** em **{$this->flowData['desc']}**?",
            [
                ['label' => 'Sim', 'action' => 'flow:add_expense_finish'],
                ['label' => 'Não', 'action' => 'flow:cancel'],
            ]
        );
        return;
    }

    // -----------------------------------------
    // VALIDAÇÃO DE NÚMEROS
    // -----------------------------------------
    if (in_array($this->flow, [
        'add_expense_step1',
        'add_income_step1',
        'add_sub_step2',
        'add_invest_step2',
        'add_goal_step2'
    ])) {
        if (!is_numeric(str_replace(',', '.', $text))) {
            $this->messages[] = $this->botMessage("Isso não parece um número válido. Tenta outra vez.");
            $this->isTyping = false;
            return;
        }
    }

    // -----------------------------------------
    // INTELIGÊNCIA: DETEÇÃO AUTOMÁTICA
    // -----------------------------------------

    // SUBSCRIÇÕES — deteção automática
    if ($this->flow === 'add_sub_step1') {
        $name = mb_strtolower($text);

        if (str_contains($name, 'netflix')) $this->flowData['cycle'] = 'monthly';
        if (str_contains($name, 'spotify')) $this->flowData['cycle'] = 'monthly';
        if (str_contains($name, 'prime'))   $this->flowData['cycle'] = 'yearly';

        if (isset($this->flowData['cycle'])) {
            $this->messages[] = $this->botMessage("Detectei que é uma subscrição {$this->flowData['cycle']}. Valor?");
            $this->flow = 'add_sub_step2';
            return;
        }
    }

    // INVESTIMENTOS — deteção automática
    if ($this->flow === 'add_invest_step1') {
        $asset = mb_strtolower($text);

        if (str_contains($asset, 'sp') || str_contains($asset, 's&p')) {
            $this->flowData['asset'] = 'S&P500';
        }

        if (str_contains($asset, 'ct')) {
            $this->flowData['asset'] = 'Certificados do Tesouro';
        }

        if (str_contains($asset, 'msci')) {
            $this->flowData['asset'] = 'MSCI World';
        }

        if (isset($this->flowData['asset'])) {
            $this->messages[] = $this->botMessage("Quanto investiste em {$this->flowData['asset']}?");
            $this->flow = 'add_invest_step2';
            return;
        }
    }

    // METAS — sugestões automáticas
    if ($this->flow === 'add_goal_step1') {
        $goal = mb_strtolower($text);

        if (str_contains($goal, 'casa')) {
            $this->messages[] = $this->botMessage("Objetivo típico: 20.000€ para entrada. Quanto queres definir?");
        }

        if (str_contains($goal, 'carro')) {
            $this->messages[] = $this->botMessage("Objetivo típico: 10.000€. Quanto queres definir?");
        }

        if (str_contains($goal, 'viagem')) {
            $this->messages[] = $this->botMessage("Objetivo típico: 1500€. Quanto queres definir?");
        }
    }

    // -----------------------------------------
    // SWITCH PRINCIPAL DOS FLUXOS
    // -----------------------------------------
    switch ($this->flow) {

        // -------------------------
        // METAS
        // -------------------------
        case 'add_goal_step1':
            $this->flowData['name'] = $text;
            $this->flow = 'add_goal_step2';
            $this->messages[] = $this->botMessage("Qual o valor objetivo?");
            break;

        case 'add_goal_step2':
            $this->flowData['target'] = (float) str_replace(',', '.', $text);
            $this->flow = 'add_goal_confirm';
            $this->messages[] = $this->botMessageWithOptions(
                "Confirmas meta **{$this->flowData['name']}** com objetivo de **{$this->flowData['target']}€**?",
                [
                    ['label' => 'Sim', 'action' => 'flow:add_goal_finish'],
                    ['label' => 'Não', 'action' => 'flow:cancel'],
                ]
            );
            break;

        // -------------------------
        // INVESTIMENTOS
        // -------------------------
        case 'add_invest_step1':
            $this->flowData['asset'] = $text;
            $this->flow = 'add_invest_step2';
            $this->messages[] = $this->botMessage("Quanto investiste?");
            break;

        case 'add_invest_step2':
            $this->flowData['amount'] = (float) str_replace(',', '.', $text);
            $this->flow = 'add_invest_confirm';
            $this->messages[] = $this->botMessageWithOptions(
                "Confirmas investimento de **{$this->flowData['amount']}€** em **{$this->flowData['asset']}**?",
                [
                    ['label' => 'Sim', 'action' => 'flow:add_invest_finish'],
                    ['label' => 'Não', 'action' => 'flow:cancel'],
                ]
            );
            break;

        // -------------------------
        // SUBSCRIÇÕES
        // -------------------------
        case 'add_sub_step1':
            $this->flowData['name'] = $text;
            $this->flow = 'add_sub_step2';
            $this->messages[] = $this->botMessage("Valor mensal?");
            break;

        case 'add_sub_step2':
            $this->flowData['amount'] = (float) str_replace(',', '.', $text);
            $this->flow = 'add_sub_step3';
            $this->messages[] = $this->botMessageWithOptions(
                "Ciclo de cobrança?",
                [
                    ['label' => 'Mensal', 'action' => 'flow:add_sub_cycle_monthly'],
                    ['label' => 'Anual',  'action' => 'flow:add_sub_cycle_yearly'],
                ]
            );
            break;

        case 'add_sub_confirm':
            $this->messages[] = $this->botMessageWithOptions(
                "Confirmas subscrição **{$this->flowData['name']}** por **{$this->flowData['amount']}€ / {$this->flowData['cycle']}**?",
                [
                    ['label' => 'Sim', 'action' => 'flow:add_sub_finish'],
                    ['label' => 'Não', 'action' => 'flow:cancel'],
                ]
            );
            break;

        // -------------------------
        // DESPESAS — ULTRA PREMIUM
        // -------------------------
        case 'add_expense_step1':
            $this->flowData['amount'] = (float) str_replace(',', '.', $text);
            $this->flowData['date']   = $text;

            if ($last = $this->recallHabit('last_expense_category')) {
                $this->messages[] = $this->botMessageWithOptions(
                    "Última categoria usada: **{$last}**. Queres repetir?",
                    [
                        ['label' => 'Sim', 'action' => "flow:set_category_{$last}"],
                        ['label' => 'Não', 'action' => 'flow:ask_expense_desc'],
                    ]
                );
                $this->flow = 'waiting_category_repeat';
                break;
            }

            $this->flow = 'add_expense_step2';
            $this->messages[] = $this->botMessage("Descrição da despesa?");
            break;

        case 'add_expense_step2':
            $this->flowData['desc'] = $text;

            $this->flow = 'add_expense_category';
            $this->messages[] = $this->botMessageWithOptions(
                "Escolhe a categoria:",
                $this->getCategories()
            );
            break;

        case 'add_expense_category':
            $this->flow = 'add_expense_confirm';

            $this->messages[] = $this->botMessageWithOptions(
                "Confirmas registar **{$this->flowData['amount']}€** em **{$this->flowData['desc']}**?",
                [
                    ['label' => 'Sim', 'action' => 'flow:add_expense_finish'],
                    ['label' => 'Não', 'action' => 'flow:cancel'],
                ]
            );
            break;

        // -------------------------
        // RENDIMENTOS
        // -------------------------
        case 'add_income_step1':
            $this->flowData['amount'] = (float) str_replace(',', '.', $text);
            $this->flow = 'add_income_step2';
            $this->messages[] = $this->botMessage("Descrição do rendimento?");
            break;

        case 'add_income_step2':
            $this->flowData['desc'] = $text;
            $this->flow = 'add_income_confirm';

            $this->messages[] = $this->botMessageWithOptions(
                "Confirmas registar **{$this->flowData['amount']}€** como **{$this->flowData['desc']}**?",
                [
                    ['label' => 'Sim', 'action' => 'flow:add_income_finish'],
                    ['label' => 'Não', 'action' => 'flow:cancel'],
                ]
            );
            break;

        // -------------------------
        // FALLBACK
        // -------------------------
        default:
            $this->messages[] = $this->botMessage("Fluxo desconhecido.");
            $this->endFlow();
    }

    $this->isTyping = false;
    $this->dispatch('message-sent');
}
























private function recallHabit($key)
{
    return Auth::user()->settings[$key] ?? null;
}

private function parseNaturalDate($text)
{
    if (!$text) {
        return now();
    }

    $lower = mb_strtolower($text);

    if (str_contains($lower, 'hoje')) return now();
    if (str_contains($lower, 'ontem')) return now()->subDay();
    if (str_contains($lower, 'anteontem')) return now()->subDays(2);

    if (preg_match('/há (\d+) dias/', $lower, $m)) {
        return now()->subDays((int)$m[1]);
    }

    if (str_contains($lower, 'semana passada')) return now()->subWeek();
    if (str_contains($lower, 'mês passado')) return now()->subMonth();

    return now();
}

private function botMessageWithOptions($text, $options)
{
    return [
        'id'      => uniqid(),
        'role'    => 'bot',
        'content' => $text,
        'options' => $options,
    ];
}

private function getGlobalContext(): string
{
    $user       = Auth::user();
    $ws         = $user->currentWorkspace;
    $monthStart = now()->startOfMonth();

    $spent = Expense::where('workspace_id', $ws->id)
        ->where('spent_at', '>=', $monthStart)
        ->sum('amount');

    $earned = Income::where('workspace_id', $ws->id)
        ->where('received_at', '>=', $monthStart)
        ->sum('amount');

    $categories = Category::where('workspace_id', $ws->id)
        ->pluck('name', 'id')
        ->toArray();

    $goals = Goal::where('workspace_id', $ws->id)
        ->get()
        ->map(fn($g) => "{$g->name} ({$g->current_amount}/{$g->target_amount})")
        ->implode(', ');

    $subs = Subscription::where('workspace_id', $ws->id)
        ->get()
        ->map(fn($s) => "{$s->name} ({$s->amount}€/{$s->cycle})")
        ->implode(', ');

    $investments = Investment::where('workspace_id', $ws->id)
        ->get()
        ->map(fn($i) => "{$i->asset} ({$i->amount}€)")
        ->implode(', ');

    // 🔥 NOVO: lembretes
    $reminders = Reminder::where('workspace_id', $ws->id)
        ->orderBy('remind_at')
        ->get()
        ->map(fn($r) => "{$r->title} ({$r->remind_at->format('d/m H:i')})")
        ->implode(', ');

    return "
TU ÉS O FINANCE PILOT.
REGRAS:
- Respostas curtas (máx. 2 frases).
- Fala sempre em português informal.
- Nunca dês links para /admin nem digas que tens acesso ao backend.

UTILIZADOR:
- Nome: {$user->name}

DADOS FINANCEIROS:
- Saldo Mensal: Entradas " . number_format($earned, 2) . "€ | Saídas " . number_format($spent, 2) . "€.
- Categorias: " . json_encode($categories) . ".
- Metas: " . ($goals ?: 'Nenhuma') . ".
- Subscrições: " . ($subs ?: 'Nenhuma') . ".
- Investimentos: " . ($investments ?: 'Nenhum') . ".
- Lembretes: " . ($reminders ?: 'Nenhum') . ".

PODER DE ESCRITA:
- Se o utilizador quiser registar algo, pergunta primeiro se confirma.
- Depois da confirmação, escreve NO FIM da resposta uma ação no formato:
  [ACTION:CREATE_EXPENSE|amount:XX.XX|desc:Descrição]
  [ACTION:CREATE_INCOME|amount:XX.XX|desc:Descrição]
  [ACTION:CREATE_SUB|name:Nome|amount:XX.XX|cycle:monthly]
  [ACTION:CREATE_INVEST|asset:Nome|amount:XX.XX]
  [ACTION:CREATE_GOAL|name:Nome|target:XX.XX]
  [ACTION:CREATE_REMINDER|title:Texto|date:YYYY-MM-DD HH:MM]

NUNCA INVENTES CAMPOS QUE NÃO CONSIGAS PREENCHER.
";
}

private function askAi(string $userText)
{
    try {
        $history = collect($this->messages)
            ->take(-6)
            ->map(fn($m) => [
                'role'    => $m['role'] === 'user' ? 'user' : 'assistant',
                'content' => $m['content'],
            ])
            ->toArray();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model'    => 'anthropic/claude-3-haiku',
            'messages' => array_merge(
                [['role' => 'system', 'content' => $this->getGlobalContext()]],
                $history,
                [['role' => 'user', 'content' => $userText]]
            ),
        ]);

        $reply = $response->json('choices.0.message.content') ?? "Erro ao processar.";

        if (Str::contains($reply, '[ACTION:')) {
            $this->executeAction($reply);
            $reply = preg_replace('/

\[ACTION:.*?\]

/s', ' ✅', $reply);
        }

        $this->messages[] = $this->botMessage($reply);
    } catch (\Exception $e) {
        $this->messages[] = $this->botMessage("Estou com soluços técnicos. Tenta de novo.");
    }

    $this->isTyping = false;
    $this->dispatch('message-sent');
}

private function executeAction(string $text): void
{

    if (!preg_match('/

\[ACTION:(.*?)\]

/s', $text, $matches)) {
        return;
    }

    $payload = $matches[1];
    $parts   = explode('|', $payload);

    if (count($parts) < 2) {
        return;
    }

    $type   = $parts[0];
    $params = collect(array_slice($parts, 1))
        ->mapWithKeys(function ($pair) {
            $split = explode(':', $pair, 2);
            if (count($split) !== 2) {
                return [];
            }
            [$key, $value] = $split;
            return [trim($key) => trim($value)];
        });

    $wsId   = Auth::user()->current_workspace_id;
    $userId = Auth::id();

    match ($type) {
        'CREATE_REMINDER' => Reminder::create([
    'user_id'      => $userId,
    'workspace_id' => $wsId,
    'title'        => $params->get('title'),
    'remind_at'    => $params->get('date'),
]),

        'CREATE_EXPENSE' => Expense::create([
            'user_id'      => $userId,
            'workspace_id' => $wsId,
            'description'  => $params->get('desc'),
            'amount'       => (float) $params->get('amount', 0),
            'spent_at'     => now(),
        ]),

        'CREATE_INCOME' => Income::create([
            'user_id'      => $userId,
            'workspace_id' => $wsId,
            'description'  => $params->get('desc'),
            'amount'       => (float) $params->get('amount', 0),
            'received_at'  => now(),
        ]),

        'CREATE_SUB' => Subscription::create([
            'user_id'        => $userId,
            'workspace_id'   => $wsId,
            'name'           => $params->get('name'),
            'amount'         => (float) $params->get('amount', 0),
            'cycle'          => $params->get('cycle', 'monthly'),
            'next_charge_at' => now()->addMonth(),
            'active'         => true,
        ]),

        'CREATE_INVEST' => Investment::create([
            'user_id'       => $userId,
            'workspace_id'  => $wsId,
            'asset'         => $params->get('asset'),
            'amount'        => (float) $params->get('amount', 0),
            'current_value' => (float) $params->get('amount', 0),
            'invested_at'   => now(),
        ]),

        'CREATE_GOAL' => Goal::create([
            'user_id'        => $userId,
            'workspace_id'   => $wsId,
            'name'           => $params->get('name'),
            'target_amount'  => (float) $params->get('target', 0),
            'current_amount' => 0,
            'status'         => 'active',
        ]),

        default => null,
    };
}































    private function userMessage($content): array
    {
        return [
            'id'      => uniqid(),
            'role'    => 'user',
            'content' => $content,
        ];
    }

    private function botMessage($content): array
    {
        return [
            'id'      => uniqid(),
            'role'    => 'bot',
            'content' => $content,
        ];
    }
public function handleAction($action)
{
    // -----------------------------------------
    // 1. AÇÕES ESPECIAIS: CATEGORIAS DINÂMICAS
    // -----------------------------------------
    if (str_starts_with($action, 'flow:set_category_')) {
        $id = str_replace('flow:set_category_', '', $action);
        $this->flowData['category_id'] = $id;

        $this->messages[] = $this->botMessage("Categoria definida!");
        $this->flow = 'add_expense_step2';
        $this->messages[] = $this->botMessage("Descrição da despesa?");
        $this->dispatch('message-sent');
        return;
    }

    // -----------------------------------------
    // 2. MATCH PRINCIPAL
    // -----------------------------------------
    $result = match($action) {

        // -----------------------------------------
        // CATEGORIAS SUGERIDAS (fluxo antigo)
        // -----------------------------------------
        'flow:set_cat_food'      => fn() => $this->flowData['category'] = 'Alimentação',
        'flow:set_cat_transport' => fn() => $this->flowData['category'] = 'Transporte',
        'flow:set_cat_fun'       => fn() => $this->flowData['category'] = 'Lazer',
        'flow:set_cat_none'      => fn() => $this->flowData['category'] = null,

        // -----------------------------------------
        // INVESTIMENTOS
        // -----------------------------------------
        'flow:portfolio' => function () {
            $inv = Investment::where('workspace_id', Auth::user()->current_workspace_id)->get();

            if ($inv->isEmpty()) {
                $this->messages[] = $this->botMessage("Ainda não tens investimentos.");
                return;
            }

            $list = $inv->map(fn($i) =>
                "{$i->asset}: {$i->current_value}€"
            )->implode("\n");

            $this->messages[] = $this->botMessage("A tua carteira:\n\n$list");
        },

        // -----------------------------------------
        // SUBSCRIÇÕES — PRÓXIMOS DÉBITOS
        // -----------------------------------------
        'flow:subs_upcoming' => function () {
            $subs = Subscription::where('workspace_id', Auth::user()->current_workspace_id)
                ->orderBy('next_charge_at')
                ->get();

            if ($subs->isEmpty()) {
                $this->messages[] = $this->botMessage("Não tens subscrições ativas.");
                return;
            }

            $list = $subs->map(fn($s) =>
                "{$s->name}: {$s->amount}€ — {$s->next_charge_at->format('d/m')}"
            )->implode("\n");

            $this->messages[] = $this->botMessage("Próximos débitos:\n\n$list");
        },
'system:new_chat' => function () {
    $this->messages = [
        $this->botMessage("Novo chat iniciado. Como posso ajudar?")
    ];
    $this->flow = null;
    $this->flowData = [];
},
     // -----------------------------------------
        // METAS
        // -----------------------------------------
        'flow:add_goal' => function () {
            $this->startFlow('add_goal_step1');
            $this->messages[] = $this->botMessage("Nome da meta?");
        },

        'flow:add_goal_finish' => function () {
            Goal::create([
                'user_id'        => Auth::id(),
                'workspace_id'   => Auth::user()->current_workspace_id,
                'name'           => $this->flowData['name'],
                'target_amount'  => $this->flowData['target'],
                'current_amount' => 0,
                'status'         => 'active',
            ]);

            $this->messages[] = $this->botMessage("Meta criada! 🎯");
            $this->endFlow();
        },

        // -----------------------------------------
        // INVESTIMENTOS
        // -----------------------------------------
        'flow:add_invest' => function () {
            $this->startFlow('add_invest_step1');
            $this->messages[] = $this->botMessage("Nome do ativo (ex: S&P500, CT 2024)?");
        },

        'flow:add_invest_finish' => function () {
            Investment::create([
                'user_id'        => Auth::id(),
                'workspace_id'   => Auth::user()->current_workspace_id,
                'asset'          => $this->flowData['asset'],
                'amount'         => $this->flowData['amount'],
                'current_value'  => $this->flowData['amount'],
                'invested_at'    => now(),
            ]);

            $this->messages[] = $this->botMessage("Investimento registado! 📈");
            $this->endFlow();
        },

        // -----------------------------------------
        // SUBSCRIÇÕES
        // -----------------------------------------
        'flow:add_sub' => function () {
            $this->startFlow('add_sub_step1');
            $this->messages[] = $this->botMessage("Nome da subscrição?");
        },

        'flow:add_sub_cycle_monthly' => function () {
            $this->flowData['cycle'] = 'monthly';
            $this->flow = 'add_sub_confirm';
            $this->processFlow('');
        },

        'flow:add_sub_cycle_yearly' => function () {
            $this->flowData['cycle'] = 'yearly';
            $this->flow = 'add_sub_confirm';
            $this->processFlow('');
        },

        'flow:add_sub_finish' => function () {
            Subscription::create([
                'user_id'        => Auth::id(),
                'workspace_id'   => Auth::user()->current_workspace_id,
                'name'           => $this->flowData['name'],
                'amount'         => $this->flowData['amount'],
                'cycle'          => $this->flowData['cycle'],
                'next_charge_at' => now()->addMonth(),
                'active'         => true,
            ]);

            $this->messages[] = $this->botMessage("Subscrição criada com sucesso! 📅");
            $this->endFlow();
        },

        // -----------------------------------------
        // DESPESAS — INICIAR
        // -----------------------------------------
        'flow:add_expense' => function () {
            $this->startFlow('add_expense_step1');
            $this->messages[] = $this->botMessage("Quanto gastaste?");
            $this->messages[] = $this->botMessage("Quando foi a despesa? (ex: hoje, ontem, há 3 dias)");
        },

        // -----------------------------------------
        // DESPESAS — FINALIZAR
        // -----------------------------------------
        'flow:add_expense_finish' => function () {

            Expense::create([
                'user_id'      => Auth::id(),
                'workspace_id' => Auth::user()->current_workspace_id,
                'description'  => $this->flowData['desc'],
                'amount'       => $this->flowData['amount'],
                'spent_at'     => $this->parseNaturalDate($this->flowData['date'] ?? ''),
                'category_id'  => $this->flowData['category_id'] ?? null,
            ]);

            if (isset($this->flowData['category_id'])) {
                $this->rememberHabit('last_expense_category', $this->flowData['category_id']);
            }

            $this->messages[] = $this->botMessage("Despesa registada com sucesso! 💸");
            $this->endFlow();
        },

        // -----------------------------------------
        // RENDIMENTOS
        // -----------------------------------------
        'flow:add_income' => function () {
            $this->startFlow('add_income_step1');
            $this->messages[] = $this->botMessage("Quanto recebeste?");
        },

        'flow:add_income_finish' => function () {
            Income::create([
                'user_id'      => Auth::id(),
                'workspace_id' => Auth::user()->current_workspace_id,
                'description'  => $this->flowData['desc'],
                'amount'       => $this->flowData['amount'],
                'received_at'  => now(),
            ]);

            $this->messages[] = $this->botMessage("Rendimento registado! 📈");
            $this->endFlow();
        },

        // -----------------------------------------
        // MENUS
        // -----------------------------------------
        'menu:root' => function () {
            $this->messages[] = $this->botMessageWithOptions(
                "Escolhe uma área para explorar:",
                [
                    ['label' => '💰 Finanças',     'action' => 'menu:financas'],
                    ['label' => '📊 Investimentos', 'action' => 'menu:invest'],
                    ['label' => '📅 Subscrições',   'action' => 'menu:subs'],
                    ['label' => '🎯 Metas',         'action' => 'menu:goals'],
                    ['label' => '⚙️ Sistema',       'action' => 'menu:system'],
                ]
            );
        },

        'menu:financas' => function () {
            $this->messages[] = $this->botMessageWithOptions(
                "Gestão financeira:",
                [
                    ['label' => '➕ Registar despesa',   'action' => 'flow:add_expense'],
                    ['label' => '➕ Registar rendimento','action' => 'flow:add_income'],
                    ['label' => '📄 Resumo mensal',      'action' => 'flow:summary'],
                    ['label' => '⬅️ Voltar',             'action' => 'menu:root'],
                ]
            );
        },

        'menu:invest' => function () {
            $this->messages[] = $this->botMessageWithOptions(
                "Investimentos:",
                [
                    ['label' => '➕ Novo investimento', 'action' => 'flow:add_invest'],
                    ['label' => '📈 Ver carteira',      'action' => 'flow:portfolio'],
                    ['label' => '⬅️ Voltar',            'action' => 'menu:root'],
                ]
            );
        },

        'menu:subs' => function () {
            $this->messages[] = $this->botMessageWithOptions(
                "Subscrições:",
                [
                    ['label' => '➕ Nova subscrição', 'action' => 'flow:add_sub'],
                    ['label' => '📅 Próximos débitos','action' => 'flow:subs_upcoming'],
                    ['label' => '⬅️ Voltar',          'action' => 'menu:root'],
                ]
            );
        },

        'menu:goals' => function () {
            $this->messages[] = $this->botMessageWithOptions(
                "Metas:",
                [
                    ['label' => '➕ Nova meta',     'action' => 'flow:add_goal'],
                    ['label' => '📊 Ver progresso','action' => 'flow:goals_progress'],
                    ['label' => '⬅️ Voltar',        'action' => 'menu:root'],
                ]
            );
        },

        'menu:system' => function () {
    $this->messages[] = $this->botMessageWithOptions(
        "Sistema:",
        [
            ['label' => '🧹 Limpar chat', 'action' => 'system:clear'],
            ['label' => '🆕 Novo Chat',   'action' => 'system:new_chat'],
            ['label' => '⬅️ Voltar',      'action' => 'menu:root'],
        ]
    );
},

        // -----------------------------------------
        // SISTEMA
        // -----------------------------------------
        'system:clear' => function () {
            $this->messages = [
                $this->botMessage("Chat limpo. Como posso ajudar?")
            ];
        },

        default => null,
    };

    // -----------------------------------------
    // 3. EXECUTAR A FUNÇÃO DO MATCH
    // -----------------------------------------
    if (is_callable($result)) {
        $result();
    }

    $this->dispatch('message-sent');
}

private function getCategories()
{
    return Category::where('workspace_id', Auth::user()->current_workspace_id)
        ->orderBy('name')
        ->get()
        ->map(fn($c) => [
            'label' => $c->name,
            'action' => "flow:set_category_{$c->id}"
        ])
        ->toArray();
}

private function startFlow($name)
{
    $this->flow = $name;
    $this->flowData = [];
}
private function endFlow()
{
    $this->flow = null;
    $this->flowData = [];
}

private function detectCategoryByName($text)
{
    $lower = mb_strtolower($text);

    // Normalizar acentos
    $normalize = fn($s) => str_replace(
        ['á','à','ã','â','é','ê','í','ó','ô','õ','ú','ç'],
        ['a','a','a','a','e','e','i','o','o','o','u','c'],
        mb_strtolower($s)
    );

    $lowerNorm = $normalize($lower);

    // Buscar categorias reais da BD
    $categories = Category::where('workspace_id', Auth::user()->current_workspace_id)->get();

    // SINÓNIMOS POR CATEGORIA
    $synonyms = [
        'saude' => ['saude','saúde','medico','médico','consulta','hospital','dentista','clinica','clínica'],
        'alimentacao' => ['comida','supermercado','restaurante','cafe','lanche','mercearia'],
        'transporte' => ['uber','taxi','combustivel','gasolina','metro','autocarro','parque','estacionamento'],
        'lazer' => ['cinema','jogo','netflix','spotify','teatro','bar'],
        'fitness' => ['ginasio','ginásio','gym','treino','personal','corrida','exercicio'],
    ];

    foreach ($categories as $cat) {

        $catNorm = $normalize($cat->name);

        // MATCH DIRETO
        if (str_contains($lowerNorm, $catNorm)) {
            return ['id' => $cat->id, 'name' => $cat->name];
        }

        // MATCH POR SINÓNIMOS
        if (isset($synonyms[$catNorm])) {
            foreach ($synonyms[$catNorm] as $word) {
                if (str_contains($lowerNorm, $normalize($word))) {
                    return ['id' => $cat->id, 'name' => $cat->name];
                }
            }
        }
    }

    return ['id' => null, 'name' => null];
}

private function detectIntent(string $text): ?string
{
    $lower = mb_strtolower($text);
if (
    str_contains($lower, 'lembrete') ||
    str_contains($lower, 'lembrar') ||
    str_contains($lower, 'recorda') ||
    str_contains($lower, 'avisa')
) {
    return 'reminder';
}
if (
    str_contains($lower, 'lembrete') &&
    (str_contains($lower, 'tenho') || str_contains($lower, 'ver') || str_contains($lower, 'quais'))
) {
    return 'list_reminders';
}
    // Despesa
    if (
        str_contains($lower, 'gastei') ||
        str_contains($lower, 'paguei') ||
        str_contains($lower, 'despesa') ||
        str_contains($lower, 'custou')
    ) {
        return 'expense';
    }

    // Rendimento
    if (
        str_contains($lower, 'recebi') ||
        str_contains($lower, 'ganhei') ||
        str_contains($lower, 'rendimento') ||
        str_contains($lower, 'salário') ||
        str_contains($lower, 'salario')
    ) {
        return 'income';
    }

    // Subscrição
    if (
        str_contains($lower, 'subscri') ||
        str_contains($lower, 'mensalidade') ||
        str_contains($lower, 'pagamento mensal') ||
        str_contains($lower, 'renovação')
    ) {
        return 'subscription';
    }

    // Investimento
    if (
        str_contains($lower, 'investi') ||
        str_contains($lower, 'investimento') ||
        str_contains($lower, 'ações') ||
        str_contains($lower, 'acções') ||
        str_contains($lower, 'etf') ||
        str_contains($lower, 'bolsa')
    ) {
        return 'investment';
    }

    // Meta
    if (
        str_contains($lower, 'meta') ||
        str_contains($lower, 'objetivo') ||
        str_contains($lower, 'objectivo')
    ) {
        return 'goal';
    }

    return null;
}

    private function firstName(): string
    {
        return explode(' ', Auth::user()->name)[0];
    }

    public function render()
    {
        return view('livewire.finance-bot');
    }
}
