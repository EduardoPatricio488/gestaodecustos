<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationHandler extends Component
{
    public function leaveImpersonation()
    {
        if (!session()->has('impersonator_id')) {
            return;
        }

        $ceoId = session()->pull('impersonator_id');
        $ceo = User::find($ceoId);

        if ($ceo) {
            Auth::login($ceo);
            return redirect()->route('hub.business.dashboard');
        }
    }

    public function render()
    {
        return <<<'HTML'
            <div>
                @if(session()->has('impersonator_id'))
                    <div class="fixed bottom-6 right-20 z-[999] animate-bounce">
                        <button
                            wire:click="leaveImpersonation"
                            class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-2xl transition-all"
                        >
                            <flux:icon name="arrow-left-on-rectangle" variant="micro" class="size-4" />
                            Sair do Modo Colaborador
                        </button>
                    </div>
                @endif
            </div>
        HTML;
    }
}
