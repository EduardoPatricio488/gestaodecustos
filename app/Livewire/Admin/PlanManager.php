<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use App\Models\Income;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PlanManager extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $viewingPlanId = null;

    public string $name = '';

    public string $color = '#10b981';

    public string $slug = '';

    public $price = 0;

    public ?string $description = '';

    public ?string $stripe_price_id = '';

    public bool $is_active = true;

    public array $features = [];

    public array $availableFeatures = SubscriptionPlan::FEATURE_LABELS;

    public function startCreate(): void
    {
        $this->resetFields();
        $this->showForm = true;
        $this->dispatch('scroll-to-top');
    }

    public function edit(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $this->editingId = $plan->id;
        $this->color = $plan->color ?? '#10b981';
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->price = $plan->price;
        $this->stripe_price_id = $plan->stripe_price_id ?? '';
        $this->description = $plan->description ?? '';
        $this->features = $plan->featureKeys();
        $this->is_active = (bool) $plan->is_active;
        $this->showForm = true;
        $this->dispatch('scroll-to-top');
    }

    public function updatedName($value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug((string) $value);
        }
    }

    public function save(): void
    {
        $this->features = array_values(array_filter((array) $this->features));
        $this->slug = Str::slug($this->slug ?: $this->name);

        $stripeId = trim((string) $this->stripe_price_id);
        if ($stripeId !== '' && ! str_starts_with($stripeId, 'price_')) {
            $fromNamedEnv = env($stripeId);
            $fromSlugEnv = env('STRIPE_PRICE_'.strtoupper(str_replace('-', '_', $this->slug)));
            $stripeId = $fromNamedEnv ?: $fromSlugEnv ?: $stripeId;
        } elseif ($stripeId === '') {
            $stripeId = env('STRIPE_PRICE_'.strtoupper(str_replace('-', '_', $this->slug))) ?: '';
        }
        $this->stripe_price_id = $stripeId;

        $this->validate([
            'name' => 'required|min:2|max:80',
            'slug' => [
                'required',
                'min:2',
                'max:80',
                Rule::unique('subscription_plans', 'slug')->ignore($this->editingId),
            ],
            'price' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'stripe_price_id' => $this->price > 0 ? 'required|string|max:120' : 'nullable|string|max:120',
            'description' => 'nullable|string|max:2000',
            'features' => 'array',
        ]);

        $payload = [
            'name' => $this->name,
            'slug' => $this->slug,
            // 🔥 FIX: Forçamos o preço a ser um número decimal para o MySQL não o zerar
            'price' => (float) $this->price,
            'color' => $this->color ?? '#10b981', // 🔥 NOVO: Grava a cor escolhida
            'description' => $this->description,
            'features' => $this->features,
            'stripe_price_id' => $this->stripe_price_id ?: null,
            'is_active' => $this->is_active ?? true,
        ];

        if ($this->editingId) {
            $plan = SubscriptionPlan::findOrFail($this->editingId);
            $plan->update($payload);
        } else {
            SubscriptionPlan::create($payload);
        }

        $this->resetFields();
        $this->dispatch('toast', text: 'Plano gravado com sucesso.');
    }

    public function toggleActive(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->update(['is_active' => ! $plan->is_active]);
        $this->dispatch('toast', text: $plan->is_active ? 'Plano publicado na loja.' : 'Plano ocultado da loja.');
    }

    public function viewDossier(int $id): void
    {
        $this->viewingPlanId = $id;
        $this->showForm = false;
    }

    public function closeDossier(): void
    {
        $this->viewingPlanId = null;
    }

    public function delete(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $subscribers = User::where('plan', $plan->slug)->count();

        if ($subscribers > 0) {
            $this->dispatch('toast', variant: 'warning', text: "Não podes eliminar: {$subscribers} utilizador(es) ainda estão neste plano. Oculta-o em vez de apagar.");

            return;
        }

        $plan->delete();
        if ($this->editingId === $id) {
            $this->resetFields();
        }
        $this->dispatch('toast', text: 'Plano removido.', variant: 'warning');
    }

    public function resetFields(): void
    {
        $this->reset(['editingId', 'name', 'slug', 'price', 'description', 'features', 'stripe_price_id', 'showForm']);
        $this->price = 0;
        $this->is_active = true;
        $this->features = [];
        $this->color = '#10b981';
        $this->resetValidation();
    }

    public function render()
    {
        $viewingPlan = null;
        $subscribers = collect();
        $stats = [
            'total_revenue' => 0,
            'yearly_projection' => 0,
            'new_users_30d' => 0,
            'avg_level' => 0,
            'activity_volume' => 0,
        ];

        if ($this->viewingPlanId) {
            $viewingPlan = SubscriptionPlan::find($this->viewingPlanId);
            if ($viewingPlan) {
                $subscribers = User::where('plan', $viewingPlan->slug)->get();
                $userIds = $subscribers->pluck('id');

                $monthlyRevenue = $subscribers->count() * $viewingPlan->price;

                $stats = [
                    'total_revenue' => $monthlyRevenue,
                    'yearly_projection' => $monthlyRevenue * 12,
                    'new_users_30d' => $subscribers->where('created_at', '>=', now()->subDays(30))->count(),
                    'avg_level' => round($subscribers->avg('level') ?? 1, 1),
                    'activity_volume' => ($userIds->isEmpty() ? 0 : Expense::whereIn('user_id', $userIds)->whereMonth('spent_at', now()->month)->count())
                        + ($userIds->isEmpty() ? 0 : Income::whereIn('user_id', $userIds)->whereMonth('received_at', now()->month)->count()),
                ];
            }
        }

        $plans = SubscriptionPlan::orderBy('price')->get()->map(function (SubscriptionPlan $plan) {
            $plan->subscribers_count = $plan->subscriberCount();

            return $plan;
        });

        return view('livewire.admin.plan-manager', [
            'plans' => $plans,
            'viewingPlan' => $viewingPlan,
            'subscribers' => $subscribers,
            'extraStats' => $stats,
        ]);
    }
}
