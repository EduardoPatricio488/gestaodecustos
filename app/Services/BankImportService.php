<?php

namespace App\Services;

use App\Models\BankStatementImport;
use App\Models\CategorizationRule;
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

    public function preview(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());
        $ext = strtolower($file->getClientOriginalExtension());
        $parsed = $this->parseTransactionsFromFile($content, $ext);
        $expenseCandidates = [];
        $expenseIndex = 0;

        foreach ($parsed['transactions'] as $transaction) {
            if (($transaction['amount'] ?? 0) >= 0) {
                continue;
            }
            $signature = $this->buildExpenseSignature($transaction, $expenseIndex);
            $expenseCandidates[] = ['signature' => $signature, 'date' => $transaction['date']->format('Y-m-d'), 'description' => Str::limit((string) $transaction['description'], 90), 'amount' => round((float) $transaction['amount'], 2)];
            $expenseIndex++;
        }

        return ['bank' => $parsed['bank'], 'source_file_type' => $parsed['source_file_type'], 'transactions_total' => count($parsed['transactions']), 'expenses_total' => count($expenseCandidates), 'rows' => $expenseCandidates];
    }

    public function import(Workspace $workspace, int $userId, UploadedFile $file, ?array $selectedSignatures = null): BankStatementImport
    {
        $import = BankStatementImport::create(['workspace_id' => $workspace->id, 'user_id' => $userId, 'filename' => $file->getClientOriginalName(), 'status' => 'processing']);
        try {
            $content = file_get_contents($file->getRealPath());
            $ext = strtolower($file->getClientOriginalExtension());
            $parsed = $this->parseTransactionsFromFile($content, $ext);
            $transactions = $parsed['transactions'];
            $bank = $parsed['bank'];
            $sourceFileType = $parsed['source_file_type'];
            $import->update(['bank_detected' => $bank]);
            $categories = Category::where('workspace_id', $workspace->id)->pluck('name', 'id');
            $imported = 0;
            $duplicatesSkipped = 0;
            $expenseIndex = 0;
            $selectedLookup = is_array($selectedSignatures) ? array_fill_keys(array_values($selectedSignatures), true) : null;

            foreach ($transactions as $transaction) {
                if (($transaction['amount'] ?? 0) >= 0) {
                    continue;
                }
                $signature = $this->buildExpenseSignature($transaction, $expenseIndex++);
                if ($selectedLookup !== null && ! isset($selectedLookup[$signature])) {
                    continue;
                }
                $amount = abs((float) $transaction['amount']);
                $description = trim((string) $transaction['description']);
                if ($this->expenseAlreadyExists($workspace->id, $userId, $transaction['date'], $amount, $description)) {
                    $duplicatesSkipped++;

                    continue;
                }
                $categoryId = $this->categorize($workspace->id, $userId, $description, $categories->toArray());
                Expense::create(['user_id' => $userId, 'workspace_id' => $workspace->id, 'category_id' => $categoryId, 'title' => Str::limit($description, 100), 'amount' => $amount, 'description' => $description, 'spent_at' => $transaction['date'], 'metadata' => ['import_id' => $import->id, 'bank' => $bank, 'source_file_type' => $sourceFileType]]);
                $imported++;
            }
            $import->update(['status' => 'completed', 'transactions_total' => count($transactions), 'transactions_imported' => $imported, 'errors' => $duplicatesSkipped > 0 ? ['duplicates_skipped' => $duplicatesSkipped] : null]);
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'errors' => ['message' => $e->getMessage()]]);
        }

        return $import->fresh();
    }

    private function expenseAlreadyExists(int $workspaceId, int $userId, Carbon $date, float $amount, string $description): bool
    {
        return Expense::query()->where('workspace_id', $workspaceId)->where('user_id', $userId)->whereDate('spent_at', $date->toDateString())->where('amount', number_format($amount, 2, '.', ''))->where('description', $description)->exists();
    }

    private function buildExpenseSignature(array $transaction, int $expenseIndex): string
    {
        $date = ($transaction['date'] instanceof Carbon) ? $transaction['date']->format('Y-m-d') : (string) ($transaction['date'] ?? '');
        $amount = number_format((float) ($transaction['amount'] ?? 0), 2, '.', '');
        $description = mb_strtolower(trim((string) ($transaction['description'] ?? '')));

        return sha1($date.'|'.$amount.'|'.$description.'|'.$expenseIndex);
    }

    private function parseTransactionsFromFile(string $content, string $ext): array
    {
        if ($ext === 'ofx') {
            return ['transactions' => $this->parseOfxTransactions($content), 'bank' => $this->detectBankFromOfx($content), 'source_file_type' => 'ofx'];
        }
        $rows = $this->parseCsv($content);
        $bank = $this->detectBank($rows);
        $transactions = [];
        foreach ($rows as $i => $row) {
            if ($i === 0 && $this->looksLikeHeader($row)) {
                continue;
            } $parsed = $this->parseRow($row, $bank);
            if ($parsed) {
                $transactions[] = $parsed;
            }
        }

        return ['transactions' => $transactions, 'bank' => $bank, 'source_file_type' => 'csv'];
    }

    private function parseCsv(string $content): array
    {
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $delimiter = str_contains($lines[0] ?? '', ';') ? ';' : ',';

        return array_map(fn ($line) => str_getcsv($line, $delimiter), array_filter($lines));
    }

    private function parseOfxTransactions(string $content): array
    {
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        $transactions = [];
        preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/is', $content, $blocks);
        foreach ($blocks[1] ?? [] as $block) {
            $date = $this->parseOfxDate($this->extractOfxField($block, 'DTPOSTED') ?: '');
            $amount = $this->parseOfxAmount($this->extractOfxField($block, 'TRNAMT') ?: '');
            if (! $date || $amount === null) {
                continue;
            }
            $description = trim(implode(' ', array_filter([$this->extractOfxField($block, 'NAME'), $this->extractOfxField($block, 'MEMO')])));
            if ($description === '') {
                $description = $this->extractOfxField($block, 'TRNTYPE') ?: 'Transacao OFX';
            }
            $transactions[] = ['date' => $date, 'amount' => $amount, 'description' => $description];
        }

        return $transactions;
    }

    private function extractOfxField(string $block, string $field): ?string
    {
        if (! preg_match('/<'.preg_quote($field, '/').'>([^<\r\n]+)/i', $block, $m)) {
            return null;
        }

        return trim($m[1]);
    }

    private function parseOfxDate(string $value): ?Carbon
    {
        $value = trim($value);
        if (preg_match('/^(\d{8})/', $value, $m)) {
            try {
                $d = Carbon::createFromFormat('Ymd', $m[1]);
                if ($d && $d->year > 2000) {
                    return $d;
                }
            } catch (\Throwable) {
            }
        }

        return $this->parseDate($value);
    }

    private function parseOfxAmount(string $value): ?float
    {
        return $this->parseAmount($value);
    }

    private function detectBankFromOfx(string $content): string
    {
        $lower = strtolower($content);
        if (str_contains($lower, 'revolut')) {
            return 'revolut';
        }
        if (str_contains($lower, 'n26')) {
            return 'n26';
        }
        if (str_contains($lower, 'millennium') || str_contains($lower, 'bcp')) {
            return 'millennium';
        }
        if (str_contains($lower, 'caixa geral') || str_contains($lower, 'cgd')) {
            return 'cgd';
        }

        return 'ofx';
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
            'revolut' => $this->parseRevolut($row), 'n26' => $this->parseN26($row), default => $this->parseGeneric($row)
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
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        $negative = str_starts_with($value, '-') || (str_starts_with($value, '(') && str_ends_with($value, ')'));
        $value = trim($value, '() ');
        $value = str_replace(['€', 'EUR', 'eur', ' '], '', $value);
        if ($value === '' || ! preg_match('/^[+-]?[0-9.,]+$/', $value)) {
            return null;
        }
        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');
        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            $fractionLength = strlen($value) - $lastComma - 1;
            $value = ($fractionLength === 1 || $fractionLength === 2) ? str_replace(',', '.', $value) : str_replace(',', '', $value);
        } elseif ($lastDot !== false) {
            $fractionLength = strlen($value) - $lastDot - 1;
            if ($fractionLength !== 1 && $fractionLength !== 2) {
                $value = str_replace('.', '', $value);
            }
        }
        if (! is_numeric($value)) {
            return null;
        }
        $amount = (float) $value;

        return $negative ? -abs($amount) : $amount;
    }

    private function categorize(int $workspaceId, int $userId, string $description, array $categories): ?int
    {
        if ($categories === []) {
            return null;
        }
        $desc = strtolower($description);
        $customRuleCategoryId = $this->categorizeWithRules($workspaceId, $userId, $desc, array_keys($categories));
        if ($customRuleCategoryId) {
            return $customRuleCategoryId;
        }
        $keywords = ['alimentação' => ['continente', 'pingo', 'lidl', 'auchan', 'mercado', 'supermercado', 'restaur'], 'transporte' => ['uber', 'bolt', 'cp ', 'metro', 'gasolina', 'galp', 'repsol'], 'saúde' => ['farmácia', 'farmacia', 'hospital', 'clínica', 'clinica'], 'entretenimento' => ['netflix', 'spotify', 'cinema', 'steam', 'playstation'], 'tecnologia' => ['apple', 'google', 'amazon', 'fnac', 'worten']];
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

        return null;
    }

    private function categorizeWithRules(int $workspaceId, int $userId, string $descriptionLower, array $validCategoryIds): ?int
    {
        $validLookup = array_fill_keys(array_map('intval', $validCategoryIds), true);
        $rules = CategorizationRule::query()->where('workspace_id', $workspaceId)->where('user_id', $userId)->where('is_active', true)->orderBy('priority')->orderByDesc('id')->get();
        foreach ($rules as $rule) {
            $keyword = mb_strtolower(trim((string) $rule->keyword));
            if ($keyword === '' || ! isset($validLookup[(int) $rule->category_id])) {
                continue;
            }
            if (str_contains($descriptionLower, $keyword)) {
                return (int) $rule->category_id;
            }
        }

        return null;
    }

    private function hasAiKey(): bool
    {
        return filled(config('services.openrouter.api_key'));
    }

    private function categorizeWithAi(string $description, array $categories): ?int
    {
        try {
            $catList = implode(', ', array_values($categories));
            $response = Http::withHeaders(['Authorization' => 'Bearer '.config('services.openrouter.api_key'), 'HTTP-Referer' => config('app.url')])->timeout(10)->post('https://openrouter.ai/api/v1/chat/completions', ['model' => config('services.openrouter.model', 'openai/gpt-4o-mini'), 'messages' => [['role' => 'system', 'content' => 'Classifica apenas com base no texto fornecido. Nunca inventes uma categoria.'], ['role' => 'user', 'content' => "Categoriza esta transação bancária numa destas categorias: {$catList}. Transação: \"{$description}\". Responde APENAS com o nome exato da categoria ou UNKNOWN."]], 'max_tokens' => 20, 'temperature' => 0]);
            $name = trim($response->json('choices.0.message.content', ''));
            if (strcasecmp($name, 'UNKNOWN') === 0) {
                return null;
            }
            foreach ($categories as $id => $catName) {
                if (strcasecmp($catName, $name) === 0) {
                    return $id;
                }
            }
        } catch (\Throwable) {
        }

        return null;
    }
}
