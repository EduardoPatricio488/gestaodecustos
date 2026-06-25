<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BusinessExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\Income;

class ExportController extends Controller
{
    /**
     * MÉTODO ADICIONADO: Exportação de Auditoria do Dashboard
     */
    public function dashboardPdf(Request $request)
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        // 1. Capturar filtros da URL
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->query('end', now()->endOfMonth()->format('Y-m-d'));
        $includeExpenses = $request->query('expenses') === '1';
        $includeIncomes = $request->query('incomes') === '1';

        // 2. Procurar Dados
        $expenses = collect();
        $incomes = collect();

        if ($includeExpenses) {
            $expenses = Expense::where('workspace_id', $workspaceId)
                ->whereBetween('spent_at', [$start, $end])
                ->with('category')
                ->latest('spent_at')
                ->get();
        }

        if ($includeIncomes) {
            $incomes = Income::where('workspace_id', $workspaceId)
                ->whereBetween('received_at', [$start, $end]) // Ajusta se a coluna for 'date'
                ->latest('received_at')
                ->get();
        }

        // 3. Preparar dados para a view
        $data = [
            'workspaceName' => $user->currentWorkspace->name,
            'start' => Carbon::parse($start)->format('d/m/Y'),
            'end' => Carbon::parse($end)->format('d/m/Y'),
            'expenses' => $expenses,
            'incomes' => $incomes,
            'totalExpenses' => $expenses->sum('amount'),
            'totalIncomes' => $incomes->sum('amount'),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        // 4. Gerar PDF (Cria este ficheiro em resources/views/pdf/financial-report.blade.php)
        $pdf = Pdf::loadView('pdf.financial-report', $data);

        return $pdf->download('Relatorio_Financeiro_' . now()->format('dmY_Hi') . '.pdf');
    }

    /**
     * Métodos que já tinhas (Mantidos)
     */
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
        $monthNumber = (int) $request->get('month', date('n'));
        $date = Carbon::create(date('Y'), $monthNumber, 1);
        $monthName = $date->translatedFormat('F');

        return Excel::download(
            new BusinessExport($user, $monthNumber),
            "Contabilidade_" . ucfirst($monthName) . "_" . date('Y') . ".xlsx"
        );
    }
}
