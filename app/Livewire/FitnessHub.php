<?php

namespace App\Livewire;

use Illuminate\Support\Facades\{Auth, Cache, Http, Storage};
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\FitnessActivity;
use App\Models\FitnessGoal;
use App\Models\ConnectedDevice;

#[Layout('components.layouts.app')]
class FitnessHub extends Component
{
    use WithFileUploads;

    // --- CHAT IA ---
    public array $messages = [];
    public string $chatInput = '';
    public bool $isTyping = false;

    // --- NOVA ATIVIDADE ---
    public string $activityType = 'corrida';
    public string $searchDevice = '';
public $searchResult = null;
public bool $isSearching = false;
    public float $activityDistance = 0;
    public int $activityDuration = 0; // minutos
    public float $activityCalories = 0;
    public string $activityNotes = '';
    public string $activityDate = '';
    public $activityPhoto = null;

    // --- META FITNESS ---
    public string $goalName = '';
    public string $goalType = 'distancia_semanal';
    public float $goalTarget = 0;
    public string $goalDeadline = '';

    // Modal states
    public bool $showActivityModal = false;
    public bool $showGoalModal = false;
    public bool $showPhotoAnalysis = false;
    public string $photoAnalysisResult = '';
    public ?int $editingGoalId = null;







    // Adiciona estas ao topo da tua classe PHP
public string $activityApp = '';
public string $activityLocation = '';
public string $activityTime = '';
public float $activityTotalCalories = 0;
public string $pace = '';
public string $hrAvg = '';
public string $hrMax = '';
public string $steps = '';
public string $cadenceAvg = '';
public string $strideAvg = '';
public string $zoneLight = '';
public string $zoneIntensive = '';
public string $zoneAerobic = '';
public string $zoneAnaerobic = '';
public string $zoneVO2Max = '';
public string $teAerobic = '';
public string $teAnaerobic = '';
public string $recoveryTime = '';
public string $trainingLoad = '';






public ?FitnessActivity $selectedActivity = null;
public bool $showDetailsModal = false;

    // --- DISPOSITIVOS CONECTADOS ---
    public bool $showDeviceModal = false;
    public string $deviceName = '';
    public string $deviceBrand = 'Xiaomi';
    public string $deviceEmoji = '⌚';
    public bool $showMiFitnessGuide = false;
    public $importFile = null;
    public bool $isImporting = false;

// Novo método para abrir os detalhes
public function viewActivity(int $id)
{
    $this->selectedActivity = FitnessActivity::findOrFail($id);
    $this->showDetailsModal = true;
}


    // Frases motivacionais por tipo de atividade
private array $motivationalQuotes = [
    'corrida' => [
        'Se me vires a correr, foge também, porque algo muito perigoso vem atrás de mim. 🏃‍♂️💨',
        'O meu ritmo de corrida é: "Parece que estou a ter um ataque, mas é só o cardio". 🥵',
        'Corro porque a minha relação com a pizza é tóxica e preciso de espaço. 🍕🏃',
        'O GPS perguntou-me se eu estava a caminhar... eu estava a dar o meu máximo a correr. 🐢',
    ],
    'ciclismo' => [
        'Gastei 3000€ numa bicicleta para pesar menos 1kg, quando podia só ter cortado o cabelo. 🚴‍♂️💸',
        'Licra: a forma mais aerodinâmica de mostrar ao mundo o que comeste ao almoço. 🩳🤢',
        'Subir esta montanha é 10% pernas e 90% arrepender-me das minhas escolhas de vida. ⛰️🚲',
    ],
    'ginasio' => [
        'Vim ao ginásio só para poder tomar um banho descansado sem ninguém me pedir nada. 🚿🧘‍♂️',
        'Leg Day: o único dia em que o meu carro parece estar estacionado a 40km de distância. 🐧😭',
        'O meu exercício favorito é o levantamento de garfo em 3 séries de repetições infinitas. 🍴🏋️‍♂️',
        'Treinar sem postar no Instagram queima zero calorias. É ciência, juro. 📸💪',
    ],
    'natacao' => [
        'Nado porque na água ninguém consegue ver que estou a chorar. 🏊‍♂️💧',
        'A minha técnica de natação chama-se "Tentar não afogar enquanto pareço um golfinho". 🐬🥴',
    ],
    'caminhada' => [
        'Caminhar 10km é ótimo, especialmente se houver uma bifana e uma imperial à espera no fim. 🚶‍♂️🍺',
        'O meu "cardio" de hoje foi procurar o comando da TV durante 20 minutos. 🚶‍♂️📺',
    ],
    'yoga' => [
        'Yoga: a arte de te dobrares como um pretzel e rezares para não dar um pum. 🥨💨',
        'A minha posição favorita de Yoga é o "Cadáver Exausto" (Savasana) por 40 minutos. 🧘‍♂️😴',
    ],
    'default' => [
        'O meu corpo é um templo, mas atualmente está classificado como património em ruínas. 🏛️🏚️',
        'Suar é a gordura a chorar. E a minha está a ter uma crise de histeria. 😭🔥',
        'Dizem que o exercício dá anos de vida. Até agora só me deu dores no corpo todo. 💀⚡',
    ],
];

