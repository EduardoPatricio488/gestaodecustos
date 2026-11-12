<?php

namespace App\Services;

use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class InvoiceScanService
{
    public const MODEL = 'openrouter/free';

    public function analyse(
        string $fileContents,
        string $filename,
        string $mimeType,
        array $options = [],
        string $optionField = 'category',
    ): array {
        if (blank(config('services.openrouter.api_key'))) {
            throw new RuntimeException('A chave OpenRouter não está configurada.');
        }

        if ($fileContents === '') {
            throw new RuntimeException('O ficheiro da fatura está vazio ou não pode ser lido.');
        }

        $mimeType = strtolower(trim(explode(';', $mimeType)[0]));
        $isImage = str_starts_with($mimeType, 'image/');
        $isPdf = $mimeType === 'application/pdf' || str_ends_with(strtolower($filename), '.pdf');

        if (! $isImage && ! $isPdf) {
            throw new RuntimeException('Formato não suportado. Envia uma imagem ou um PDF.');
        }

        if ($isImage) {
            [$fileContents, $mimeType] = $this->prepareImage($fileContents, $mimeType);
        }

        $quota = app(OpenRouterQuotaService::class)->dailyQuota();
        if (($quota['available'] ?? false) && (int) ($quota['remaining'] ?? 0) <= 0) {
            throw new RuntimeException('A quota diária gratuita do OpenRouter está esgotada. A reposição ocorre às 00:00 UTC.');
        }

        $limit = max(1, (int) config('services.openrouter.requests_per_minute', 18));
        $limiter = app(RateLimiter::class);
        $rateKey = 'openrouter:invoice-scans:global';
        if ($limiter->tooManyAttempts($rateKey, $limit)) {
            throw new RuntimeException('O limite temporário de análises foi atingido. Aguarda '.max(1, $limiter->availableIn($rateKey)).' segundos.');
        }
        $limiter->hit($rateKey, 60);

        $allowed = array_values(array_filter(array_map(
            static fn ($item) => is_array($item) ? trim((string) ($item['name'] ?? '')) : trim((string) $item),
            $options
        )));
        $allowedText = $allowed ? implode(' | ', $allowed) : 'Não aplicável';

        $prompt = <<<PROMPT
Lê a fatura ou recibo português completo, incluindo cabeçalho, artigos, totais e rodapé.
Devolve APENAS um objeto JSON válido, sem markdown, comentários ou texto adicional.
Não inventes, não estimes e não calcules dados ausentes. Usa null quando um campo não estiver impresso ou não for legível.
amount é o total final pago; gross_amount é o total ilíquido; net_amount é a base tributável; vat_amount é o IVA total.
date deve ser YYYY-MM-DD; time deve ser HH:MM; valores numéricos devem usar ponto decimal.
Não confundas o NIF do emitente com o NIF do cliente.
payment_method deve ser Dinheiro, Multibanco, Cartão, MB Way, Transferência ou null.
{$optionField} deve corresponder exatamente a uma das opções seguintes, ou ser null: {$allowedText}.
Devolve todos estes campos:
{"category":null,"subcategory":null,"amount":null,"date":null,"time":null,"quantity":null,"unit":null,"store":null,"description":null,"item_name":null,"unit_price":null,"vat_rate":null,"gross_amount":null,"net_amount":null,"vat_amount":null,"tax_incidence":null,"amount_tendered":null,"change_amount":null,"payment_condition":null,"payment_method":null,"company_nif":null,"customer_nif":null,"invoice_number":null,"notes":null}
PROMPT;

        $encoded = base64_encode($fileContents);
        $part = $isImage
            ? ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mimeType.';base64,'.$encoded, 'detail' => 'high']]
            : ['type' => 'file', 'file' => ['filename' => basename($filename), 'file_data' => 'data:application/pdf;base64,'.$encoded]];

        $payload = [
            'model' => self::MODEL,
            'temperature' => 0,
            'max_tokens' => 3000,
            'messages' => [[
                'role' => 'user',
                'content' => [['type' => 'text', 'text' => $prompt], $part],
            ]],
        ];

        if ($isPdf) {
            $payload['plugins'] = [[
                'id' => 'file-parser',
                'pdf' => ['engine' => 'cloudflare-ai'],
            ]];
        }

        $response = Http::withToken(config('services.openrouter.api_key'))
            ->acceptJson()
            ->withHeaders([
                'HTTP-Referer' => (string) config('app.url'),
                'X-Title' => 'Finance Pro AI',
            ])
            ->connectTimeout((int) config('services.openrouter.connect_timeout', 8))
            ->timeout((int) config('services.openrouter.request_timeout', 45))
            ->post('https://openrouter.ai/api/v1/chat/completions', $payload);

        if (! $response->successful()) {
            $status = $response->status();
            $message = (string) ($response->json('error.message') ?: 'Erro de comunicação com o OpenRouter.');
            Log::warning('Invoice scanner: OpenRouter request failed.', [
                'http_status' => $status,
                'provider_error' => $response->json('error'),
            ]);

            if ($status === 429) {
                throw new RuntimeException('O OpenRouter está a limitar pedidos ou o fornecedor gratuito não tem capacidade. '.$message);
            }
            if ($status === 402) {
                throw new RuntimeException('O OpenRouter recusou o pedido por limite de créditos/orçamento. Não foi criada qualquer despesa.');
            }
            if ($status === 401 || $status === 403) {
                throw new RuntimeException('A chave OpenRouter é inválida ou não tem permissões. Confirma OPENROUTER_API_KEY.');
            }
            throw new RuntimeException('Não foi possível analisar a fatura (HTTP '.$status.'): '.$message);
        }

        $content = $response->json('choices.0.message.content');
        if (is_array($content)) {
            $content = collect($content)
                ->map(static fn ($part) => is_string($part) ? $part : data_get($part, 'text', data_get($part, 'content', '')))
                ->filter(static fn ($part) => is_string($part) && trim($part) !== '')
                ->implode("\n");
        }

        $raw = trim((string) $content);
        $raw = preg_replace('/^\x60{3}(?:json)?\s*|\s*\x60{3}$/i', '', $raw);
        $result = json_decode($raw, true);

        if (! is_array($result) && preg_match('/\{.*\}/s', $raw, $matches)) {
            $result = json_decode($matches[0], true);
        }

        if (! is_array($result) || ($response->json('choices.0.finish_reason') === 'length')) {
            throw new RuntimeException('A IA não devolveu uma leitura completa e válida. Não foi criada qualquer despesa; tenta novamente ou preenche os dados manualmente.');
        }

        $aliases = [
            'amount' => ['total', 'total_paid', 'valor_total', 'total_amount'],
            'date' => ['purchase_date', 'invoice_date', 'data'],
            'store' => ['merchant', 'establishment', 'supplier', 'loja', 'estabelecimento'],
            'description' => ['items_description', 'purchase_description', 'details', 'descricao'],
            'item_name' => ['product', 'product_name', 'item', 'purchase'],
            'vat_amount' => ['iva_amount', 'valor_iva', 'tax'],
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

        $result['amount'] = $this->normaliseAmount($result['amount'] ?? null);
        $result['date'] = $this->normaliseDate($result['date'] ?? null);

        $essential = ['amount', 'date', 'store', 'description', 'item_name', 'invoice_number'];
        $hasData = collect($essential)->contains(
            static fn ($field) => isset($result[$field]) && $result[$field] !== '' && $result[$field] !== null
        );
        if (! $hasData) {
            throw new RuntimeException('O documento foi enviado, mas a IA não conseguiu ler dados essenciais. Se for um PDF digitalizado, tenta enviar uma fotografia nítida.');
        }

        if ($allowed && ! empty($result[$optionField])) {
            $matched = collect($allowed)->first(
                static fn ($value) => mb_strtolower($value) === mb_strtolower(trim((string) $result[$optionField]))
            );
            $result[$optionField] = $matched;
        }

        return $result;
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
            $scale = min(1, 1800 / max($width, $height));
            if ($scale >= 1) {
                return [$contents, $mimeType];
            }

            $targetWidth = max(1, (int) round($width * $scale));
            $targetHeight = max(1, (int) round($height * $scale));
            $target = imagecreatetruecolor($targetWidth, $targetHeight);
            $white = imagecolorallocate($target, 255, 255, 255);
            imagefill($target, 0, 0, $white);
            imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            ob_start();
            imagejpeg($target, null, 84);
            $compressed = ob_get_clean();
            imagedestroy($target);

            return is_string($compressed) && $compressed !== '' ? [$compressed, 'image/jpeg'] : [$contents, $mimeType];
        } finally {
            imagedestroy($source);
        }
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

    private function normaliseDate(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'd/m/y', 'd-m-y', 'd.m.y'] as $format) {
            try {
                $date = \DateTimeImmutable::createFromFormat('!'.$format, $value);
                $errors = \DateTimeImmutable::getLastErrors();
                if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                // Tenta o formato seguinte.
            }
        }

        return null;
    }
}
