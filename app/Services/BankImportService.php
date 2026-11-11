<?php

namespace App\Services;

use App\Models\BankStatementImport;
use App\Models\BankTransfer;
use App\Models\Debt;
use App\Models\CategorizationRule;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GoalContribution;
use App\Models\Income;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
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

        foreach ($parsed['transactions'] as $transactionIndex => $transaction) {
            $amount = (float) ($transaction['amount'] ?? 0);
            $classification = $this->classifyMovement((string) ($transaction['description'] ?? ''), $amount);
            $expenseCandidates[] = [
                'signature' => $this->buildExpenseSignature($transaction, $transactionIndex),
                'date' => $transaction['date']->format('Y-m-d'),
                'description' => Str::limit((string) $transaction['description'], 90),
                'amount' => round($amount, 2),
                'type' => $classification['type'] === 'transfer' ? 'transfer' : ($classification['type'] === 'debt_payment' ? 'debt_payment' : ($amount >= 0 ? 'income' : 'expense')),
                'movement_label' => $classification['label'],
                'transfer_category' => $classification['transfer_category'] ?? null,
            ];
        }

        $this->refineMovementTypesWithAi($expenseCandidates);
        foreach ($expenseCandidates as $index => $row) {
            if ((float) ($row['amount'] ?? 0) < 0 && $this->isKnownMerchantExpense($this->normalizeMovementDescription((string) ($row['description'] ?? '')))) {
                $expenseCandidates[$index]['type'] = 'expense';
                $expenseCandidates[$index]['transfer_category'] = null;
            }
        }
        $expenseCandidates = array_values(array_filter($expenseCandidates, fn ($row) =>
            (float) $row['amount'] < 0 || $row['type'] === 'transfer'
        ));

        return [
            'bank' => $parsed['bank'],
            'source_file_type' => $parsed['source_file_type'],
            'transactions_total' => count($parsed['transactions']),
            'expenses_total' => count(array_filter($expenseCandidates, fn ($row) => in_array($row['type'], ['expense', 'debt_payment'], true))),
            'transfers_total' => count(array_filter($expenseCandidates, fn ($row) => $row['type'] === 'transfer')),
            'rows' => $expenseCandidates,
        ];
    }

    public function previewSantanderPdf(UploadedFile $file, int $workspaceId, int $userId): array
    {
        if (! class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new \RuntimeException('O leitor de PDF não está instalado no servidor.');
        }

        $document = (new \Smalot\PdfParser\Parser())->parseFile($file->getRealPath());
        $text = preg_replace('/\s+/u', ' ', trim($document->getText()));
        if (! $text || ! str_contains(mb_strtolower($text), 'movimentos da sua conta')) {
            throw new \RuntimeException('Este PDF não parece ser um extrato de movimentos do Santander ou não contém texto legível.');
        }

        $monthMap = ['jan'=>'01','fev'=>'02','mar'=>'03','abr'=>'04','mai'=>'05','jun'=>'06','jul'=>'07','ago'=>'08','set'=>'09','out'=>'10','nov'=>'11','dez'=>'12'];
        $monthPattern = '(?:jan|fev|mar|abr|mai|jun|jul|ago|set|out|nov|dez)';
        $segments = preg_split('/(?=\d{2}\s+'.$monthPattern.'\s+\d{4}\s+D\.\s*valor:)/iu', $text, -1, PREG_SPLIT_NO_EMPTY);
        $otherCategoryId = $this->ensureOtherCategory($workspaceId, $userId);
        $categories = Category::where('workspace_id', $workspaceId)->pluck('name', 'id')->all();
        $rows = [];

        foreach ($segments as $segment) {
            if (! preg_match('/(?<date>\d{2}\s+'.$monthPattern.'\s+\d{4})\s+D\.\s*valor:\s*\d{2}\s+'.$monthPattern.'\s+\d{4}\s*/iu', $segment, $header, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            $dateParts = preg_split('/\s+/', mb_strtolower(trim($header['date'][0])));
            $month = $monthMap[$dateParts[1] ?? ''] ?? null;
            if (! $month) {
                continue;
            }

            try {
                $date = Carbon::createFromFormat('d/m/Y', $dateParts[0].'/'.$month.'/'.$dateParts[2])->startOfDay();
            } catch (\Throwable) {
                continue;
            }

            preg_match_all('/(?<amount>[−–-]?\s*\d[\d.]*,\d{2})\s*€?\s+(?<balance>[−–-]?\s*\d[\d.]*,\d{2})\s*€?/u', $segment, $moneyMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            if ($moneyMatches === []) {
                continue;
            }

            $money = $moneyMatches[count($moneyMatches) - 1];
            $amountText = str_replace(['−', '–'], '-', preg_replace('/\s+/u', '', $money['amount'][0]));
            $balanceText = str_replace(['−', '–'], '-', preg_replace('/\s+/u', '', $money['balance'][0]));
            $amount = $this->parseAmount($amountText);
            $balance = $this->parseAmount($balanceText);
            if ($amount === null || abs($amount) < 0.001) {
                continue;
            }

            $descriptionStart = $header[0][1] + strlen($header[0][0]);
            $descriptionLength = max(0, $money['amount'][1] - $descriptionStart);
            $description = trim(preg_replace('/\s+/u', ' ', substr($segment, $descriptionStart, $descriptionLength)));
            $description = trim($description, " \t\n\r\0\x0B|;,-");
            if ($description === '') {
                continue;
            }

            $classification = $this->classifyMovement($description, $amount);
            $mafrenseNemPasses = $this->isMafrenseNemPasses($description);
            $transportCategoryId = $categories
                ? collect($categories)->filter(fn ($name) => str_contains(mb_strtolower((string) $name), 'transport'))->keys()->first()
                : null;
            $categoryId = $mafrenseNemPasses
                ? ($transportCategoryId ? (int) $transportCategoryId : $otherCategoryId)
                : ($classification['type'] === 'expense'
                    ? ($this->categorize($workspaceId, $userId, $description, $categories, false) ?: $otherCategoryId)
                    : null);
            $rows[] = [
                'signature' => sha1($date->format('Y-m-d').'|'.number_format($amount, 2, '.', '').'|'.mb_strtolower($description).'|'.count($rows)),
                'date' => $date->format('Y-m-d'),
                'description' => Str::limit($description, 160),
                'amount' => round($amount, 2),
                'balance' => round((float) ($balance ?? 0), 2),
                'type' => $classification['type'] === 'transfer' ? 'transfer' : ($classification['type'] === 'debt_payment' ? 'debt_payment' : ($amount < 0 ? 'expense' : 'income')),
                'category_id' => $categoryId,
                'movement_label' => $classification['label'],
                'transfer_category' => $classification['transfer_category'] ?? null,
            ];
        }

        if ($rows === []) {
            throw new \RuntimeException('Não consegui identificar movimentos neste PDF. Confirma que exportaste o extrato completo em PDF através de Contas → Movimentos → PDF.');
        }

        $this->refineMovementTypesWithAi($rows);

        // Compras a comerciantes conhecidos nunca podem ser promovidas a transferência pela IA.
        foreach ($rows as $rowIndex => $row) {
            $description = (string) ($row['description'] ?? '');
            if ($this->isKnownMerchantExpense($this->normalizeMovementDescription($description)) && (float) ($row['amount'] ?? 0) < 0) {
                $rows[$rowIndex]['type'] = 'expense';
                $rows[$rowIndex]['transfer_category'] = null;
                $rows[$rowIndex]['category_id'] = $this->categorize($workspaceId, $userId, $description, $categories, false) ?: $otherCategoryId;
            }
        }

        $unknownIndexes = [];
        foreach ($rows as $rowIndex => $row) {
            if ($row['type'] === 'expense' && ! $row['category_id']) {
                $unknownIndexes[] = $rowIndex;
            }
        }
        if ($unknownIndexes !== []) {
            $descriptions = array_map(fn ($rowIndex) => $rows[$rowIndex]['description'], $unknownIndexes);
            $suggestions = $this->categorizeManyWithAi($descriptions, $categories);
            foreach ($unknownIndexes as $position => $rowIndex) {
                $rows[$rowIndex]['category_id'] = $suggestions[$position] ?? $otherCategoryId;
            }
        }

        return [
            'bank' => 'Santander',
            'source_file_type' => 'pdf',
            'transactions_total' => count($rows),
            'expenses_total' => count(array_filter($rows, fn ($row) => in_array($row['type'], ['expense', 'debt_payment'], true))),
            'incomes_total' => count(array_filter($rows, fn ($row) => $row['type'] === 'income')),
            'transfers_total' => count(array_filter($rows, fn ($row) => $row['type'] === 'transfer')),
            'rows' => $rows,
        ];
    }

    public function confirmPreview(Workspace $workspace, int $userId, string $filename, array $rows): BankStatementImport
    {
        $import = BankStatementImport::create([
            'workspace_id' => $workspace->id,
            'user_id' => $userId,
            'filename' => $filename,
            'bank_detected' => 'santander',
            'status' => 'processing',
        ]);

        try {
            $otherCategoryId = $this->ensureOtherCategory((int) $workspace->id, $userId);
            $categories = Category::where('workspace_id', $workspace->id)->pluck('name', 'id');
            $imported = 0;
            $duplicates = 0;

            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $rowDescription = trim((string) ($row['description'] ?? ''));
                if ($this->isKnownMerchantExpense($this->normalizeMovementDescription($rowDescription)) && (float) ($row['amount'] ?? 0) < 0) {
                    $row['type'] = 'expense';
                    $row['transfer_category'] = null;
                    $row['category_id'] = $this->categorize((int) $workspace->id, $userId, $rowDescription, $categories->all(), false) ?: $otherCategoryId;
                }
                if (! in_array($row['type'] ?? null, ['expense', 'income', 'transfer', 'debt_payment'], true)) {
                    continue;
                }
                $date = Carbon::parse($row['date'] ?? '');
                $amount = round((float) ($row['amount'] ?? 0), 2);
                $description = trim((string) ($row['description'] ?? ''));
                if ($description === '' || $amount === 0 || (in_array($row['type'], ['expense', 'debt_payment'], true) && $amount >= 0) || ($row['type'] === 'income' && $amount <= 0)) {
                    continue;
                }
                $categoryId = isset($row['category_id']) && $categories->has((int) $row['category_id'])
                    ? (int) $row['category_id']
                    : (in_array($row['type'], ['expense', 'debt_payment'], true) ? $otherCategoryId : null);
                if ($row['type'] === 'debt_payment') {
                    $transportCategoryId = $categories->filter(fn ($name) => str_contains(mb_strtolower((string) $name), 'transport'))->keys()->first();
                    $categoryId = $transportCategoryId ? (int) $transportCategoryId : $otherCategoryId;
                }
                $absoluteAmount = abs($amount);

                if ($row['type'] === 'transfer') {
                    $direction = $amount > 0 ? 'in' : 'out';
                    $exists = BankTransfer::query()->where('workspace_id', $workspace->id)->where('user_id', $userId)
                        ->whereDate('transferred_at', $date->toDateString())->where('amount', number_format($absoluteAmount, 2, '.', ''))
                        ->where('description', $description)->exists();
                    if ($exists) {
                        $duplicates++;
                        continue;
                    }
                    BankTransfer::create([
                        'workspace_id' => $workspace->id, 'user_id' => $userId,
                        'from_account_id' => null, 'to_account_id' => null,
                        'direction' => $direction, 'recipient' => $this->extractCounterparty($description),
                        'amount' => $absoluteAmount, 'currency' => 'EUR', 'category' => $row['transfer_category'] ?? null,
                        'description' => $description,
                        'status' => 'completed', 'transferred_at' => $date,
                        'bank_statement_import_id' => $import->id,
                        'notes' => 'Importado automaticamente do extrato bancário.',
                    ]);
                } elseif ($row['type'] === 'debt_payment') {
                    $exists = Expense::query()->where('workspace_id', $workspace->id)->where('user_id', $userId)
                        ->whereDate('spent_at', $date->toDateString())->where('amount', number_format($absoluteAmount, 2, '.', ''))
                        ->where('description', $description)->exists();
                    if ($exists) {
                        $duplicates++;
                        continue;
                    }
                    $expense = Expense::create([
                        'user_id' => $userId, 'workspace_id' => $workspace->id, 'category_id' => $categoryId,
                        'bank_statement_import_id' => $import->id, 'title' => Str::limit($description, 100),
                        'amount' => $absoluteAmount, 'amount_paid' => $absoluteAmount, 'status' => 'paid',
                        'description' => $description, 'spent_at' => $date,
                        'metadata' => ['import_id' => $import->id, 'bank' => 'santander', 'source_file_type' => 'pdf', 'movement_type' => 'debt_payment'],
                    ]);
                    Debt::create([
                        'workspace_id' => $workspace->id, 'user_id' => $userId,
                        'type' => 'owe', 'person_name' => str_contains($this->normalizeMovementDescription($description), 'mafrense') ? 'Mafrense' : 'Credor não identificado', 'amount' => $absoluteAmount,
                        'description' => 'Dívida de transportes — '.$description,
                        'due_at' => $date, 'is_paid' => true, 'expense_id' => $expense->id,
                        'bank_statement_import_id' => $import->id,
                    ]);
                } elseif ($row['type'] === 'expense') {
                    $exists = Expense::query()->where('workspace_id', $workspace->id)->where('user_id', $userId)
                        ->whereDate('spent_at', $date->toDateString())->where('amount', number_format($absoluteAmount, 2, '.', ''))
                        ->where('description', $description)->exists();
                    if ($exists) {
                        $duplicates++;
                        continue;
                    }
                    Expense::create([
                        'user_id' => $userId, 'workspace_id' => $workspace->id, 'category_id' => $categoryId,
                        'bank_statement_import_id' => $import->id,
                        'title' => Str::limit($description, 100), 'amount' => $absoluteAmount,
                        'description' => $description, 'spent_at' => $date,
                        'metadata' => ['import_id' => $import->id, 'bank' => 'santander', 'source_file_type' => 'pdf'],
                    ]);
                } else {
                    $exists = \App\Models\Income::query()->where('workspace_id', $workspace->id)->where('user_id', $userId)
                        ->whereDate('received_at', $date->toDateString())->where('amount', number_format($absoluteAmount, 2, '.', ''))
                        ->where('description', $description)->exists();
                    if ($exists) {
                        $duplicates++;
                        continue;
                    }
                    Income::create([
                        'user_id' => $userId, 'workspace_id' => $workspace->id, 'category_id' => $categoryId,
                        'bank_statement_import_id' => $import->id,
                        'description' => $description, 'amount' => $absoluteAmount,
                        'received_at' => $date, 'type' => 'Extra', 'source' => 'outros',
                        'frequency' => 'pontual',
                    ]);
                }
                $imported++;
            }

            $import->update([
                'status' => 'completed',
                'transactions_total' => count($rows),
                'transactions_imported' => $imported,
                'errors' => $duplicates > 0 ? ['duplicates_skipped' => $duplicates] : null,
            ]);
        } catch (\Throwable $exception) {
            $import->update(['status' => 'failed', 'errors' => ['message' => $exception->getMessage()]]);
        }

        return $import->fresh();
    }

    public function deleteImport(BankStatementImport $import): array
    {
        return DB::transaction(function () use ($import): array {
            $expensesQuery = Expense::query()
                ->where('workspace_id', $import->workspace_id)
                ->where('user_id', $import->user_id)
                ->where(function ($query) use ($import) {
                    $query->where('bank_statement_import_id', $import->id)
                        ->orWhere('metadata->import_id', $import->id);
                });

            $deletedTransfers = BankTransfer::query()->where('workspace_id', $import->workspace_id)->where('user_id', $import->user_id)
                ->where('bank_statement_import_id', $import->id)->count();
            BankTransfer::query()->where('workspace_id', $import->workspace_id)->where('user_id', $import->user_id)
                ->where('bank_statement_import_id', $import->id)->delete();
            $deletedDebts = Debt::query()->where('workspace_id', $import->workspace_id)->where('user_id', $import->user_id)
                ->where('bank_statement_import_id', $import->id)->count();
            Debt::query()->where('workspace_id', $import->workspace_id)->where('user_id', $import->user_id)
                ->where('bank_statement_import_id', $import->id)->delete();

            $incomeIds = Income::query()
                ->where('workspace_id', $import->workspace_id)
                ->where('user_id', $import->user_id)
                ->where('bank_statement_import_id', $import->id)
                ->pluck('id');

            $contributions = GoalContribution::query()
                ->whereIn('income_id', $incomeIds)
                ->get(['id', 'goal_id', 'amount']);

            foreach ($contributions->groupBy('goal_id') as $goalId => $items) {
                $total = (float) $items->sum('amount');
                DB::table('goals')->where('id', $goalId)->where('workspace_id', $import->workspace_id)
                    ->update(['current_amount' => DB::raw('GREATEST(0, current_amount - '.number_format($total, 2, '.', '').')')]);
            }

            $contributions->each->delete();
            $deletedExpenses = $expensesQuery->count();
            $deletedIncomes = Income::query()
                ->where('workspace_id', $import->workspace_id)
                ->where('user_id', $import->user_id)
                ->where('bank_statement_import_id', $import->id)
                ->delete();
            $expensesQuery->delete();
            $import->delete();

            return ['expenses' => $deletedExpenses, 'incomes' => $deletedIncomes, 'transfers' => $deletedTransfers, 'debts' => $deletedDebts];
        });
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
            $otherCategoryId = $this->ensureOtherCategory((int) $workspace->id, $userId);
            $categories = Category::where('workspace_id', $workspace->id)->pluck('name', 'id');
            $imported = 0;
            $duplicatesSkipped = 0;
            $aiRows = [];
            foreach ($transactions as $transactionIndex => $candidate) {
                $candidateAmount = (float) ($candidate['amount'] ?? 0);
                $candidateDescription = (string) ($candidate['description'] ?? '');
                $candidateClassification = $this->classifyMovement($candidateDescription, $candidateAmount);
                $aiRows[$transactionIndex] = [
                    'type' => $candidateClassification['type'] === 'transfer' || $candidateClassification['type'] === 'debt_payment'
                        ? $candidateClassification['type']
                        : ($candidateAmount >= 0 ? 'income' : 'expense'),
                    'description' => $candidateDescription,
                    'amount' => $candidateAmount,
                ];
            }
            $this->refineMovementTypesWithAi($aiRows);
            $selectedLookup = is_array($selectedSignatures) ? array_fill_keys(array_values($selectedSignatures), true) : null;

            foreach ($transactions as $transactionIndex => $transaction) {
                $signedAmount = (float) ($transaction['amount'] ?? 0);
                $description = trim((string) ($transaction['description'] ?? ''));
                $classification = $this->classifyMovement($description, $signedAmount);
                if (isset($aiRows[$transactionIndex])) {
                    $classification['type'] = $aiRows[$transactionIndex]['type'];
                }
                // A Mafrense Nem Passes é sempre uma despesa de transporte,
                // mesmo que uma classificação anterior ou IA a marque como transferência/dívida.
                $normalizedDescription = $this->normalizeMovementDescription($description);
                $isRegularMerchantPurchase = $signedAmount < 0 && (
                    str_contains($normalizedDescription, 'mercadona')
                    || str_contains($normalizedDescription, 'hamb da vila')
                    || $this->isKnownMerchantExpense($normalizedDescription)
                );
                if ($isRegularMerchantPurchase) {
                    // Proteção final antes de gravar: compras comerciais nunca criam transferências ou dívidas.
                    $classification['type'] = 'expense';
                    $classification['transfer_category'] = null;
                }
                if ($signedAmount >= 0 && $classification['type'] !== 'transfer') {
                    continue;
                }

                $signature = $this->buildExpenseSignature($transaction, $transactionIndex);
                if ($selectedLookup !== null && ! isset($selectedLookup[$signature])) {
                    continue;
                }

                $amount = abs($signedAmount);
                $date = $transaction['date'];
                if ($classification['type'] === 'transfer') {
                    $exists = BankTransfer::query()->where('workspace_id', $workspace->id)->where('user_id', $userId)
                        ->whereDate('transferred_at', $date->toDateString())->where('amount', number_format($amount, 2, '.', ''))
                        ->where('description', $description)->exists();
                    if ($exists) {
                        $duplicatesSkipped++;
                        continue;
                    }
                    BankTransfer::create([
                        'workspace_id' => $workspace->id, 'user_id' => $userId,
                        'from_account_id' => null, 'to_account_id' => null,
                        'direction' => $signedAmount > 0 ? 'in' : 'out',
                        'recipient' => $this->extractCounterparty($description),
                        'amount' => $amount, 'currency' => 'EUR',
                        'category' => $classification['transfer_category'] ?? null,
                        'description' => $description,
                        'status' => 'completed', 'transferred_at' => $date,
                        'bank_statement_import_id' => $import->id,
                        'notes' => 'Importado automaticamente do extrato bancário.',
                    ]);
                } elseif ($classification['type'] === 'debt_payment') {
                    if ($this->expenseAlreadyExists($workspace->id, $userId, $date, $amount, $description)) {
                        $duplicatesSkipped++;
                        continue;
                    }
                    $transportCategoryId = $categories->filter(fn ($name) => str_contains(mb_strtolower((string) $name), 'transport'))->keys()->first();
                    $categoryId = $transportCategoryId ? (int) $transportCategoryId : $otherCategoryId;
                    $expense = Expense::create([
                        'user_id' => $userId, 'workspace_id' => $workspace->id, 'category_id' => $categoryId,
                        'bank_statement_import_id' => $import->id, 'title' => Str::limit($description, 100),
                        'amount' => $amount, 'amount_paid' => $amount, 'status' => 'paid',
                        'description' => $description, 'spent_at' => $date,
                        'metadata' => ['import_id' => $import->id, 'bank' => $bank, 'source_file_type' => $sourceFileType, 'movement_type' => 'debt_payment'],
                    ]);
                    Debt::create([
                        'workspace_id' => $workspace->id, 'user_id' => $userId, 'type' => 'owe',
                        'person_name' => str_contains($this->normalizeMovementDescription($description), 'mafrense') ? 'Mafrense' : 'Credor não identificado', 'amount' => $amount,
                        'description' => 'Dívida de transportes — '.$description,
                        'due_at' => $date, 'is_paid' => true, 'expense_id' => $expense->id,
                        'bank_statement_import_id' => $import->id,
                    ]);
                } else {
                    if ($this->expenseAlreadyExists($workspace->id, $userId, $date, $amount, $description)) {
                        $duplicatesSkipped++;
                        continue;
                    }
                    $categoryId = $this->categorize($workspace->id, $userId, $description, $categories->toArray()) ?: $otherCategoryId;
                    Expense::create([
                        'user_id' => $userId, 'workspace_id' => $workspace->id, 'category_id' => $categoryId,
                        'bank_statement_import_id' => $import->id, 'title' => Str::limit($description, 100),
                        'amount' => $amount, 'description' => $description, 'spent_at' => $date,
                        'metadata' => ['import_id' => $import->id, 'bank' => $bank, 'source_file_type' => $sourceFileType],
                    ]);
                }
                $imported++;
            }
            $import->update(['status' => 'completed', 'transactions_total' => count($transactions), 'transactions_imported' => $imported, 'errors' => $duplicatesSkipped > 0 ? ['duplicates_skipped' => $duplicatesSkipped] : null]);
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'errors' => ['message' => $e->getMessage()]]);
        }

        return $import->fresh();
    }

    private function ensureOtherCategory(int $workspaceId, int $userId): int
    {
        $category = Category::query()->where('workspace_id', $workspaceId)
            ->whereRaw('LOWER(name) = ?', ['outros'])->first();

        if (! $category) {
            $category = Category::create([
                'workspace_id' => $workspaceId, 'user_id' => $userId,
                'name' => 'Outros', 'color' => '#64748b',
            ]);
        }

        return (int) $category->id;
    }

    private function normalizeMovementDescription(string $description): string
    {
        $description = Str::ascii(mb_strtolower(trim($description)));
        return preg_replace('/[^a-z0-9]+/i', ' ', $description) ?? $description;
    }

    private function classifyMovement(string $description, float $amount): array
    {
        $normalized = $this->normalizeMovementDescription($description);

        // O Imposto do Selo é um custo associado às comissões bancárias,
        // apresentado na página de Transferências como custo de transferência.
        if (str_contains($normalized, 'imposto do selo sobre comissao')) {
            return [
                'type' => 'transfer',
                'label' => 'Imposto do Selo sobre comissão',
                'transfer_category' => 'Imposto do Selo',
            ];
        }

        // Estes comerciantes representam compras/serviços, nunca transferências.
        if ($this->isKnownMerchantExpense($normalized)) {
            return ['type' => $amount >= 0 ? 'income' : 'expense', 'label' => $amount >= 0 ? 'Receita' : 'Despesa'];
        }

        $patterns = [
            'trf mbway', 'trf mb way', 'transferencia mbway', 'transferencia mb way',
            'trf imed', 'trf imediata', 'transferencia imediata', 'betclic pt',
            'comissao disponibilizacao de cartao de debito',
            'anulacao de comissao disponibilizacao de cartao de debito',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($normalized, $this->normalizeMovementDescription($pattern))) {
                return ['type' => 'transfer', 'label' => 'Transferência bancária'];
            }
        }

        // Alguns PDFs do Santander perdem o "ã" em "Comissão".
        if (str_starts_with($normalized, 'comisso ') || str_starts_with($normalized, 'comissao ')) {
            return ['type' => 'transfer', 'label' => 'Transferência bancária'];
        }

        // Mafrense Nem Passes é uma despesa normal de transporte, não uma dívida.
        return ['type' => $amount >= 0 ? 'income' : 'expense', 'label' => $amount >= 0 ? 'Receita' : 'Despesa'];
    }

    private function isMafrenseNemPasses(string $description): bool
    {
        $normalized = $this->normalizeMovementDescription($description);

        // Os extratos podem apresentar o comerciante apenas como "Mafrense" ou "Nem Passes".
        return str_contains($normalized, 'mafrense') || str_contains($normalized, 'nem passes');
    }

    private function isKnownMerchantExpense(string $normalizedDescription): bool
    {
        return preg_match('/\\bh3\\b/', $normalizedDescription) === 1
            || (str_contains($normalizedDescription, 'hamb') && str_contains($normalizedDescription, 'vila'))
            || str_contains($normalizedDescription, 'mercadona')
            || str_contains($normalizedDescription, 'github')
            || str_contains($normalizedDescription, 'sbksileven')
            || str_contains($normalizedDescription, 'mafrense')
            || str_contains($normalizedDescription, 'nem passes')
            || (str_contains($normalizedDescription, 'compra') && str_contains($normalizedDescription, '1293'))
            || str_contains($normalizedDescription, 'www amazon')
            || str_contains($normalizedDescription, 'amazon')
            || str_contains($normalizedDescription, 'farmacia falcao');
    }

    private function categoryIdByName(array $categories, string $wantedName): ?int
    {
        foreach ($categories as $id => $name) {
            if (mb_strtolower(trim((string) $name), 'UTF-8') === mb_strtolower($wantedName, 'UTF-8')) {
                return (int) $id;
            }
        }

        return null;
    }

    private function refineMovementTypesWithAi(array &$rows): void
    {
        if (! $this->hasAiKey() || $rows === []) {
            return;
        }

        $candidates = [];
        foreach ($rows as $index => $row) {
            if (in_array($row['type'], ['transfer', 'debt_payment'], true)
                || $this->isKnownMerchantExpense($this->normalizeMovementDescription((string) ($row['description'] ?? '')))) {
                continue;
            }
            $candidates[] = ['index' => $index, 'description' => $row['description'], 'amount' => $row['amount']];
        }

        if ($candidates === []) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.openrouter.api_key'),
                'HTTP-Referer' => config('app.url'),
            ])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 5))
                ->timeout((int) config('limits.ai.request_timeout_seconds', 30))
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model', 'openai/gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'És um classificador de movimentos bancários portugueses. Para cada descrição, escolhe exclusivamente uma classe: transfer, debt_payment, expense ou income. Transfer significa envio/receção de dinheiro, transferências MB WAY, imediatas, movimentos Betclic e comissões bancárias explicitamente indicadas como transferências. debt_payment significa liquidação de uma dívida/obrigação identificável. Uma compra, serviço, subscrição, levantamento ou pagamento comercial normal é expense. Um crédito/rendimento é income. Não inventes relações nem assumes que qualquer pagamento é transferência. Usa debt_payment apenas quando o texto indicar claramente uma dívida. Devolve JSON: {"items":[{"index":0,"type":"expense","confidence":0.0}]}.'],
                        ['role' => 'user', 'content' => json_encode($candidates, JSON_UNESCAPED_UNICODE)],
                    ],
                    'max_tokens' => 900, 'temperature' => 0,
                ]);

            $content = trim((string) $response->json('choices.0.message.content', ''));
            $content = preg_replace('/^\x60{3}(?:json)?\s*|\s*\x60{3}$/i', '', $content);
            $decoded = json_decode($content, true);

            foreach (($decoded['items'] ?? []) as $item) {
                $index = (int) ($item['index'] ?? -1);
                $type = (string) ($item['type'] ?? '');
                $confidence = (float) ($item['confidence'] ?? 0);
                if (! isset($rows[$index]) || $confidence < 0.80 || ! in_array($type, ['transfer', 'debt_payment', 'expense', 'income'], true)) {
                    continue;
                }
                if ($type === 'income' && (float) $rows[$index]['amount'] < 0) {
                    continue;
                }
                if (in_array($type, ['expense', 'debt_payment', 'transfer'], true) && (float) $rows[$index]['amount'] > 0 && $type !== 'transfer') {
                    continue;
                }
                $rows[$index]['type'] = $type;
                $rows[$index]['movement_label'] = match ($type) {
                    'transfer' => 'Transferência bancária',
                    'debt_payment' => 'Pagamento de dívida',
                    'income' => 'Receita',
                    default => 'Despesa',
                };
            }
        } catch (\Throwable) {
            // Mantém a classificação determinística se a IA estiver indisponível.
        }
    }

    private function extractCounterparty(string $description): string
    {
        $clean = trim(preg_replace('/^(trf\.?\s*mb\s*way|trf\.?\s*imed\.?|transferencia\s+imediata|transferencia\s+mb\s*way)\s*[-:]?\s*/iu', '', $description) ?? $description);
        return Str::limit($clean !== '' ? $clean : $description, 255, '');
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

    private function categorize(int $workspaceId, int $userId, string $description, array $categories, bool $useAi = true): ?int
    {
        if ($categories === []) {
            return null;
        }
        $desc = strtolower($description);
        $normalized = $this->normalizeMovementDescription($description);

        // Regras determinísticas para os comerciantes indicados pelo utilizador.
        $fixedCategory = match (true) {
            str_contains($normalized, 'github') => 'Tecnologia',
            str_contains($normalized, 'sbksileven') => 'Entretenimento',
            str_contains($normalized, 'mercadona'),
            str_contains($normalized, 'hamb da vila'),
            preg_match('/\\bh3\\b/', $normalized) === 1 => 'Alimentação',
            (str_contains($normalized, 'mafrense') || str_contains($normalized, 'nem passes')) => 'Transporte',
            str_contains($normalized, 'farmacia falcao') => 'Saúde',
            str_contains($normalized, 'www amazon'),
            str_contains($normalized, 'amazon') => 'Casa',
            default => null,
        };

        if ($fixedCategory) {
            $fixedCategoryId = $this->categoryIdByName($categories, $fixedCategory);
            if ($fixedCategoryId) {
                return $fixedCategoryId;
            }
        }

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
        if ($useAi && $this->hasAiKey()) {
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

    private function categorizeManyWithAi(array $descriptions, array $categories): array
    {
        if (! $this->hasAiKey() || $descriptions === [] || $categories === []) {
            return [];
        }

        try {
            $categoryNames = array_values($categories);
            $payload = array_map(fn ($description, $index) => ['index' => $index, 'description' => $description], $descriptions, array_keys($descriptions));
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.openrouter.api_key'),
                'HTTP-Referer' => config('app.url'),
            ])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 5))
                ->timeout((int) config('limits.ai.request_timeout_seconds', 30))
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model', 'openai/gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'Classifica movimentos bancários apenas nas categorias fornecidas. Não inventes categorias. Devolve exclusivamente JSON válido no formato {"categories":[{"index":0,"category":"nome exato ou UNKNOWN"}]}.'],
                        ['role' => 'user', 'content' => 'Categorias disponíveis: '.json_encode($categoryNames, JSON_UNESCAPED_UNICODE).'. Movimentos: '.json_encode($payload, JSON_UNESCAPED_UNICODE)],
                    ],
                    'max_tokens' => 700,
                    'temperature' => 0,
                ]);

            $content = trim((string) $response->json('choices.0.message.content', ''));
            $content = preg_replace('/^\x60{3}(?:json)?\s*|\s*\x60{3}$/i', '', $content);
            $decoded = json_decode($content, true);
            $mapped = [];
            foreach (($decoded['categories'] ?? []) as $item) {
                $index = (int) ($item['index'] ?? -1);
                $name = trim((string) ($item['category'] ?? ''));
                if ($index < 0 || $index >= count($descriptions) || $name === '' || strcasecmp($name, 'UNKNOWN') === 0) {
                    continue;
                }
                foreach ($categories as $id => $categoryName) {
                    if (strcasecmp($categoryName, $name) === 0) {
                        $mapped[$index] = (int) $id;
                        break;
                    }
                }
            }

            return $mapped;
        } catch (\Throwable) {
            return [];
        }
    }

    private function categorizeWithAi(string $description, array $categories): ?int
    {
        try {
            $catList = implode(', ', array_values($categories));
            $response = Http::withHeaders(['Authorization' => 'Bearer '.config('services.openrouter.api_key'), 'HTTP-Referer' => config('app.url')])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 5))->timeout((int) config('limits.ai.request_timeout_seconds', 30))->post('https://openrouter.ai/api/v1/chat/completions', ['model' => config('services.openrouter.model', 'openai/gpt-4o-mini'), 'messages' => [['role' => 'system', 'content' => 'Classifica apenas com base no texto fornecido. Nunca inventes uma categoria.'], ['role' => 'user', 'content' => "Categoriza esta transação bancária numa destas categorias: {$catList}. Transação: \"{$description}\". Responde APENAS com o nome exato da categoria ou UNKNOWN."]], 'max_tokens' => 20, 'temperature' => 0]);
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
