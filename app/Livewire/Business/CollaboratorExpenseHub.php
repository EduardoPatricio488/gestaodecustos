<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Task;
use App\Models\Category;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CollaboratorExpenseHub extends Component
{
    use WithFileUploads, WithPagination;

    public $editingId = null;
    public $amount, $description, $project_id, $task_id, $spent_at, $category_id;
    public $receipt;
    public $existingReceiptPath;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|min:3|max:255',
        'spent_at' => 'required|date',
        'category_id' => 'required|exists:categories,id',
        'project_id' => 'nullable|exists:projects,id',
        'task_id' => 'nullable|exists:tasks,id',
        'receipt' => 'nullable|image|max:2048',
    ];

    public function mount() { $this->resetForm(); }

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
        $expense = Expense::where('user_id', auth()->id())
            ->whereRaw('LOWER(status) = ?', ['pendente'])
            ->findOrFail($id);

        $this->editingId = $expense->id;
        $this->amount = $expense->amount;
        $this->description = $expense->description;
        $this->spent_at = \Carbon\Carbon::parse($expense->spent_at)->format('Y-m-d');
        $this->category_id = $expense->category_id;
        $this->project_id = $expense->project_id;
        $this->task_id = $expense->task_id;
        $this->existingReceiptPath = $expense->receipt_path;

        $this->dispatch('modal-show', name: 'expense-modal');
    }

   public function save()
    {
        $this->validate();

        $data = [
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'project_id' => $this->project_id ?: null, // Grava ID do Projeto
            'task_id' => $this->task_id ?: null,       // Grava ID da Tarefa
            'spent_at' => $this->spent_at,
            'is_company' => true,
            'status' => 'pendente'
        ];

        if ($this->receipt) {
            $data['receipt_path'] = $this->receipt->store('receipts', 'public');
        }

        Expense::updateOrCreate(['id' => $this->editingId], $data);

        $this->dispatch('modal-close', name: 'expense-modal');
        $this->dispatch('toast', text: $this->editingId ? 'Gasto atualizado!' : 'Gasto submetido!');
        $this->resetForm();
    }

    public function delete($id)
    {
        $expense = Expense::where('user_id', auth()->id())
            ->whereRaw('LOWER(status) = ?', ['pendente'])
            ->findOrFail($id);
        $expense->delete();
        $this->dispatch('toast', text: 'Registo removido.', variant: 'warning');
    }

     public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        // Importante: .with(['project', 'task']) para a tabela não dar erro
        $query = Expense::where('user_id', auth()->id())
            ->where('is_company', true)
            ->with(['project', 'task', 'category']);

        return view('livewire.business.collaborator-expense-hub', [
            'expenses' => $query->latest('spent_at')->paginate(10),
            'projects' => $workspace->projects, // Lista de projetos do workspace
            'tasks' => \App\Models\Task::where('workspace_id', $workspace->id)->get(), // Todas as tarefas da empresa
            'categories' => \App\Models\Category::where('workspace_id', $workspace->id)->get(),
            'stats' => [
                'total_pending' => Expense::where('user_id', auth()->id())->whereRaw('LOWER(status) = ?', ['pendente'])->sum('amount'),
                'total_approved' => Expense::where('user_id', auth()->id())->whereRaw('LOWER(status) = ?', ['aprovado'])->sum('amount'),
            ]
        ]);
    }
}
