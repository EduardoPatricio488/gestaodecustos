<?php

namespace App\Traits;

use App\Models\Badge;
use Illuminate\Support\Facades\DB;

trait HasGamification
{
    public function xpToastText(int $amount, ?string $actionLabel = null): string
    {
        $label = trim((string) ($actionLabel ?? 'conquista'));

        return $label !== ''
            ? "🏆 +{$amount} XP · {$label}"
            : "🏆 +{$amount} XP";
    }

    public function awardXp(int $amount, ?string $actionLabel = null): int
    {
        if ($amount <= 0) {
            return 0;
        }

        DB::table('users')->where('id', $this->id)->increment('xp', $amount);
        $this->refresh();

        $newLevel = (int) floor($this->xp / 1000) + 1;
        if ($newLevel > ($this->level ?? 1)) {
            DB::table('users')->where('id', $this->id)->update(['level' => $newLevel]);
            session()->flash('level-up', "Nível subido! Agora és Nível {$newLevel}!");
        }

        session()->flash('xp_award', $this->xpToastText($amount, $actionLabel));

        return $amount;
    }

    public function addXp($amount)
    {
        return $this->awardXp((int) $amount, 'conquista');
    }

    public function awardBadge($badgeName)
    {
        $badge = Badge::where('name', $badgeName)->first();
        if ($badge && ! $this->badges()->where('badge_id', $badge->id)->exists()) {
            $this->badges()->attach($badge->id);

            return true;
        }

        return false;
    }
}
