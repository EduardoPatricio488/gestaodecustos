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
        $categoriesQuery = Category::where('workspace_id', $user->current_workspace_id)
            ->where('hidden_from_sidebar', false);
        if ($user->currentWorkspace?->type === 'personal') {
            $categoriesQuery->where('user_id', $user->id);
        }
        $categories = $categoriesQuery->orderBy('name')->get(['id', 'name', 'slug']);
        if ($categories->isEmpty()) {
            $this->scannerError = 'Não existem categorias disponíveis para classificar a fatura.';
            return;
        }

        if (blank(config('services.openrouter.api_key'))) {
            $this->scannerError = 'A chave OpenRouter não está configurada.';
            return;
        }

        $storedPath = $this->scannerReceipt->store('expenses/pending-scans', 'public');
        if (! $storedPath) {
            $this->scannerError = 'Não foi possível guardar temporariamente a fatura.';
            return;
        }

        $scanId = (string) \Illuminate\\Support\\Str::uuid();
        $cacheKey = 'invoice_scan:'.$user->id.':'.$user->current_workspace_id.':'.$scanId;
        $this->invoiceScanId = $scanId;
        $this->invoiceScanStartedAt = now()->timestamp;
        $this->isScanningInvoice = true;

        \Illuminate\\Support\\Facades\\Cache::put($cacheKey, [
            'status' => 'processing',
            'started_at' => now()->timestamp,
            'user_id' => (int) $user->id,
            'workspace_id' => (int) $user->current_workspace_id,
        ], now()->addMinutes(20));

        $categoryOptions = $categories->map(fn ($category) => [
            'name' => $category->name,
            'slug' => $category->slug,
        ])->values()->all();

        try {
            dispatch(new \App\\Jobs\\ProcessInvoiceScan(
                $cacheKey,
                $storedPath,
                $this->scannerReceipt->getClientOriginalName(),
                $this->scannerReceipt->getMimeType() ?: 'image/jpeg',
                $categoryOptions,
                (int) $user->id,
                (int) $user->current_workspace_id,
                'category',
            ));
        } catch (\Throwable $exception) {
            \Illuminate\\Support\\Facades\\Log::error('Expenses invoice scanner: could not enqueue analysis.', [
                'message' => $exception->getMessage(),
                'user_id' => $user->id,
                'workspace_id' => $user->current_workspace_id,
            ]);

            \Illuminate\\Support\\Facades\\Cache::put($cacheKey, [
                'status' => 'failed',
                'error' => 'Não foi possível iniciar o processamento em segundo plano. Confirma se o worker da fila está ativo e tenta novamente.',
            ], now()->addMinutes(20));

            $this->isScanningInvoice = false;
            $this->invoiceScanId = null;
            $this->scannerError = 'Não foi possível iniciar o processamento em segundo plano. Confirma se o worker da fila está ativo e tenta novamente.';
        }
    }

    public function checkInvoiceScan(): void
    {
        if (! $this->isScanningInvoice || blank($this->invoiceScanId)) {
            return;
        }

        $user = auth()->user();
        $cacheKey = 'invoice_scan:'.$user->id.':'.$user->current_workspace_id.':'.$this->invoiceScanId;
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