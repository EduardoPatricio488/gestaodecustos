<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class GamificationHub extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedUserId;
    public $pointsToAdd = 0;
    public $badgeToAssign = '';

    // Lista de badges disponíveis (podes expandir isto)
    public $availableBadges = [
        ['id' => 'poupador_pro', 'name' => 'Poupador Pro', 'icon' => '💰'],
        ['id' => 'mestre_ia', 'name' => 'Mestre da IA', 'icon' => '🤖'],
        ['id' => 'investidor_flash', 'name' => 'Investidor Flash', 'icon' => '⚡'],
        ['id' => 'fogo_eterno', 'name' => 'Fogo Eterno', 'icon' => '🔥'],
    ];

    /**
     * Atribui pontos/XP e recalcula o nível
     */
    public function awardPoints()
    {
        $this->validate([
            'pointsToAdd' => 'required|numeric|min:1',
            'selectedUserId' => 'required'
        ]);

        $user = User::findOrFail($this->selectedUserId);

        // 1 ponto = 10 XP
        $xpGain = (int)$this->pointsToAdd * 10;

        $user->increment('points', (int)$this->pointsToAdd);
        $user->increment('xp', $xpGain);

        // Lógica de Nível: Cada 1000 XP = 1 Nível
        $newLevel = floor($user->xp / 1000) + 1;
        if ($newLevel > $user->level) {
            $user->update(['level' => $newLevel]);
            auth()->user()->logActivity("Subiu o nível de {$user->name} para Lvl {$newLevel}", "gamificacao");
        }

        auth()->user()->logActivity("Atribuiu {$this->pointsToAdd} moedas a {$user->name}", "financeiro");

        $this->dispatch('toast', text: "Recompensa atribuída com sucesso!");
        $this->dispatch('modal-close', name: 'edit-gamification');
        $this->reset(['pointsToAdd', 'selectedUserId']);
    }

    /**
     * Atribui uma medalha (Badge) ao utilizador
     */
    public function assignBadge()
    {
        if (!$this->badgeToAssign || !$this->selectedUserId) return;

        $user = User::findOrFail($this->selectedUserId);
        $currentBadges = is_array($user->badges) ? $user->badges : [];

        // Evita duplicados
        if (!in_array($this->badgeToAssign, $currentBadges)) {
            $currentBadges[] = $this->badgeToAssign;
            $user->update(['badges' => $currentBadges]);

            auth()->user()->logActivity("Atribuiu a medalha '{$this->badgeToAssign}' a {$user->name}", "gamificacao");
            $this->dispatch('toast', text: "Medalha atribuída!");
        }

        $this->dispatch('modal-close', name: 'edit-gamification');
    }

    public function render()
    {
        $ranking = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('xp', 'desc')
            ->paginate(12);

        return view('livewire.admin.gamification-hub', [
            'ranking' => $ranking,
            'stats' => [
                'total_xp' => User::sum('xp'),
                'total_points' => User::sum('points'),
                'avg_level' => round(User::avg('level'), 1),
                'top_player' => User::orderBy('xp', 'desc')->first(),
            ]
        ]);
    }
}