    public function mount()
    {
        $this->activityDate = now()->format('Y-m-d');

        // Mensagem de boas-vindas do coach IA
        $this->messages = [
            [
                'role' => 'assistant',
                'text' => '👋 Olá! Sou o teu **Coach IA**. Estou aqui para analisar os teus treinos, dar conselhos personalizados e ajudar-te a atingir os teus objetivos. Podes perguntar-me qualquer coisa sobre treino, nutrição ou recuperação!',
                'time' => now()->format('H:i'),
            ]
        ];
    }

    // ─── CHAT IA ────────────────────────────────────────────────────────────
public function openModal()
{
    $this->showActivityModal = true;
    $this->dispatch('modal-opened'); // para debug
    \Log::info('Modal aberto: ' . ($this->showActivityModal ? 'true' : 'false'));
}
    public function sendChat()
    {
        if (blank($this->chatInput)) return;

        $userMessage = trim($this->chatInput);
        $this->chatInput = '';

        $this->messages[] = [
            'role' => 'user',
            'text' => $userMessage,
            'time' => now()->format('H:i'),
        ];

        $this->isTyping = true;
        $this->dispatch('chat-sent');

        // Contexto com dados reais do utilizador
        $stats = $this->getUserStats();
        $context = "És um coach de fitness pessoal entusiasta e motivador. Respondes sempre em Português de Portugal. Aqui estão os dados do utilizador: Total de atividades este mês: {$stats['total_activities']}. Distância total: {$stats['total_distance']} km. Calorias queimadas: {$stats['total_calories']} kcal. Tipo de atividade preferida: {$stats['top_activity']}. Responde de forma concisa (máx 3 parágrafos), usa emojis com moderação e dá sempre um conselho prático.";

        // Construir histórico para a API
        $apiMessages = [];
        foreach (array_slice($this->messages, -6) as $msg) {
            if ($msg['role'] === 'user') {
                $apiMessages[] = ['role' => 'user', 'content' => $msg['text']];
            } elseif ($msg['role'] === 'assistant' && $msg['text'] !== $this->messages[0]['text']) {
                $apiMessages[] = ['role' => 'assistant', 'content' => $msg['text']];
            }
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                    'Content-Type'  => 'application/json',
                    'HTTP-Referer'  => config('app.url'),
                    'X-Title'       => config('app.name'),
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model'      => 'anthropic/claude-haiku-4-5',
                    'max_tokens' => 500,
                    'messages'   => array_merge(
                        [['role' => 'system', 'content' => $context]],
                        $apiMessages
                    ),
                ]);

            $reply = $response->json('choices.0.message.content')
                ?? 'Desculpa, não consegui processar a tua pergunta. Tenta novamente!';
        } catch (\Exception $e) {
            $reply = '⚠️ Sem ligação ao coach IA. Verifica a tua ligação à internet.';
        }

        $this->isTyping = false;
        $this->messages[] = [
            'role' => 'assistant',
            'text' => $reply,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('chat-reply');
    }

    // ─── ANÁLISE DE FOTO COM IA (preenche campos automaticamente) ──────────────
