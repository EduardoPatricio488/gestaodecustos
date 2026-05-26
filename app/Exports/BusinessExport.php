<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusinessExport implements FromCollection, WithHeadings, WithMapping
{
    protected $user;
    protected $month;

    public function __construct($user, $month)
    {
        $this->user = $user;
        $this->month = $month;
    }

    public function collection()
    {
        // Busca as faturas e as despesas de empresa do mês selecionado
        $expenses = $this->user->expenses()
            ->where('is_company', true)
            ->whereMonth('spent_at', $this->month)
            ->get();

        $invoices = $this->user->invoices()
            ->whereMonth('created_at', $this->month)
            ->get();

        return $invoices->concat($expenses);
    }

    public function headings(): array
    {
        return ['Data', 'Tipo', 'Entidade/Cliente', 'Documento', 'Base (€)', 'IVA (€)', 'Total (€)'];
    }

    public function map($row): array
    {
        // Se for uma Fatura (Venda)
        if (isset($row->invoice_number)) {
            return [
                $row->created_at->format('d/m/Y'),
                'VENDA',
                $row->client_name,
                $row->invoice_number,
                $row->amount_excl_vat,
                $row->vat_amount,
                $row->total_amount,
            ];
        }

        // Se for uma Despesa de Empresa (Compra)
        return [
            $row->spent_at->format('d/m/Y'),
            'COMPRA',
            $row->description ?? 'Fornecedor Diversos',
            '-',
            $row->amount - ($row->vat_amount ?? 0),
            $row->vat_amount ?? 0,
            $row->amount,
        ];
    }
}
