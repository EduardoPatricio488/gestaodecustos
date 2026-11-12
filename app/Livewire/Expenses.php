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

    public string $scannerError = '';

    public array $aiQuota = [];

    public function refreshScannerQuota(): void
    {
        $this->aiQuota = app(\App\Services\OpenRouterQuotaService::class)->dailyQuota(true);
    }

    private function loadScannerQuota(): void
    {
        $this->aiQuota = app(\App\Services\OpenRouterQuotaService::class)->dailyQuota();
    }

    public function updatedScannerReceipt(): void
    {
        $this->resetValidation('scannerReceipt');
        $this->scannerError = '';

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
        // A análise multimodal pode demorar mais do que o limite PHP habitual de 30 segundos.
        // Evita que o PHP termine o pedido antes de o tratamento de timeout da OpenRouter ser executado.
        @set_time_limit(120);

        if (auth()->user()->isViewer()) {
            $this->scannerError = 'Apenas utilizadores com permissões de edição podem criar despesas.';
            return;
        }

        $this->scannerError = '';
        $this->validate([
            'scannerReceipt' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);
        $this->isScanningInvoice = true;

        try {
            $this->loadScannerQuota();
            if (($this->aiQuota['available'] ?? false) && (int) ($this->aiQuota['remaining'] ?? 0) <= 0) {
                throw new \Exception('A quota diária gratuita do OpenRouter está esgotada. A reposição ocorre às 00:00 UTC.');
            }
            $user = auth()->user();
            $workspace = $user->currentWorkspace;
            $categoriesQuery = Category::where('workspace_id', $user->current_workspace_id)
                ->where('hidden_from_sidebar', false);
            if ($workspace?->type === 'personal') {
                $categoriesQuery->where('user_id', $user->id);
            }
            $categories = $categoriesQuery->orderBy('name')->get(['id', 'name', 'slug']);
            if ($categories->isEmpty()) {
                throw new \Exception('Não existem categorias disponíveis para classificar a fatura.');
            }

            $categoryOptions = $categories->map(fn ($category) => [
                'name' => $category->name,
                'slug' => $category->slug,
            ])->values()->all();

            $filePath = $this->scannerReceipt->getRealPath();
            if (! $filePath || ! file_exists($filePath)) {
                throw new \Exception('Não foi possível ler o ficheiro enviado.');
            }

            $mimeType = $this->scannerReceipt->getMimeType() ?: 'image/jpeg';
            $fileContents = file_get_contents($filePath);
            if (! is_string($fileContents) || $fileContents === '') {
                throw new \Exception('Não foi possível ler o conteúdo da fatura.');
            }

            // Reduz fotografias grandes para acelerar o pedido multimodal sem perder legibilidade.
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

            $fileData = base64_encode($fileContents);
            $contentPart = str_starts_with($mimeType, 'image/')
                ? ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mimeType.';base64,'.$fileData, 'detail' => 'high']]
                : ['type' => 'file', 'file' => ['filename' => $this->scannerReceipt->getClientOriginalName(), 'file_data' => 'data:'.$mimeType.';base64,'.$fileData]];

            // Nesta etapa só precisamos dos dados essenciais para abrir a revisão da despesa.
            // Um prompt curto reduz o tempo de resposta e evita truncagens dos modelos gratuitos.
            $categoryNames = json_encode($categories->pluck('name')->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $prompt = <<<PROMPT
Analisa a fatura portuguesa na imagem ou PDF, página a página, incluindo cabeçalho, linhas de artigos, totais e rodapé. Devolve APENAS JSON válido, sem markdown.
A categoria tem de ser exatamente uma destas: {$categoryNames}.
Extrai TODOS os campos visíveis. Não inventes nem calcules dados ausentes. Usa null quando não estiverem impressos ou legíveis.
amount = total final efetivamente pago; gross_amount = total ilíquido; net_amount = base tributável/valor líquido; vat_amount = IVA total; vat_rate = taxa de IVA.
date = YYYY-MM-DD; time = HH:MM; quantity e valores monetários são números decimais com ponto.
Não confundas NIF do emitente com NIF do cliente, nem subtotal com total pago. Para payment_method usa apenas Dinheiro, Multibanco, Cartão, MB Way, Transferência ou null.
Devolve exatamente esta estrutura, mantendo todos os campos:
{"category":null,"amount":null,"date":null,"time":null,"quantity":null,"unit":null,"store":null,"description":null,"item_name":null,"subcategory":null,"unit_price":null,"vat_rate":null,"gross_amount":null,"net_amount":null,"vat_amount":null,"tax_incidence":null,"amount_tendered":null,"change_amount":null,"payment_condition":null,"payment_method":null,"company_nif":null,"customer_nif":null,"invoice_number":null,"notes":null}
PROMPT;

            $apiKey = config('services.openrouter.api_key');
            if (blank($apiKey)) {
                throw new \Exception('A chave da IA não está configurada.');
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Finance Pro AI',
            ])->connectTimeout(10)
                ->timeout(60)
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model', 'openrouter/free'),
                    'temperature' => 0,
                    'max_tokens' => 3200,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [['type' => 'text', 'text' => $prompt], $contentPart],
                    ]],
                ]);

            if (! $response->successful()) {
                throw new \Exception('A IA não conseguiu analisar a fatura: '.($response->json('error.message') ?? 'erro de comunicação.'));
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

            $finishReason = $response->json('choices.0.finish_reason') ?: 'não indicado';

            // Se o modelo gratuito cortar a resposta, recuperamos os campos
            // que já tenham sido enviados e deixamos os restantes para revisão.
            if ((! is_array($result) || json_last_error() !== JSON_ERROR_NONE) && $finishReason === 'length') {
                $partial = [];
                $stringFields = ['category', 'date', 'time', 'unit', 'store', 'description', 'item_name', 'subcategory', 'tax_incidence', 'payment_condition', 'payment_method', 'company_nif', 'customer_nif', 'invoice_number', 'notes'];
                $numericFields = ['amount', 'quantity', 'unit_price', 'vat_rate', 'gross_amount', 'net_amount', 'vat_amount', 'amount_tendered', 'change_amount'];
                foreach ($stringFields as $field) {
                    if (preg_match('/"'.preg_quote($field, '/').'\\s*:\\s*"([^"]*)"/i', $raw, $match)) {
                        $partial[$field] = trim($match[1]);
                    }
                }
                foreach ($numericFields as $field) {
                    if (preg_match('/"'.preg_quote($field, '/').'\\s*:\\s*"?(-?[0-9]+(?:[.,][0-9]+)?)"?/i', $raw, $match)) {
                        $partial[$field] = str_replace(',', '.', $match[1]);
                    }
                }
                $result = $partial;
            }

            if (! is_array($result)) {
                $returnedModel = $response->json('model') ?: config('services.openrouter.model', 'openrouter/free');
                $refusal = $response->json('choices.0.message.refusal');
                $detail = is_string($refusal) && trim($refusal) !== ''
                    ? trim($refusal)
                    : 'A IA não conseguiu devolver os dados num formato válido ('.$returnedModel.', motivo: '.$finishReason.').';

                throw new \Exception($detail.' Podes tentar novamente ou escolher a categoria manualmente.');
            }

            // Uma categoria isolada não significa que a fatura foi extraída.
            // Se valor, data, estabelecimento e descrição vierem vazios, tenta novamente
            // com o mesmo modelo gratuito e a mesma imagem, pedindo apenas os campos essenciais.
            $hasEssentialData = collect(['amount', 'date', 'store', 'description'])
                ->contains(fn ($field) => isset($result[$field]) && $result[$field] !== '');

            if (! $hasEssentialData) {
                $retryPrompt = "Lê novamente a fatura inteira e completa os campos em falta. Responde apenas com JSON válido, sem markdown. Não inventes; usa null quando ausente/ilegível. ".
                    "A categoria tem de ser exatamente uma destas: ".
                    json_encode($categories->pluck('name')->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).
                    ". amount é o total final pago; gross_amount ilíquido; net_amount base tributável; vat_amount IVA; date YYYY-MM-DD; time HH:MM. ".
                    '{"category":null,"amount":null,"date":null,"time":null,"quantity":null,"unit":null,"store":null,"description":null,"item_name":null,"subcategory":null,"unit_price":null,"vat_rate":null,"gross_amount":null,"net_amount":null,"vat_amount":null,"tax_incidence":null,"amount_tendered":null,"change_amount":null,"payment_condition":null,"payment_method":null,"company_nif":null,"customer_nif":null,"invoice_number":null,"notes":null}';

                $retryResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => 'Finance Pro AI',
                ])->connectTimeout(4)->timeout(20)
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model' => config('services.openrouter.model', 'openrouter/free'),
                        'temperature' => 0,
                        'max_tokens' => 2400,
                        'messages' => [[
                            'role' => 'user',
                            'content' => [['type' => 'text', 'text' => $retryPrompt], $contentPart],
                        ]],
                    ]);

                if ($retryResponse->successful()) {
                    $retryRaw = $retryResponse->json('choices.0.message.content');
                    if (is_array($retryRaw)) {
                        $retryRaw = collect($retryRaw)
                            ->map(fn ($part) => is_string($part) ? $part : data_get($part, 'text', data_get($part, 'content', '')))
                            ->filter(fn ($part) => is_string($part) && trim($part) !== '')
                            ->implode("\\n");
                    }
                    $retryRaw = trim(preg_replace('/^\\x60{3}(?:json)?\\s*|\\s*\\x60{3}$/i', '', (string) $retryRaw));
                    $retryResult = json_decode($retryRaw, true);
                    if (! is_array($retryResult) && preg_match('/\\{.*\\}/s', $retryRaw, $retryMatches)) {
                        $retryResult = json_decode($retryMatches[0], true);
                    }
                    if (is_array($retryResult)) {
                        foreach (['category', 'amount', 'date', 'time', 'quantity', 'unit', 'store', 'description', 'item_name', 'subcategory', 'unit_price', 'vat_rate', 'gross_amount', 'net_amount', 'vat_amount', 'tax_incidence', 'amount_tendered', 'change_amount', 'payment_condition', 'payment_method', 'company_nif', 'customer_nif', 'invoice_number', 'notes'] as $field) {
                            if ((! isset($result[$field]) || $result[$field] === '') && isset($retryResult[$field]) && $retryResult[$field] !== '') {
                                $result[$field] = $retryResult[$field];
                            }
                        }
                    }
                }
            }

            $hasEssentialData = collect(['amount', 'date', 'store', 'description'])
                ->contains(fn ($field) => isset($result[$field]) && $result[$field] !== '');

            if (! $hasEssentialData) {
                $usedModel = $response->json('model') ?: config('services.openrouter.model', 'openrouter/free');
                $finishReason = $response->json('choices.0.finish_reason') ?: 'não indicado';
                \Illuminate\Support\Facades\Log::warning('Expenses invoice scanner returned no readable invoice fields.', [
                    'model' => $usedModel,
                    'finish_reason' => $finishReason,
                    'image_mime' => $mimeType,
                    'image_bytes' => strlen($fileContents),
                    'response_has_content' => filled($content),
                ]);
                throw new \Exception('O modelo gratuito ('.$usedModel.') recebeu a imagem, mas não conseguiu ler os dados da fatura ('.$finishReason.'). Tenta novamente; se continuar, será necessário usar um modelo de visão diferente.');
            }

            $suggestedCategory = trim((string) ($result['category'] ?? ''));
            $category = $categories->first(fn ($item) => mb_strtolower(trim($item->name)) === mb_strtolower($suggestedCategory));

            // Se a resposta tiver sido cortada antes de indicar a categoria,
            // abre a revisão em "Outros" (ou na primeira categoria disponível).
            if (! $category && $finishReason === 'length') {
                $category = $categories->first(fn ($item) => mb_strtolower(trim($item->name)) === 'outros')
                    ?? $categories->first();
                $result['category'] = $category?->name;
            }

            if (! $category) {
                throw new \Exception('A IA não conseguiu associar a fatura a uma categoria existente. Experimenta a escolha manual.');
            }
            if (blank($category->slug)) {
                $category->slug = Category::uniqueSlugFor($category->name, $user->current_workspace_id, $category->id);
                $category->saveQuietly();
            }

            $amount = $result['amount'] ?? null;
            if (is_string($amount)) {
                $amount = str_replace([' ', "\u{00A0}", '€'], '', $amount);
                if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $amount)) {
                    $amount = str_replace(['.', ','], ['', '.'], $amount);
                } else {
                    $amount = str_replace(',', '.', $amount);
                }
            }
            $result['amount'] = is_numeric($amount) ? (float) $amount : null;

            if (! empty($result['date'])) {
                try {
                    $result['date'] = \Carbon\Carbon::parse($result['date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $result['date'] = null;
                }
            }

            $pendingDocumentPath = $this->scannerReceipt->store('expenses/pending-scans', 'public');
            session()->put('pending_expense_scan:'.$user->current_workspace_id.':'.$user->id, [
                'data' => $result,
                'document_path' => $pendingDocumentPath,
                'document_name' => $this->scannerReceipt->getClientOriginalName(),
                'document_mime' => $mimeType,
            ]);

            $this->redirect(route('hub.category', [
                'slug' => $category->slug,
                'open_scan_review' => 1,
            ]), navigate: true);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Illuminate\Support\Facades\Log::warning('Expenses invoice scanner timed out or lost connection to OpenRouter.', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            $this->scannerError = 'A IA demorou demasiado tempo a responder. A fatura continua carregada; tenta novamente dentro de alguns segundos.';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Expenses invoice scanner: '.$e->getMessage());
            $this->scannerError = 'Não foi possível analisar a fatura. '.$e->getMessage();
        } finally {
            $this->isScanningInvoice = false;
            $this->loadScannerQuota();
        }
    }

    public function mount(): void
    {
        $this->loadScannerQuota();
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