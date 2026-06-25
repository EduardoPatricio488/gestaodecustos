<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
class ManageExpense extends Component
{
    use WithFileUploads;

    public ?Expense $expense = null;

    // Propriedades do Form
    public $amount, $description, $spent_at, $category_id, $subcategory, $meta = [];
    public $currency = 'EUR', $receipt;
    public $previousUrl; // <--- NOVA PROPRIEDADE

    // Estado do Scanner
    public $isScanning  = false;
    public $scannedData = [];
    public $scanSuccess = false;
    public $scanError   = '';

    public array $hubConfigs = [
        'carro'       => ['subs' => ['Gasolina', 'Manutenção', 'Seguro', 'Portagens', 'Estacionamento', 'Lavagem', 'Multa', 'Inspeção']],
        'casa'        => ['subs' => ['Renda', 'Luz/Água', 'Internet', 'Gás', 'Condomínio', 'Obras', 'Mobiliário', 'Limpeza']],
        'alimentacao' => ['subs' => ['Supermercado', 'Restaurante', 'Café', 'Takeaway', 'Delivery', 'Padaria', 'Mercado']],
        'saude'       => ['subs' => ['Consulta', 'Farmácia', 'Exame', 'Dentista', 'Óptica', 'Ginásio', 'Seguro Saúde']],
        'tecnologia'  => ['subs' => ['Software', 'Hardware', 'Subscrição', 'Domínio', 'Hosting', 'Acessório', 'Reparação']],
        'educacao'    => ['subs' => ['Propinas', 'Livros', 'Curso', 'Certificação', 'Material', 'Formação']],
        'emprestimos' => ['subs' => ['Prestação', 'Juros', 'Amortização', 'Seguro Associado']],
        'seguros'     => ['subs' => ['Automóvel', 'Habitação', 'Saúde', 'Vida', 'Multirriscos']],
    ];

    protected $rules = [
        'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
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

    public function mount(Expense $expense = null): void
    {
        // GUARDA A URL DE ONDE O UTILIZADOR VEIO
        $this->previousUrl = url()->previous();

        if ($expense && $expense->exists) {
            $this->expense = $expense;
            $this->amount = $expense->amount;
            $this->spent_at = $expense->spent_at->format('Y-m-d');
            $this->category_id = $expense->category_id;
            $this->subcategory = $expense->subcategory;
            $this->description = $expense->description;
            $this->meta = is_array($expense->metadata) ? $expense->metadata : (json_decode($expense->metadata, true) ?? []);
        } else {
            $this->spent_at = now()->format('Y-m-d');
        }
        $this->currency = auth()->user()->currentWorkspace->currency ?? 'EUR';
    }

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
            $imageData = base64_encode(file_get_contents($imageFullPath));
            $mimeType  = $this->receipt->getMimeType() ?: 'image/jpeg';

            $selectedCat = Category::find($this->category_id);
            $subsStr = ($selectedCat && isset($this->hubConfigs[$selectedCat->slug]))
                ? implode(', ', $this->hubConfigs[$selectedCat->slug]['subs'])
                : "Geral, Outros";

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

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
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
                throw new \Exception('Erro na API.');
            }

            $rawText = preg_replace('/```json|```/i', '', $response->json('choices.0.message.content') ?? '');
            $rawText = trim($rawText);

            if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                $result = json_decode($matches[0], true);

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

                $date = $result['date'] ?? null;
                try {
                    $date = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = now()->format('Y-m-d');
                }

                $this->scannedData = $result;
                $this->scannedData['amount'] = $amount;
                $this->scannedData['date'] = $date;

                $this->amount      = $amount;
                $this->spent_at    = $date;
                $this->description = trim($result['store'] ?? '');
                $this->subcategory = $result['subcategory'] ?? $this->subcategory;

                $this->scanSuccess = true;
                $this->dispatch('scan-completed');
            }
        } catch (\Exception $e) {
            $this->scanError = 'Erro ao processar.';
        } finally {
            $this->isScanning = false;
        }
    }

    public function save()
    {
        $this->validate([
            'amount'      => 'required|numeric|min:0.01',
            'spent_at'    => 'required|date',
            'category_id' => 'required',
        ]);

        $data = [
            'user_id'      => auth()->id(),
            'category_id'  => $this->category_id,
            'subcategory'  => $this->subcategory,
            'amount'       => $this->amount,
            'currency'     => $this->currency,
            'description'  => $this->description,
            'spent_at'     => $this->spent_at,
            'workspace_id' => auth()->user()->current_workspace_id,
            'metadata'     => !empty($this->meta) ? $this->meta : null,
        ];

        if ($this->receipt) {
            $data['receipt_path'] = $this->receipt->store('receipts', 'public');
        }

        if ($this->expense && $this->expense->exists) {
            $this->expense->update($data);
        } else {
            Expense::create($data);
        }

        // REDIRECIONA PARA A PÁGINA ANTERIOR SE POSSÍVEL, CASO CONTRÁRIO PARA A LISTA
        $target = ($this->previousUrl && $this->previousUrl !== url()->current())
                  ? $this->previousUrl
                  : route('expenses');

        return $this->redirect($target, navigate: true);
    }

    public function render()
    {
        $wsId = auth()->user()->current_workspace_id;
        $exclude = ['Streaming (Vídeo/TV)', 'Música & Podcasts', 'Software & SaaS', 'Gaming', 'Fitness & Ginásio', 'Cloud & Armazenamento', 'Notícias & Revistas', 'Educação & Cursos', 'VPN & Segurança', 'Seguros & Finanças', 'Serviços Casa (Net/TV)', 'Outros'];

        $categories = Category::where('workspace_id', $wsId)->whereNotIn('name', $exclude)->orderBy('order')->get();
        $selectedCat = $this->category_id ? Category::find($this->category_id) : null;

        $categoryFields = [];
        if ($selectedCat && $selectedCat->fields->count() > 0) {
            $categoryFields[$selectedCat->slug] = [
                'icon' => $selectedCat->icon ?? 'tag',
                'fields' => $selectedCat->fields->sortBy('order')->map(fn($f) => [
                    'name' => $f->key, 'label' => $f->label, 'type' => $f->type, 'options' => $f->options ?? []
                ])->toArray(),
            ];
        }

        return view('livewire.manage-expense', [
            'categories'    => $categories,
            'categoryColor' => $selectedCat->color ?? '#6366f1',
            'categoryFields'=> $categoryFields,
            'activeSlug'    => $selectedCat->slug ?? null,
            'subcategories' => ($selectedCat && isset($this->hubConfigs[$selectedCat->slug]))
                               ? $this->hubConfigs[$selectedCat->slug]['subs']
                               : ['Geral', 'Outros'],
        ]);
    }
}
