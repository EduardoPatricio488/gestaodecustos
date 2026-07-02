<div class="space-y-8 pb-24" x-data="{ slide: 0, total: 6 }">
  <div class="text-center py-8">
    <p class="text-[9px] font-black uppercase tracking-[0.5em] text-brand-500 mb-4">O Teu Ano Financeiro</p>
    <h1 class="text-5xl md:text-7xl font-black dark:text-white uppercase tracking-tighter italic">{{ $year }}</h1>
    <p class="text-zinc-400 mt-2">Wrapped · Finance Pro</p>
  </div>

  <div class="max-w-lg mx-auto">
    {{-- Slide 0: Total gasto --}}
    <div x-show="slide === 0" class="bg-zinc-950 text-white p-10 rounded-3xl text-center min-h-[300px] flex flex-col justify-center">
      <p class="text-[9px] font-black uppercase tracking-widest text-red-400 mb-4">Gastaste</p>
      <p class="text-6xl font-black italic tabular-nums">{{ number_format($spent, 0, ',', ' ') }}€</p>
      <p class="text-zinc-400 mt-4 text-sm">em {{ $year }}</p>
    </div>

    {{-- Slide 1: Poupaste --}}
    <div x-show="slide === 1" class="bg-emerald-600 text-white p-10 rounded-3xl text-center min-h-[300px] flex flex-col justify-center">
      <p class="text-[9px] font-black uppercase tracking-widest text-emerald-200 mb-4">Poupaste</p>
      <p class="text-6xl font-black italic tabular-nums">{{ number_format($saved, 0, ',', ' ') }}€</p>
      <p class="text-emerald-200 mt-4 text-sm">de {{ number_format($earned, 0, ',', ' ') }}€ recebidos</p>
    </div>

    {{-- Slide 2: Top categoria --}}
    <div x-show="slide === 2" class="bg-violet-600 text-white p-10 rounded-3xl text-center min-h-[300px] flex flex-col justify-center">
      <p class="text-[9px] font-black uppercase tracking-widest text-violet-200 mb-4">A Tua Maior Categoria</p>
      <p class="text-4xl font-black italic">{{ $topCategory?->name ?? '—' }}</p>
      @if($topCategory)
        <p class="text-violet-200 mt-4 text-sm">{{ number_format($topCategory->expenses_sum_amount ?? 0, 0, ',', ' ') }}€</p>
      @endif
    </div>

    {{-- Slide 3: Metas --}}
    <div x-show="slide === 3" class="bg-amber-500 text-white p-10 rounded-3xl text-center min-h-[300px] flex flex-col justify-center">
      <p class="text-[9px] font-black uppercase tracking-widest text-amber-200 mb-4">Metas Concluídas</p>
      <p class="text-6xl font-black italic tabular-nums">{{ $goalsCompleted }}</p>
      <p class="text-amber-200 mt-4 text-sm">objetivos alcançados</p>
    </div>

    {{-- Slide 4: Gamificação --}}
    <div x-show="slide === 4" class="bg-zinc-900 text-white p-10 rounded-3xl text-center min-h-[300px] flex flex-col justify-center border border-zinc-700">
      <p class="text-[9px] font-black uppercase tracking-widest text-brand-400 mb-4">Subiste para</p>
      <p class="text-6xl font-black italic">Nível {{ $level }}</p>
      <p class="text-zinc-400 mt-4 text-sm">{{ number_format($xp, 0, ',', ' ') }} XP acumulados</p>
    </div>

    {{-- Slide 5: Finance Score --}}
    <div x-show="slide === 5" class="bg-gradient-to-br from-emerald-600 to-blue-600 text-white p-10 rounded-3xl text-center min-h-[300px] flex flex-col justify-center">
      <p class="text-[9px] font-black uppercase tracking-widest text-white/70 mb-4">Finance Score</p>
      <p class="text-7xl font-black italic tabular-nums">{{ $score }}</p>
      <p class="text-white/80 mt-4 text-lg font-black">{{ $scoreGrade }}</p>
    </div>

    <div class="flex justify-center gap-4 mt-6">
      <button @click="slide = Math.max(0, slide - 1)" class="px-6 py-3 rounded-2xl bg-zinc-100 dark:bg-zinc-800 font-black text-sm" :disabled="slide === 0">←</button>
      <button @click="slide = Math.min(total - 1, slide + 1)" class="px-6 py-3 rounded-2xl bg-brand-600 text-white font-black text-sm" :disabled="slide === total - 1">→</button>
    </div>

    <div class="flex justify-center gap-1.5 mt-4">
      <template x-for="i in total" :key="i">
        <div class="size-2 rounded-full transition-all" :class="slide === i - 1 ? 'bg-brand-500 w-6' : 'bg-zinc-300 dark:bg-zinc-700'"></div>
      </template>
    </div>

    <div class="text-center mt-8">
      <flux:button wire:click="shareToSocial" variant="primary" class="rounded-2xl font-black">
        Partilhar no Finance Connect
      </flux:button>
    </div>
  </div>
</div>
