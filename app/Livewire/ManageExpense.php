<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Expense;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ManageExpense extends Component
{
    use WithFileUploads;

    public ?Expense $expense = null;

    public $amount;

    public $description;

    public $spent_at;

    public $category_id;

    public $subcategory;

    public $meta = [];

    public $currency = 'EUR';

    public $bankAccountId = '';

    public $receipt;

    public $previousUrl;

    public $isScanning = false;

    public $scannedData = [];

    public $scanSuccess = false;

    public $scanError = '';

    public array $aiQuota = [];

    public function refreshScannerQuota(): void
    {
        $this->aiQuota = app(\App\Services\OpenRouterQuotaService::class)->dailyQuota(true);
    }

    public array $hubConfigs = [
        'carro' => ['subs' => ['Gasolina', 'Manutenção', 'Seguro', 'Portagens', 'Estacionamento', 'Lavagem', 'Multa', 'Inspeção']],
        'casa' => ['subs' => ['Renda', 'Luz/Água', 'Internet', 'Gás', 'Condomínio', 'Obras', 'Mobiliário', 'Limpeza']],
        'alimentacao' => ['subs' => ['Supermercado', 'Restaurante', 'Café', 'Takeaway', 'Delivery', 'Padaria', 'Mercado']],
        'saude' => ['subs' => ['Consulta', 'Farmácia', 'Exame', 'Dentista', 'Óptica', 'Ginásio', 'Seguro Saúde']],
        'tecnologia' => ['subs' => ['Software', 'Hardware', 'Subscrição', 'Domínio', 'Hosting', 'Acessório', 'Reparação']],
        'educacao' => ['subs' => ['Propinas', 'Livros', 'Curso', 'Certificação', 'Material', 'Formação']],
        'emprestimos' => ['subs' => ['Prestação', 'Juros', 'Amortização', 'Seguro Associado']],
        'seguros' => ['subs' => ['Automóvel', 'Habitação', 'Saúde', 'Vida', 'Multirriscos']],
    ];

    protected $rules = [
        'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
    ];

    #[Computed]
    public function bankAccounts()
    {
        $user = auth()->user();
        $query = BankAccount::where('workspace_id', $user->current_workspace_id);

        if ($user->currentWorkspace?->type === 'personal') {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('name')->get();
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

    public function mount(?Expense $expense = null): void
    {
        $previous = url()->previous();
        $currentHost = parse_url(url('/'), PHP_URL_HOST);
        $previousHost = parse_url($previous, PHP_URL_HOST);
        $this->previousUrl = $previousHost === null || $previousHost === $currentHost ? $previous : null;

        if ($expense && $expense->exists) {
            $this->expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('user_id', auth()->id())
                ->findOrFail($expense->id);
            $this->amount = $expense->amount;
            $this->spent_at = Carbon::parse((string) $expense->spent_at)->format('Y-m-d');
            $this->category_id = $expense->category_id;
            $this->subcategory = $expense->subcategory;
            $this->description = $expense->description;
            $this->bankAccountId = $expense->bank_account_id ?: '';
            $rawMetadata = $expense->getRawOriginal('metadata');
            if (is_array($expense->metadata)) {
                $this->meta = $expense->metadata;
            } elseif (is_string($rawMetadata)) {
                $decoded = json_decode($rawMetadata, true);
                $this->meta = is_array($decoded) ? $decoded : [];
            } else {
                $this->meta = [];
            }
        } else {
            $this->spent_at = now()->format('Y-m-d');
        }
        $this->currency = auth()->user()->currentWorkspace->currency ?? 'EUR';
        $this->refreshScannerQuota();
    }

    public function updatedCategoryId($value): void
    {
        if (! $value || ($this->expense && $this->expense->exists)) {
            return;
        }

        $category = Category::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->find($value);

        if (! $category) {
            return;
        }

        $this->redirect(route('hub.category', ['slug' => $category->slug]), navigate: true);
    }

    public function scanReceiptWithAI(): void
    {
        $this->scanSuccess = false;
        $this->scanError = '';
        $this->scannedData = [];
        $this->isScanning = true;

        if (! $this->receipt) {
            $this->scanError = 'Nenhum ficheiro selecionado.';
            $this->isScanning = false;
            return;
        }

        try {
            $path = $this->receipt->getRealPath();
            $contents = $path && is_file($path) ? file_get_contents($path) : false;
            if (! is_string($contents) || $contents === '') {
                throw new \RuntimeException('Não foi possível ler o ficheiro selecionado.');
            }

            $selectedCat = Category::where('workspace_id', auth()->user()->current_workspace_id)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->find($this->category_id);
            $subcategories = ($selectedCat && isset($this->hubConfigs[$selectedCat->slug]))
                ? $this->hubConfigs[$selectedCat->slug]['subs']
                : ['Geral', 'Outros'];

            $result = app(\App\\Services\\InvoiceScanService::class)->analyse(
                $contents,
                $this->receipt->getClientOriginalName(),
                $this->receipt->getMimeType() ?: 'image/jpeg',
                $subcategories,
                'subcategory',
            );

            $amount = $result['amount'] ?? null;
            $this->amount = $amount;
            if (! empty($result['date'])) $this->spent_at = $result['date'];

            $metaFields = [
                'item_name', 'time', 'quantity', 'unit', 'unit_price', 'vat_rate',
                'gross_amount', 'net_amount', 'vat_amount', 'tax_incidence',
                'amount_tendered', 'change_amount', 'payment_condition', 'payment_method',
                'company_nif', 'customer_nif', 'invoice_number', 'notes',
            ];
            foreach ($metaFields as $field) {
                if (isset($result[$field]) && $result[$field] !== '') {
                    $this->meta[$field] = $result[$field];
                }
            }
            if (! empty($result['store'])) $this->meta['local'] = trim((string) $result['store']);
            if (blank($this->meta['item_name'] ?? null) && filled($result['description'] ?? null)) {
                $this->meta['item_name'] = trim((string) $result['description']);
            }

            $this->description = trim((string) ($result['description'] ?? $result['item_name'] ?? $result['store'] ?? ''));
            if (! empty($result['subcategory']) && in_array($result['subcategory'], $subcategories, true)) {
                $this->subcategory = $result['subcategory'];
            }

            $this->scannedData = $result;
            $this->scanSuccess = true;
            $this->dispatch('scan-completed');
        } catch (\Throwable $exception) {
            \Illuminate\\Support\\Facades\\Log::error('ManageExpense invoice scan failed.', [
                'message' => $exception->getMessage(),
                'user_id' => auth()->id(),
                'workspace_id' => auth()->user()?->current_workspace_id,
            ]);
            $this->scanError = 'Não foi possível analisar a fatura: '.$exception->getMessage();
        } finally {
            $this->isScanning = false;
            $this->refreshScannerQuota();
        }
    }

    public function save()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'spent_at' => 'required|date',
            'category_id' => 'required|integer',
        ]);

        abort_unless(Category::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->whereKey($this->category_id)
            ->exists(), 422, 'Categoria inválida.');

        if ($this->bankAccountId) {
            $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('user_id', auth()->id())
                ->find($this->bankAccountId);

            if ($account) {
                $available = (float) $account->current_balance;
                if ($this->expense && $this->expense->exists && (int) $this->expense->bank_account_id === (int) $this->bankAccountId) {
                    $available += (float) $this->expense->amount;
                }
                if ((float) $this->amount > $available) {
                    $this->addError('bankAccountId', 'Saldo insuficiente nesta conta.');
                    $this->dispatch('toast', variant: 'error', text: 'Saldo insuficiente em "'.$account->name.'": disponível '.number_format($available, 2, ',', '.').'€.');

                    return;
                }
            }
        }

        $data = [
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'subcategory' => $this->subcategory,
            'amount' => $this->amount,
            'currency' => strtoupper((string) $this->currency),
            'description' => $this->description,
            'spent_at' => $this->spent_at,
            'workspace_id' => auth()->user()->current_workspace_id,
            'bank_account_id' => $this->bankAccountId ?: null,
            'metadata' => ! empty($this->meta) ? $this->meta : null,
        ];

        if ($this->receipt) {
            $data['receipt_path'] = $this->receipt->store('receipts', 'local');
        }

        if ($this->expense && $this->expense->exists) {
            $this->expense->update($data);
        } else {
            Expense::create($data);
        }

        $this->receipt = null;

        $target = ($this->previousUrl && $this->previousUrl !== url()->current())
            ? $this->previousUrl
            : route('expenses');

        return $this->redirect($target, navigate: true);
    }

    public function downloadReceipt()
    {
        abort_unless($this->expense?->exists, 404);
        abort_unless((int) $this->expense->workspace_id === (int) auth()->user()->current_workspace_id, 403);
        abort_unless((int) $this->expense->user_id === (int) auth()->id(), 403);
        abort_unless($this->expense->receipt_path && Storage::disk('local')->exists($this->expense->receipt_path), 404);

        return Storage::disk('local')->download($this->expense->receipt_path, basename($this->expense->receipt_path));
    }

    public function render()
    {
        $wsId = auth()->user()->current_workspace_id;
        $categories = Category::where('workspace_id', $wsId)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->orderBy('order')->get();
        $selectedCat = $this->category_id
            ? Category::where('workspace_id', $wsId)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->find($this->category_id)
            : null;

        $categoryFields = [];
        if ($selectedCat && $selectedCat->fields->count() > 0) {
            $categoryFields[$selectedCat->slug] = [
                'icon' => $selectedCat->icon ?? 'tag',
                'fields' => $selectedCat->fields->sortBy('order')->map(fn ($f) => [
                    'name' => $f->key, 'label' => $f->label, 'type' => $f->type, 'options' => $f->options ?? [],
                ])->toArray(),
            ];
        }

        return view('livewire.manage-expense', [
            'categories' => $categories,
            'currencyOptions' => CurrencyService::getSymbols(),
            'categoryColor' => $selectedCat->color ?? '#6366f1',
            'categoryFields' => $categoryFields,
            'activeSlug' => $selectedCat->slug ?? null,
            'subcategories' => ($selectedCat && isset($this->hubConfigs[$selectedCat->slug]))
                               ? $this->hubConfigs[$selectedCat->slug]['subs']
                               : ['Geral', 'Outros'],
        ]);
    }
}