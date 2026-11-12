<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class InvoiceScanService
{
    private const MODEL = 'openrouter/free';
    private const CHAT_URL = 'https://openrouter.ai/api/v1/chat/completions';
    private const KEY_URL = 'https://openrouter.ai/api/v1/key';

    private const FIELDS = [
        'amount', 'item_name', 'date', 'time', 'quantity', 'unit', 'store',
        'description', 'category', 'subcategory', 'unit_price', 'vat_rate',
        'gross_amount', 'net_amount', 'vat_amount', 'tax_incidence',
        'amount_tendered', 'change_amount', 'payment_condition', 'payment_method',
        'company_nif', 'customer_nif', 'invoice_number', 'notes',
    ];

    /**
     * Analisa um PDF ou imagem através do modelo gratuito do OpenRouter.
     *
     * $options pode conter "categories" (nome/slug) ou "subcategories" (strings).
     * Não existe fallback para modelos pagos nem repetição automática do pedido.
     */
    public function analyse(
        string $filePath,
        string $filename,
        string $mimeType,
        array $options = []
    ): array {
        $apiKey = config('services.openrouter.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('A chave OpenRouter não está configurada.');
        }

        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw new RuntimeException('O ficheiro da fatura já não está disponível.');
        }

        $contents = file_get_contents($filePath);

        if (! is_string($contents) || $contents === '') {
            throw new RuntimeException('Não foi possível ler o conteúdo da fatura.');
        }

        $mimeType = strtolower(trim(explode(';', $mimeType)[0]));
        if (! in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('Formato de ficheiro não suportado pelo scanner.');
        }

        if (str_starts_with($mimeType, 'image/')) {
            [$contents, $mimeType] = $this->prepareImage($contents, $mimeType);
        }

        $this->assertQuotaAvailable($apiKey);
        $this->applyRateLimit();

        $contentPart = $mimeType === 'application/pdf'
            ? [
                'type' => 'file',
                'file' => [
                    'filename' => $filename,
                    'file_data' => 'data:application/pdf;base64,'.base64_encode($contents),
                ],
            ]
            : [
                'type' => 'image_url',
                'image_url' => [
                    'url' => 'data:'.$mimeType.';base64,'.base64_encode($contents),
                    'detail' => 'high',
                ],
            ];

        $prompt = $this->buildPrompt($options);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Finance Pro AI',
            ])
                ->acceptJson()
                ->connectTimeout((int) config('services.openrouter.connect_timeout', 8))
                ->timeout((int) config('services.openrouter.request_timeout', 45))
                ->post(self::CHAT_URL, [
                    'model' => self::MODEL,
                    'temperature' => 0,
                    'max_tokens' => 3500,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            $contentPart,
                        ],
                    ]],
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            throw new RuntimeException(
                'O OpenRouter não respondeu dentro do tempo limite. A fatura não foi registada; podes tentar novamente.',
                previous: $exception
            );
        }

        if (! $response->successful()) {
            $status = $response->status();
            $providerMessage = (string) ($response->json('error.message') ?: 'Erro de comunicação.');

            Log::warning('Invoice scanner: OpenRouter request failed.', [
                'http_status' => $status,
                'provider_message' => $providerMessage,
            ]);

            if ($status === 429) {
                $retryAfter = (int) $response->header('Retry-After', 0);
                throw new RuntimeException('O OpenRouter está temporariamente limitado. '.($retryAfter > 0 ? 'Tenta novamente daqui a '.$retryAfter.' segundos.' : 'Aguarda alguns minutos e tenta novamente.'));
            }

            if ($status === 402) {
                throw new RuntimeException('O OpenRouter recusou o pedido por limite de créditos/orçamento (HTTP 402). Não foi criada qualquer despesa.');
            }

            if ($status === 401 || $status === 403) {
                throw new RuntimeException('A chave OpenRouter é inválida ou não tem permissões. Confirma OPENROUTER_API_KEY.');
            }

            throw new RuntimeException('O OpenRouter devolveu um erro (HTTP '.$status.'): '.$providerMessage);
        }

        $finishReason = (string) ($response->json('choices.0.finish_reason') ?? '');
        if ($finishReason === 'length') {
            throw new RuntimeException('A resposta da IA ficou incompleta. Tenta novamente com uma imagem mais nítida ou um ficheiro mais pequeno.');
        }

        $content = $response->json('choices.0.message.content');
        if (is_array($content)) {
            $content = collect($content)
                ->map(fn ($part) => is_string($part) ? $part : data_get($part, 'text', data_get($part, 'content', '')))
                ->filter(fn ($part) => is_string($part) && trim($part) !== '')
                ->implode("\n");
        }

        $result = $this->decodeJson((string) $content);
        $result = $this->normaliseResult($result);
        $this->assertReadableInvoice($result);

        if (! empty($options['categories'])) {
            $category = collect($options['categories'])->first(
                fn ($item) => mb_strtolower(trim((string) ($item['name'] ?? ''))) === mb_strtolower(trim((string) ($result['category'] ?? '')))
            );

            if (! $category) {
                throw new RuntimeException('A IA não conseguiu associar a fatura a uma categoria existente. Escolhe a categoria manualmente.');
            }

            $result['category'] = $category['name'];
            $result['category_slug'] = $category['slug'];
        }

        if (! empty($options['subcategories'])) {
            $allowed = array_map(
                fn ($value) => mb_strtolower(trim((string) $value)),
                $options['subcategories']
            );
            $suggested = mb_strtolower(trim((string) ($result['subcategory'] ?? '')));

            if ($suggested !== '' && ! in_array($suggested, $allowed, true)) {
                $result['subcategory'] = null;
            }
        }

        return $result;
    }

    private function assertQuotaAvailable(string $apiKey): void
    {
        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->connectTimeout(4)
                ->timeout(8)
                ->get(self::KEY_URL);

            if (in_array($response->status(), [401, 403], true)) {
                throw new RuntimeException('A chave OpenRouter é inválida ou não tem permissões.');
            }

            if ($response->successful()) {
                $quota = $response->json('data.free_model_daily_requests');

                if (is_array($quota) && isset($quota['remaining']) && (int) $quota['remaining'] <= 0) {
                    $used = (int) ($quota['used'] ?? 0);
                    $limit = (int) ($quota['limit'] ?? 0);
                    throw new RuntimeException('A quota diária gratuita do OpenRouter está esgotada ('.$used.'/'.$limit.' pedidos). Renova às 00:00 UTC.');
                }
            } else {
                Log::notice('Invoice scanner: quota pre-check unavailable; continuing with openrouter/free.', [
                    'http_status' => $response->status(),
                ]);
            }
        } catch (RuntimeException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::notice('Invoice scanner: quota pre-check failed; continuing with openrouter/free.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function applyRateLimit(): void
    {
        $limit = max(1, (int) config('services.openrouter.requests_per_minute', 18));
        $key = 'openrouter:invoice-scans:global';
        $limiter = app(RateLimiter::class);

        if ($limiter->tooManyAttempts($key, $limit)) {
            throw new RuntimeException(
                'Foi atingido o limite de segurança do scanner. Aguarda '.$limiter->availableIn($key).' segundos.'
            );
        }

        $limiter->hit($key, 60);
    }

    private function buildPrompt(array $options): string
    {
        $allowed = ! empty($options['categories'])
            ? array_column($options['categories'], 'name')
            : ($options['subcategories'] ?? []);

        $allowedJson = json_encode(array_values($allowed), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $classification = ! empty($options['categories'])
            ? 'A categoria tem de ser exatamente uma destas: '.$allowedJson.'.'
            : (! empty($options['subcategories'])
                ? 'A subcategoria tem de ser exatamente uma destas: '.$allowedJson.'.'
                : 'Não inventes categorias nem subcategorias; usa null quando não identificável.');

        return <<<PROMPT
Analisa a fatura ou recibo português completo, incluindo cabeçalho, linhas de artigos, totais e rodapé. Devolve APENAS um objeto JSON válido, sem markdown.
{$classification}
Extrai apenas informação efetivamente impressa e legível. Não inventes, não estimes e não calcules dados ausentes; usa null.
amount = total final efetivamente pago; gross_amount = total ilíquido; net_amount = base tributável; vat_amount = IVA total.
date = YYYY-MM-DD; time = HH:MM; valores monetários e quantidades são números decimais com ponto.
Não confundas NIF do emitente com NIF do cliente.
payment_method: Dinheiro, Multibanco, Cartão, MB Way, Transferência ou null.
Devolve exatamente esta estrutura:
{"category":null,"subcategory":null,"amount":null,"date":null,"time":null,"quantity":null,"unit":null,"store":null,"description":null,"item_name":null,"unit_price":null,"vat_rate":null,"gross_amount":null,"net_amount":null,"vat_amount":null,"tax_incidence":null,"amount_tendered":null,"change_amount":null,"payment_condition":null,"payment_method":null,"company_nif":null,"customer_nif":null,"invoice_number":null,"notes":null}
PROMPT;
    }

    private function decodeJson(string $content): array
    {
        $raw = trim($content);
        $raw = preg_replace('/^\x60{3}(?:json)?\s*|\s*\x60{3}$/i', '', $raw);
        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('A IA não devolveu JSON completo e válido. Não foi possível confirmar a leitura da fatura.');
        }

        return $decoded;
    }

    private function normaliseResult(array $result): array
    {
        $aliases = [
            'amount' => ['total', 'total_paid', 'valor_total', 'total_amount'],
            'date' => ['purchase_date', 'invoice_date', 'data'],
            'store' => ['merchant', 'establishment', 'supplier', 'loja', 'estabelecimento'],
            'description' => ['items_description', 'purchase_description', 'details', 'descricao'],
            'item_name' => ['product', 'product_name', 'item', 'purchase'],
            'vat_rate' => ['iva', 'tax_rate'],
            'vat_amount' => ['iva_amount', 'valor_iva', 'tax'],
            'gross_amount' => ['total_gross'],
            'net_amount' => ['total_net'],
        ];

        foreach ($aliases as $target => $alternatives) {
            if (! isset($result[$target]) || $result[$target] === '') {
                foreach ($alternatives as $alternative) {
                    if (isset($result[$alternative]) && $result[$alternative] !== '') {
                        $result[$target] = $result[$alternative];
                        break;
                    }
                }
            }
        }

        $normalised = array_fill_keys(self::FIELDS, null);
        foreach (self::FIELDS as $field) {
            if (array_key_exists($field, $result)) {
                $normalised[$field] = $result[$field];
            }
        }

        $normalised['amount'] = $this->normaliseAmount($normalised['amount']);
        foreach (['quantity', 'unit_price', 'vat_rate', 'gross_amount', 'net_amount', 'vat_amount', 'amount_tendered', 'change_amount'] as $field) {
            $normalised[$field] = $this->normaliseAmount($normalised[$field]);
        }

        if (! empty($normalised['date'])) {
            $normalised['date'] = $this->normaliseDate((string) $normalised['date']);
        }

        foreach (['store', 'description', 'item_name', 'subcategory', 'category', 'unit', 'time', 'payment_condition', 'payment_method', 'company_nif', 'customer_nif', 'invoice_number', 'notes'] as $field) {
            if (is_string($normalised[$field])) {
                $normalised[$field] = trim($normalised[$field]) !== '' ? trim($normalised[$field]) : null;
            }
        }

        return $normalised;
    }

    private function normaliseAmount(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace([' ', "\u{00A0}", '€'], '', trim($value));

            if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
                $value = str_replace(['.', ','], ['', '.'], $value);
            } else {
                $value = str_replace(',', '.', $value);
            }
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function normaliseDate(string $value): ?string
    {
        $value = trim($value);

        if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $value)) {
            try {
                return Carbon::createFromFormat('!Y-m-d', $value)->format('Y-m-d');
            } catch (\\Throwable) {
                return null;
            }
        }

        if (preg_match('~^(\d{1,2})[./-](\d{1,2})[./-](\d{2}|\d{4})$~', $value, $matches)) {
            $year = (int) $matches[3];
            if ($year < 100) {
                $year += $year >= 70 ? 1900 : 2000;
            }

            try {
                return Carbon::createFromFormat('!Y-n-j', $year.'-'.$matches[2].'-'.$matches[1])->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function assertReadableInvoice(array $result): void
    {
        $hasAmount = is_numeric($result['amount'] ?? null) && (float) $result['amount'] > 0;
        $hasContext = collect(['date', 'store', 'description', 'item_name', 'invoice_number'])
            ->contains(fn ($field) => ! blank($result[$field] ?? null));

        if (! $hasAmount || ! $hasContext) {
            throw new RuntimeException('O ficheiro foi enviado, mas a IA não conseguiu confirmar dados suficientes da fatura. Experimenta uma fotografia mais nítida, direita e com o documento completo.');
        }
    }

    private function prepareImage(string $contents, string $mimeType): array
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagejpeg')) {
            return [$contents, $mimeType];
        }

        $source = @imagecreatefromstring($contents);
        if ($source === false) {
            return [$contents, $mimeType];
        }

        try {
            $width = imagesx($source);
            $height = imagesy($source);
            $scale = min(1, 2200 / max($width, $height));

            if ($scale >= 1 && $mimeType === 'image/jpeg') {
                return [$contents, $mimeType];
            }

            $targetWidth = max(1, (int) round($width * $scale));
            $targetHeight = max(1, (int) round($height * $scale));
            $target = imagecreatetruecolor($targetWidth, $targetHeight);
            $white = imagecolorallocate($target, 255, 255, 255);
            imagefill($target, 0, 0, $white);
            imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            ob_start();
            imagejpeg($target, null, 88);
            $jpeg = ob_get_clean();
            imagedestroy($target);

            return is_string($jpeg) && $jpeg !== '' ? [$jpeg, 'image/jpeg'] : [$contents, $mimeType];
        } finally {
            imagedestroy($source);
        }
    }
}
