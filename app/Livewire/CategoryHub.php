<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Category;
use App\Models\PriceHistory;
use App\Services\PriceComparisonService;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
class CategoryHub extends Component
{
    use WithFileUploads;

    public $slug, $title, $icon, $subcategories = [], $dbName;
    public $amount, $description, $spent_at, $subcategory, $meta = [];
    public $currency = 'EUR', $receipt, $budgetLimit, $editingBudget = false;

    public $isScanning  = false;
    public $scannedData = [];
    public $editingId   = null;
    public $scanSuccess = false;
    public $scanError   = '';

    protected $rules = [
        'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
        'photo'   => 'nullable|image|max:5120',
    ];

    public function updatedReceipt(): void
    {
        $this->resetValidation('receipt');
        $this->scanError = '';

        try {
            $this->validate([
                'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->scanError = 'Ficheiro inválido: ' . collect($e->errors())->flatten()->first();
            $this->receipt   = null;
        }
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:5120',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  MOUNT
    // ─────────────────────────────────────────────────────────────────
    public function mount($slug): void
    {
        $this->slug     = $slug;
        $this->spent_at = now()->format('Y-m-d');
        $this->currency = auth()->user()->currentWorkspace->currency ?? 'EUR';
        $this->setHubConfig();

        $category = Category::where('name', $this->dbName)
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->first();

        $this->budgetLimit = $category ? (float) $category->budget_limit : 0;
    }

    // ─────────────────────────────────────────────────────────────────
    //  HUB CONFIG
    // ─────────────────────────────────────────────────────────────────
   private function setHubConfig(): void
{
    // 1. Configurações para as categorias "estáticas" (estilos e labels)
    $configs = [
        'carro'       => ['db' => 'Carro',       'title' => 'Carro',        'icon' => 'truck',         'subs' => ['Gasolina', 'Manutenção', 'Seguro', 'Portagens', 'Estacionamento', 'Lavagem', 'Multa', 'Inspeção']],
        'casa'        => ['db' => 'Casa',         'title' => 'Casa',         'icon' => 'home',          'subs' => ['Renda', 'Luz/Água', 'Internet', 'Gás', 'Condomínio', 'Obras', 'Mobiliário', 'Limpeza']],
        'alimentacao' => ['db' => 'Alimentação',  'title' => 'Alimentação',  'icon' => 'shopping-cart', 'subs' => ['Supermercado', 'Restaurante', 'Café', 'Takeaway', 'Delivery', 'Padaria', 'Mercado']],
        'saude'       => ['db' => 'Saúde',        'title' => 'Saúde',        'icon' => 'heart',         'subs' => ['Consulta', 'Farmácia', 'Exame', 'Dentista', 'Óptica', 'Ginásio', 'Seguro Saúde']],
        'tecnologia'  => ['db' => 'Tecnologia',   'title' => 'Tecnologia',   'icon' => 'cpu-chip',      'subs' => ['Software', 'Hardware', 'Subscrição', 'Domínio', 'Hosting', 'Acessório', 'Reparação']],
        'educacao'    => ['db' => 'Educação',     'title' => 'Educação',     'icon' => 'academic-cap',  'subs' => ['Propinas', 'Livros', 'Curso', 'Certificação', 'Material', 'Formação']],
        'emprestimos' => ['db' => 'Empréstimos',  'title' => 'Empréstimos',  'icon' => 'banknotes',     'subs' => ['Prestação', 'Juros', 'Amortização', 'Seguro Associado']],
        'seguros'        => ['db' => 'Seguros',        'title' => 'Seguros',        'icon' => 'shield-check',  'subs' => ['Automóvel', 'Habitação', 'Saúde', 'Vida', 'Multirriscos']],
        'transporte'     => ['db' => 'Transporte',     'title' => 'Transporte',     'icon' => 'bolt',          'subs' => ['Metro', 'Autocarro', 'Comboio', 'Táxi', 'Uber/Bolt', 'Bilhete']],
        'entretenimento' => ['db' => 'Entretenimento', 'title' => 'Entretenimento', 'icon' => 'film',          'subs' => ['Cinema', 'Streaming', 'Concertos', 'Jogos', 'Lazer', 'Viagens']],
        'outras'         => ['db' => 'Outras',         'title' => 'Outras',         'icon' => 'ellipsis-horizontal', 'subs' => ['Geral', 'Outros']],
    ];

    // Se for uma das categorias base, carrega a config manual
    if (isset($configs[$this->slug])) {
        $c = $configs[$this->slug];
        $this->dbName        = $c['db'];
        $this->title         = $c['title'];
        $this->icon          = $c['icon'];
        $this->subcategories = $c['subs'];
        return;
    }

    // 2. Se não for fixa, procura na base de dados pelo SLUG
    $workspaceId = auth()->user()->current_workspace_id;

    $category = Category::where('workspace_id', $workspaceId)
        ->where('slug', $this->slug)
        ->first();

    if (! $category) {
        $category = Category::where('workspace_id', $workspaceId)
            ->get()
            ->first(fn (Category $c) => Str::slug($c->name) === $this->slug);
    }

    if ($category) {
        if (blank($category->slug)) {
            $category->slug = Category::uniqueSlugFor($category->name, $workspaceId, $category->id);
            $category->saveQuietly();
        }

        $this->dbName        = $category->name;
        $this->title         = $category->name;
        $this->icon          = $category->icon ?? 'tag';
        $this->subcategories = ['Geral', 'Outros'];
        return;
    }

    abort(404);
}

    // ─────────────────────────────────────────────────────────────────
    //  SCANNER IA
    // ─────────────────────────────────────────────────────────────────
    public function scanReceiptWithAI(): void
    {
        $this->scanSuccess = false;
        $this->scanError   = '';
        $this->scannedData = [];
        $this->isScanning  = true;

        if (!$this->receipt) {
            $this->scanError  = 'Nenhum ficheiro selecionado.';
            $this->isScanning = false;
            return;
        }

        try {
            $imageFullPath = $this->receipt->getRealPath();

            if (!$imageFullPath || !file_exists($imageFullPath)) {
                throw new \Exception('Não foi possível ler o ficheiro enviado.');
            }

            $imageData = base64_encode(file_get_contents($imageFullPath));
            $mimeType  = $this->receipt->getMimeType() ?: 'image/jpeg';

            $subsStr = implode(', ', $this->subcategories);

            $prompt = <<<PROMPT
Analisa esta fatura/recibo com atenção. Devolve APENAS um objeto JSON válido, sem markdown, sem explicações.
O JSON deve ter exatamente estes campos:
{
  "amount": <número decimal, valor total pago>,
  "date": "<data no formato YYYY-MM-DD>",
  "store": "<nome do estabelecimento ou fornecedor>",
  "subcategory": "<escolhe a mais adequada de: {$subsStr}>",
  "invoice_number": "<número da fatura se visível, caso contrário null>",
  "nif_emitter": "<NIF/número fiscal do emitente se visível, caso contrário null>",
  "payment_method": "<Multibanco, Cartão Crédito, Dinheiro, MB Way, Transferência ou desconhecido>",
  "tax": <valor do IVA se discriminado, caso contrário null>,
  "notes": "<observação útil sobre a despesa, máx 1 frase, ou null>"
}
PROMPT;

            $apiKey = env('OPENROUTER_API_KEY');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'      => 'google/gemini-2.5-flash',
                'max_tokens' => 2000,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => [
                            ['type' => 'text',      'text'      => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => 'data:' . $mimeType . ';base64,' . $imageData]],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new \Exception('Erro API: ' . ($error['error']['message'] ?? 'Falha na comunicação'));
            }

            $rawText = $response->json('choices.0.message.content') ?? '';
            $rawText = preg_replace('/```json|```/i', '', $rawText);
            $rawText = trim($rawText);

            if (!preg_match('/\{.*\}/s', $rawText, $matches)) {
                throw new \Exception('A IA não devolveu um formato de dados reconhecível.');
            }

            $result = json_decode($matches[0], true);

            if (!$result || json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON inválido: ' . json_last_error_msg());
            }

            // Normalizar amount
            $amount = $result['amount'] ?? 0;
            if (is_string($amount)) {
                $amount = str_replace([' ', "\u{00A0}"], '', $amount);
                if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $amount)) {
                    $amount = str_replace(['.', ','], ['', '.'], $amount);
                } else {
                    $amount = str_replace(',', '.', $amount);
                }
                $amount = (float) filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            }

            // Normalizar data
            $date = $result['date'] ?? null;
            try {
                $date = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');
            } catch (\Exception) {
                $date = now()->format('Y-m-d');
            }

            // Match subcategoria
            $suggested  = strtolower(trim($result['subcategory'] ?? ''));
            $matchedSub = null;
            foreach ($this->subcategories as $sub) {
                if (Str::contains(strtolower($sub), $suggested) || Str::contains($suggested, strtolower($sub))) {
                    $matchedSub = $sub;
                    break;
                }
            }
            $matchedSub ??= $this->subcategories[0] ?? 'Geral';

            $this->scannedData = [
                'amount'         => $amount,
                'date'           => $date,
                'store'          => trim($result['store'] ?? ''),
                'subcategory'    => $matchedSub,
                'invoice_number' => $result['invoice_number'] ?? null,
                'nif_emitter'    => $result['nif_emitter'] ?? null,
                'payment_method' => $result['payment_method'] ?? 'desconhecido',
                'tax'            => is_numeric($result['tax'] ?? null) ? (float) $result['tax'] : null,
                'notes'          => $result['notes'] ?? null,
                'items'          => $result['items'] ?? [],
                'meta'           => [],
            ];

            $this->amount      = $amount;
            $this->spent_at    = $date;
            $this->description = trim($result['store'] ?? '');
            $this->subcategory = $matchedSub;

            $this->scanSuccess = true;
            $this->dispatch('scan-completed');

        } catch (\Exception $e) {
            Log::error('CategoryHub scanReceiptWithAI: ' . $e->getMessage());
            $this->scanError = 'Erro ao processar: ' . $e->getMessage();
        } finally {
            $this->isScanning = false;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  BUDGET
    // ─────────────────────────────────────────────────────────────────


    public function openCreateModal(): void
    {
        $this->reset(['amount', 'description', 'subcategory', 'receipt', 'meta', 'scannedData', 'scanSuccess', 'scanError', 'editingId']);
        $this->spent_at = now()->format('Y-m-d');
    }

    public function openScannerModal(): void
    {
        $this->resetValidation('receipt');
        $this->scanError = '';
        $this->scannedData = [];
        $this->scanSuccess = false;
        $this->receipt = null;
    }

public function editExpense(int $id)
{
    $expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
        ->findOrFail($id);

    $this->editingId = $expense->id;
    $this->amount = $expense->amount;
    $this->spent_at = $expense->spent_at->format('Y-m-d');
    $this->subcategory = $expense->subcategory;
    $this->description = $expense->description;

    // Carregar os metadados (campos personalizados)
    $metaRaw = $expense->metadata;
    $this->meta = is_array($metaRaw) ? $metaRaw : (json_decode($metaRaw, true) ?? []);

    // Abre o modal via Alpine
    $this->dispatch('open-add-expense-modal');
}
    public function updateBudget(): void
    {
        $this->validate(['budgetLimit' => 'numeric|min:0']);

        Category::updateOrCreate(
            ['name' => $this->dbName, 'workspace_id' => auth()->user()->current_workspace_id],
            ['user_id' => auth()->id(), 'budget_limit' => $this->budgetLimit]
        );

        $this->editingBudget = false;
        $this->dispatch('toast', text: 'Limite orçamental atualizado!');
    }

    // ─────────────────────────────────────────────────────────────────
    //  SAVE
    // ─────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $this->validate([
            'amount'      => 'required|numeric|min:0.01',
            'spent_at'    => 'required|date',
            'subcategory' => 'required',
        ]);