public function searchSmartwatch()
{
    if (empty($this->searchDevice)) return;

    $this->isSearching = true;
    $this->searchResult = null;

    try {
        $prompt = "És um especialista em wearables. O utilizador tem o dispositivo: \"{$this->searchDevice}\". Devolve APENAS um JSON: {\"name\":\"...\",\"brand\":\"...\",\"emoji\":\"...\",\"battery\":\"...\",\"gps\":true,\"health_metrics\":[],\"sports\":[],\"apps\":[],\"api_available\":true,\"integration_tip\":\"...\"}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'google/gemini-2.0-flash-lite-001', // ou o modelo que preferires
            'messages' => [['role' => 'user', 'content' => $prompt]]
        ]);

        $raw = preg_replace('/```json|```/i', '', $response->json('choices.0.message.content'));
        $this->searchResult = json_decode(trim($raw), true);

    } catch (\Exception $e) {
        $this->dispatch('toast', text: 'Erro ao ligar à IA.');
    }

    $this->isSearching = false;
}
 public function analyzePhoto()
{
    if (!$this->activityPhoto) return;

    $this->showPhotoAnalysis = true;
    $this->photoAnalysisResult = '🔍 O Coach está a analisar todos os detalhes...';

    try {
        $imageData = base64_encode(file_get_contents($this->activityPhoto->getRealPath()));
        $mimeType  = $this->activityPhoto->getMimeType() ?: 'image/jpeg';

        // Prompt expandido para capturar TUDO o que está na imagem do Eduardo
        $prompt = 'Analisa esta imagem de fitness. Devolve APENAS um objeto JSON.
        Campos: {
            "type":"corrida", "distance":10.01, "duration":55, "calories":562, "total_calories":652,
            "date":"2026-06-18", "time":"19:45", "app":"Mi Fitness", "location":"Gradil",
            "pace":"5:31", "hr_avg":"186", "hr_max":"201", "steps":"9072", "cadence":"163", "stride":"109",
            "z_light":"00:01", "z_int":"00:02", "z_aer":"02:17", "z_ana":"12:24", "z_vo2":"40:16",
            "te_aer":"5.0", "te_ana":"2.5", "recovery":"72", "load":"311",
            "comment":"frase sarcastica em PT-PT"
        }';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model'      => 'google/gemini-2.5-flash', // Mantido conforme o seu código
            'max_tokens' => 1000,
            'messages'   => [[
                'role'    => 'user',
                'content' => [
                    ['type' => 'text',      'text'      => $prompt],
                    ['type' => 'image_url', 'image_url' => ['url' => 'data:' . $mimeType . ';base64,' . $imageData]],
                ],
            ]],
        ]);

        $rawText = $response->json('choices.0.message.content') ?? '';
        $rawText = preg_replace('/```json|```/i', '', $rawText);
        if (preg_match('/\{.*\}/s', $rawText, $matches)) {
            $data = json_decode($matches[0], true);

            // --- MAPEAR PARA AS VARIÁVEIS ---
            $this->activityType     = $data['type'] ?? 'corrida';
            $this->activityDistance = (float)($data['distance'] ?? 0);
            $this->activityDuration = (int)($data['duration'] ?? 0);
            $this->activityCalories = (float)($data['calories'] ?? 0);
            $this->activityTotalCalories = (float)($data['total_calories'] ?? 0);
            $this->activityDate     = $data['date'] ?? $this->activityDate;
            $this->activityTime     = $data['time'] ?? '';
            $this->activityApp      = $data['app'] ?? '';
            $this->activityLocation = $data['location'] ?? '';

            // Performance
            $this->pace       = $data['pace'] ?? '';
            $this->hrAvg      = $data['hr_avg'] ?? '';
            $this->hrMax      = $data['hr_max'] ?? '';
            $this->steps      = $data['steps'] ?? '';
            $this->cadenceAvg = $data['cadence'] ?? '';
            $this->strideAvg  = $data['stride'] ?? '';

            // Zonas
            $this->zoneLight     = $data['z_light'] ?? '';
            $this->zoneIntensive = $data['z_int'] ?? '';
            $this->zoneAerobic   = $data['z_aer'] ?? '';
            $this->zoneAnaerobic = $data['z_ana'] ?? '';
            $this->zoneVO2Max    = $data['z_vo2'] ?? '';

            // Efeito e Carga
            $this->teAerobic    = $data['te_aer'] ?? '';
            $this->teAnaerobic  = $data['te_ana'] ?? '';
            $this->recoveryTime = $data['recovery'] ?? '';
            $this->trainingLoad = $data['load'] ?? '';

            $this->photoAnalysisResult = $data['comment'] ?? '✅ Dados extraídos!';
            $this->dispatch('toast', text: '✅ IA: Formulário preenchido!');
        }
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        $this->photoAnalysisResult = '⚠️ Erro ao analisar.';
    }
}
    // ─── ATIVIDADES ──────────────────────────────────────────────────────────

    public function saveActivity()
{
    $this->validate([
        'activityType' => 'required|string',
        'activityDuration' => 'required|integer|min:1',
        'activityDate' => 'required|date',
        'activityDistance' => 'nullable|numeric|min:0',
        'activityCalories' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();
    $ws = $user->currentWorkspace;

    $photoPath = null;
    if ($this->activityPhoto) {
        $photoPath = $this->activityPhoto->store('fitness/photos', 'public');
    }

    FitnessActivity::create([
        'user_id' => $user->id,
        'workspace_id' => $ws->id,
        'type' => $this->activityType,
        'distance_km' => $this->activityDistance ?: null,
        'duration_minutes' => $this->activityDuration,
        'calories' => $this->activityCalories ?: null,
        'photo_path' => $photoPath,
        'activity_date' => $this->activityDate,
        // --- ADICIONE ESTES CAMPOS (Certifique-se que existem na sua tabela SQL) ---
        'pace' => $this->pace,
        'hr_avg' => $this->hrAvg,
        'hr_max' => $this->hrMax,
        'steps' => $this->steps,
        'cadence' => $this->cadenceAvg,
        'stride' => $this->strideAvg,
        'te_aerobic' => $this->teAerobic,
        'te_anaerobic' => $this->teAnaerobic,
        'recovery_time' => $this->recoveryTime,
        'training_load' => $this->trainingLoad,
        'zone_vo2' => $this->zoneVO2Max,
        'zone_anaerobic' => $this->zoneAnaerobic,
    ]);

    Cache::forget("fitness:stats:{$ws->id}:{$user->id}");
    $this->showActivityModal = false;
    $this->resetActivityForm();
    $this->dispatch('toast', text: '🏃 Atividade gravada com todos os detalhes!');
}

    public function deleteActivity(int $id)
    {
        $activity = FitnessActivity::where('user_id', Auth::id())->findOrFail($id);
        if ($activity->photo_path) {
            \Storage::disk('public')->delete($activity->photo_path);
        }
        $activity->delete();
        Cache::forget("fitness:stats:{$activity->workspace_id}:" . Auth::id());
        $this->dispatch('toast', text: 'Atividade removida.');
    }

    // ─── METAS FITNESS ───────────────────────────────────────────────────────

    public function saveGoal()
    {
        $this->validate([
            'goalName' => 'required|string|max:100',
            'goalTarget' => 'required|numeric|min:1',
            'goalType' => 'required|string',
            'goalDeadline' => 'nullable|date|after:today',
        ]);

        $user = Auth::user();
        $ws = $user->currentWorkspace;

        $data = [
            'user_id' => $user->id,
            'workspace_id' => $ws->id,
            'name' => $this->goalName,
            'type' => $this->goalType,
            'target' => $this->goalTarget,
            'deadline' => $this->goalDeadline ?: null,
        ];

        if ($this->editingGoalId) {
            FitnessGoal::where('user_id', Auth::id())->findOrFail($this->editingGoalId)->update($data);
        } else {
            FitnessGoal::create($data);
        }

        $this->showGoalModal = false;
        $this->resetGoalForm();
        $this->dispatch('toast', text: '🎯 Meta fitness guardada!');
    }

    public function deleteGoal(int $id)
    {
        FitnessGoal::where('user_id', Auth::id())->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Meta removida.');
    }

    // ─── DISPOSITIVOS CONECTADOS ─────────────────────────────────────────────

    public function saveDevice(): void
    {
        $this->validate([
            'deviceName'  => 'required|string|max:100',
            'deviceBrand' => 'required|string|max:50',
            'deviceEmoji' => 'required|string|max:10',
        ]);

        ConnectedDevice::create([
            'user_id'  => Auth::id(),
            'name'     => $this->deviceName,
            'brand'    => $this->deviceBrand,
            'emoji'    => $this->deviceEmoji,
            'provider' => 'manual',
            'is_active' => true,
        ]);

        $this->showDeviceModal = false;
        $this->deviceName = '';
        $this->dispatch('toast', text: '⌚ Dispositivo associado com sucesso!');
    }

    public function disconnectDevice(int $id): void
    {
        ConnectedDevice::where('user_id', Auth::id())->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Dispositivo removido.');
    }

    public function markSynced(int $id): void
    {
        ConnectedDevice::where('user_id', Auth::id())
            ->findOrFail($id)
            ->update(['last_synced_at' => now()]);
        $this->dispatch('toast', text: '✅ Sincronização registada!');
    }

    // ─── RESET FORMS ─────────────────────────────────────────────────────────

    private function resetActivityForm()
    {
        $this->activityType = 'corrida';
        $this->activityDistance = 0;
        $this->activityDuration = 0;
        $this->activityCalories = 0;
        $this->activityNotes = '';
        $this->activityDate = now()->format('Y-m-d');
        $this->activityPhoto = null;
        $this->photoAnalysisResult = '';
        $this->showPhotoAnalysis = false;
    }

    private function resetGoalForm()
    {
        $this->goalName = '';
        $this->goalType = 'distancia_semanal';
        $this->goalTarget = 0;
        $this->goalDeadline = '';
        $this->editingGoalId = null;
    }

    // ─── DADOS PARA A VIEW ────────────────────────────────────────────────────

    private function getUserStats(): array
    {
        $user = Auth::user();
        $ws = $user->currentWorkspace;

        return Cache::remember("fitness:stats:{$ws->id}:{$user->id}", 60, function () use ($user, $ws) {
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();

            $activities = FitnessActivity::where('workspace_id', $ws->id)
                ->where('user_id', $user->id)
                ->whereBetween('activity_date', [$monthStart, $monthEnd])
                ->get();

            $topActivity = FitnessActivity::where('workspace_id', $ws->id)
                ->where('user_id', $user->id)
                ->select('type', \DB::raw('count(*) as total'))
                ->groupBy('type')
                ->orderByDesc('total')
                ->first()?->type ?? 'corrida';

            return [
                'total_activities' => $activities->count(),
                'total_distance' => round($activities->sum('distance_km'), 1),
                'total_calories' => round($activities->sum('calories')),
                'total_minutes' => $activities->sum('duration_minutes'),
                'top_activity' => $topActivity,
            ];
        });
    }

    public function render()
    {
        $user = Auth::user();
        $ws = $user->currentWorkspace;

        $monthStart = now()->startOfMonth();
        $weekStart = now()->startOfWeek();

        // Atividades recentes
        $recentActivities = FitnessActivity::where('workspace_id', $ws->id)
            ->where('user_id', $user->id)
            ->with('user:id,name')
            ->orderByDesc('activity_date')
            ->take(8)
            ->get();

        // Gráfico semana atual (últimos 7 dias)
        $weekChart = collect(range(6, 0))->map(function ($i) use ($user, $ws) {
            $date = now()->subDays($i);
            $activities = FitnessActivity::where('workspace_id', $ws->id)
                ->where('user_id', $user->id)
                ->whereDate('activity_date', $date)
                ->get();

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->format('d'),
                'count' => $activities->count(),
                'minutes' => $activities->sum('duration_minutes'),
                'calories' => $activities->sum('calories'),
                'isToday' => $date->isToday(),
            ];
        });

        // Metas fitness
        $fitnessGoals = FitnessGoal::where('workspace_id', $ws->id)
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($goal) use ($user, $ws) {
                // Calcular progresso baseado no tipo
                $progress = match($goal->type) {
                    'distancia_semanal' => FitnessActivity::where('workspace_id', $ws->id)->where('user_id', $user->id)->whereBetween('activity_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('distance_km'),
                    'calorias_mensais' => FitnessActivity::where('workspace_id', $ws->id)->where('user_id', $user->id)->whereBetween('activity_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('calories'),
                    'sessoes_semanais' => FitnessActivity::where('workspace_id', $ws->id)->where('user_id', $user->id)->whereBetween('activity_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'tempo_semanal' => FitnessActivity::where('workspace_id', $ws->id)->where('user_id', $user->id)->whereBetween('activity_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('duration_minutes'),
                    default => 0,
                };

                $goal->progress = round($progress, 1);
                $goal->percentage = min(100, $goal->target > 0 ? ($progress / $goal->target) * 100 : 0);
                $goal->completed = $goal->percentage >= 100;
                return $goal;
            });

        $stats = $this->getUserStats();

        // Frase motivacional do dia
        $quotes = $this->motivationalQuotes[$stats['top_activity']] ?? $this->motivationalQuotes['default'];
        $dailyQuote = $quotes[now()->dayOfWeek % count($quotes)];

        // Streak de dias consecutivos
        $streak = 0;
        $checkDate = now()->toDateString();
        while (true) {
            $hasActivity = FitnessActivity::where('workspace_id', $ws->id)
                ->where('user_id', $user->id)
                ->whereDate('activity_date', $checkDate)
                ->exists();
            if (!$hasActivity) break;
            $streak++;
            $checkDate = now()->subDays($streak)->toDateString();
            if ($streak > 365) break;
        }

        $connectedDevices = ConnectedDevice::where('user_id', $user->id)
            ->orderByDesc('is_active')
            ->orderByDesc('last_synced_at')
            ->get();

        return view('livewire.fitness-hub', [
            'currentWs'        => $ws,
            'recentActivities' => $recentActivities,
            'weekChart'        => $weekChart,
            'fitnessGoals'     => $fitnessGoals,
            'stats'            => $stats,
            'dailyQuote'       => $dailyQuote,
            'streak'           => $streak,
            'connectedDevices' => $connectedDevices,
        ]);
    }
}
