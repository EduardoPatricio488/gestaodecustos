<x-guest-layout>
<div class="max-w-4xl mx-auto py-20 px-6">
    <div class="space-y-4 mb-12">
        <flux:badge variant="neutral" class="uppercase font-black text-[9px] tracking-widest">Legal</flux:badge>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black dark:text-white uppercase italic tracking-tighter">Termos de Serviço</h1>
        <p class="text-zinc-500 italic">Última atualização: {{ date('d/m/Y') }}</p>
    </div>

    <div class="prose dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 space-y-8 font-medium">
        <section>
            <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase italic">1. Aceitação dos Termos</h2>
            <p>Ao aceder ao {{ config('app.name') }}, o utilizador concorda em cumprir estes termos de serviço, todas as leis e regulamentos aplicáveis. Se não concordar com algum destes termos, está proibido de usar ou aceder a este site.</p>
        </section>

        <section>
            <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase italic">2. Licença de Uso</h2>
            <p>É concedida permissão para descarregar temporariamente uma cópia dos materiais no site para visualização pessoal e não comercial apenas. Esta é a concessão de uma licença, não uma transferência de título.</p>
        </section>

        <section>
            <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase italic">3. Responsabilidade Financeira</h2>
            <p>O {{ config('app.name') }} é uma ferramenta de gestão. Não nos responsabilizamos por decisões financeiras tomadas com base nos dados apresentados. Recomendamos sempre a consulta de um contabilista certificado para decisões empresariais.</p>
        </section>
    </div>
</div>
</x-guest-layout>
