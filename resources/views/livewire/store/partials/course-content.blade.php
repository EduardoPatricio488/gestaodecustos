{{-- CURSO: módulos com vídeo YouTube, imagens e PDF --}}
<div class="space-y-6">
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
        <flux:icon name="academic-cap" class="size-6 text-emerald-600" />
        <div>
            <p class="text-[10px] font-black uppercase text-emerald-700">Formato do curso</p>
            <p class="text-sm text-emerald-900">Vídeos YouTube + infográficos + PDF por módulo</p>
        </div>
    </div>

    @if($product->video_url)
        <div class="bg-zinc-900 rounded-3xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/10">
                <p class="text-[10px] font-black uppercase text-emerald-400 tracking-widest">Vídeo de apresentação</p>
            </div>
            <div class="aspect-video">
                <iframe src="{{ $product->video_url }}" class="w-full h-full" allowfullscreen loading="lazy" title="Apresentação do curso"></iframe>
            </div>
        </div>
    @endif

    @if($product->learning_outcomes)
        <div class="bg-white border rounded-3xl p-8">
            <h2 class="text-lg font-black uppercase mb-4">O que vais aprender</h2>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($product->learning_outcomes as $outcome)
                    <li class="flex gap-3 text-sm p-3 bg-zinc-50 rounded-xl">
                        <span class="text-emerald-500 shrink-0">✓</span>{{ $outcome }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($product->course_modules)
        <div class="space-y-4">
            <h2 class="text-lg font-black uppercase">
                Programa do curso
                <span class="text-sm font-bold text-zinc-400 normal-case">({{ count($product->course_modules) }} módulos)</span>
            </h2>

            @foreach($product->course_modules as $module)
                <div class="bg-white border rounded-3xl overflow-hidden" wire:key="module-{{ $module['number'] }}" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-6 text-left hover:bg-zinc-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <span class="size-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center font-black text-sm">{{ $module['number'] }}</span>
                            <div>
                                <h3 class="font-black uppercase text-sm">{{ $module['title'] }}</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ $module['duration'] ?? '' }}</p>
                            </div>
                        </div>
                        <span class="text-zinc-400 font-bold" x-text="open ? '−' : '+'"></span>
                    </button>

                    <div x-show="open" x-collapse class="px-6 pb-6 space-y-5 border-t border-zinc-100 pt-5">
                        <p class="text-sm text-zinc-700">{{ $module['description'] }}</p>

                        @if($alreadyOwned && !empty($module['video_url']))
                            <div class="rounded-2xl overflow-hidden bg-zinc-900 aspect-video">
                                <iframe src="{{ $module['video_url'] }}" class="w-full h-full" allowfullscreen loading="lazy" title="{{ $module['title'] }}"></iframe>
                            </div>
                        @elseif(!empty($module['video_url']))
                            <div class="relative rounded-2xl overflow-hidden bg-zinc-200 aspect-video flex items-center justify-center">
                                <div class="text-center p-6">
                                    <flux:icon name="lock-closed" class="size-8 mx-auto text-zinc-400 mb-2" />
                                    <p class="text-sm font-bold text-zinc-500">Vídeo disponível após compra</p>
                                </div>
                            </div>
                        @endif

                        @if(!empty($module['images']))
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($module['images'] as $img)
                                    <div class="p-4 bg-gradient-to-br from-emerald-50 to-zinc-100 rounded-2xl border border-emerald-100 text-center">
                                        <span class="text-4xl block mb-2">{{ $img['emoji'] ?? '🖼️' }}</span>
                                        <p class="text-xs font-bold text-zinc-600">{{ $img['caption'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($module['topics']))
                            <div>
                                <p class="text-[10px] font-black uppercase text-zinc-400 mb-2">Conteúdos</p>
                                <ul class="text-sm text-zinc-600 space-y-1">
                                    @foreach($module['topics'] as $topic)
                                        <li>• {{ $topic }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($alreadyOwned && $ownedPurchase && !empty($module['pdf_path']))
                            <a href="{{ route('store.download.request', ['purchase' => $ownedPurchase->id, 'module' => $module['number']]) }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500">
                                <flux:icon name="document-arrow-down" class="size-4" />
                                PDF Módulo {{ $module['number'] }}
                            </a>
                        @elseif(!empty($module['pdf_path']))
                            <p class="text-xs text-zinc-400 flex items-center gap-2">
                                <flux:icon name="document" class="size-4" /> PDF incluído após compra
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($alreadyOwned && $ownedPurchase && $product->download_path)
            <div class="p-6 bg-zinc-900 text-white rounded-3xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase text-emerald-400">Workbook completo</p>
                    <p class="font-black uppercase">Todos os módulos num PDF</p>
                </div>
                <a href="{{ route('store.download.request', $ownedPurchase) }}"
                   class="px-6 py-3 bg-emerald-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-400">
                    Descarregar PDF Completo
                </a>
            </div>
        @endif
    @endif
</div>
