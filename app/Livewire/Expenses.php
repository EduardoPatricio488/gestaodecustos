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
                    $scale = min(1, 1600 / max($originalWidth, $originalHeight));
                    $targetWidth = max(1, (int) round($originalWidth * $scale));
                    $targetHeight = max(1, (int) round($originalHeight * $scale));
                    $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
                    $white = imagecolorallocate($resizedImage, 255, 255, 255);
                    imagefill($resizedImage, 0, 0, $white);
                    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);
                    ob_start();
                    imagejpeg($resizedImage, null, 78);
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
                ? ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mimeType.';base64,'.$fileData]]
                : ['type' => 'file', 'file' => ['filename' => $this->scannerReceipt->getClientOriginalName(), 'file_data' => 'data:'.$mimeType.';base64,'.$fileData]];

            $prompt = "Analisa esta fatura ou recibo português e devolve APENAS JSON válido, minificado, sem markdown. ".
                "A categoria tem de ser exatamente uma destas: ".json_encode($categories->pluck('name')->values()->all(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).". ".
                "Extrai todos os campos visíveis. Não inventes, não estimes e não calcules valores ausentes; usa null quando não estiverem impressos ou legíveis. ".
                "amount=total final da compra; item_name=produto(s) comprado(s); date=YYYY-MM-DD; time=HH:MM; quantity=número; unit=unidade; ".
                "store=nome do estabelecimento; description=descrição curta; subcategory=subcategoria curta; unit_price=preço unitário; vat_rate=taxa IVA percentual; ".
                "gross_amount=total ilíquido/com IVA; net_amount=base tributável sem IVA; vat_amount=valor do IVA; tax_incidence=descrição da incidência/taxa; ".
                "amount_tendered=valor entregue; change_amount=troco; payment_condition=Pronto pagamento, A prazo, Pagamento parcial ou Pagamento diferido; ".
                "payment_method=método indicado (Dinheiro, Multibanco, Cartão, MB Way, Transferência ou outro texto explícito); ".
                "company_nif=NIF do emitente; customer_nif=NIF do cliente; invoice_number=número do documento; notes=observações impressas relevantes. ".
                "Não confundas o NIF do emitente com o NIF do cliente. Valores numéricos apenas, com ponto decimal. ".
                "Estrutura exata: {\"category\":null,\"amount\":null,\"item_name\":null,\"date\":null,\"time\":null,\"quantity\":null,\"unit\":null,\"store\":null,\"description\":null,\"subcategory\":null,\"unit_price\":null,\"vat_rate\":null,\"gross_amount\":null,\"net_amount\":null,\"vat_amount\":null,\"tax_incidence\":null,\"amount_tendered\":null,\"change_amount\":null,\"payment_condition\":null,\"payment_method\":null,\"company_nif\":null,\"customer_nif\":null,\"invoice_number\":null,\"notes\":null}";

            $apiKey = config('services.openrouter.api_key');
            if (blank($apiKey)) {
                throw new \Exception('A chave da IA não está configurada.');
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Finance Pro AI',
            ])->connectTimeout(8)
                ->timeout(45)
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model', 'openrouter/free'),
                    'temperature' => 0,
                    'max_tokens' => 2200,
                    'response_format' => ['type' => 'json_object'],
                    'reasoning' => ['effort' => 'minimal'],
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

            if (! is_array($result) || json_last_error() !== JSON_ERROR_NONE) {
                $returnedModel = $response->json('model') ?: config('services.openrouter.model', 'openrouter/free');
                $finishReason = $response->json('choices.0.finish_reason') ?: 'não indicado';
                $refusal = $response->json('choices.0.message.refusal');
                if (is_string($refusal) && trim($refusal) !== '') {
                    $detail = trim($refusal);
                } elseif ($finishReason === 'length') {
                    $detail = 'A resposta da IA foi interrompida pelo limite de geração do modelo gratuito ('.$returnedModel.').';
                } else {
                    $detail = 'A IA não conseguiu devolver os dados num formato válido ('.$returnedModel.', motivo: '.$finishReason.').';
                }

                throw new \Exception($detail.' Podes tentar novamente ou escolher a categoria manualmente.');
            }

            $suggestedCategory = trim((string) ($result['category'] ?? ''));
            $category = $categories->first(fn ($item) => mb_strtolower(trim($item->name)) === mb_strtolower($suggestedCategory));
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Expenses invoice scanner: '.$e->getMessage());
            $this->scannerError = 'Não foi possível analisar a fatura. '.$e->getMessage();
        } finally {
            $this->isScanningInvoice = false;
        }
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