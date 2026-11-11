<?php

namespace App\Http\Controllers;

use App\Exports\BusinessExport;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\BankTransfer;
use App\Services\BusinessAccessService;
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
        if ($isBusiness) {
            app(BusinessAccessService::class)->assert('export_reports', $user, $workspace);
        }
        $companyFlag = $isBusiness;

        $expenses = $request->query('expenses') === '1'
            ? $workspace->expenses()
                ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id))
                ->where('is_company', $companyFlag)
                ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
                ->with(['category', 'supplier'])
                ->latest('spent_at')
                ->get()
            : collect();

        $incomes = $request->query('incomes') === '1'
            ? $workspace->incomes()
                ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id))
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
        if ($isBusiness) {
            app(BusinessAccessService::class)->assert('export_reports', $user, $workspace);
        }
        $expenses = $workspace->expenses()
            ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id))
            ->where('is_company', $isBusiness)
            ->with('category')
            ->latest('spent_at')
            ->get();

        return Pdf::loadView('pdf.expenses', compact('expenses'))->download('despesas_pessoais.pdf');
    }

    public function movementsPdf()
    {
        [$user, $workspace] = $this->currentUserAndWorkspace();
        $isBusiness = in_array($workspace->type, ['business', 'company'], true);

        if ($isBusiness) {
            app(BusinessAccessService::class)->assert('export_reports', $user, $workspace);
        }

        $scope = fn ($query) => $query->where('workspace_id', $workspace->id)
            ->when(! $isBusiness, fn ($q) => $q->where('user_id', $user->id));

        $rows = collect();

        $scope(Expense::query()->with(['category', 'supplier']))->get()->each(function ($item) use ($rows) {
            $rows->push([
                'date' => $item->spent_at,
                'type' => 'Despesa',
                'description' => $item->title ?: $item->description ?: 'Despesa',
                'detail' => $item->category?->name ?: 'Sem categoria',
                'amount' => (float) $item->amount,
                'direction' => 'Saída',
                'status' => 'Completo',
            ]);
        });

        $scope(Income::query()->with('category'))
            ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
            ->get()->each(function ($item) use ($rows) {
                $rows->push([
                    'date' => $item->received_at,
                    'type' => 'Receita',
                    'description' => $item->description ?: 'Receita',
                    'detail' => $item->category?->name ?: ($item->source ?: 'Receita'),
                    'amount' => (float) $item->amount,
                    'direction' => 'Entrada',
                    'status' => 'Recebida',
                ]);
            });

        $scope(BankTransfer::query()->with(['fromAccount', 'toAccount']))->get()->each(function ($item) use ($rows) {
            $rows->push([
                'date' => $item->transferred_at,
                'type' => 'Transferência bancária',
                'description' => $item->description ?: ($item->recipient ?: 'Transferência bancária'),
                'detail' => 'Transferências bancárias',
                'amount' => (float) $item->amount,
                'direction' => ($item->direction ?? 'out') === 'in' ? 'Entrada' : 'Saída',
                'status' => in_array(mb_strtolower(trim((string) $item->status)), ['completed', 'complete', 'completo'], true) ? 'Completo' : ($item->status ?: 'Completo'),
            ]);
        });

        $scope(Subscription::query())->get()->each(function ($item) use ($rows) {
            $rows->push([
                'date' => $item->renewal_date ?: $item->started_at,
                'type' => 'Assinatura',
                'description' => $item->name ?: 'Assinatura',
                'detail' => 'Periodicidade: '.($item->cycle ?: 'Recorrente'),
                'amount' => (float) $item->amount,
                'direction' => $item->paid_by_company ? 'Pago pela empresa' : 'Compromisso',
                'status' => $item->is_active ? 'Ativa' : 'Inativa',
            ]);
        });

        $scope(SubscriptionPayment::query()->with('subscription'))->get()->each(function ($payment) use ($rows) {
            $rows->push([
                'date' => $payment->paid_at,
                'type' => 'Mensalidade',
                'description' => $payment->subscription?->name ?: 'Assinatura',
                'detail' => 'Mensalidade paga pela empresa',
                'amount' => (float) $payment->amount,
                'direction' => 'Pago pela empresa',
                'status' => 'Pago',
            ]);
        });

        $rows = $rows->sortByDesc(fn ($row) => $row['date'] ? Carbon::parse($row['date'])->timestamp : 0)->values();

        return Pdf::loadView('pdf.movements', [
            'workspaceName' => $workspace->name,
            'rows' => $rows,
            'total' => $rows->count(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape')
            ->download('Todos_os_Movimentos_'.now()->format('Ymd_His').'.pdf');
    }

    public function businessExport(Request $request)
    {
        [$user, $workspace] = $this->businessUserAndWorkspace();
        app(BusinessAccessService::class)->assert('export_reports', $user, $workspace);
        $monthNumber = (int) $request->get('month', now()->month);
        abort_unless($monthNumber >= 1 && $monthNumber <= 12, 422, 'Mês inválido.');
        $date = Carbon::create(now()->year, $monthNumber, 1);

        return Excel::download(
            new BusinessExport($user, $monthNumber, $date->year),
            'Contabilidade_'.$date->translatedFormat('F').'_'.$date->year.'.xlsx'
        );
    }
}
