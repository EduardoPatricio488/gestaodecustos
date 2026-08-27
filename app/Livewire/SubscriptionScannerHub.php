<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Subscription;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubscriptionScannerHub extends Component
{
    public int $lookbackMonths = 8;

    public float $amountTolerancePct = 15.0;

    private function normalizeMerchant(string $text): string
    {
        $clean = mb_strtolower($text);
        $clean = preg_replace('/\d+/', ' ', $clean);
        $clean = preg_replace('/[^\p{L}\s]/u', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        $stop = ['compra', 'debito', 'pagamento', 'cartao', 'visa', 'mastercard', 'mbway', 'transferencia'];
        $parts = array_filter(explode(' ', $clean), fn ($p) => ! in_array($p, $stop, true) && mb_strlen($p) > 2);

        return implode(' ', array_slice($parts, 0, 4));
    }

    private function buildSuggestions(): array
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $start = now()->copy()->subMonths($this->lookbackMonths)->startOfMonth();

        $expenses = Expense::where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', $start)
            ->whereNotNull('description')
            ->get();

        $groups = [];

        foreach ($expenses as $exp) {
            $merchant = $this->normalizeMerchant((string) $exp->description);
            if ($merchant === '' || mb_strlen($merchant) < 3) {
                continue;
            }

            $groups[$merchant][] = $exp;
        }

        $subs = Subscription::where('workspace_id', $workspaceId)->get();
        $suggestions = [];

        foreach ($groups as $merchant => $items) {
            if (count($items) < 3) {
                continue;
            }

            $months = collect($items)->map(fn ($e) => Carbon::parse($e->spent_at)->format('Y-m'))->unique();
            if ($months->count() < 3) {
                continue;
            }

            $amounts = collect($items)->map(fn ($e) => (float) $e->amount)->sort()->values();
            $avg = (float) $amounts->avg();
            if ($avg <= 0) {
                continue;
            }

            $stdDev = 0.0;
            $count = max(1, $amounts->count());
            foreach ($amounts as $v) {
                $stdDev += pow($v - $avg, 2);
            }
            $stdDev = sqrt($stdDev / $count);
            $cv = $avg > 0 ? $stdDev / $avg : 999;

            if ($cv > 0.25) {
                continue;
            }

            $sortedByDate = collect($items)->sortBy('spent_at')->values();
            $intervals = [];
            for ($i = 1; $i < $sortedByDate->count(); $i++) {
                $prev = Carbon::parse($sortedByDate[$i - 1]->spent_at);
                $curr = Carbon::parse($sortedByDate[$i]->spent_at);
                $intervals[] = $prev->diffInDays($curr);
            }

            $medianInterval = 30;
            if (! empty($intervals)) {
                sort($intervals);
                $n = count($intervals);
                $mid = intdiv($n, 2);
                $medianInterval = $n % 2 === 0 ? (int) round(($intervals[$mid - 1] + $intervals[$mid]) / 2) : (int) $intervals[$mid];
            }

            if ($medianInterval < 20 || $medianInterval > 40) {
                continue;
            }

            $dayValues = $sortedByDate->map(fn ($e) => (int) Carbon::parse($e->spent_at)->day)->sort()->values();
            $day = (int) $dayValues[intdiv(max(0, $dayValues->count() - 1), 2)];

            $alreadyExists = $subs->contains(function ($sub) use ($merchant, $avg) {
                $subName = $this->normalizeMerchant((string) $sub->name);
                $nameMatch = str_contains($subName, $merchant) || str_contains($merchant, $subName);
                $amountMatch = abs((float) $sub->amount - $avg) <= ($avg * 0.2);

                return $nameMatch && $amountMatch;
            });

            if ($alreadyExists) {
                continue;
            }

            $signature = sha1($merchant.'|'.number_format($avg, 2, '.', '').'|'.$day);

            $suggestions[] = [
                'signature' => $signature,
                'merchant' => ucfirst($merchant),
                'avg_amount' => $avg,
                'billing_day' => max(1, min(31, $day)),
                'occurrences' => count($items),
                'months' => $months->count(),
                'confidence' => max(40, min(97, (int) round((1 - min($cv, 0.3)) * 100))),
                'last_seen' => Carbon::parse($sortedByDate->last()->spent_at),
                'category_id' => $sortedByDate->groupBy('category_id')->sortByDesc(fn ($g) => $g->count())->keys()->first(),
            ];
        }

        usort($suggestions, fn ($a, $b) => $b['confidence'] <=> $a['confidence']);

        return $suggestions;
    }

    public function createSubscription(string $signature): void
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $userId = auth()->id();

        $suggestion = collect($this->buildSuggestions())->firstWhere('signature', $signature);
        if (! $suggestion) {
            $this->dispatch('toast', variant: 'warning', text: 'Sugestão já não está disponível.');

            return;
        }

        $categoryId = $suggestion['category_id'];
        if (! $categoryId) {
            $fallback = Category::firstOrCreate(
                ['workspace_id' => $workspaceId, 'name' => 'Outros'],
                ['user_id' => $userId, 'icon' => 'credit-card', 'color' => '#6366f1']
            );
            $categoryId = $fallback->id;
        }

        Subscription::create([
            'user_id' => $userId,
            'workspace_id' => $workspaceId,
            'category_id' => $categoryId,
            'name' => $suggestion['merchant'],
            'amount' => round($suggestion['avg_amount'], 2),
            'billing_day' => $suggestion['billing_day'],
            'cycle' => 'monthly',
            'status' => 'active',
            'is_active' => true,
            'notes' => 'Criado automaticamente pelo Scanner de Subscrições.',
        ]);

        $this->dispatch('toast', variant: 'success', text: 'Assinatura criada automaticamente!');
    }

    public function render()
    {
        $suggestions = collect($this->buildSuggestions());

        return view('livewire.subscription-scanner-hub', [
            'suggestions' => $suggestions,
            'totalDetected' => $suggestions->count(),
            'totalEstimatedMonthly' => (float) $suggestions->sum('avg_amount'),
        ]);
    }
}
