<div class="max-w-4xl mx-auto py-20 px-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">

        {{-- Texto Esquerda --}}
        <div class="space-y-6">
            <flux:badge variant="neutral" class="uppercase font-black text-[9px] tracking-widest">Suporte</flux:badge>
            <h1 class="text-5xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Fala com a <span class="text-emerald-500">Equipa</span>.</h1>
            <p class="text-zinc-500 dark:text-zinc-400 font-medium italic leading-relaxed">
                Tens dúvidas sobre o teu plano Business ou precisas de ajuda com a segurança do teu cofre? Envia uma mensagem.
            </p>
        </div>

        {{-- Formulário Direita --}}
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-xl relative overflow-hidden">
            @if($sent)
                <div class="py-12 text-center space-y-4 animate-in fade-in zoom-in-95">
                    <div class="size-16 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="check" variant="micro" class="size-8" />
                    </div>
                    <h2 class="text-xl font-black dark:text-white uppercase italic">Mensagem Enviada!</h2>
                    <p class="text-zinc-500 text-sm italic">Responderemos para o teu email em menos de 24h.</p>
                    <flux:button wire:click="$set('sent', false)" variant="ghost" class="text-xs uppercase font-black">Enviar outra</flux:button>
                </div>
            @else
                <form wire:submit.prevent="send" class="space-y-4">
                    <flux:input wire:model="name" label="Nome Completo" placeholder="Eduardo..." />
                    <flux:input wire:model="email" label="Teu Email" type="email" placeholder="eduardo@exemplo.com" />
                    <flux:textarea wire:model="message" label="Em que podemos ajudar?" rows="5" placeholder="Escreve aqui..." />

                    <div class="pt-4">
                        <flux:button type="submit" variant="primary" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 font-black uppercase text-xs tracking-widest shadow-lg shadow-emerald-500/20 border-none">
                            Enviar Mensagem 🟢
                        </flux:button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
