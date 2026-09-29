<x-guest-layout>
<div class="max-w-4xl mx-auto py-16 px-6">
@include('partials.breadcrumbs', ['breadcrumbs' => [['label' => 'Início', 'url' => route('home')], ['label' => 'Acessibilidade']]])
<h1 class="text-3xl font-black dark:text-white">Declaração de Acessibilidade</h1>
<p class="mt-4 text-zinc-500">Última atualização: 29/09/2026</p>
<div class="prose dark:prose-invert mt-10">
<h2>Compromisso</h2><p>O Finance Pro AI procura tornar o serviço utilizável pelo maior número possível de pessoas, incluindo pessoas com deficiência.</p>
<h2>Medidas adotadas</h2><ul><li>botões e links com texto ou nome acessível;</li><li>labels associados aos campos;</li><li>texto alternativo para imagens relevantes;</li><li>navegação sem depender exclusivamente do rato;</li><li>contraste e estados de foco considerados;</li><li>formulários com instruções e mensagens de erro claras.</li></ul>
<h2>Conteúdo de terceiros</h2><p>Conteúdo incorporado ou serviços externos podem não estar totalmente sob o controlo do Finance Pro AI.</p>
<h2>Limitações</h2><p>Uma avaliação completa com testes manuais e tecnologias de assistência deve ser realizada antes de declarar conformidade formal com um nível específico das WCAG.</p>
<h2>Feedback</h2><p>Se encontrares uma barreira de acessibilidade, utiliza a página de <a href="{{ route('public.contact') }}">Contacto</a> indicando a página e a dificuldade encontrada.</p>
</div></div>
</x-guest-layout>