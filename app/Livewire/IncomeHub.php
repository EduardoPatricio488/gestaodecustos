<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Support\Facades\Auth;
use App\Models\Workspace; // <--- FALTA ISTO
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class IncomeHub extends Component
{
    // Receita Extra
    public $description = '';
    public $amount = '';
    public bool $showExtraModal = false;

    public $received_at = '';
    public bool $showRaiseModal = false;
public $raiseValue = 0;
public string $raiseMode = 'total'; // 'total' ou 'addition'
public $selectedFixedId;
    public $type = 'Extra';
    public $source = 'emprego';
    public $frequency = 'pontual';
    public $tax_estimate = '';
    public $notes = '';

    // Receita Fixa
    public $recWorkspaceId = '';
    public $recDescription = '';
    public $recAmount = '';
    public $recDay = '';
    public $recSource = 'emprego';
    public $recFrequency = 'mensal';
    public $recTaxEstimate = '';
    public $recNotes = '';

    // Edição de rendimento fixo
    public ?int $editingFixedId = null;

    public function mount()
    {
        $this->received_at = now()->format('Y-m-d');
    }

    public function saveExtra()
{
    if (auth()->user()->isViewer()) {
        $this->dispatch('toast', variant: 'error', text: 'Permissão negada.');
        return;
    }
        $this->validate([
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'received_at'  => 'required|date',
            'source'       => 'required|in:emprego,freelance,investimento,outro',
            'frequency'    => 'required|in:pontual,semanal,mensal,anual',
            'tax_estimate' => 'nullable|numeric|min:0|max:100',
            'notes'        => 'nullable|string|max:500',
        ]);

        Income::create([
            'user_id'      => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'description'  => $this->description,
            'amount'       => $this->amount,
            'received_at'  => $this->received_at,
            'type'         => 'Extra',
            'source'       => $this->source,
            'frequency'    => $this->frequency,
            'tax_estimate' => $this->tax_estimate ?: null,
            'notes'        => $this->notes ?: null,
        ]);

        $this->reset(['description', 'amount', 'tax_estimate', 'notes']);
        $this->received_at = now()->format('Y-m-d');
        $this->source = 'emprego';
        $this->frequency = 'pontual';
        $this->showExtraModal = false;
        $this->dispatch('modal-close-receita-extra');
        $this->dispatch('toast', text: 'Receita extra registada!');
    }

// Método para abrir o modal
public function openRaiseModal($id)
{
    $this->selectedFixedId = $id;
    $this->raiseValue = 0;
    $this->raiseMode = 'total';
    $this->showRaiseModal = true; // Ativa o @if no Blade

    // Dispara o evento com um pequeno delay para o Blade renderizar primeiro
    $this->dispatch('modal-show-upgrade');
}

// Método para aplicar o aumento
public function applyRaise()
{
    $this->validate([
        'raiseValue' => 'required|numeric|min:0.01',
    ]);

    $fixed = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
        ->findOrFail($this->selectedFixedId);

    if ($this->raiseMode === 'addition') {
        // Soma ao valor atual
        $fixed->amount += (float) $this->raiseValue;
    } else {
        // Define como o novo valor total
        $fixed->amount = (float) $this->raiseValue;
    }

    $fixed->save();

    $this->showRaiseModal = false;
    $this->reset(['raiseValue', 'selectedFixedId']);
    $this->dispatch('toast', text: 'Salário atualizado! Parabéns pelo aumento! 🚀');
}

 public function updatedRecWorkspaceId($value)
    {
        if ($value) {
            $ws = Workspace::find($value);
            if ($ws) {
                $this->recDescription = "Salário - " . $ws->name;
                $this->recSource = 'emprego';
                $this->recFrequency = 'mensal';

                $myRecord = Employee::where('workspace_id', $ws->id)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($myRecord) {
                    $this->recAmount = $myRecord->salary;
                    if ($myRecord->pay_day) {
                        $this->recDay = $myRecord->pay_day;
                    }
                }
            }
        } else {
            $this->recDescription = '';
            $this->recAmount = '';
            $this->recDay = '';
            $this->recSource = 'emprego';
            $this->recFrequency = 'mensal';
        }
    }
    public function saveFixed()
    {
        // 1. Alterado: Apenas bloqueia se for estritamente um "viewer" (leitura)
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Não tens permissão para gravar dados.');
            return;
        }

        $this->validate([
            'recDescription'  => 'required|string|max:255',
            'recAmount'       => 'required|numeric|min:0.01',
            'recDay'          => 'required|integer|between:1,31',
            'recSource'       => 'required|in:emprego,freelance,investimento,outro',
            'recFrequency'    => 'required|in:semanal,mensal,anual',
        ]);

        RecurringIncome::create([
            'user_id'      => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'description'  => $this->recDescription,
            'amount'       => $this->recAmount,
            'day_of_month' => $this->recDay,
            'is_active'    => true,
            'source'       => $this->recSource,
            'frequency'    => $this->recFrequency,
            'tax_estimate' => $this->recTaxEstimate ?: null,
            'notes'        => $this->recNotes ?: null,
        ]);

        $this->reset(['recWorkspaceId', 'recDescription', 'recAmount', 'recDay', 'recTaxEstimate', 'recNotes']);
        $this->recSource = 'emprego';
        $this->recFrequency = 'mensal';
        $this->dispatch('modal-close-salario');
        $this->dispatch('toast', text: 'Rendimento fixo configurado!');
    }