        $category = Category::firstOrCreate([
            'name'         => $this->dbName,
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id'      => auth()->id(),
        ]);

        $data = [
            'user_id'      => auth()->id(),
            'category_id'  => $category->id,
            'subcategory'  => $this->subcategory,
            'amount'       => $this->amount,
            'currency'     => $this->currency,
            'description'  => $this->description,
            'spent_at'     => $this->spent_at,
            'workspace_id' => auth()->user()->current_workspace_id,
            'metadata'     => !empty($this->meta) ? $this->meta : null,
        ];

        if ($this->editingId) {
            Expense::find($this->editingId)->update($data);
            $msg = 'Registo atualizado!';
            $expense = Expense::find($this->editingId);
        } else {
            $expense = Expense::create($data);
            $msg = 'Gasto guardado com sucesso!';
        }

        $this->trackPriceHistory($expense, $category);
        app(PriceComparisonService::class)->recordFromExpense($expense, $category);

        $this->reset(['amount', 'description', 'subcategory', 'receipt', 'meta', 'scannedData', 'scanSuccess', 'scanError', 'editingId']);
        $this->spent_at = now()->format('Y-m-d');

        $this->dispatch('modal-close-add-expense');
        $this->dispatch('toast', text: $msg);
    }

    // ─────────────────────────────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────────────────────────────
    public function deleteExpense(int $id): void
    {
        Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)
            ->delete();

        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    private function trackPriceHistory(Expense $expense, Category $category): void
    {
        $meta = is_array($expense->metadata) ? $expense->metadata : [];
        $unitPrice = $meta['preco_litro'] ?? $meta['preco_unitario'] ?? $meta['unit_price'] ?? null;
        $unitType = $meta['preco_litro'] ? 'litro' : ($meta['preco_unitario'] ? 'unidade' : null);
        $merchant = $meta['local'] ?? $meta['supermercado'] ?? $meta['estabelecimento'] ?? $expense->subcategory;

        if ($unitPrice && is_numeric($unitPrice)) {
            PriceHistory::create([
                'workspace_id' => $expense->workspace_id,
                'category_id' => $category->id,
                'expense_id' => $expense->id,
                'unit_price' => $unitPrice,
                'unit_type' => $unitType,
                'merchant' => $merchant,
                'recorded_at' => $expense->spent_at,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  RENDER
    // ─────────────────────────────────────────────────────────────────
   public function render()
{
    $user = auth()->user();
    $currentWs = $user->currentWorkspace;

    // 1. Busca os dados da categoria para saber a cor e campos
    $category = \App\Models\Category::where('name', $this->dbName)
        ->where('workspace_id', $currentWs->id)
        ->first();

    $categoryColor = $category->color ?? '#6366f1';

    // 2. Query das despesas
    $query = Expense::whereHas('category', fn ($q) => $q->where('name', $this->dbName))
        ->where('workspace_id', $currentWs->id);

    // 3. Carrega os campos dinâmicos
    $categoryFields = [];
    if ($category && $category->fields->count() > 0) {
        $categoryFields[$this->slug] = [
            'icon'   => $category->icon ?? 'tag',
            'fields' => $category->fields->sortBy('order')->map(fn($f) => [
                'name' => $f->key,
                'label' => $f->label,
                'type' => $f->type,
                'options' => $f->options ?? []
            ])->toArray(),
        ];
    }

    return view('livewire.category-hub', [
        'expenses'       => $query->latest('spent_at')->get(),
        'spentThisMonth' => $query->whereMonth('spent_at', now()->month)->sum('amount'),
        'currentWs'      => $currentWs,
        'categoryFields' => $categoryFields,
        'categoryColor'  => $categoryColor,
        'canManage'      => in_array($user->currentRole(), ['admin', 'editor', 'member'], true),
        'isOwner'        => $user->isOwner(),
        'priceHistory'   => $category ? PriceHistory::where('category_id', $category->id)
            ->where('workspace_id', $currentWs->id)
            ->orderByDesc('recorded_at')
            ->limit(10)
            ->get() : collect(),
        'priceAnalysis'  => $category
            ? app(PriceComparisonService::class)->getAnalysis($currentWs, $category)
            : null,
    ]);
}
}
