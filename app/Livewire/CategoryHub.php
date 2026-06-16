<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Category;
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

    // Propriedades para controle do Scanner
    public $isScanning = false;
    public $scannedData = [];
    public $scanSuccess = false;
    public $scanError   = '';

    public function mount($slug)
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

    private function setHubConfig(): void
    {
        $configs = [
            'carro'       => ['db' => 'Carro',      'title' => 'Carro',       'icon' => 'truck',         'subs' => ['Gasolina', 'Manutenção', 'Seguro', 'Portagens', 'Estacionamento', 'Lavagem', 'Multa', 'Inspeção']],
            'casa'        => ['db' => 'Casa',        'title' => 'Casa',        'icon' => 'home',          'subs' => ['Renda', 'Luz/Água', 'Internet', 'Gás', 'Condomínio', 'Obras', 'Mobiliário', 'Limpeza']],
            'alimentacao' => ['db' => 'Alimentação', 'title' => 'Alimentação', 'icon' => 'shopping-cart', 'subs' => ['Supermercado', 'Restaurante', 'Café', 'Takeaway', 'Delivery', 'Padaria', 'Mercado']],
            'saude'       => ['db' => 'Saúde',       'title' => 'Saúde',       'icon' => 'heart',         'subs' => ['Consulta', 'Farmácia', 'Exame', 'Dentista', 'Óptica', 'Ginásio', 'Seguro Saúde']],
            'tecnologia'  => ['db' => 'Tecnologia',  'title' => 'Tecnologia',  'icon' => 'cpu-chip',      'subs' => ['Software', 'Hardware', 'Subscrição', 'Domínio', 'Hosting', 'Acessório', 'Reparação']],
        ];

        $c = $configs[$this->slug] ?? ['db' => ucfirst($this->slug), 'title' => ucfirst($this->slug), 'icon' => 'tag', 'subs' => ['Geral']];

        $this->dbName        = $c['db'];
        $this->title         = $c['title'];
        $this->icon          = $c['icon'];
        $this->subcategories = $c['subs'];
    }

    public function scanReceiptWithAI(): void
    {
        $this->scanSuccess = false;
        $this->scanError   = '';
        $this->scannedData = [];
        $this->isScanning  = true; // Ativa o loader no Blade

        if (!$this->receipt) {
            $this->scanError = 'Nenhum ficheiro selecionado.';
            $this->isScanning = false;
            return;
        }

        try {
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY não configurada no .env');
            }

            $imagePath = $this->receipt->getRealPath();
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType  = $this->receipt->getMimeType() ?: 'image/jpeg';
            $subsStr   = implode(', ', $this->subcategories);

            $prompt = "Analisa esta fatura. Devolve APENAS JSON puro: { 'amount': float, 'date': 'YYYY-MM-DD', 'store': 'string', 'subcategory': 'uma de: {$subsStr}' }";

            // 🔥 Mudança crucial: gemini-1.5-flash (Versão estável com alta quota gratuita)
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [[
                        'parts' => [
                            ['text' => $prompt],
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]],
                        ],
                    ]]
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new \Exception('Erro Gemini: ' . ($error['error']['message'] ?? 'Falha na comunicação'));
            }

            $rawText = $response->json('candidates.0.content.parts.0.text') ?? '';

            // Limpeza de resposta (pega apenas o que está entre { })
            if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                $result = json_decode($matches[0], true);

                if ($result) {
                    $this->amount      = (float) str_replace(',', '.', (string) ($result['amount'] ?? 0));
                    $this->spent_at    = $result['date'] ?? now()->format('Y-m-d');
                    $this->description = $result['store'] ?? '';

                    // Match inteligente de subcategoria
                    $suggested = strtolower($result['subcategory'] ?? '');
                    foreach ($this->subcategories as $sub) {
                        if (Str::contains(strtolower($sub), $suggested)) {
                            $this->subcategory = $sub;
                            break;
                        }
                    }

                    $this->scanSuccess = true;
                    // Notifica o Blade para fechar o scanner e abrir o registo
                    $this->dispatch('scan-completed');
                }
            } else {
                throw new \Exception('A IA não devolveu um formato válido.');
            }

        } catch (\Exception $e) {
            Log::error('Scanner Error: ' . $e->getMessage());
            $this->dispatch('toast', text: 'Erro: ' . $e->getMessage(), variant: 'error');
        } finally {
            $this->isScanning = false;
        }
    }

    public function updateBudget(): void
    {
        $this->validate(['budgetLimit' => 'numeric|min:0']);

        $category = Category::updateOrCreate(
            ['name' => $this->dbName, 'workspace_id' => auth()->user()->current_workspace_id],
            ['user_id' => auth()->id(), 'budget_limit' => $this->budgetLimit]
        );

        $this->editingBudget = false;
        $this->dispatch('toast', text: 'Limite orçamental atualizado!');
    }

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
    'user_id'      => auth()->id(), // ADICIONA ESTA LINHA
]);

        Expense::create([
            'user_id'      => auth()->id(),
            'category_id'  => $category->id,
            'subcategory'  => $this->subcategory,
            'amount'       => $this->amount,
            'currency'     => $this->currency,
            'description'  => $this->description,
            'spent_at'     => $this->spent_at,
            'workspace_id' => auth()->user()->current_workspace_id,
            'metadata'     => !empty($this->meta) ? $this->meta : null,
        ]);

        $this->reset(['amount', 'description', 'subcategory', 'receipt', 'meta', 'scannedData', 'scanSuccess', 'scanError']);
        $this->spent_at = now()->format('Y-m-d');

        $this->dispatch('modal-close', name: 'add-expense-modal');
        $this->dispatch('toast', text: 'Gasto guardado com sucesso!');
    }

    public function deleteExpense(int $id): void
    {
        Expense::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    public function render()
    {
        $query = Expense::whereHas('category', fn ($q) => $q->where('name', $this->dbName))
            ->where('workspace_id', auth()->user()->current_workspace_id);

        return view('livewire.category-hub', [
            'expenses'       => $query->latest('spent_at')->get(),
            'spentThisMonth' => $query->whereMonth('spent_at', now()->month)->sum('amount'),
            'currentWs'      => auth()->user()->currentWorkspace,
            'canManage'      => !auth()->user()->isViewer(),
            'isOwner'        => auth()->user()->isOwner(),
        ]);
    }
}
