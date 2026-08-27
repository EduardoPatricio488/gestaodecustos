@extends('components.layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[600px] text-center space-y-8 animate-in fade-in zoom-in-95 duration-700">
    <div class="relative">
        <div class="absolute inset-0 bg-red-500/20 blur-[100px] rounded-full"></div>
        <h1 class="relative text-[150px] font-black italic leading-none tracking-tighter text-zinc-200 dark:text-zinc-800">500</h1>
        <div class="absolute inset-0 flex items-center justify-center">
            <flux:icon name="exclamation-triangle" variant="outline" class="size-24 text-red-500 opacity-80" />
        </div>
    </div>

    <div class="space-y-2">
        <h2 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Erro de Sistema</h2>
        <p class="text-zinc-500 font-medium max-w-md mx-auto">O motor financeiro teve um solucio técnico. A nossa equipa já foi notificada. Tenta atualizar a página.</p>
    </div>

    <div class="flex gap-4">
        <flux:button onclick="window.location.reload()" variant="primary" icon="arrow-path" class="rounded-2xl px-8 h-14 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20">
            Atualizar Página
        </flux:button>
    </div>
</div>
@endsection
