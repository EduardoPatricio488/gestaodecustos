<?php

namespace App\Http\Controllers;

use App\Exports\BusinessExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private function currentUserAndWorkspace(): array
    {
        $user = auth()->user();
        abort_unless($user && $user->current_workspace_id, 403);

        $workspace = $user->workspaces()->whereKey($user->current_workspace_id)->first();
        abort_unless($workspace, 403);

        return [$user, $workspace];
    }

    private function businessUserAndWorkspace(): array
    {
        [$user, $workspace] = $this->currentUserAndWorkspace();
        abort_unless(in_array($workspace->type, ['business', 'company'], true), 403);

        return [$user, $workspace];
    }

    public function dashboardPdf(Request $request)
    {
        [$user, $workspace] = $this->currentUserAndWorkspace();

        try {
            $start = Carbon::parse($request->query('start', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
            $end = Carbon::parse($request->query('end', now()->endOfMonth()->format('Y-m-d')))->endOfDay();
        } catch (\Throwable) {
            abort(422, 'Período de exportação inválido.');
        }

        abort_if($end->lt($start) || $start->diffInDays($end) > 366, 422, 'Período de exportação inválido.');

        $isBusiness = in_array($workspace->type, ['business', 'company'], true);
        $companyFlag = $isBusiness;

        $expenses = $request->query('expenses') === '1'
            ? $workspace->expenses()
                ->where('is_company', $companyFlag)
                ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
                ->with(['category', 'supplier'])
                ->latest('spent_at')
                ->get()
            : collect();

        $incomes = $request->query('incomes') === '1'
            ? $workspace->incomes()
                ->whereBetween('received_at', [$start->toDateString(), $end->toDateString()])
                ->latest('received_at')
                ->get()
            : collect();

        $data = [
            'workspaceName' => $workspace->name,
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y'),
            'expenses' => $expenses,
            'incomes' => $incomes,
            'totalExpenses' => round((float) $expenses->sum('amount'), 2),
            'totalIncomes' => round((float) $incomes->sum('amount'), 2),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        return Pdf::loadView('pdf.financial-report', $data)
            ->download('Relatorio_Financeiro_'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
    }

    public function expensesPdf()
    {
        [$user, $workspace] = $this->currentUserAndWorkspace();

        $isBusiness = in_array($workspace->type, ['business', 'company'], true);
        $expenses = $workspace->expenses()
            ->where('is_company', $isBusiness)
            ->with('category')
            ->latest('spent_at')
            ->get();

        return Pdf::loadView('pdf.expenses', compact('expenses'))->download('despesas_pessoais.pdf');
    }

    public function businessExport(Request $request)
    {
        [$user, $workspace] = $this->businessUserAndWorkspace();
        $monthNumber = (int) $request->get('month', now()->month);
        abort_unless($monthNumber >= 1 && $monthNumber <= 12, 422, 'Mês inválido.');
        $date = Carbon::create(now()->year, $monthNumber, 1);

        return Excel::download(
            new BusinessExport($user, $monthNumber, $date->year),
            'Contabilidade_'.$date->translatedFormat('F').'_'.$date->year.'.xlsx'
        );
    }
}
