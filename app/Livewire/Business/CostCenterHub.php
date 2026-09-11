<?php

namespace App\Livewire\Business;

use App\Models\CostCenter;
use App\Services\BusinessAccessService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CostCenterHub extends Component
{
    public string $code = '';

    public string $name = '';

    public string $description = '';

    public function mount(): void
    {
        app(BusinessAccessService::class)->assert('manage_financials');
    }

    public function save(): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_financials', auth()->user(), $workspace);

        $code = strtoupper(trim($this->code));

        $this->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cost_centers', 'code')->where(fn ($query) => $query->where('workspace_id', $workspace->id)),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'code.unique' => 'Já existe um centro de custo com este código neste workspace.',
        ]);

        CostCenter::create([
            'workspace_id' => $workspace->id,
            'user_id' => auth()->id(),
            'code' => $code,
            'name' => trim($this->name),
            'description' => trim($this->description),
            'is_active' => true,
        ]);

        $this->reset('code', 'name', 'description');
        $this->dispatch('toast', text: 'Centro de custo criado.', variant: 'success');
    }

    public function toggle(int $id): void
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();
        app(BusinessAccessService::class)->assert('manage_financials', auth()->user(), $workspace);

        $center = CostCenter::where('workspace_id', $workspace->id)->findOrFail($id);
        $center->update(['is_active' => ! $center->is_active]);
    }

    public function render()
    {
        $workspace = app(BusinessAccessService::class)->assertWorkspace();

        return view('livewire.business.cost-center-hub', [
            'centers' => $workspace->costCenters()
                ->withCount(['expenses', 'invoices'])
                ->orderBy('code')
                ->get(),
        ]);
    }
}
