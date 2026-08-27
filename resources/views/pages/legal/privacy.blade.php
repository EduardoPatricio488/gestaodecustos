<x-guest-layout>
<div class="max-w-4xl mx-auto py-20 px-6">
    <div class="space-y-4 mb-12">
        <flux:badge variant="success" class="uppercase font-black text-[9px] tracking-widest">Segurança</flux:badge>
        <h1 class="text-4xl font-black dark:text-white uppercase italic tracking-tighter">Política de Privacidade</h1>
        <p class="text-zinc-500 italic">O teu bunker financeiro é a nossa prioridade.</p>
    </div>

    <div class="prose dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 space-y-8 font-medium">
        <section>
            <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase italic">Processamento de Dados</h2>
            <p>Utilizamos os teus dados exclusivamente para gerar os teus relatórios financeiros. Não vendemos informações a terceiros. Os dados de pagamento são processados de forma segura via Stripe, e o {{ config('app.name') }} nunca armazena o número do teu cartão de crédito.</p>
        </section>

        <section>
            <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase italic">Criptografia</h2>
            <p>Todos os dados de despesas e receitas são protegidos por criptografia de ponta a ponta. O teu "Cofre Privado" é inacessível até para os administradores do sistema.</p>
        </section>
    </div>
</div>
</x-guest-layout>
