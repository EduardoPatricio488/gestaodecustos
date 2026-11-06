<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Investment;
use App\Services\BusinessAccessService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\XtbReportImport;

#[Layout('components.layouts.app')]
class XtbInvestmentImportHub extends Component
{
    use WithFileUploads;

    public $reportFile;
    public array $positions = [];
    public array $reportSummary = [];
    public ?string $reportDate = null;
    public ?string $aiSummary = null;
    public bool $previewReady = false;
    public bool $isAnalyzing = false;
    public bool $isImporting = false;

    public function analyzeReport(): void
    {
        $this->authorizeImport();
        $this->validate([
            'reportFile' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        try {
            $sheets = Excel::toArray(new XtbReportImport(), $this->reportFile);
            $parsed = $this->extractOpenPositions($sheets);

            if (empty($parsed['positions'])) {
                $this->addError('reportFile', 'Não encontrei a tabela «Open Positions» neste ficheiro. Confirma que carregaste o relatório completo da XTB.');

                return;
            }

            $this->positions = $parsed['positions'];
            $this->reportSummary = $parsed['summary'];
            $this->reportDate = $parsed['date'];
            $this->previewReady = true;
            $this->aiSummary = $this->getAiValidationSummary($this->positions, $this->reportSummary);
            $this->dispatch('toast', variant: 'success', text: count($this->positions).' posições identificadas. Confirma os dados antes de importar.');
        } catch (\Throwable $exception) {
            Log::warning('XTB report import: failed to parse report', ['user_id' => auth()->id(), 'message' => $exception->getMessage()]);
            $this->addError('reportFile', 'Não foi possível ler este ficheiro. Experimenta exportar novamente o relatório da XTB em Excel (.xlsx).');
        }
    }

    private function extractOpenPositions(array $sheets): array
    {
        $positions = [];
        $reportDate = null;

        foreach ($sheets as $sheet) {
            if (! is_array($sheet)) {
                continue;
            }

            foreach ($sheet as $rowIndex => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $normalized = array_map(fn ($value) => mb_strtolower(trim((string) $value)), $row);
                $required = ['ticker', 'category', 'volume', 'value'];
                $hasHeader = count(array_intersect($required, $normalized)) === count($required)
                    && (in_array('open price', $normalized, true) || in_array('open price ', $normalized, true));

                if (! $hasHeader) {
                    continue;
                }

                $columns = [];
                foreach ($normalized as $index => $heading) {
                    $columns[$heading] = $index;
                }

                for ($i = $rowIndex + 1; $i < count($sheet); $i++) {
                    $data = $sheet[$i] ?? [];
                    if (! is_array($data) || $this->rowIsEmpty($data)) {
                        continue;
                    }

                    $product = trim((string) ($data[$columns['product'] ?? -1] ?? ''));
                    $ticker = strtoupper(trim((string) ($data[$columns['ticker']] ?? '')));
                    $instrument = trim((string) ($data[$columns['instrument/position'] ?? -1] ?? ''));
                    if ($ticker === '' || ! preg_match('/^[A-Z0-9.\-_^]{1,24}$/', $ticker)) {
                        continue;
                    }

                    $volume = $this->parseNumber($data[$columns['volume']] ?? null);
                    $value = $this->parseNumber($data[$columns['value']] ?? null);
                    $openPrice = $this->parseNumber($data[$columns['open price']] ?? null);
                    if ($volume <= 0 || $value <= 0) {
                        continue;
                    }

                    $currentPrice = $this->parseNumber($data[$columns['current price'] ?? -1] ?? null);
                    $openTime = $data[$columns['open time (utc)'] ?? ($columns['open time'] ?? -1)] ?? null;
                    $isSummaryRow = $instrument !== '' && ! ctype_digit($instrument);

                    $entry = [
                        'name' => $isSummaryRow ? $instrument : $ticker,
                        'symbol' => $ticker,
                        'category' => trim((string) ($data[$columns['category']] ?? '')),
                        'type' => $this->investmentType((string) ($data[$columns['category']] ?? '')),
                        'quantity' => $volume,
                        'current_value' => $value,
                        'current_price' => $currentPrice > 0 ? $currentPrice : $value / $volume,
                        'average_price' => $openPrice > 0 ? $openPrice : null,
                        'open_time' => $this->parseDate($openTime),
                        'is_summary' => $isSummaryRow,
                        'product' => $product,
                    ];

                    $positions[$ticker]['summary'] = $positions[$ticker]['summary'] ?? null;
                    $positions[$ticker]['details'] = $positions[$ticker]['details'] ?? [];
                    if ($isSummaryRow) {
                        $positions[$ticker]['summary'] = $entry;
                    } else {
                        $positions[$ticker]['details'][] = $entry;
                    }
                }

                break;
            }

            foreach ($sheet as $row) {
                if (! is_array($row) || count($row) < 3) {
                    continue;
                }
                if (mb_strtolower(trim((string) ($row[0] ?? ''))) === 'data as of report generated') {
                    $reportDate = trim((string) ($row[1] ?? ''));
                }
            }
        }

        $result = [];
        foreach ($positions as $ticker => $bucket) {
            $summary = $bucket['summary'];
            $details = $bucket['details'];

            if ($summary) {
                $position = $summary;
                if ($position['current_price'] <= 0) {
                    $position['current_price'] = $position['current_value'] / max($position['quantity'], 0.00000001);
                }
                if (! $position['average_price'] && count($details)) {
                    $cost = 0;
                    $quantity = 0;
                    foreach ($details as $detail) {
                        if (($detail['average_price'] ?? 0) > 0) {
                            $cost += $detail['average_price'] * $detail['quantity'];
                            $quantity += $detail['quantity'];
                        }
                    }
                    $position['average_price'] = $quantity > 0 ? $cost / $quantity : $position['current_price'];
                }
                if (count($details)) {
                    $dates = array_filter(array_column($details, 'open_time'));
                    if ($dates) {
                        $position['open_time'] = min($dates);
                    }
                }
            } elseif ($details) {
                $quantity = array_sum(array_column($details, 'quantity'));
                $value = array_sum(array_column($details, 'current_value'));
                $cost = 0;
                $costQuantity = 0;
                foreach ($details as $detail) {
                    if (($detail['average_price'] ?? 0) > 0) {
                        $cost += $detail['average_price'] * $detail['quantity'];
                        $costQuantity += $detail['quantity'];
                    }
                }
                $first = $details[0];
                $dates = array_filter(array_column($details, 'open_time'));
                $position = array_merge($first, [
                    'quantity' => $quantity,
                    'current_value' => $value,
                    'current_price' => $quantity > 0 ? $value / $quantity : 0,
                    'average_price' => $costQuantity > 0 ? $cost / $costQuantity : $first['current_price'],
                    'open_time' => $dates ? min($dates) : null,
                ]);
            } else {
                continue;
            }

            $position['symbol'] = $ticker;
            $position['profit'] = $position['current_value'] - ($position['quantity'] * $position['average_price']);
            $result[] = $position;
        }

        return [
            'positions' => $result,
            'summary' => [
                'open_value' => array_sum(array_column($result, 'current_value')),
                'positions_count' => count($result),
            ],
            'date' => $reportDate,
        ];
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value instanceof \\DateTimeInterface) {
                return false;
            }

            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parseNumber(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $value = trim(str_replace(["\xc2\xa0", ' ', '€'], '', (string) $value));
        if ($value === '' || $value === '-') {
            return 0.0;
        }

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = strrpos($value, ',') > strrpos($value, '.') ? str_replace('.', '', $value) : str_replace(',', '', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value) && (float) $value > 20000) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
        try {
            return filled($value) ? \Illuminate\Support\Carbon::parse($value)->toDateString() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function investmentType(string $category): string
    {
        return match (mb_strtolower(trim($category))) {
            'etf' => 'ETF',
            'crypto', 'cryptocurrency', 'cripto' => 'Cripto',
            'fund', 'fundo' => 'Fundo',
            default => 'Acao',
        };
    }

    private function getAiValidationSummary(array $positions, array $summary): ?string
    {
        $apiKey = config('services.openrouter.api_key');
        if (blank($apiKey)) {
            return 'A leitura do relatório foi feita através das tabelas oficiais da XTB. A validação IA fica disponível quando a chave OpenRouter estiver configurada.';
        }

        try {
            $payload = array_map(fn ($position) => [
                'nome' => $position['name'],
                'ticker' => $position['symbol'],
                'categoria' => $position['category'],
                'quantidade' => $position['quantity'],
                'valor_atual_eur' => $position['current_value'],
            ], $positions);

            $response = Http::withToken($apiKey)->timeout(20)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => config('services.openrouter.model', 'openai/gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => 'És um assistente de validação de relatórios financeiros. Não alteres nem inventes números. Usa apenas os dados fornecidos. Responde em português de Portugal, com no máximo 3 frases.'],
                    ['role' => 'user', 'content' => 'Resume e valida a estrutura destas posições abertas da XTB. Indica quantidade de ativos, valor total e eventuais limitações. Dados: '.json_encode(['resumo' => $summary, 'posicoes' => $payload], JSON_UNESCAPED_UNICODE)],
                ],
                'temperature' => 0.1,
                'max_tokens' => 250,
            ]);

            if ($response->successful()) {
                return trim((string) $response->json('choices.0.message.content'));
            }
        } catch (\Throwable $exception) {
            Log::info('XTB AI validation unavailable', ['message' => $exception->getMessage()]);
        }

        return 'As posições foram extraídas diretamente da tabela «Open Positions». Os valores numéricos mantêm-se fiéis ao relatório da XTB.';
    }

    public function importPositions(): void
    {
        $this->authorizeImport();
        if (! $this->previewReady || empty($this->positions)) {
            $this->dispatch('toast', variant: 'warning', text: 'Carrega e analisa primeiro um relatório da XTB.');

            return;
        }

        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        abort_unless($workspace && $user->current_workspace_id, 403);

        $this->isImporting = true;
        try {
            DB::transaction(function () use ($user, $workspace) {
                $account = BankAccount::firstOrCreate(
                    ['workspace_id' => $workspace->id, 'user_id' => $user->id, 'name' => 'XTB'],
                    ['type' => 'investimentos', 'balance' => 0, 'currency' => 'EUR', 'color' => '#16a34a', 'include_in_total' => false, 'status' => 'active', 'description' => 'Conta de investimentos importada da XTB']
                );

                foreach ($this->positions as $position) {
                    $existing = Investment::query()
                        ->where('workspace_id', $workspace->id)
                        ->where('user_id', $user->id)
                        ->where('broker', 'XTB')
                        ->where('symbol', $position['symbol'])
                        ->first();

                    $data = [
                        'user_id' => $user->id,
                        'workspace_id' => $workspace->id,
                        'bank_account_id' => $account->id,
                        'name' => $position['name'] ?: $position['symbol'],
                        'symbol' => $position['symbol'],
                        'type' => $position['type'],
                        'quantity' => $position['quantity'],
                        'average_price' => max(0.00000001, $position['average_price'] ?: $position['current_price']),
                        'current_price' => $position['current_price'],
                        'provider' => 'XTB',
                        'broker' => 'XTB',
                        'exchange' => str_contains($position['symbol'], '.') ? substr(strrchr($position['symbol'], '.'), 1) : null,
                        'operation_date' => $position['open_time'],
                        'fees' => 0,
                    ];

                    if ($existing) {
                        $existing->update($data);
                    } else {
                        Investment::create($data);
                    }
                }
            });

            $this->dispatch('toast', variant: 'success', text: count($this->positions).' posições XTB sincronizadas com os Investimentos.');
            $this->reset(['reportFile', 'positions', 'reportSummary', 'reportDate', 'aiSummary', 'previewReady']);
        } catch (\Throwable $exception) {
            Log::error('XTB investment import failed', ['user_id' => $user->id, 'message' => $exception->getMessage()]);
            $this->dispatch('toast', variant: 'danger', text: 'Não foi possível importar as posições. Nenhuma alteração parcial foi guardada.');
        } finally {
            $this->isImporting = false;
        }
    }

    public function clearReport(): void
    {
        $this->reset(['reportFile', 'positions', 'reportSummary', 'reportDate', 'aiSummary', 'previewReady']);
        $this->resetValidation();
    }

    private function authorizeImport(): void
    {
        $user = auth()->user();
        $workspace = $user?->currentWorkspace;
        if ($workspace && in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', $user, $workspace);
        }
    }

    public function render()
    {
        $this->authorizeImport();

        return view('livewire.xtb-investment-import-hub');
    }
}
