<?php

namespace App\Http\Controllers;

use App\Exports\BusinessExport;
use App\Models\Expense;
use App\Models\Income;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private function businessUser()
    {
        $user = auth()->user();
        abort_unless($user && $user->current_workspace_id, 403);
        abort_unless($user->workspaces()->whereKey($user->current_workspace_id)->exists(), 403);
        return $user;
    }

    public function dashboardPdf(Request $request)
    {
        $user = $this->businessUser();
        $workspace = $user->currentWorkspace;
        abort_unless($workspace && $workspace->type !== 'personal', 403);

        $start = Carbon::parse($request->query('start', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $end = Carbon::parse($request->query('end', now()->endOfMonth()->format('Y-m-d')))->endOfDay();
        abort_if($end->lt($start) || $start->diffInDays($end) > 366, 422, 'Período de exportação inválido.');

        $expenses = $request->query('expenses') === '1'
            ? $workspace->expenses()->where('is_company', true)->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])->with(['category', 'supplier'])->latest('spent_at')->get()
            : collect();
        $incomes = $request->query('incomes') === '1'
            ? $workspace->incomes()->whereBetween('received_at', [$start->toDateString(), $end->toDateString()])->latest('received_at')->get()
            : collect();

        $data = [
            'workspaceName' => $workspace->name,
            'start' => $start->format('d/m/Y'), 'end' => $end->format('d/m/Y'),
            'expenses' => $expenses, 'incomes' => $incomes,
            'totalExpenses' => round((float) $expenses->sum('amount'), 2),
            'totalIncomes' => round((float) $incomes->sum('amount'), 2),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        return Pdf::loadView('pdf.financial-report', $data)
            ->download('Relatorio_Financeiro_'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
    }

    public function expensesPdf()
    {
        $user = auth()->user();
        $expenses = $user->expenses()->with('category')->latest()->get();
        return Pdf::loadView('pdf.expenses', compact('expenses'))->download('despesas_pessoais.pdf');
    }

    public function businessExport(Request $request)
    {
        $user = $this->businessUser();
        abort_unless($user->currentWorkspace && $user->currentWorkspace->type !== 'personal', 403);
        $monthNumber = (int) $request->get('month', now()->month);
        abort_unless($monthNumber >= 1 && $monthNumber <= 12, 422, 'Mês inválido.');
        $date = Carbon::create(now()->year, $monthNumber, 1);
        return Excel::download(new BusinessExport($user, $monthNumber, $date->year), 'Contabilidade_'.$date->translatedFormat('F').'_'.$date->year.'.xlsx');
    }
}
