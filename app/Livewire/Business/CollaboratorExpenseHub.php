<?php

namespace App\Livewire\Business;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Task;
use App\Services\BusinessAccessService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class CollaboratorExpenseHub extends Component
{
    use WithFileUploads, WithPagination;

    public $editingId = null;

    public $amount;

    public $description;

    public $project_id;

    public $task_id;

    public $spent_at;

    public $category_id;

    public $receipt;

    public $existingReceiptPath;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|min:3|max:255',
        'spent_at' => 'required|date',
        'category_id' => 'required|integer',
        'project_id' => 'nullable|integer',
        'task_id' => 'nullable|integer',
        'receipt' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->amount = '';
        $this->description = '';
        $this->spent_at = now()->format('Y-m-d');
        $this->project_id = null;
        $this->task_id = null;
        $this->receipt = null;
        $this->existingReceiptPath = null;
        $defaultCat = Category::where('workspace_id', auth()->user()->current_workspace_id)->first();
        $this->category_id = $defaultCat ? $defaultCat->id : null;
    }

    public function edit($id)
    {
        app(BusinessAccessService::class)->assert('create_expense');
        $expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereRaw('LOWER(status) = ?', ['pendente'])
            ->findOrFail($id);

        $this->editingId = $expense->id;
        $this->amount = $expense->amount;
        $this->description = $expense->description;
        $this->spent_at = Carbon::parse($expense->spent_at)->format('Y-m-d');
        $this->category_id = $expense->category_id;
        $this->project_id = $expense->project_id;
        $this->task_id = $expense->task_id;
        $this->existingReceiptPath = $expense->receipt_path;

        $this->dispatch('modal-show', name: 'expense-modal');
    }

    public function save()
    {
        app(BusinessAccessService::class)->assert('create_expense');
        $this->validate();

        $workspaceId = auth()->user()->current_workspace_id;
        abort_unless($workspaceId, 403);

        abort_unless(Category::whereKey($this->category_id)->where('workspace_id', $workspaceId)->exists(), 422);
        if ($this->project_id) {
            abort_unless(Project::whereKey($this->project_id)->where('workspace_id', $workspaceId)->exists(), 422);
        }
        if ($this->task_id) {
            abort_unless(Task::whereKey($this->task_id)->where('workspace_id', $workspaceId)->exists(), 422);
        }

        $data = [
            'workspace_id' => $workspaceId,
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'project_id' => $this->project_id ?: null, // Grava ID do Projeto
            'task_id' => $this->task_id ?: null,       // Grava ID da Tarefa
            'spent_at' => $this->spent_at,
            'is_company' => true,
            'status' => 'pendente',
        ];

        if ($this->receipt) {
            $data['receipt_path'] = $this->receipt->store('receipts', 'local');
        }

        if ($this->editingId) {
            $expense = Expense::whereKey($this->editingId)
                ->where('workspace_id', $workspaceId)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            $expense->update($data);
        } else {
            Expense::create($data);
        }

        $this->dispatch('modal-close', name: 'expense-modal');
        $this->dispatch('toast', text: $this->editingId ? 'Gasto atualizado!' : 'Gasto submetido!');
        $this->resetForm();
    }

    public function downloadReceipt($id)
    {
        app(BusinessAccessService::class)->assert('create_expense');
        $expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        abort_unless($expense->receipt_path && Storage::disk('local')->exists($expense->receipt_path), 404);

        return Storage::disk('local')->download($expense->receipt_path, basename($expense->receipt_path));
    }

    public function delete($id)
    {
        app(BusinessAccessService::class)->assert('create_expense');
        $expense = Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereRaw('LOWER(status) = ?', ['pendente'])
            ->findOrFail($id);
        if ($expense->receipt_path) {
            Storage::disk('local')->delete($expense->receipt_path);
        }
        $expense->delete();

        $this->dispatch('toast', text: 'Registo removido.', variant: 'warning');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        // Importante: .with(['project', 'task']) para a tabela não dar erro
        $query = Expense::where('workspace_id', $workspace->id)
            ->where('user_id', auth()->id())
            ->where('is_company', true)
            ->with(['project', 'task', 'category']);

        return view('livewire.business.collaborator-expense-hub', [
            'expenses' => $query->latest('spent_at')->paginate(10),
            'projects' => $workspace->projects, // Lista de projetos do workspace
            'tasks' => Task::where('workspace_id', $workspace->id)->get(), // Todas as tarefas da empresa
            'categories' => Category::where('workspace_id', $workspace->id)->get(),
            'stats' => [
                'total_pending' => Expense::where('workspace_id', $workspace->id)->where('user_id', auth()->id())->whereRaw('LOWER(status) = ?', ['pendente'])->sum('amount'),
                'total_approved' => Expense::where('workspace_id', $workspace->id)->where('user_id', auth()->id())->whereRaw('LOWER(status) = ?', ['aprovado'])->sum('amount'),
            ],
        ]);
    }
}
