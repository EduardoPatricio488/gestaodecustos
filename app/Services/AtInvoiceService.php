<?php

namespace App\Services;

use App\Models\AtInvoice;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class AtInvoiceService
{
    public function importCsv(Workspace $workspace, int $userId, UploadedFile $file): array
    {
        $content = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'UTF-8, ISO-8859-1');
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $delimiter = str_contains($lines[0] ?? '', ';') ? ';' : ',';
        $rows = array_map(fn ($l) => str_getcsv($l, $delimiter), array_filter($lines));

        $imported = 0;
        $skipped = 0;
        $totalVat = 0;

        foreach ($rows as $i => $row) {
            if ($i === 0 && $this->isHeader($row)) {
                continue;
            }

            $parsed = $this->parseRow($row);
            if (! $parsed) {
                $skipped++;

                continue;
            }

            if ($parsed['at_uid'] && AtInvoice::where('workspace_id', $workspace->id)->where('at_uid', $parsed['at_uid'])->exists()) {
                $skipped++;

                continue;
            }

            AtInvoice::create(array_merge($parsed, [
                'workspace_id' => $workspace->id,
                'user_id' => $userId,
                'status' => 'imported',
            ]));

            $totalVat += $parsed['vat_amount'];
            $imported++;
        }

        return compact('imported', 'skipped', 'totalVat');
    }

    public function validateNif(string $nif): bool
    {
        $nif = preg_replace('/\D/', '', $nif);
        if (strlen($nif) !== 9 || ! in_array($nif[0], ['1', '2', '3', '5', '6', '8', '9'])) {
            return false;
        }

        $check = 0;
        for ($i = 0; $i < 8; $i++) {
            $check += (int) $nif[$i] * (9 - $i);
        }
        $check = 11 - ($check % 11);
        if ($check >= 10) {
            $check = 0;
        }

        return (int) $nif[8] === $check;
    }

    public function getQuarterlyVatSummary(Workspace $workspace, int $year, int $quarter): array
    {
        $start = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfMonth();
        $end = $start->copy()->addMonths(3)->subDay();

        $invoices = AtInvoice::where('workspace_id', $workspace->id)
            ->whereBetween('issued_at', [$start, $end])
            ->get();

        return [
            'quarter' => "T{$quarter} {$year}",
            'count' => $invoices->count(),
            'total' => (float) $invoices->sum('amount'),
            'vat' => (float) $invoices->sum('vat_amount'),
            'due_date' => $end->copy()->addMonth()->day(20)->format('d/m/Y'),
        ];
    }

    private function isHeader(array $row): bool
    {
        $joined = strtolower(implode(' ', $row));

        return str_contains($joined, 'nif') || str_contains($joined, 'data') || str_contains($joined, 'valor');
    }

    private function parseRow(array $row): ?array
    {
        if (count($row) < 3) {
            return null;
        }

        $nif = null;
        $name = null;
        $date = null;
        $amount = null;
        $vat = 0;
        $uid = null;
        $docType = 'FT';

        foreach ($row as $cell) {
            $cell = trim($cell);
            if (preg_match('/^\d{9}$/', preg_replace('/\D/', '', $cell)) && ! $nif) {
                $nif = preg_replace('/\D/', '', $cell);
            } elseif ($d = $this->parseDate($cell)) {
                $date = $d;
            } elseif (is_numeric(str_replace([',', '.', ' '], '', $cell)) && $amount === null) {
                $amount = $this->parseAmount($cell);
            } elseif (str_starts_with(strtoupper($cell), 'PT') || strlen($cell) > 20) {
                $uid = $cell;
            } elseif (strlen($cell) > 3 && ! is_numeric(str_replace([',', '.'], '', $cell))) {
                $name = $name ?: $cell;
            }
        }

        if (! $date || ! $amount) {
            return null;
        }

        return [
            'at_uid' => $uid,
            'issuer_nif' => $nif,
            'issuer_name' => $name,
            'amount' => $amount,
            'vat_amount' => $vat,
            'issued_at' => $date,
            'document_type' => $docType,
            'raw_data' => $row,
        ];
    }

    private function parseDate(string $value): ?Carbon
    {
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            try {
                $d = Carbon::createFromFormat($format, trim($value));
                if ($d && $d->year > 2000) {
                    return $d;
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function parseAmount(string $value): float
    {
        $value = str_replace(['€', ' '], '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
