<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BusinessExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function expensesPdf()
    {
        $user = auth()->user();
        $expenses = $user->expenses()->with('category')->latest()->get();
        $pdf = Pdf::loadView('pdf.expenses', compact('expenses'));
        return $pdf->download('despesas_pessoais.pdf');
    }

    public function businessExport(Request $request)
    {
        $user = auth()->user();

        // 1. Pegamos no valor e garantimos que é um NÚMERO INTEIRO
        $monthNumber = (int) $request->get('month', date('n'));

        // 2. Criamos a data usando apenas números para evitar o erro de "string"
        $date = Carbon::create(date('Y'), $monthNumber, 1);

        // 3. Obtemos o nome do mês traduzido
        $monthName = $date->translatedFormat('F');

        return Excel::download(
            new BusinessExport($user, $monthNumber),
            "Contabilidade_" . ucfirst($monthName) . "_" . date('Y') . ".xlsx"
        );
    }
}