public function openExtraModal()
{
    $this->reset(['description', 'amount', 'tax_estimate', 'notes']);
    $this->received_at = now()->format('Y-m-d');
    $this->source = 'emprego';
    $this->frequency = 'pontual';

    // 1. Primeiro ativamos a renderização do modal no Blade
    $this->showExtraModal = true;

    // 2. Disparamos o evento para o Alpine.js abrir a animação
    // Usamos um pequeno delay interno ou apenas o dispatch
    $this->dispatch('modal-show-receita-extra');
}
public function closeExtraModal()
{
    $this->showExtraModal = false;
}
public function closeRaiseModal()
{
    $this->showRaiseModal = false;
}
    public function editFixed($id)
{
    $record = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
        ->findOrFail($id);

    $this->editingFixedId  = $record->id;
    $this->recWorkspaceId  = '';
    $this->recDescription  = $record->description;
    $this->recAmount       = $record->amount;
    $this->recDay          = $record->day_of_month;
    $this->recSource       = $record->source ?? 'emprego';
    $this->recFrequency    = $record->frequency ?? 'mensal';
    $this->recTaxEstimate  = $record->tax_estimate ?? '';
    $this->recNotes        = $record->notes ?? '';

    // MUDANÇA AQUI: Nome do evento deve ser igual ao que o modal ouve
    $this->dispatch('modal-show-salario');
}

    public function updateFixed()
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada.');
            return;
        }

        $this->validate([
            'recDescription'  => 'required|string|max:255',
            'recAmount'       => 'required|numeric|min:0.01',
            'recDay'          => 'required|integer|between:1,31',
        ]);

        RecurringIncome::where('id', $this->editingFixedId)
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->update([
                'description'  => $this->recDescription,
                'amount'       => $this->recAmount,
                'day_of_month' => $this->recDay,
                'source'       => $this->recSource,
                'frequency'    => $this->recFrequency,
                'tax_estimate' => $this->recTaxEstimate ?: null,
                'notes'        => $this->recNotes ?: null,
            ]);

        $this->editingFixedId = null;
        $this->reset(['recWorkspaceId', 'recDescription', 'recAmount', 'recDay', 'recTaxEstimate', 'recNotes']);
        $this->recSource = 'emprego';
        $this->recFrequency = 'mensal';
        $this->dispatch('modal-close-salario');
        $this->dispatch('toast', text: 'Salário atualizado!');
    }

    public function deleteFixed($id)
    {
        if (!auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas o administrador pode apagar rendimentos.');
            return;
        }
        RecurringIncome::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Registo removido.');
    }

    public function deleteExtra($id)
    {
        if (!auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Não tens permissão para apagar este registo.');
            return;
        }
        Income::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Receita removida.');
    }

  public function render()
{
    $user = auth()->user();
    $workspaceId = $user->current_workspace_id;

    // --- BUSCAR APENAS EMPRESAS ONDE O UTILIZADOR É COLABORADOR (Exclui Admin e Pessoal) ---
    $collabBusinesses = $user->workspaces()
        ->where('type', '!=', 'personal')
        ->wherePivot('role', '!=', 'admin')
        ->get();

    $fixedIncomes = RecurringIncome::where('workspace_id', $workspaceId)->get();

    $extraIncomes = Income::where('workspace_id', $workspaceId)
        ->whereMonth('received_at', now()->month)
        ->whereYear('received_at', now()->year)
        ->latest()
        ->get();

    $totalMonthly = $fixedIncomes->sum('amount') + $extraIncomes->sum('amount');

    // Estatísticas
    $allIncomes = Income::where('workspace_id', $workspaceId)->get();

    // Salário fixo mensal (soma dos rendimentos recorrentes ativos)
    $monthlySalary = (float) RecurringIncome::where('workspace_id', $workspaceId)
        ->where('is_active', true)
        ->sum('amount');

    // Média mensal (últimos 6 meses) — inclui salário fixo
    $monthlyTotals = collect();
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $monthExtra = (float) Income::where('workspace_id', $workspaceId)
            ->whereMonth('received_at', $date->month)
            ->whereYear('received_at', $date->year)
            ->sum('amount');
        $monthlyTotals->push([
            'label' => $date->translatedFormat('M'),
            'total' => $monthExtra + $monthlySalary,
        ]);
    }

    $avgMonthly = $monthlyTotals->avg('total');
    $bestMonth = $monthlyTotals->sortByDesc('total')->first();

    // Total anual
    $monthsElapsed = min(now()->month, 12);
    $totalYear = (float) Income::where('workspace_id', $workspaceId)
        ->whereYear('received_at', now()->year)
        ->sum('amount') + ($monthlySalary * $monthsElapsed);

    // Breakdown por fonte
    $bySource = $allIncomes->groupBy('source')->map->sum('amount');

    // Imposto estimado
    $taxEstimated = $extraIncomes->sum(function ($i) {
        return $i->tax_estimate > 0 ? ($i->amount * $i->tax_estimate / 100) : 0;
    });

    return view('livewire.income-hub', [
        'collabBusinesses' => $collabBusinesses, // <--- ENVIADO PARA O BLADE
        'fixedIncomes'     => $fixedIncomes,
        'extraIncomes'     => $extraIncomes,
        'totalMonthly'     => $totalMonthly,
        'avgMonthly'       => $avgMonthly,
        'bestMonth'        => $bestMonth,
        'totalYear'        => $totalYear,
        'monthlyTotals'    => $monthlyTotals,
        'bySource'         => $bySource,
        'taxEstimated'     => $taxEstimated,
        'isOwner'          => $user->isOwner(),
        'canManage'        => !$user->isViewer(),
    ]);
}
}
