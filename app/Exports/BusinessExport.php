<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusinessExport implements FromCollection, WithHeadings, WithMapping
{
    protected $workspace;

    protected int $month;

    protected int $year;

    public function __construct($user, int $month, ?int $year = null)
    {
        $this->workspace = $user->currentWorkspace;
        $this->month = max(1, min(12, $month));
        $this->year = $year ?: now()->year;
    }

    public function collection()
    {
        if (! $this->workspace) {
            return collect();
        }

        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $expenses = $this->workspace->expenses()
            ->where('is_company', true)
            ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
            ->with(['category', 'supplier'])
            ->latest('spent_at')->get();

        $invoices = $this->workspace->invoices()
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')->get();

        return $invoices->concat($expenses);
    }

    public function headings(): array
    {
        $currency = strtoupper((string) ($this->workspace?->currency ?? 'EUR'));

        return ['Data', 'Tipo', 'Entidade/Cliente', 'Documento', "Base ({$currency})", "IVA ({$currency})", "Total ({$currency})", 'Estado'];
    }

    public function map($row): array
    {
        if (isset($row->invoice_number)) {
            return [
                optional($row->created_at)->format('d/m/Y'), 'VENDA', $row->client_name,
                $row->invoice_number, $row->amount_excl_vat, $row->vat_amount,
                $row->total_amount, $row->status,
            ];
        }

        return [
            optional($row->spent_at)->format('d/m/Y'), 'COMPRA', $row->supplier?->name ?? $row->description ?? 'Fornecedor não identificado',
            '-', (float) $row->amount - (float) ($row->vat_amount ?? 0), $row->vat_amount ?? 0,
            $row->amount, $row->status ?? 'registada',
        ];
    }
}
