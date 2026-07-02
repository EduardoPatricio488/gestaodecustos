<?php

namespace App\Services;

use App\Models\BankStatementImport;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BankImportService
{
    private const BANK_PATTERNS = [
        'revolut' => ['data', 'date', 'started date', 'completed date'],
        'n26' => ['booking date', 'value date', 'partner name'],
        'cgd' => ['data mov.', 'data valor', 'descritivo'],
        'millennium' => ['data', 'data valor', 'descrição'],
    ];

    public function import(Workspace $workspace, int $userId, UploadedFile $file): BankStatementImport
    {
        $import = BankStatementImport::create([
            'workspace_id' => $workspace->id,
            'user_id' => $userId,
            'filename' => $file->getClientOriginalName(),
            'status' => 'processing',
        ]);

        try {
            $content = file_get_contents($file->getRealPath());
            $rows = $this->parseCsv($content);
            $bank = $this->detectBank($rows);
            $import->update(['bank_detected' => $bank]);

            $categories = Category::where('workspace_id', $workspace->id)->pluck('name', 'id');
            $imported = 0;
            $errors = [];

            foreach ($rows as $i => $row) {
                if ($i === 0 && $this->looksLikeHeader($row)) {
                    continue;
                }

                $parsed = $this->parseRow($row, $bank);
                if (! $parsed || $parsed['amount'] >= 0) {
                    continue;
                }

                $categoryId = $this->categorize($parsed['description'], $categories->toArray());

                Expense::create([
                    'user_id' => $userId,
                    'workspace_id' => $workspace->id,
                    'category_id' => $categoryId,
                    'title' => Str::limit($parsed['description'], 100),
                    'amount' => abs($parsed['amount']),
                    'description' => $parsed['description'],
                    'spent_at' => $parsed['date'],
                    'metadata' => ['import_id' => $import->id, 'bank' => $bank],
                ]);
                $imported++;
            }

            $import->update([
                'status' => 'completed',
                'transactions_total' => count($rows) - 1,
                'transactions_imported' => $imported,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => 'failed',
                'errors' => ['message' => $e->getMessage()],
            ]);
        }

        return $import->fresh();
    }

    private function parseCsv(string $content): array
    {
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $delimiter = str_contains($lines[0] ?? '', ';') ? ';' : ',';

        return array_map(fn ($line) => str_getcsv($line, $delimiter), array_filter($lines));
    }

    private function detectBank(array $rows): string
    {
        $header = strtolower(implode(' ', $rows[0] ?? []));

        foreach (self::BANK_PATTERNS as $bank => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($header, $pattern)) {
                    return $bank;
                }
            }
        }

        return 'generic';
    }

    private function looksLikeHeader(array $row): bool
    {
        $joined = strtolower(implode(' ', $row));

        return str_contains($joined, 'data') || str_contains($joined, 'date') || str_contains($joined, 'amount');
    }

    private function parseRow(array $row, string $bank): ?array
    {
        if (count($row) < 2) {
            return null;
        }

        return match ($bank) {
            'revolut' => $this->parseRevolut($row),
            'n26' => $this->parseN26($row),
            default => $this->parseGeneric($row),
        };
    }

    private function parseRevolut(array $row): ?array
    {
        $date = $this->parseDate($row[0] ?? $row[1] ?? '');
        $amount = $this->parseAmount($row[2] ?? $row[3] ?? '');
        $desc = $row[4] ?? $row[3] ?? $row[1] ?? 'Transação';

        return $date && $amount !== null ? ['date' => $date, 'amount' => $amount, 'description' => $desc] : null;
    }

    private function parseN26(array $row): ?array
    {
        $date = $this->parseDate($row[0] ?? '');
        $amount = $this->parseAmount($row[1] ?? $row[2] ?? '');
        $desc = $row[3] ?? $row[2] ?? 'Transação';

        return $date && $amount !== null ? ['date' => $date, 'amount' => $amount, 'description' => $desc] : null;
    }

    private function parseGeneric(array $row): ?array
    {
        $date = null;
        $amount = null;
        $desc = '';

        foreach ($row as $cell) {
            if (! $date && $d = $this->parseDate($cell)) {
                $date = $d;
            } elseif ($amount === null && ($a = $this->parseAmount($cell)) !== null) {
                $amount = $a;
            } elseif (strlen($cell) > 3 && ! is_numeric(str_replace([',', '.', '-', ' '], '', $cell))) {
                $desc = $cell;
            }
        }

        return $date && $amount !== null ? ['date' => $date, 'amount' => $amount, 'description' => $desc ?: 'Transação'] : null;
    }

    private function parseDate(string $value): ?Carbon
    {
        $value = trim($value);
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d'] as $format) {
            try {
                $d = Carbon::createFromFormat($format, $value);
                if ($d && $d->year > 2000) {
                    return $d;
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function parseAmount(string $value): ?float
    {
        $value = trim(str_replace(['€', 'EUR', ' '], '', $value));
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function categorize(string $description, array $categories): ?int
    {
        $desc = strtolower($description);
        $keywords = [
            'alimentação' => ['continente', 'pingo', 'lidl', 'auchan', 'mercado', 'supermercado', 'restaur'],
            'transporte' => ['uber', 'bolt', 'cp ', 'metro', 'gasolina', 'galp', 'repsol'],
            'saúde' => ['farmácia', 'farmacia', 'hospital', 'clínica', 'clinica'],
            'entretenimento' => ['netflix', 'spotify', 'cinema', 'steam', 'playstation'],
            'tecnologia' => ['apple', 'google', 'amazon', 'fnac', 'worten'],
        ];

        foreach ($keywords as $catName => $words) {
            foreach ($words as $word) {
                if (str_contains($desc, $word)) {
                    foreach ($categories as $id => $name) {
                        if (str_contains(strtolower($name), $catName) || strtolower($name) === $catName) {
                            return $id;
                        }
                    }
                }
            }
        }

        if ($this->hasAiKey()) {
            return $this->categorizeWithAi($description, $categories);
        }

        return array_key_first($categories);
    }

    private function hasAiKey(): bool
    {
        return filled(env('OPENROUTER_API_KEY'));
    }

    private function categorizeWithAi(string $description, array $categories): ?int
    {
        try {
            $catList = implode(', ', array_values($categories));
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.env('OPENROUTER_API_KEY'),
                'HTTP-Referer' => config('app.url'),
            ])->timeout(10)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'anthropic/claude-3-haiku',
                'messages' => [[
                    'role' => 'user',
                    'content' => "Categoriza esta transação bancária numa destas categorias: {$catList}. Transação: \"{$description}\". Responde APENAS com o nome exato da categoria.",
                ]],
                'max_tokens' => 20,
            ]);

            $name = trim($response->json('choices.0.message.content', ''));
            foreach ($categories as $id => $catName) {
                if (strcasecmp($catName, $name) === 0) {
                    return $id;
                }
            }
        } catch (\Throwable) {
        }

        return array_key_first($categories);
    }
}
