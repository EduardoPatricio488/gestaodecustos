<?php

namespace App\Traits;

use App\Models\Badge;
use Illuminate\Support\Facades\DB;

trait HasGamification {
    public function addXp($amount) {
        // 1. Forçamos o incremento direto na tabela users via SQL puro para não falhar
        DB::table('users')->where('id', $this->id)->increment('xp', $amount);

        // 2. Recarregamos os dados no utilizador atual
        $this->refresh();

        // 3. Calculamos o nível (1 nível por cada 1000 XP)
        $newLevel = (int) floor($this->xp / 1000) + 1;

        if ($newLevel > $this->level) {
            DB::table('users')->where('id', $this->id)->update(['level' => $newLevel]);
            session()->flash('level-up', "Nível Máximo! Agora és Nível {$newLevel}!");
        }
    }

    public function awardBadge($badgeName) {
        $badge = Badge::where('name', $badgeName)->first();
        if ($badge && !$this->badges()->where('badge_id', $badge->id)->exists()) {
            $this->badges()->attach($badge->id);
            return true;
        }
        return false;
    }
}
