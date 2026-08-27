@extends('components.layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[600px] text-center space-y-8 animate-in fade-in zoom-in-95 duration-700">
    {{-- Efeito Visual de Fundo --}}
    <div class="relative">
        <div class="absolute inset-0 bg-brand-500/20 blur-[100px] rounded-full"></div>
        <h1 class="relative text-[150px] font-black italic leading-none tracking-tighter text-zinc-200 dark:text-zinc-800">404</h1>
        <div class="absolute inset-0 flex items-center justify-center">
            <flux:icon name="magnifying-glass-circle" variant="outline" class="size-24 text-brand-500 opacity-80" />
        </div>
    </div>

    {{-- Texto de Erro --}}
    <div class="space-y-2">
        <h2 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Página não encontrada</h2>
        <p class="text-zinc-500 font-medium max-w-md mx-auto">Parece que este cofre financeiro não existe ou foi movido para outra localização. Vamos voltar ao Dashboard?</p>
    </div>

    {{-- Botão de Retorno --}}
    <flux:button href="{{ route('dashboard') }}" variant="primary" icon="home" class="rounded-2xl px-10 h-14 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 !bg-brand-600">
        Voltar ao Início
    </flux:button>
</div>
@endsection
