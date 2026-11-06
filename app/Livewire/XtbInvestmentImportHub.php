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
    public string $activeProvider = 'xtb';
    public $aforroNetFile;
    public array $aforroNetSubscriptions = [];
    public array $aforroNetPosition = [];
    public bool $aforroNetPreviewReady = false;
    public bool $isAnalyzingAforroNet = false;
    public ?string $aforroNetDiagnostic = null;
    public bool $isImportingAforroNet = false;
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
                        'reported_profit' => isset($columns['net profit']) && filled($data[$columns['net profit']] ?? null) ? $this->parseNumber($data[$columns['net profit']]) : null,
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
            $reportedProfits = array_values(array_filter(
                array_column($details, 'reported_profit'),
                fn ($profit) => $profit !== null
            ));
            $position['profit'] = count($reportedProfits) > 0
                ? array_sum($reportedProfits)
                : $position['current_value'] - ($position['quantity'] * $position['average_price']);
            $result[] = $position;
        }

        return [
            'positions' => $result,
            'summary' => [
                'open_value' => array_sum(array_column($result, 'current_value')),
                'open_profit' => array_sum(array_column($result, 'profit')),
                'positions_count' => count($result),
            ],
            'date' => $reportDate,
        ];
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value instanceof \DateTimeInterface) {
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


    public function analyzeAforroNetFile(): void
    {
        $this->authorizeImport();
        $this->aforroNetDiagnostic = null;
        $this->validate([
            'aforroNetFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $pdfPath = $this->aforroNetFile->getRealPath();
            if (! $pdfPath || ! is_file($pdfPath)) {
                throw new \RuntimeException('Não foi possível aceder ao ficheiro PDF carregado.');
            }

            $text = $this->extractAforroNetPdfText($pdfPath);
            // Normaliza espaços Unicode (incluindo NBSP) comuns em PDFs.
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace('/[\p{Z}\s]+/u', ' ', trim($text));

            if (mb_strlen($text) < 80) {
                throw new \RuntimeException('Não foi possível ler conteúdo suficiente do PDF. Confirma que não está protegido por palavra-passe e que contém o extrato completo.');
            }

            // Os PDFs do AforroNet podem apresentar a data com rótulos e
            // separadores diferentes consoante a versão da declaração.
            $datePatterns = [
                '/Data\s+do\s+Extrato\s*:?\s*(\d{2}[.\/-]\d{2}[.\/-]\d{4})/iu',
                '/Data\s+(?:de\s+)?(?:emiss[aã]o|refer[eê]ncia)\s*:?\s*(\d{2}[.\/-]\d{2}[.\/-]\d{4})/iu',
                '/Extrato\s+(?:emitido\s+)?em\s*:?\s*(\d{2}[.\/-]\d{2}[.\/-]\d{4})/iu',
            ];
            $reportDate = null;
            foreach ($datePatterns as $pattern) {
                if (preg_match($pattern, $text, $dateMatch)) {
                    $reportDate = str_replace(['/', '.'], '-', $dateMatch[1]);
                    break;
                }
            }

            // No extrato oficial, a data do cabeçalho é a primeira data completa.
            if (! $reportDate && preg_match('/(?:^|\s)(\d{2}-\d{2}-\d{4})(?=\s)/u', $text, $dateMatch)) {
                $reportDate = $dateMatch[1];
            }

            if (! $reportDate) {
                throw new \RuntimeException('Não foi possível identificar uma data no cabeçalho do extrato AforroNet.');
            }
            // A série pode aparecer no resumo geral ou no detalhe CAF / Série E.
            // Normaliza acentos porque alguns extratores de PDF os corrompem.
            $normalizedText = $text;
            if (function_exists('iconv')) {
                $asciiText = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
                if ($asciiText !== false) {
                    $normalizedText = $asciiText;
                }
            }
            $normalizedText = preg_replace('/[\p{Z}\s]+/u', ' ', $normalizedText);

            $series = $this->detectAforroNetSeries($normalizedText);

            // O total do resumo é opcional: quando o PDF não o disponibiliza
            // de forma legível, será calculado pela soma das subscrições.
            $reportedUnits = null;
            $reportedValue = null;
            if (preg_match('/Certificados\s+de\s+Aforro\s+S[eé]rie\s+[A-Z]\s+(?:Unidades\s+Valor\s*)?([\d.]+)\s+([\d.,]+)/iu', $normalizedText, $totalMatch)
                || preg_match('/Certificados\s+de\s+Aforro.{0,140}?([A-Z])\s+([\d.]+)\s+([\d.,]+)/iu', $normalizedText, $totalMatch)) {
                if (count($totalMatch) === 3) {
                    $reportedUnits = $this->parsePortugueseNumber($totalMatch[1]);
                    $reportedValue = $this->parsePortugueseNumber($totalMatch[2]);
                } elseif (count($totalMatch) >= 4) {
                    $reportedUnits = $this->parsePortugueseNumber($totalMatch[2]);
                    $reportedValue = $this->parsePortugueseNumber($totalMatch[3]);
                }
            }

            if ($series !== null && $series !== 'E') {
                throw new \RuntimeException('Este importador automático está preparado para a Série E.');
            }

            $subscriptions = [];

            // Os motores de extração podem separar as colunas por quebras de
            // linha, tabs ou espaços. Procura os campos dentro de cada registo
            // de subscrição, em vez de exigir que estejam lado a lado.
            $rowPattern = '/(\d{2}[-\/.]\d{2}[-\/.]\d{4})[\s\S]{0,100}?(\d{6,})[\s\S]{0,100}?([\d.,]+)[\s\S]{0,100}?([\d.]+)[\s\S]{0,100}?([\d.,]+)/u';
            preg_match_all($rowPattern, $normalizedText, $rows, PREG_SET_ORDER);

            foreach ($rows as $row) {
                $date = str_replace(['/', '.'], '-', $row[1]);
                try {
                    $operationDate = \Illuminate\Support\Carbon::createFromFormat('!d-m-Y', $date)->toDateString();
                } catch (\Throwable) {
                    continue;
                }

                $subscriptions[] = [
                    'operation_date' => $operationDate,
                    'subscription_number' => $row[2],
                    'current_price' => $this->parsePortugueseNumber($row[3]),
                    'quantity' => $this->parsePortugueseNumber($row[4]),
                    'current_value' => $this->parsePortugueseNumber($row[5]),
                ];
            }

            if (count($subscriptions) === 0) {
                throw new \RuntimeException('Não foram encontradas linhas individuais de subscrição no PDF. Não é seguro importar sem validar as unidades.');
            }

            if ($series === null) {
                throw new \RuntimeException('O PDF contém subscrições, mas não identifica inequivocamente a série dos certificados.');
            }

            $units = array_sum(array_column($subscriptions, 'quantity'));
            $value = array_sum(array_column($subscriptions, 'current_value'));

            if ($reportedUnits !== null && $reportedValue !== null
                && (abs($units - $reportedUnits) > 0.00001 || abs($value - $reportedValue) > 0.02)) {
                throw new \RuntimeException('A soma das subscrições individuais não coincide com o total indicado no PDF.');
            }

            foreach ($subscriptions as $subscription) {
                if ($subscription['quantity'] <= 0 || $subscription['current_price'] <= 0 || $subscription['current_value'] <= 0) {
                    throw new \RuntimeException('O PDF contém uma subscrição com valores inválidos.');
                }
                if (abs(($subscription['quantity'] * $subscription['current_price']) - $subscription['current_value']) > 0.03) {
                    throw new \RuntimeException('O valor unitário e o total de uma subscrição não coincidem.');
                }
            }

            $this->aforroNetSubscriptions = $subscriptions;
            $this->aforroNetPosition = [
                'name' => 'Certificados de Aforro — Série E',
                'series' => 'E',
                'quantity' => $units,
                'current_value' => $value,
                'profit' => $value - $units,
                'report_date' => $reportDate,
            ];
            $this->aforroNetPreviewReady = true;
            $this->dispatch('toast', variant: 'success', text: count($subscriptions).' subscrições identificadas e validadas a partir do PDF.');
        } catch (\Throwable $exception) {
            Log::warning('AforroNet PDF analysis failed', ['user_id' => auth()->id(), 'message' => $exception->getMessage()]);
            $this->addError('aforroNetFile', $exception->getMessage());
            $this->aforroNetPreviewReady = false;
            $this->aforroNetSubscriptions = [];
            $this->aforroNetPosition = [];
        }
    }

    /**
     * Identifica a série mesmo quando o extrator separa o rótulo em blocos,
     * colunas ou linhas diferentes. Só aceita uma série ligada ao contexto
     * "CAF" ou "Certificados de Aforro", nunca uma letra isolada.
     */
    private function detectAforroNetSeries(string $text): ?string
    {
        $text = mb_strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text);
        $text = preg_replace('/[^A-Z0-9]+/', ' ', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));

        $patterns = [
            '/\bCERTIFICADOS\s+DE\s+AFORRO\b.{0,180}?\bSERIE\s*([A-Z])\b/',
            '/\bCAF\b.{0,180}?\bSERIE\s*([A-Z])\b/',
            '/\bSERIE\s*([A-Z])\b.{0,100}?\b(?:UNIDADES|VALOR|SUBSCRICAO)\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                return strtoupper($match[1]);
            }
        }

        // Alguns leitores devolvem os elementos do cabeçalho em ordem inversa.
        if (preg_match('/\bSERIE\s*([A-Z])\b.{0,180}?\b(?:CAF|CERTIFICADOS\s+DE\s+AFORRO)\b/', $text, $match)) {
            return strtoupper($match[1]);
        }

        return null;
    }

    /**
     * Extrai texto de PDFs digitais e aplica OCR quando o documento é digitalizado.
     * Funciona no Docker/Linux e tenta localizar automaticamente as instalações
     * do Poppler e Tesseract no Windows, sem exigir alterações manuais ao PATH.
     */
    private function extractAforroNetPdfText(string $pdfPath): string
    {
        $candidates = [];
        $candidateSources = [];
        $errors = [];

        // Recolhe todas as extrações digitais disponíveis. Não aceita uma
        // extração apenas por ter texto: tem de preservar os dados do extrato.
        try {
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                $parser = new \Smalot\PdfParser\Parser();
                $document = $parser->parseFile($pdfPath);
                $text = trim($document->getText());
                if (mb_strlen($text) >= 80) {
                    $candidates[] = $text;
                    $candidateSources[] = 'Smalot';
                }
            }
        } catch (\Throwable $exception) {
            $errors[] = 'PHP parser: '.$exception->getMessage();
        }

        $pdftotext = $this->resolvePdfExecutable('pdftotext');
        $pdftoppm = $this->resolvePdfExecutable('pdftoppm');
        $tesseract = $this->resolvePdfExecutable('tesseract');

        // Diagnóstico temporário do ambiente real do pedido Livewire.
        $runtimeUserProfile = getenv('USERPROFILE')
            ?: ($_SERVER['USERPROFILE'] ?? null)
            ?: ((getenv('HOMEDRIVE') && getenv('HOMEPATH')) ? getenv('HOMEDRIVE').getenv('HOMEPATH') : null);
        $runtimeLocalAppData = getenv('LOCALAPPDATA')
            ?: ($_SERVER['LOCALAPPDATA'] ?? null)
            ?: ($runtimeUserProfile ? $runtimeUserProfile.'/AppData/Local' : null);
        $runtimePopplerRoot = $runtimeLocalAppData
            ? rtrim(str_replace('\\', '/', $runtimeLocalAppData), '/').'/Microsoft/WinGet/Packages'
            : null;
        Log::info('AforroNet executable runtime diagnostics', [
            'pid' => getmypid(),
            'php_binary' => basename(PHP_BINARY),
            'php_os_family' => PHP_OS_FAMILY,
            'localappdata_available' => (bool) $runtimeLocalAppData,
            'userprofile_available' => (bool) $runtimeUserProfile,
            'poppler_root_exists' => $runtimePopplerRoot ? is_dir($runtimePopplerRoot) : false,
            'pdftotext_found' => (bool) $pdftotext,
            'pdftoppm_found' => (bool) $pdftoppm,
            'tesseract_found' => (bool) $tesseract,
            'pdftotext_glob_count' => $runtimePopplerRoot
                ? count(glob($runtimePopplerRoot.'/*/*/Library/bin/pdftotext.exe') ?: [])
                : 0,
        ]);

        $ocrStatus = ($pdftoppm && $tesseract) ? 'disponivel, ainda nao executado' : 'indisponivel';
        $processClass = \Symfony\Component\Process\Process::class;

        if ($pdftotext) {
            foreach ([['-layout'], ['-raw']] as $options) {
                try {
                    $process = new $processClass(array_merge([$pdftotext], $options, [$pdfPath, '-']));
                    $process->setTimeout(45);
                    $process->run();
                    $output = trim($process->getOutput());
                    if ($process->isSuccessful() && mb_strlen($output) >= 80) {
                        $candidates[] = $output;
                        $candidateSources[] = $options[0] === '-layout' ? 'Poppler layout' : 'Poppler raw';
                    } else {
                        $errors[] = trim($process->getErrorOutput());
                    }
                } catch (\Throwable $exception) {
                    $errors[] = $exception->getMessage();
                }
            }
        } else {
            $errors[] = 'O executável pdftotext não foi encontrado.';
        }

        $scoreText = function (string $candidate): int {
            $upper = mb_strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $candidate) ?: $candidate);
            $score = 0;
            foreach (['AFORRO', 'SERIE', 'SUBSCRICAO', 'UNIDADES', 'VALOR UNITARIO', 'TOTAL'] as $term) {
                if (str_contains($upper, $term)) {
                    $score += 3;
                }
            }
            if (preg_match('/\b\d{2}[-\/.]\d{2}[-\/.]\d{4}\b/', $upper)) {
                $score += 3;
            }
            if (preg_match('/\b\d{8,}\b/', $upper)) {
                $score += 2;
            }
            if ($this->detectAforroNetSeries($upper) !== null) {
                // Prioriza extrações que preservam a série, não apenas números.
                $score += 40;
            }
            if (preg_match_all('/\\b\\d{2}[-\\/.]\\d{2}[-\\/.]\\d{4}\\b/', $upper) >= 2) {
                $score += 4;
            }
            if (preg_match_all('/\\b\\d{6,}\\b/', $upper) >= 2) {
                $score += 4;
            }
            return $score;
        };

        $bestText = '';
        $bestScore = -1;
        foreach ($candidates as $candidate) {
            $score = $scoreText($candidate);
            if ($score > $bestScore) {
                $bestText = $candidate;
                $bestScore = $score;
            }
        }

        // O OCR também é obrigatório quando a extração tem texto, mas perdeu a série.
        // Uma pontuação alta não significa que os campos essenciais foram lidos.
        if (($bestScore < 12 || $this->detectAforroNetSeries($bestText) === null) && $pdftoppm && $tesseract) {
            $ocrStatus = 'executado';
            $temporaryDirectory = storage_path('app/private/aforronet-ocr/'.uniqid('pdf-', true));
            if (! is_dir($temporaryDirectory) && ! mkdir($temporaryDirectory, 0775, true) && ! is_dir($temporaryDirectory)) {
                throw new \RuntimeException('Não foi possível preparar a leitura OCR do PDF.');
            }

            try {
                $prefix = $temporaryDirectory.DIRECTORY_SEPARATOR.'page';
                $render = new $processClass([$pdftoppm, '-r', '300', '-png', $pdfPath, $prefix]);
                $render->setTimeout(90);
                $render->run();
                if (! $render->isSuccessful()) {
                    $errors[] = trim($render->getErrorOutput());
                } else {
                    $pages = glob($prefix.'-*.png') ?: [];
                    sort($pages, SORT_NATURAL);
                    $ocrModes = ['--psm', '6'];
                    $ocrText = [];
                    foreach ($pages as $page) {
                        $ocr = new $processClass([$tesseract, $page, 'stdout', '-l', 'por+eng', ...$ocrModes]);
                        $ocr->setTimeout(60);
                        $ocr->run();
                        if ($ocr->isSuccessful() && trim($ocr->getOutput()) !== '') {
                            $ocrText[] = $ocr->getOutput();
                        }
                    }
                    $ocrCandidate = trim(implode("\n", $ocrText));
                    if (mb_strlen($ocrCandidate) >= 80) {
                        $candidates[] = $ocrCandidate;
                        $candidateSources[] = 'OCR';
                    }
                    if (mb_strlen($ocrCandidate) >= 80 && $scoreText($ocrCandidate) > $bestScore) {
                        $bestText = $ocrCandidate;
                        $bestScore = $scoreText($ocrCandidate);
                    }
                }
            } catch (\Throwable $exception) {
                $ocrStatus = 'falhou';
                $errors[] = 'OCR indisponivel durante a execucao: '.$exception->getMessage();
            } finally {
                foreach (glob($temporaryDirectory.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
                @rmdir($temporaryDirectory);
            }
        }

        if (mb_strlen(trim($bestText)) < 80) {
            throw new \RuntimeException('Não foi possível extrair texto suficiente do PDF. '.implode(' ', array_filter($errors)));
        }

        // Diagnóstico visível apenas com indicadores técnicos; nunca inclui texto
        // extraído, nomes, NIF, IBAN, números de conta ou números de subscrição.
        $diagnosticParts = [];
        foreach ($candidates as $index => $candidate) {
            $upper = mb_strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $candidate) ?: $candidate);
            $compact = trim(preg_replace('/\\s+/', ' ', preg_replace('/[^A-Z0-9]+/', ' ', $upper)));
            $diagnosticParts[] = ($candidateSources[$index] ?? 'Leitor')
                . ':AFORRO='.(str_contains($compact, 'AFORRO') ? 'sim' : 'nao')
                . ',CAF='.(preg_match('/\\bCAF\\b/', $compact) ? 'sim' : 'nao')
                . ',SERIE_E='.(preg_match('/\\bSERIE\\s*E\\b/', $compact) ? 'sim' : 'nao')
                . ',SERIE_DETETADA='.($this->detectAforroNetSeries($candidate) ?? 'nao');
        }
        $this->aforroNetDiagnostic = 'Leitores: '.implode(' | ', $diagnosticParts)
            .'. Selecionado: '.($candidateSources[array_search($bestText, $candidates, true)] ?? 'desconhecido')
            .'. Executaveis: pdftotext='.(($pdftotext) ? 'detetado' : 'nao detetado')
            .', pdftoppm='.(($pdftoppm) ? 'detetado' : 'nao detetado')
            .', tesseract='.(($tesseract) ? 'detetado' : 'nao detetado')
            .'. Estado OCR: '.$ocrStatus.'.';

        Log::info('AforroNet PDF extraction selected', [
            'user_id' => auth()->id(),
            'candidate_count' => count($candidates),
            'selected_score' => $bestScore,
            'has_series_label' => (bool) preg_match('/CAF\s*\/\s*SERIE\s*E|CERTIFICADOS\s+DE\s+AFORRO\s+SERIE\s+E/i', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $bestText) ?: $bestText),
        ]);

        return $bestText;
    }

    /**
     * Resolve executáveis no PATH e, no Windows, nas pastas de instalação
     * comuns do Poppler/Tesseract instalados através do WinGet.
     */
    private function resolvePdfExecutable(string $binary): ?string
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return $binary;
        }

        $executable = $binary.'.exe';
        $processClass = \Symfony\Component\Process\Process::class;

        try {
            $where = new $processClass(['where.exe', $executable]);
            $where->setTimeout(5);
            $where->run();

            if ($where->isSuccessful()) {
                $found = trim(strtok($where->getOutput(), "\r\n"));
                if ($found !== '' && is_file($found)) {
                    return $found;
                }
            }
        } catch (\Throwable) {
            // Continua a procura nas pastas habituais.
        }

        $userProfile = getenv('USERPROFILE')
            ?: ($_SERVER['USERPROFILE'] ?? null)
            ?: ((getenv('HOMEDRIVE') && getenv('HOMEPATH')) ? getenv('HOMEDRIVE').getenv('HOMEPATH') : null);
        $localAppData = getenv('LOCALAPPDATA')
            ?: ($_SERVER['LOCALAPPDATA'] ?? null)
            ?: ($userProfile ? $userProfile.'\\AppData\\Local' : null);

        $roots = array_filter([
            getenv('ProgramFiles') ?: 'C:\\Program Files',
            getenv('ProgramFiles(x86)') ?: 'C:\\Program Files (x86)',
            $localAppData ? $localAppData.'\\Microsoft\\WinGet\\Packages' : null,
            getenv('ProgramData') ? getenv('ProgramData'). '\\chocolatey\\bin' : null,
        ]);

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            // O glob() do PHP no Windows pode interpretar barras invertidas
            // como caracteres de escape. Usa sempre barras normais nos padrões.
            $globRoot = rtrim(str_replace('\\', '/', $root), '/');
            $patterns = [
                $globRoot.'/*/'.$executable,
                $globRoot.'/*/Library/bin/'.$executable,
                $globRoot.'/*/*/Library/bin/'.$executable,
                $globRoot.'/*/bin/'.$executable,
            ];

            foreach ($patterns as $pattern) {
                foreach (glob($pattern) ?: [] as $candidate) {
                    if (is_file($candidate)) {
                        return $candidate;
                    }
                }
            }
        }

        // O servidor pode correr como serviço e não receber USERPROFILE nem
        // LOCALAPPDATA. Procura a instalação WinGet nos perfis locais do Windows.
        $windowsProfilesRoot = getenv('SystemDrive') ?: 'C:';
        $profilePattern = rtrim(str_replace('\\', '/', $windowsProfilesRoot), '/')
            .'/Users/*/AppData/Local/Microsoft/WinGet/Packages/*/*/Library/bin/'.$executable;

        foreach (glob($profilePattern) ?: [] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function parsePortugueseNumber(string $value): float
    {
        $value = trim(str_replace(["\xc2\xa0", ' ', '€'], '', $value));
        if ($value === '') {
            return 0.0;
        }

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            // O último separador é o decimal; o outro é separador de milhares.
            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            // Nos extratos portugueses, a vírgula representa os decimais.
            $value = str_replace(',', '.', $value);
        } elseif ($lastDot !== false) {
            // 2.300 representa milhares; 1.10412 representa um valor unitário.
            $decimals = strlen($value) - $lastDot - 1;
            if ($decimals === 3) {
                $value = str_replace('.', '', $value);
            }
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    public function importAforroNetPosition(): void
    {
        $this->authorizeImport();
        if (! $this->aforroNetPreviewReady || empty($this->aforroNetSubscriptions) || empty($this->aforroNetPosition)) {
            $this->dispatch('toast', variant: 'warning', text: 'Carrega e analisa primeiro o PDF do extrato.');
            return;
        }

        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        abort_unless($workspace && $user->current_workspace_id, 403);
        $this->isImportingAforroNet = true;

        try {
            DB::transaction(function () use ($user, $workspace) {
                $account = BankAccount::firstOrCreate(
                    ['workspace_id' => $workspace->id, 'user_id' => $user->id, 'name' => 'AforroNet'],
                    ['type' => 'investimentos', 'balance' => 0, 'currency' => 'EUR', 'color' => '#0f766e', 'include_in_total' => false, 'status' => 'active', 'description' => 'Certificados de Aforro importados do AforroNet']
                );

                $reportDate = \Illuminate\Support\Carbon::createFromFormat('d-m-Y', $this->aforroNetPosition['report_date'])->startOfDay();

                foreach ($this->aforroNetSubscriptions as $subscription) {
                    $operationDate = \Illuminate\Support\Carbon::parse($subscription['operation_date']);
                    $nextCapitalization = $operationDate->copy();
                    do {
                        $nextCapitalization->addMonthsNoOverflow(3);
                    } while ($nextCapitalization->lte($reportDate));

                    $symbol = 'CAF-E-'.$subscription['subscription_number'];
                    $existing = Investment::query()
                        ->where('workspace_id', $workspace->id)
                        ->where('user_id', $user->id)
                        ->where('broker', 'AforroNet')
                        ->where('subscription_number', $subscription['subscription_number'])
                        ->first();

                    if ($existing && $existing->valuation_date && $existing->valuation_date->gt($reportDate)) {
                        throw new \RuntimeException('O extrato é mais antigo do que os dados já guardados para a subscrição '.$subscription['subscription_number'].'.');
                    }

                    $data = [
                        'user_id' => $user->id,
                        'workspace_id' => $workspace->id,
                        'bank_account_id' => $account->id,
                        'name' => 'Certificados de Aforro Série E — '.$subscription['subscription_number'],
                        'symbol' => $symbol,
                        'type' => 'Divida',
                        'quantity' => $subscription['quantity'],
                        'average_price' => 1.000000,
                        'current_price' => $subscription['current_price'],
                        'provider' => 'AforroNet',
                        'broker' => 'AforroNet',
                        'issuer' => 'IGCP',
                        'series' => 'E',
                        'product_type' => 'CA',
                        'subscription_number' => $subscription['subscription_number'],
                        'operation_date' => $operationDate->toDateString(),
                        'valuation_date' => $reportDate->toDateString(),
                        'capitalization_date' => $nextCapitalization->toDateString(),
                        'fees' => 0,
                    ];

                    $existing ? $existing->update($data) : Investment::create($data);
                }

                // Remove apenas a antiga posição agregada criada pelo importador anterior.
                Investment::query()
                    ->where('workspace_id', $workspace->id)
                    ->where('user_id', $user->id)
                    ->where('broker', 'AforroNet')
                    ->where('symbol', 'CAF-SERIE-E')
                    ->delete();
            });

            $this->dispatch('toast', variant: 'success', text: count($this->aforroNetSubscriptions).' subscrições Série E sincronizadas individualmente.');
            $this->reset(['aforroNetFile', 'aforroNetPosition', 'aforroNetSubscriptions', 'aforroNetPreviewReady', 'aforroNetDiagnostic']);
        } catch (\Throwable $exception) {
            Log::error('AforroNet investment import failed', ['user_id' => $user->id, 'message' => $exception->getMessage()]);
            $this->dispatch('toast', variant: 'danger', text: $exception->getMessage());
        } finally {
            $this->isImportingAforroNet = false;
        }
    }

    public function clearAforroNet(): void
    {
        $this->reset(['aforroNetFile', 'aforroNetFile', 'aforroNetPosition', 'aforroNetSubscriptions', 'aforroNetPreviewReady']);
        $this->resetValidation();
    }

    public function switchProvider(string $provider): void
    {
        if (in_array($provider, ['xtb', 'aforronet'], true)) {
            $this->activeProvider = $provider;
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
