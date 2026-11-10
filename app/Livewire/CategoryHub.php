<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Expense;
use App\Models\PriceHistory;
use App\Services\PriceComparisonService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class CategoryHub extends Component
{
    use WithFileUploads;

    public $slug;

    public $title;

    public $icon;

    public $subcategories = [];

    public $dbName;

    public $amount;

    public $customSubcategory;

    public $description;

    public $spent_at;

    public $subcategory;

    public $meta = [];

    public $currency = 'EUR';

    // Destino do pagamento: conta bancária ou dinheiro físico (vazio = físico)
    public $bankAccountId = '';

    public bool $transportPassMode = false;

    public $transportPassName = 'Passe mensal';

    public $transportPassAmount;

    public $transportPassStartedAt;

    public $transportPassPaymentMethod = 'card';

    public bool $transportPassPaidByCompany = false;

    public $receipt;

    public $document;

    public ?string $pendingDocumentPath = null;

    public ?string $pendingDocumentName = null;

    public ?string $pendingDocumentMime = null;

    public ?string $savedDocumentPath = null;

    public $budgetLimit;

    public $editingBudget = false;

    public $isScanning = false;

    public $scannedData = [];

    public $editingId = null;

    public $scanSuccess = false;

    public $scanError = '';

    protected $rules = [
        'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
    ];

    public function updatedDocument(): void
    {
        $this->resetValidation('document');
        $this->scanError = '';
        $this->scanSuccess = false;
        if (! $this->document) return;
        try {
            $this->validate(['document' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480']);
            $this->scanReceiptWithAI();
        } catch (ValidationException $e) {
            $this->scanError = 'Ficheiro inválido: '.collect($e->errors())->flatten()->first();
            $this->document = null;
        }
    }

    public function updatedReceipt(): void
    {
        $this->resetValidation('receipt');
        $this->scanError = '';

        try {
            $this->validate([
                'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
            ]);
        } catch (ValidationException $e) {
            $this->scanError = 'Ficheiro inválido: '.collect($e->errors())->flatten()->first();
            $this->receipt = null;
        }
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:5120',
        ]);
    }

    #[Computed]
    public function bankAccounts()
    {
        $query = BankAccount::where('workspace_id', auth()->user()->current_workspace_id);
        if (auth()->user()->currentWorkspace?->type === 'personal') {
            $query->where('user_id', auth()->id());
        }
        return $query->orderBy('name')->get();
    }

    // ─────────────────────────────────────────────────────────────────
    //  MOUNT
    // ─────────────────────────────────────────────────────────────────
    public function mount($slug): void
    {
        $this->slug = $slug;
        $this->spent_at = now()->format('Y-m-d');
        $this->currency = auth()->user()->currentWorkspace->currency ?? 'EUR';
        $this->setHubConfig();

        $pendingScan = session()->pull('pending_expense_scan:'.auth()->user()->current_workspace_id.':'.auth()->id());

        if (request()->boolean('open_scan_review') && is_array($pendingScan)) {
            $result = $pendingScan['data'] ?? $pendingScan;
            $toNumber = static function ($value): ?float {
                if ($value === null || $value === '') return null;
                if (is_string($value)) {
                    $value = str_replace([' ', "\u{00A0}", '€'], '', $value);
                    if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
                        $value = str_replace(['.', ','], ['', '.'], $value);
                    } else {
                        $value = str_replace(',', '.', $value);
                    }
                }
                return is_numeric($value) ? (float) $value : null;
            };

            $suggestedSubcategory = mb_strtolower(trim((string) ($result['subcategory'] ?? '')));
            foreach ($this->subcategories as $availableSubcategory) {
                if ($suggestedSubcategory !== '' && (
                    Str::contains(mb_strtolower($availableSubcategory), $suggestedSubcategory) ||
                    Str::contains($suggestedSubcategory, mb_strtolower($availableSubcategory))
                )) {
                    $this->subcategory = $availableSubcategory;
                    break;
                }
            }

            if (blank($this->subcategory)) {
                $this->subcategory = in_array('Geral', $this->subcategories, true)
                    ? 'Geral'
                    : ($this->subcategories[0] ?? 'Outros');
            }

            $this->amount = $toNumber($result['amount'] ?? null)
                ?? $toNumber($result['gross_amount'] ?? null)
                ?? $toNumber($result['net_amount'] ?? null);
            $this->spent_at = $result['date'] ?? now()->format('Y-m-d');
            $store = trim((string) ($result['store'] ?? ''));
            $description = trim((string) ($result['description'] ?? ''));
            $this->description = $description !== '' ? $description : $store;

            $numericKeys = ['quantity', 'unit_price', 'vat_rate', 'gross_amount', 'net_amount', 'vat_amount', 'amount_tendered', 'change_amount'];
            $metaKeys = [
                'item_name', 'time', 'quantity', 'unit', 'unit_price', 'vat_rate',
                'gross_amount', 'net_amount', 'vat_amount', 'tax_incidence',
                'amount_tendered', 'change_amount', 'payment_condition', 'payment_method',
                'company_nif', 'customer_nif', 'invoice_number', 'notes',
            ];
            foreach ($metaKeys as $key) {
                $value = $result[$key] ?? null;
                if ($value !== null && $value !== '') {
                    $this->meta[$key] = in_array($key, $numericKeys, true)
                        ? $toNumber($value)
                        : trim((string) $value);
                }
            }

            $this->pendingDocumentPath = $pendingScan['document_path'] ?? null;
            if ($this->pendingDocumentPath) {
                session()->put('pending_expense_scan_preview:'.auth()->user()->current_workspace_id.':'.auth()->id(), $this->pendingDocumentPath);
            }
            $this->pendingDocumentName = $pendingScan['document_name'] ?? null;
            $this->pendingDocumentMime = $pendingScan['document_mime'] ?? null;
            $this->scanSuccess = true;
            $this->scannedData = $result;
            $this->dispatch('open-scanned-expense-form');
        }

        $categoryQuery = Category::where('name', $this->dbName)
            ->where('workspace_id', auth()->user()->current_workspace_id);
        if (auth()->user()->currentWorkspace?->type === 'personal') {
            $categoryQuery->where('user_id', auth()->id());
        }
        $category = $categoryQuery->first();

        $this->budgetLimit = $category ? (float) $category->budget_limit : 0;

        if (request()->boolean('open_new')) {
            $this->dispatch('open-add-expense-modal');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  HUB CONFIG
    // ─────────────────────────────────────────────────────────────────
    private function setHubConfig(): void
    {
        // 1. Configurações para as categorias "estáticas" (estilos e labels)
        $configs = [
            'carro' => ['db' => 'Carro',       'title' => 'Carro',        'icon' => 'truck',         'subs' => ['Gasolina', 'Manutenção', 'Seguro', 'Portagens', 'Estacionamento', 'Lavagem', 'Multa', 'Inspeção']],
            'casa' => ['db' => 'Casa',         'title' => 'Casa',         'icon' => 'home',          'subs' => ['Renda', 'Luz/Água', 'Internet', 'Gás', 'Condomínio', 'Obras', 'Mobiliário', 'Limpeza']],
            'alimentacao' => [
                'db' => 'Alimentação',
                'title' => 'Alimentação',
                'icon' => 'shopping-cart',
                'subs' => [
                    'Supermercado / Hiper',
                    'Restaurante / Almoço',
                    'Jantares Sociais / Grupo',
                    'Café & Snacks',
                    'Padaria & Padaria',
                    'Takeaway (Uber/Glovo)',
                    'Fast Food',
                    'Mercado & Frescos',
                    'Talho & Peixaria',
                    'Bares & Saídas à Noite',
                    'Suplementos & Nutrição',
                    'Cantina / Refeitório',
                    'Máquinas de Vending',
                    'Pequeno-almoço Fora',
                    'Guloseimas & Doces',
                    'Outro',
                ],
            ],
            'saude' => ['db' => 'Saúde',        'title' => 'Saúde',        'icon' => 'heart',         'subs' => ['Consulta', 'Farmácia', 'Exame', 'Dentista', 'Óptica', 'Ginásio', 'Seguro Saúde']],
            'tecnologia' => ['db' => 'Tecnologia',   'title' => 'Tecnologia',   'icon' => 'cpu-chip',      'subs' => ['Software', 'Hardware', 'Subscrição', 'Domínio', 'Hosting', 'Acessório', 'Reparação']],
            'educacao' => ['db' => 'Educação',     'title' => 'Educação',     'icon' => 'academic-cap',  'subs' => ['Propinas', 'Livros', 'Curso', 'Certificação', 'Material', 'Formação']],
            'emprestimos' => ['db' => 'Empréstimos',  'title' => 'Empréstimos',  'icon' => 'banknotes',     'subs' => ['Prestação', 'Juros', 'Amortização', 'Seguro Associado']],
            'seguros' => ['db' => 'Seguros',        'title' => 'Seguros',        'icon' => 'shield-check',  'subs' => ['Automóvel', 'Habitação', 'Saúde', 'Vida', 'Multirriscos']],
            'transporte' => ['db' => 'Transporte',     'title' => 'Transporte',     'icon' => 'bolt',          'subs' => ['Metro', 'Autocarro', 'Comboio', 'Táxi', 'Uber/Bolt', 'Bilhete']],
            'entretenimento' => ['db' => 'Entretenimento', 'title' => 'Entretenimento', 'icon' => 'film',          'subs' => ['Cinema', 'Streaming', 'Concertos', 'Jogos', 'Lazer', 'Viagens']],
            'outras' => ['db' => 'Outras',         'title' => 'Outras',         'icon' => 'ellipsis-horizontal', 'subs' => ['Geral', 'Outros']],
        ];

        // Se for uma das categorias base, carrega a config manual (Lógica que aceita variações como -2, -3)
        $configKey = null;
        foreach (array_keys($configs) as $key) {
            if (str_starts_with($this->slug, $key)) {
                $configKey = $key;
                break;
            }
        }

        if ($configKey) {
            $c = $configs[$configKey];
            $this->dbName = $c['db'];
            $this->title = $c['title'];
            $this->icon = $c['icon'];
            $this->subcategories = $c['subs'];

            return;
        }

        // 2. Se não for fixa, procura na base de dados pelo SLUG
        $workspaceId = auth()->user()->current_workspace_id;

        $categoryQuery = Category::where('workspace_id', $workspaceId)
            ->where('slug', $this->slug);
        if (auth()->user()->currentWorkspace?->type === 'personal') {
            $categoryQuery->where('user_id', auth()->id());
        }
        $category = $categoryQuery->first();

        if (! $category) {
            $categoryQuery = Category::where('workspace_id', $workspaceId);
            if (auth()->user()->currentWorkspace?->type === 'personal') {
                $categoryQuery->where('user_id', auth()->id());
            }
            $category = $categoryQuery->get()
                ->first(fn (Category $c) => Str::slug($c->name) === $this->slug);
        }

        if ($category) {
            if (blank($category->slug)) {
                $category->slug = Category::uniqueSlugFor($category->name, $workspaceId, $category->id);
                $category->saveQuietly();
            }

            $this->dbName = $category->name;
            $this->title = $category->name;
            $this->icon = $category->icon ?? 'tag';
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
        $this->scanError = '';
        $this->scannedData = [];
        $this->isScanning = true;
        $file = $this->document ?: $this->receipt;

        if (! $file) {
            $this->scanError = 'Seleciona primeiro uma fatura ou recibo.';
            $this->isScanning = false;
            return;
        }

        try {
            $filePath = $file->getRealPath();
            if (! $filePath || ! file_exists($filePath)) throw new \Exception('Não foi possível ler o ficheiro enviado.');

            $mimeType = $file->getMimeType() ?: 'image/jpeg';
            $fileData = base64_encode(file_get_contents($filePath));
            $contentPart = str_starts_with($mimeType, 'image/')
                ? ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mimeType.';base64,'.$fileData]]
                : ['type' => 'file', 'file' => ['filename' => $file->getClientOriginalName(), 'file_data' => 'data:'.$mimeType.';base64,'.$fileData]];

            $subsStr = implode(', ', $this->subcategories);
            $prompt = <<<PROMPT
Lê esta fatura/recibo português e extrai todos os campos visíveis. Devolve APENAS JSON válido.
Nunca inventes nem calcules dados que não estejam impressos; usa null quando ausente/ilegível.
Números decimais com ponto, data YYYY-MM-DD, hora HH:MM. Não confundas NIF do emitente com NIF do cliente.
Escolhe subcategoria apenas desta lista: {$subsStr}.
amount é o total pago; gross_amount é o valor ilíquido; net_amount é a base/valor líquido; vat_amount é o valor do IVA.
payment_condition: "Pronto pagamento", "A prazo", "Pagamento parcial", "Pagamento diferido" ou null.
payment_method: "Dinheiro", "Multibanco", "Cartão", "MB Way", "Transferência" ou null.
Devolve exatamente esta estrutura:
{
 "amount":null,"item_name":null,"date":null,"time":null,"quantity":null,"unit":null,
 "store":null,"description":null,"subcategory":null,"unit_price":null,"vat_rate":null,
 "gross_amount":null,"net_amount":null,"vat_amount":null,"tax_incidence":null,
 "amount_tendered":null,"change_amount":null,"payment_condition":null,"payment_method":null,
 "company_nif":null,"customer_nif":null,"invoice_number":null,"notes":null
}
PROMPT;

            $apiKey = config('services.openrouter.api_key');
            if (blank($apiKey)) throw new \Exception('A chave da IA não está configurada.');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 5))
                ->timeout((int) config('limits.ai.request_timeout_seconds', 60))
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'google/gemini-2.5-flash',
                    'max_tokens' => 3000,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [['type' => 'text', 'text' => $prompt], $contentPart],
                    ]],
                ]);

            if (! $response->successful()) {
                throw new \Exception('A IA não conseguiu analisar a fatura: '.($response->json('error.message') ?? 'erro de comunicação.'));
            }

            $rawText = trim(preg_replace('/\x60{3}json|\x60{3}/i', '', (string) $response->json('choices.0.message.content')));
            if (! preg_match('/\{.*\}/s', $rawText, $matches)) throw new \Exception('A IA não devolveu dados reconhecíveis.');
            $result = json_decode($matches[0], true);
            if (! is_array($result) || json_last_error() !== JSON_ERROR_NONE) throw new \Exception('Não foi possível interpretar os dados extraídos da fatura.');

            $toNumber = static function ($value): ?float {
                if ($value === null || $value === '') return null;
                if (is_string($value)) {
                    $value = str_replace([' ', "\u{00A0}", '€'], '', $value);
                    if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) $value = str_replace(['.', ','], ['', '.'], $value);
                    else $value = str_replace(',', '.', $value);
                }
                return is_numeric($value) ? (float) $value : null;
            };

            $date = null;
            if (! empty($result['date'])) {
                try { $date = Carbon::parse($result['date'])->format('Y-m-d'); }
                catch (\Throwable) { $date = null; }
            }

            $suggested = mb_strtolower(trim((string) ($result['subcategory'] ?? '')));
            $matchedSub = null;
            foreach ($this->subcategories as $sub) {
                if ($suggested !== '' && (Str::contains(mb_strtolower($sub), $suggested) || Str::contains($suggested, mb_strtolower($sub)))) {
                    $matchedSub = $sub;
                    break;
                }
            }

            $mapping = [
                'item_name' => 'item_name', 'time' => 'time', 'quantity' => 'quantity', 'unit' => 'unit',
                'local' => 'store', 'unit_price' => 'unit_price', 'vat_rate' => 'vat_rate',
                'gross_amount' => 'gross_amount', 'net_amount' => 'net_amount', 'vat_amount' => 'vat_amount',
                'tax_incidence' => 'tax_incidence', 'amount_tendered' => 'amount_tendered',
                'change_amount' => 'change_amount', 'payment_condition' => 'payment_condition',
                'payment_method' => 'payment_method', 'company_nif' => 'company_nif',
                'customer_nif' => 'customer_nif', 'invoice_number' => 'invoice_number', 'notes' => 'notes',
            ];
            $numericKeys = ['quantity', 'unit_price', 'vat_rate', 'gross_amount', 'net_amount', 'vat_amount', 'amount_tendered', 'change_amount'];
            foreach ($mapping as $metaKey => $resultKey) {
                $value = $result[$resultKey] ?? null;
                if ($value !== null && $value !== '') {
                    $this->meta[$metaKey] = in_array($metaKey, $numericKeys, true) ? $toNumber($value) : trim((string) $value);
                }
            }

            $amount = $toNumber($result['amount'] ?? null);
            if ($amount !== null) $this->amount = $amount;
            if ($date !== null) $this->spent_at = $date;
            $store = trim((string) ($result['store'] ?? ''));
            $description = trim((string) ($result['description'] ?? ''));
            if ($description !== '') $this->description = $description;
            elseif ($store !== '') $this->description = $store;
            if ($matchedSub !== null) $this->subcategory = $matchedSub;

            $this->scannedData = $result;
            $this->scannedData['amount'] = $amount;
            $this->scannedData['date'] = $date;
            $this->scannedData['store'] = $store;
            $this->scannedData['subcategory'] = $matchedSub;
            $this->scannedData['meta'] = $this->meta;
            $this->scanSuccess = true;
            $this->dispatch('toast', variant: 'success', text: 'Fatura analisada. Os campos identificados foram preenchidos automaticamente.');
        } catch (\Throwable $e) {
            Log::error('CategoryHub scanReceiptWithAI: '.$e->getMessage());
            $this->scanError = 'Não foi possível analisar a fatura: '.$e->getMessage();
        } finally {
            $this->isScanning = false;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  BUDGET
    // ─────────────────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->reset(['amount', 'description', 'subcategory', 'receipt', 'document', 'meta', 'scannedData', 'scanSuccess', 'scanError', 'editingId', 'bankAccountId', 'pendingDocumentPath', 'pendingDocumentName', 'pendingDocumentMime', 'savedDocumentPath']);
        $this->spent_at = now()->format('Y-m-d');
        $this->transportPassMode = false;
        $this->transportPassName = 'Passe mensal';
        $this->transportPassAmount = null;
        $this->transportPassStartedAt = now()->format('Y-m-d');
        $this->transportPassPaymentMethod = 'card';
        $this->transportPassPaidByCompany = false;
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
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        $this->editingId = $expense->id;
        $this->savedDocumentPath = $expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)
            ? $expense->receipt_path
            : null;
        $this->pendingDocumentPath = null;
        $this->pendingDocumentName = null;
        $this->pendingDocumentMime = null;
        $this->document = null;
        $this->amount = $expense->amount;
        $this->spent_at = $expense->spent_at->format('Y-m-d');
        $this->subcategory = trim($expense->subcategory);
        $this->description = $expense->description;
        $this->bankAccountId = $expense->bank_account_id ?: '';
        if (! in_array($this->subcategory, $this->subcategories)) {
            $this->customSubcategory = $this->subcategory;
            $this->subcategory = 'Outro';
        }
        // Carregar os metadados (campos personalizados)
        $metaRaw = $expense->metadata;
        $this->meta = is_array($metaRaw) ? $metaRaw : (json_decode($metaRaw, true) ?? []);

        // Abre o modal via Alpine
        $this->dispatch('open-add-expense-modal');
    }

    public function updateBudget(): void
    {
        $this->validate(['budgetLimit' => 'numeric|min:0']);

        $categoryQuery = Category::where('name', $this->dbName)
            ->where('workspace_id', auth()->user()->current_workspace_id);
        if (auth()->user()->currentWorkspace?->type === 'personal') {
            $categoryQuery->where('user_id', auth()->id());
        }
        $category = $categoryQuery->first();
        if ($category) {
            $category->update(['budget_limit' => $this->budgetLimit]);
        } else {
            Category::create([
                'name' => $this->dbName,
                'workspace_id' => auth()->user()->current_workspace_id,
                'user_id' => auth()->id(),
                'budget_limit' => $this->budgetLimit,
            ]);
        }

        $this->editingBudget = false;
        $this->dispatch('toast', text: 'Limite orçamental atualizado!');
    }

    // ─────────────────────────────────────────────────────────────────
    //  SAVE
    // ─────────────────────────────────────────────────────────────────
    public function save(): void
    {
        if (str_starts_with((string) $this->slug, 'transporte') && $this->transportPassMode) {
            $this->saveTransportPass();
            return;
        }

        try {
            $this->validate([
                'amount' => 'required|numeric|min:0.01',
                'spent_at' => 'required|date',
                'subcategory' => 'required',
                'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
                'meta.vat_amount' => 'nullable|numeric|min:0',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('toast', variant: 'error', text: 'Não foi possível confirmar: verifica os campos obrigatórios assinalados.');
            throw $e;
        }

        if ($this->bankAccountId) {
            $accountQuery = BankAccount::where('workspace_id', auth()->user()->current_workspace_id);

            if (auth()->user()->currentWorkspace?->type === 'personal') {
                $accountQuery->where('user_id', auth()->id());
            }

            $account = $accountQuery->find($this->bankAccountId);

            if ($account) {
                $available = (float) $account->current_balance;

                // Ao editar, devolve o valor antigo desta despesa antes de validar o novo montante
                if ($this->editingId) {
                    $originalQuery = Expense::where('workspace_id', auth()->user()->current_workspace_id);

                    if (auth()->user()->currentWorkspace?->type === 'personal') {
                        $originalQuery->where('user_id', auth()->id());
                    }

                    $original = $originalQuery->find($this->editingId);
                    if ($original && (int) $original->bank_account_id === (int) $this->bankAccountId) {
                        $available += (float) $original->amount;
                    }
                }

                if ((float) $this->amount > $available) {
                    $this->addError('bankAccountId', 'Saldo insuficiente nesta conta.');
                    $this->dispatch('toast', variant: 'error', text: 'Saldo insuficiente em "'.$account->name.'": disponível '.number_format($available, 2, ',', '.').'€.');

                    return;
                }
            }
        }

        $category = Category::firstOrCreate([
            'name' => $this->dbName,
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id' => auth()->id(),
        ]);

        $data = [
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'subcategory' => ($this->subcategory === 'Outro') ? $this->customSubcategory : $this->subcategory,
            'amount' => $this->amount,
            'vat_amount' => is_numeric($this->meta['vat_amount'] ?? null) ? (float) $this->meta['vat_amount'] : 0,
            'currency' => $this->currency,
            'description' => $this->description,
            'spent_at' => $this->spent_at,
            'workspace_id' => auth()->user()->current_workspace_id,
            'bank_account_id' => $this->bankAccountId ?: null,
            'metadata' => ! empty($this->meta) ? $this->meta : null,
        ];

        $existingExpense = $this->editingId
            ? Expense::where('workspace_id', auth()->user()->current_workspace_id)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->find($this->editingId)
            : null;
        $previousReceiptPath = $existingExpense?->receipt_path;

        if ($this->document) {
            $data['receipt_path'] = $this->document->store('expenses/receipts', 'public');
        } elseif ($this->pendingDocumentPath) {
            $disk = Storage::disk('public');
            if (! $disk->exists($this->pendingDocumentPath)) {
                throw new \RuntimeException('O documento temporário da fatura já não está disponível. Anexa-o novamente.');
            }

            $extension = pathinfo($this->pendingDocumentPath, PATHINFO_EXTENSION);
            $finalPath = 'expenses/receipts/'.Str::uuid().($extension ? '.'.$extension : '');

            // Copia primeiro para o destino definitivo; não apaga o anexo anterior antes de guardar.
            if (! $disk->copy($this->pendingDocumentPath, $finalPath)) {
                throw new \RuntimeException('Não foi possível preparar o documento da fatura para guardar.');
            }

            $data['receipt_path'] = $finalPath;
        }

        $user = auth()->user();

        if ($this->editingId) {
            $expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($this->editingId);
            $expense->update($data);
            $msg = 'Registo atualizado! ✅';
        } else {
            $expense = Expense::create($data);
            $user->awardXp(20, 'despesa registada');
            $msg = 'Nova despesa adicionada! '.$user->xpToastText(20, 'despesa registada');
        }

        // Só remove o anexo antigo depois de a despesa ter sido guardada com sucesso.
        if ($previousReceiptPath && $expense->receipt_path && $previousReceiptPath !== $expense->receipt_path) {
            Storage::disk('public')->delete($previousReceiptPath);
        }
        if ($this->pendingDocumentPath && $expense->receipt_path !== $this->pendingDocumentPath) {
            Storage::disk('public')->delete($this->pendingDocumentPath);
        }

        try {
            $this->trackPriceHistory($expense, $category);
            app(PriceComparisonService::class)->recordFromExpense($expense, $category);
        } catch (\Throwable $analyticsError) {
            Log::warning('Expense saved but optional price analytics failed: '.$analyticsError->getMessage());
        }
        session()->forget('pending_expense_scan_preview:'.auth()->user()->current_workspace_id.':'.auth()->id());

        $this->reset(['amount', 'description', 'subcategory', 'receipt', 'document', 'meta', 'scannedData', 'scanSuccess', 'scanError', 'editingId', 'bankAccountId', 'pendingDocumentPath', 'pendingDocumentName', 'pendingDocumentMime', 'savedDocumentPath']);
        $this->spent_at = now()->format('Y-m-d');

        $this->dispatch('modal-close-add-expense');
        $this->dispatch('toast', variant: 'success', text: $msg);
    }

    private function saveTransportPass(): void
    {
        $this->validate([
            'transportPassName' => 'required|string|max:120',
            'transportPassAmount' => 'required|numeric|min:0.01',
            'transportPassStartedAt' => 'required|date',
            'transportPassPaymentMethod' => 'required|in:card,direct_debit,bank_transfer,paypal,cash',
            'transportPassPaidByCompany' => 'boolean',
        ]);

        $paidByCompany = (bool) $this->transportPassPaidByCompany;
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        $categoryQuery = Category::where('workspace_id', $workspaceId)->where('name', 'Transporte');
        if ($user->currentWorkspace?->type === 'personal') {
            $categoryQuery->where('user_id', $user->id);
        }

        $category = $categoryQuery->first();
        if (! $category) {
            $category = Category::create([
                'name' => 'Transporte',
                'workspace_id' => $workspaceId,
                'user_id' => $user->id,
                'icon' => 'truck',
                'color' => '#0ea5e9',
            ]);
        }

        $startDate = Carbon::parse($this->transportPassStartedAt);

        \App\Models\Subscription::create([
            'user_id' => $user->id,
            'workspace_id' => $workspaceId,
            'category_id' => $category->id,
            'name' => trim($this->transportPassName),
            'amount' => $this->transportPassAmount,
            'billing_day' => $startDate->day,
            'cycle' => 'monthly',
            'status' => 'active',
            'is_active' => true,
            'payment_method' => $this->transportPassPaymentMethod,
            'started_at' => $startDate->toDateString(),
            'renewal_date' => $startDate->copy()->addMonthNoOverflow()->toDateString(),
            'notes' => $paidByCompany
                ? 'Passe de transporte pago pela empresa. Excluído dos cálculos financeiros pessoais.'
                : 'Passe mensal de transporte.',
            'notify_before_billing' => false,
            'paid_by_company' => $paidByCompany,
        ]);

        \Illuminate\Support\Facades\Cache::forget("dashboard:subscriptions-monthly:{$workspaceId}:{$user->id}");
        $user->awardXp(20, 'passe mensal registado');
        $this->reset(['transportPassAmount', 'transportPassPaidByCompany']);
        $this->transportPassName = 'Passe mensal';
        $this->transportPassStartedAt = now()->format('Y-m-d');
        $this->transportPassPaymentMethod = 'card';
        $this->transportPassMode = false;
        $this->dispatch('modal-close-add-expense');
        $message = $paidByCompany
            ? 'Passe registado. Como é pago pela empresa, não entra nas tuas contas.'
            : 'Passe mensal adicionado às assinaturas.';
        $this->dispatch('toast', variant: 'success', text: $message);
    }

    // ─────────────────────────────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────────────────────────────
    public function deleteExpense(int $id): void
    {
        Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id)
            ->delete();

        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    private function trackPriceHistory(Expense $expense, Category $category): void
    {
        $meta = is_array($expense->metadata) ? $expense->metadata : [];

        // 1. Pegamos os valores de forma segura
        $precoLitro = $meta['preco_litro'] ?? null;
        $precoUnitario = $meta['preco_unitario'] ?? $meta['unit_price'] ?? null;

        // 2. Definimos o preço final (Prioridade ao litro)
        $unitPrice = $precoLitro ?? $precoUnitario;

        // 3. Definimos o tipo de unidade de forma segura (usando as variáveis já protegidas)
        $unitType = $precoLitro ? 'litro' : ($precoUnitario ? 'unidade' : null);

        $merchant = $meta['local'] ?? $meta['supermercado'] ?? $meta['estabelecimento'] ?? $expense->subcategory;

        // 4. Só grava se houver um preço válido
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
        $categoryQuery = Category::where('name', $this->dbName)
            ->where('workspace_id', $currentWs->id);
        if ($currentWs->type === 'personal') {
            $categoryQuery->where('user_id', $user->id);
        }
        $category = $categoryQuery->first();

        $categoryColor = $category->color ?? '#6366f1';

        // 2. Query das despesas
        $query = Expense::with('bankAccount')
            ->whereHas('category', fn ($q) => $q->where('name', $this->dbName))
            ->where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id));

        // 3. Carrega os campos dinâmicos
        $categoryFields = [];
        if ($category && $category->fields->count() > 0) {
            $categoryFields[$this->slug] = [
                'icon' => $category->icon ?? 'tag',
                'fields' => $category->fields->sortBy('order')->map(fn ($f) => [
                    'name' => $f->key,
                    'label' => $f->label,
                    'type' => $f->type,
                    'options' => $f->options ?? [],
                ])->toArray(),
            ];
        }

        return view('livewire.category-hub', [
            'expenses' => $query->latest('spent_at')->get(),
            'spentThisMonth' => $query->whereMonth('spent_at', now()->month)->sum('amount'),
            'currentWs' => $currentWs,
            'categoryFields' => $categoryFields,
            'categoryColor' => $categoryColor,
            'canManage' => in_array($user->currentRole(), ['admin', 'editor', 'member'], true),
            'isOwner' => $user->isOwner(),
            'priceHistory' => $category ? PriceHistory::where('category_id', $category->id)
                ->where('workspace_id', $currentWs->id)
                ->when($currentWs->type === 'personal', fn ($q) => $q->whereHas('category', fn ($cq) => $cq->where('user_id', $user->id)))
                ->orderByDesc('recorded_at')
                ->limit(10)
                ->get() : collect(),
            'priceAnalysis' => $category
                ? app(PriceComparisonService::class)->getAnalysis($currentWs, $category)
                : null,
        ]);
    }
}
