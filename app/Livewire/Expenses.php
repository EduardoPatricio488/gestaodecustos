<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Expenses extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';

    public ?int $filterCategory = null;

    public bool $openCreateAfterCategory = false;

    public $scannerReceipt;

    public bool $isScanningInvoice = false;

    public ?string $invoiceScanId = null;

    public ?int $invoiceScanStartedAt = null;

    public string $scannerError = '';

    public ?array $openRouterQuota = null;

    public bool $loadingOpenRouterQuota = false;

    public string $openRouterQuotaError = '';

    /**
     * Atualiza a quota diária gratuita apresentada no scanner.
     * A resposta é guardada em cache durante 45 segundos para evitar pedidos repetidos.
     */
    public function refreshOpenRouterQuota(): void
    {
        $apiKey = config('services.openrouter.api_key');

        $this->openRouterQuotaError = '';
        $this->loadingOpenRouterQuota = true;

        if (blank($apiKey)) {
            $this->openRouterQuota = null;
            $this->openRouterQuotaError = 'A chave OpenRouter não está configurada.';
            $this->loadingOpenRouterQuota = false;

            return;
        }

        try {
            $quota = \Illuminate\Support\Facades\Cache::remember(
                'openrouter:quota:'.hash('sha256', $apiKey),
                now()->addSeconds(45),
                function () use ($apiKey) {
                    $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                        ->connectTimeout(5)
                        ->timeout(8)
                        ->get('https://openrouter.ai/api/v1/key');

                    if (! $response->successful()) {
                        throw new \RuntimeException('Não foi possível consultar a quota OpenRouter (HTTP '.$response->status().').');
                    }

                    $daily = $response->json('data.free_model_daily_requests');

                    if (! is_array($daily) || ! isset($daily['remaining'], $daily['limit'], $daily['used'])) {
                        throw new \RuntimeException('O OpenRouter não disponibilizou os dados da quota diária para esta chave.');
                    }

                    return [
                        'used' => max(0, (int) $daily['used']),
                        'limit' => max(0, (int) $daily['limit']),
                        'remaining' => max(0, (int) $daily['remaining']),
                    ];
                }
            );

            $this->openRouterQuota = $quota;
        } catch (\Throwable $exception) {
            $this->openRouterQuota = null;
            $this->openRouterQuotaError = $exception->getMessage();

            \Illuminate\Support\Facades\Log::notice('Expenses scanner: OpenRouter quota display could not be refreshed.', [
                'message' => $exception->getMessage(),
                'user_id' => auth()->id(),
            ]);
        } finally {
            $this->loadingOpenRouterQuota = false;
        }
    }

    public function updatedScannerReceipt(): void
    {
        $this->resetValidation('scannerReceipt');
        $this->scannerError = '';
        $this->isScanningInvoice = false;
        $this->invoiceScanId = null;
        $this->invoiceScanStartedAt = null;

        try {
            $this->validate([
                'scannerReceipt' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->scannerError = 'Ficheiro inválido: '.collect($e->errors())->flatten()->first();
            $this->scannerReceipt = null;
        }
    }

    public function scanInvoiceAndDetectCategory(): void
    {
        if (auth()->user()->isViewer()) {
            $this->scannerError = 'Apenas utilizadores com permissões de edição podem criar despesas.';
            return;
        }

        $this->scannerError = '';
        $this->validate([
            'scannerReceipt' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        $categoriesQuery = Category::where('workspace_id', $user->current_workspace_id)
            ->where('hidden_from_sidebar', false);
        if ($workspace?->type === 'personal') {
            $categoriesQuery->where('user_id', $user->id);
        }
        $categories = $categoriesQuery->orderBy('name')->get(['id', 'name', 'slug']);
        if ($categories->isEmpty()) {
            $this->scannerError = 'Não existem categorias disponíveis para classificar a fatura.';
            return;
        }

        $storedPath = $this->scannerReceipt->store('expenses/pending-scans', 'public');
        if (! $storedPath) {
            $this->scannerError = 'Não foi possível guardar temporariamente a fatura.';
            return;
        }

        $scanId = (string) \Illuminate\Support\Str::uuid();
        $cacheKey = 'invoice_scan:'.$user->id.':'.$scanId;
        $this->invoiceScanId = $scanId;
        $this->invoiceScanStartedAt = now()->timestamp;
        $this->isScanningInvoice = true;

        \Illuminate\Support\Facades\Cache::put($cacheKey, [
            'status' => 'processing',
            'started_at' => now()->timestamp,
        ], now()->addMinutes(20));

        $categoryOptions = $categories->map(fn ($category) => [
            'name' => $category->name,
            'slug' => $category->slug,
        ])->values()->all();
        $userId = (int) $user->id;
        $workspaceId = (int) $user->current_workspace_id;
        $documentName = $this->scannerReceipt->getClientOriginalName();
        $documentMime = $this->scannerReceipt->getMimeType() ?: 'image/jpeg';
        $apiKey = config('services.openrouter.api_key');
        $appUrl = config('app.url');

        if (blank($apiKey)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($storedPath);
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
            $this->isScanningInvoice = false;
            $this->scannerError = 'A chave da IA não está configurada.';
            return;
        }

        // A análise é colocada na fila da base de dados para não bloquear o pedido Livewire.
        $processInvoiceScan = function () use ($cacheKey, $storedPath, $documentName, $documentMime, $categoryOptions, $apiKey, $appUrl, $userId, $workspaceId) {
            @set_time_limit(180);

            try {
                $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($storedPath);
                if (! is_file($filePath)) {
                    throw new \RuntimeException('O ficheiro temporário da fatura já não está disponível.');
                }

                $fileContents = file_get_contents($filePath);
                if (! is_string($fileContents) || $fileContents === '') {
                    throw new \RuntimeException('Não foi possível ler o conteúdo da fatura.');
                }

                $mimeType = $documentMime;
                if (str_starts_with($mimeType, 'image/') && function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                    $sourceImage = @imagecreatefromstring($fileContents);
                    if ($sourceImage !== false) {
                        $originalWidth = imagesx($sourceImage);
                        $originalHeight = imagesy($sourceImage);
                        $scale = min(1, 2200 / max($originalWidth, $originalHeight));
                        $targetWidth = max(1, (int) round($originalWidth * $scale));
                        $targetHeight = max(1, (int) round($originalHeight * $scale));
                        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
                        $white = imagecolorallocate($resizedImage, 255, 255, 255);
                        imagefill($resizedImage, 0, 0, $white);
                        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);
                        ob_start();
                        imagejpeg($resizedImage, null, 88);
                        $compressedImage = ob_get_clean();
                        if (is_string($compressedImage) && $compressedImage !== '') {
                            $fileContents = $compressedImage;
                            $mimeType = 'image/jpeg';
                        }
                        imagedestroy($resizedImage);
                        imagedestroy($sourceImage);
                    }
                }

                // Protege a quota gratuita: limita globalmente os pedidos e consulta
                // o saldo diário da chave antes de consumir uma chamada de inferência.
                $requestsPerMinute = max(1, (int) config('services.openrouter.requests_per_minute', 18));
                $rateLimitKey = 'openrouter:invoice-scans:global';
                $rateLimiter = app(\Illuminate\Cache\RateLimiter::class);

                if ($rateLimiter->tooManyAttempts($rateLimitKey, $requestsPerMinute)) {
                    $seconds = max(1, $rateLimiter->availableIn($rateLimitKey));
                    throw new \RuntimeException('O limite de segurança da IA foi atingido. Aguarda '.$seconds.' segundos antes de voltares a analisar uma fatura.');
                }

                $rateLimiter->hit($rateLimitKey, 60);

                $quotaResponse = \Illuminate\Support\Facades\Http::withToken($apiKey)
                    ->connectTimeout(5)
                    ->timeout(10)
                    ->get('https://openrouter.ai/api/v1/key');

                if (in_array($quotaResponse->status(), [401, 403], true)) {
                    throw new \RuntimeException('A chave OpenRouter é inválida ou não tem permissões. Confirma OPENROUTER_API_KEY nas variáveis de ambiente.');
                }

                if ($quotaResponse->successful()) {
                    $dailyQuota = $quotaResponse->json('data.free_model_daily_requests');
                    if (is_array($dailyQuota) && isset($dailyQuota['remaining']) && (int) $dailyQuota['remaining'] <= 0) {
                        $used = (int) ($dailyQuota['used'] ?? 0);
                        $limit = (int) ($dailyQuota['limit'] ?? 0);
                        throw new \RuntimeException('A quota diária gratuita do OpenRouter está esgotada ('.$used.'/'.$limit.' pedidos). A quota renova às 00:00 UTC. Não foi enviada nenhuma análise desta fatura.');
                    }
                } elseif ($quotaResponse->status() === 429) {
                    throw new \RuntimeException('O OpenRouter está a limitar temporariamente as consultas de quota. Aguarda alguns segundos e tenta novamente.');
                } else {
                    \Illuminate\Support\Facades\Log::notice('Expenses invoice scanner: could not pre-check OpenRouter quota; proceeding with inference.', [
                        'http_status' => $quotaResponse->status(),
                        'user_id' => $userId,
                    ]);
                }

                $fileData = base64_encode($fileContents);
                $contentPart = str_starts_with($mimeType, 'image/')
                    ? ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mimeType.';base64,'.$fileData, 'detail' => 'high']]
                    : ['type' => 'file', 'file' => ['filename' => $documentName, 'file_data' => 'data:'.$mimeType.';base64,'.$fileData]];

                $categoryNames = json_encode(array_column($categoryOptions, 'name'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $prompt = <<<PROMPT
Analisa a fatura portuguesa na imagem ou PDF, incluindo cabeçalho, linhas de artigos, totais e rodapé. Devolve APENAS JSON válido, sem markdown.
A categoria tem de ser exatamente uma destas: {$categoryNames}.
Extrai os campos visíveis. Não inventes nem calcules dados ausentes. Usa null quando não estiverem impressos ou legíveis.
amount = total final efetivamente pago; gross_amount = total ilíquido; net_amount = base tributável; vat_amount = IVA total.
date = YYYY-MM-DD; time = HH:MM; quantity e valores monetários são números decimais com ponto.
Não confundas NIF do emitente com NIF do cliente. payment_method: Dinheiro, Multibanco, Cartão, MB Way, Transferência ou null.
Devolve exatamente esta estrutura:
{"category":null,"amount":null,"date":null,"time":null,"quantity":null,"unit":null,"store":null,"description":null,"item_name":null,"subcategory":null,"unit_price":null,"vat_rate":null,"gross_amount":null,"net_amount":null,"vat_amount":null,"tax_incidence":null,"amount_tendered":null,"change_amount":null,"payment_condition":null,"payment_method":null,"company_nif":null,"customer_nif":null,"invoice_number":null,"notes":null}
PROMPT;

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => $appUrl,
                    'X-Title' => 'Finance Pro AI',
                ])->connectTimeout((int) config('services.openrouter.connect_timeout', 8))
                    ->timeout((int) config('services.openrouter.request_timeout', 45))
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                    // Modelo utilizado na versão do scanner confirmada como funcional.
                    'model' => 'openrouter/free',
                    'temperature' => 0,
                    'max_tokens' => 3200,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [['type' => 'text', 'text' => $prompt], $contentPart],
                    ]],
                ]);

                if (! $response->successful()) {
                    $providerError = $response->json('error', []);
                    \Illuminate\Support\Facades\Log::warning('Expenses invoice scanner: OpenRouter request failed.', [
                        'http_status' => $response->status(),
                        'provider_error' => $providerError,
                        'user_id' => $userId,
                        'workspace_id' => $workspaceId,
                    ]);

                    $status = $response->status();
                    $providerMessage = (string) (data_get($providerError, 'message') ?: 'erro de comunicação');
                    $providerCode = data_get($providerError, 'metadata.provider_code');

                    if ($status === 429) {
                        $retryAfter = (int) $response->header('Retry-After', 0);
                        $waitMessage = $retryAfter > 0 ? ' Tenta novamente daqui a '.$retryAfter.' segundos.' : ' Aguarda alguns minutos antes de tentar novamente.';
                        throw new \RuntimeException('O OpenRouter atingiu um limite de pedidos ou o fornecedor gratuito está sem capacidade. '.$providerMessage.$waitMessage);
                    }

                    if ($status === 402) {
                        throw new \RuntimeException('O OpenRouter recusou o pedido por limite de créditos/orçamento (HTTP 402). Confirma o saldo e os limites da chave na tua conta OpenRouter.');
                    }

                    if ($status === 408 || $status >= 500) {
                        throw new \RuntimeException('O serviço OpenRouter/fornecedor está temporariamente indisponível (HTTP '.$status.'). Não foi criada qualquer despesa. Tenta novamente mais tarde.');
                    }

                    throw new \RuntimeException('A OpenRouter devolveu um erro (HTTP '.$status.'): '.$providerMessage.($providerCode ? ' Código do fornecedor: '.$providerCode.'.' : '.'));
                }

                $content = $response->json('choices.0.message.content');
                if (is_array($content)) {
                    $content = collect($content)
                        ->map(fn ($part) => is_string($part) ? $part : data_get($part, 'text', data_get($part, 'content', '')))
                        ->filter(fn ($part) => is_string($part) && trim($part) !== '')
                        ->implode("\n");
                }

                $raw = trim((string) $content);
                $raw = preg_replace('/^\x60{3}(?:json)?\s*|\s*\x60{3}$/i', '', $raw);
                $result = json_decode($raw, true);
                if (! is_array($result) && preg_match('/\{.*\}/s', $raw, $matches)) {
                    $result = json_decode($matches[0], true);
                }

                if (! is_array($result)) {
                    throw new \RuntimeException('A IA não conseguiu devolver os dados num formato válido. Podes tentar novamente ou escolher a categoria manualmente.');
                }

                $hasEssentialData = collect(['amount', 'date', 'store', 'description'])
                    ->contains(fn ($field) => isset($result[$field]) && $result[$field] !== '');
                if (! $hasEssentialData) {
                    throw new \RuntimeException('O modelo recebeu a fatura, mas não conseguiu ler os dados essenciais. Tenta novamente mais tarde.');
                }

                $finishReason = $response->json('choices.0.finish_reason') ?: '';
                $suggestedCategory = trim((string) ($result['category'] ?? ''));
                $category = collect($categoryOptions)->first(fn ($item) => mb_strtolower(trim($item['name'])) === mb_strtolower($suggestedCategory));
                if (! $category && $finishReason === 'length') {
                    $category = collect($categoryOptions)->first(fn ($item) => mb_strtolower(trim($item['name'])) === 'outros')
                        ?? $categoryOptions[0];
                    $result['category'] = $category['name'];
                }
                if (! $category) {
                    throw new \RuntimeException('A IA não conseguiu associar a fatura a uma categoria existente. Experimenta a escolha manual.');
                }

                $amount = $result['amount'] ?? null;
                if (is_string($amount)) {
                    $amount = str_replace([' ', "\u{00A0}", '€'], '', $amount);
                    $amount = preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $amount)
                        ? str_replace(['.', ','], ['', '.'], $amount)
                        : str_replace(',', '.', $amount);
                }
                $result['amount'] = is_numeric($amount) ? (float) $amount : null;
                if (! empty($result['date'])) {
                    try {
                        $result['date'] = \Carbon\Carbon::parse($result['date'])->format('Y-m-d');
                    } catch (\Throwable) {
                        $result['date'] = null;
                    }
                }

                \Illuminate\Support\Facades\Cache::put($cacheKey, [
                    'status' => 'completed',
                    'data' => $result,
                    'category_slug' => $category['slug'],
                    'document_path' => $storedPath,
                    'document_name' => $documentName,
                    'document_mime' => $mimeType,
                ], now()->addMinutes(20));
            } catch (\Throwable $exception) {
                \Illuminate\Support\Facades\Log::warning('Expenses invoice scanner background analysis failed.', [
                    'message' => $exception->getMessage(),
                    'user_id' => $userId,
                    'workspace_id' => $workspaceId,
                ]);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($storedPath);
                \Illuminate\Support\Facades\Cache::put($cacheKey, [
                    'status' => 'failed',
                    'error' => $exception instanceof \Illuminate\Http\Client\ConnectionException
                        ? 'A OpenRouter não respondeu dentro do tempo limite. A fatura continua carregada e não foi criada qualquer despesa.'
                        : $exception->getMessage(),
                ], now()->addMinutes(20));
            }
        };

        try {
            dispatch($processInvoiceScan)->onConnection('database')->onQueue('default');
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('Expenses invoice scanner: could not enqueue analysis.', [
                'message' => $exception->getMessage(),
                'user_id' => $userId,
                'workspace_id' => $workspaceId,
            ]);

            \Illuminate\Support\Facades\Storage::disk('public')->delete($storedPath);
            \Illuminate\Support\Facades\Cache::put($cacheKey, [
                'status' => 'failed',
                'error' => 'Não foi possível iniciar o processamento em segundo plano. Confirma se o serviço de filas está ativo e tenta novamente.',
            ], now()->addMinutes(20));

            $this->checkInvoiceScan();
        }
    }

    public function checkInvoiceScan(): void
    {
        if (! $this->isScanningInvoice || blank($this->invoiceScanId)) {
            return;
        }

        $user = auth()->user();
        $cacheKey = 'invoice_scan:'.$user->id.':'.$this->invoiceScanId;
        $scan = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (! is_array($scan)) {
            $startedAt = $this->invoiceScanStartedAt ?? now()->timestamp;
            if (now()->timestamp - $startedAt <= 180) {
                return;
            }

            $this->isScanningInvoice = false;
            $this->invoiceScanId = null;
            $this->invoiceScanStartedAt = null;
            $this->scannerError = 'Não foi recebido qualquer resultado do processamento após 3 minutos. Verifica se o worker da fila está ativo e tenta novamente.';
            return;
        }

        if (($scan['status'] ?? null) === 'processing') {
            $startedAt = (int) ($scan['started_at'] ?? now()->timestamp);

            if (now()->timestamp - $startedAt <= 180) {
                return;
            }

            $scan = [
                'status' => 'failed',
                'error' => 'A análise excedeu o tempo máximo de espera. A fila pode não estar ativa. Tenta novamente; não foi criada qualquer despesa.',
            ];
            \Illuminate\Support\Facades\Cache::put($cacheKey, $scan, now()->addMinutes(5));
        }

        $this->isScanningInvoice = false;
        $this->invoiceScanId = null;
        \Illuminate\Support\Facades\Cache::forget($cacheKey);

        if (($scan['status'] ?? null) !== 'completed') {
            $this->scannerError = $scan['error'] ?? 'Não foi possível analisar a fatura.';
            return;
        }

        session()->put('pending_expense_scan:'.$user->current_workspace_id.':'.$user->id, [
            'data' => $scan['data'],
            'document_path' => $scan['document_path'],
            'document_name' => $scan['document_name'],
            'document_mime' => $scan['document_mime'],
        ]);

        // Força um carregamento completo para que o CategoryHub::mount()
        // leia a análise guardada na sessão e abra o formulário de revisão.
        $this->redirect(route('hub.category', [
            'slug' => $scan['category_slug'],
            'open_scan_review' => 1,
        ]));
    }

    public function mount(): void
    {
        $this->openCreateAfterCategory = request()->boolean('open_new');

        if ($this->openCreateAfterCategory) {
            $this->dispatch('open-expense-category-picker');
        }
    }

    /**
     * Reseta a paginação ao pesquisar.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Redireciona para a página de criação.
     * BLOQUEIO: Apenas Editores ou Admins podem criar.
     */
    public function newExpense(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas leitura: não tens permissão para criar registos.');

            return;
        }

        $this->redirect(route('expenses.create'), navigate: true);
    }

    /**
     * Abre a página da categoria escolhida.
     */
    public function selectCategory(int $categoryId): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas leitura: não tens permissão para criar registos.');

            return;
        }

        $category = Category::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->where('hidden_from_sidebar', false)
            ->whereKey($categoryId)
            ->firstOrFail();

        $this->redirect(route('hub.category', [
            'slug' => $category->slug,
            'open_new' => $this->openCreateAfterCategory ? 1 : null,
        ]), navigate: true);
    }

    /**
     * Redireciona para a página de edição.
     * BLOQUEIO: Visualizadores não podem editar.
     */
    public function edit(int $id): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas leitura: não podes editar este registo.');

            return;
        }

        $this->redirect(route('expenses.edit', $id), navigate: true);
    }

    /**
     * Elimina a despesa.
     * BLOQUEIO: Apenas o Administrador (Dono) pode apagar registos.
     */
    public function delete(int $id): void
    {
        if (! auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada: apenas o administrador do grupo pode apagar dados.');

            return;
        }

        $query = Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->whereKey($id);

        if (auth()->user()->currentWorkspace?->type === 'personal') {
            $query->where('user_id', auth()->id());
        }

        $query->delete();

        session()->flash('ok', 'Despesa eliminada com sucesso.');
    }

    public function render()
    {
        $user = auth()->user();
        $isPersonalWorkspace = $user->currentWorkspace?->type === 'personal';

        // O trait filtra pelo workspace; em workspaces pessoais, cada utilizador
        // deve ver apenas os seus próprios registos.
        $expenseQuery = Expense::with(['category', 'user', 'bankAccount']);

        if ($isPersonalWorkspace) {
            $expenseQuery->where('user_id', $user->id);
        }

        $expenses = $expenseQuery
            ->when($this->search, fn ($q) => $q->where('description', 'like', '%'.$this->search.'%')
            )
            ->when($this->filterCategory, fn ($q) => $q->where('category_id', $this->filterCategory)
            )
            ->latest('spent_at')
            ->latest('id')
            ->paginate(20);

        $monthTotalQuery = Expense::where('spent_at', '>=', now()->startOfMonth());

        if ($isPersonalWorkspace) {
            $monthTotalQuery->where('user_id', $user->id);
        }

        $monthTotal = (float) $monthTotalQuery->sum('amount');

        return view('livewire.expenses', [
            'expenses' => $expenses,
            'categories' => Category::where('workspace_id', $user->current_workspace_id)
                ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
                ->where('hidden_from_sidebar', false)
                ->orderBy('name')
                ->get(),
            'monthTotal' => $monthTotal,
            'isShared' => $user->currentWorkspace->users()->count() > 1,

            // Passamos as permissões para a vista esconder os botões visualmente
            'canEdit' => ! $user->isViewer(),
            'canDelete' => $user->isOwner(),
        ]);
    }
}