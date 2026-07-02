@php
    $visibilityLabels = [
        'public'    => 'Público',
        'workspace' => 'Workspace',
        'followers' => 'Seguidores',
        'private'   => 'Privado',
    ];
    $visibilityIcons = [
        'public'    => 'globe-alt',
        'workspace' => 'building-office-2',
        'followers' => 'user-group',
        'private'   => 'lock-closed',
    ];
    $filterTabs = [
        'todos'     => ['label' => 'Para Ti',   'icon' => 'sparkles'],
        'seguir'    => ['label' => 'A Seguir',  'icon' => 'user-group'],
        'workspace' => ['label' => 'Workspace', 'icon' => 'building-office-2'],
    ];
@endphp

<div
    x-data="{
        postMenuOpen: null,
        notifOpen: false,
        lightboxSrc: null,
        lightboxOpen: false,
        openLightbox(src) { this.lightboxSrc = src; this.lightboxOpen = true; },
        closeLightbox() { this.lightboxOpen = false; this.lightboxSrc = null; },
    }"
    @keydown.escape.window="notifOpen = false; postMenuOpen = null; lightboxOpen = false"
    class="fc-wrap w-full max-w-7xl mx-auto pb-24 px-3 md:px-5"
>

@once
<style>
[x-cloak]{display:none!important}

.fc-wrap *{box-sizing:border-box}

.fc-card{
    background:#fff;
    border:1px solid rgba(0,0,0,.06);
    border-radius:1.5rem;
    box-shadow:0 1px 12px -2px rgba(0,0,0,.08),0 1px 3px rgba(0,0,0,.04);
    transition:box-shadow .15s;
}
.fc-card:hover{box-shadow:0 4px 20px -4px rgba(0,0,0,.12)}

.fc-input{
    background:rgba(0,0,0,.03);
    border:1.5px solid rgba(0,0,0,.07);
    border-radius:.875rem;
    outline:none;
    transition:border-color .18s,box-shadow .18s,background .18s;
    color:#0f172a;
    font-size:.875rem;
}
.fc-input:focus{
    background:#fff;
    border-color:#10b981;
    box-shadow:0 0 0 3px rgba(16,185,129,.1);
}

.fc-scroll::-webkit-scrollbar{width:3px;height:3px}
.fc-scroll::-webkit-scrollbar-thumb{background:#10b981;border-radius:10px}
.fc-scroll::-webkit-scrollbar-track{background:transparent}

.fc-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:.375rem;
    background:linear-gradient(135deg,#059669,#10b981);
    color:#fff!important;
    border:none;border-radius:.875rem;
    font-weight:800;font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;
    cursor:pointer;transition:filter .15s,transform .1s,box-shadow .15s;
    box-shadow:0 4px 14px -2px rgba(16,185,129,.35);
}
.fc-btn:hover{filter:brightness(1.07);box-shadow:0 6px 20px -2px rgba(16,185,129,.45)}
.fc-btn:active{transform:scale(.96)}
.fc-btn:disabled{opacity:.4;pointer-events:none}

.fc-tab{
    display:inline-flex;align-items:center;gap:.35rem;
    padding:.5rem 1.1rem;border-radius:999px;
    font-size:.65rem;font-weight:900;letter-spacing:.1em;text-transform:uppercase;
    transition:all .18s;white-space:nowrap;cursor:pointer;border:1.5px solid transparent;
}
.fc-tab-idle{background:#fff;border-color:rgba(0,0,0,.07);color:#71717a}
.fc-tab-idle:hover{border-color:#10b981;color:#059669}
.fc-tab-active{background:linear-gradient(135deg,#059669,#10b981);color:#fff!important;border-color:transparent;box-shadow:0 4px 14px -2px rgba(16,185,129,.35)}

@keyframes fc-pop{0%{transform:scale(.95);opacity:0}100%{transform:scale(1);opacity:1}}
@keyframes fc-pulse{0%,100%{opacity:1}50%{opacity:.3}}
@keyframes fc-heart{0%,100%{transform:scale(1)}40%{transform:scale(1.3)}}

.fc-anim-pop{animation:fc-pop .2s ease both}
.fc-dot-pulse{animation:fc-pulse 2s ease infinite}
.fc-liked .fc-hi{animation:fc-heart .3s ease}
@media(prefers-reduced-motion:reduce){.fc-anim-pop,.fc-dot-pulse,.fc-liked .fc-hi{animation:none}}

.fc-post-card{
    background:#fff;
    border:1px solid rgba(0,0,0,.055);
    border-radius:1.5rem;
    box-shadow:0 1px 8px -2px rgba(0,0,0,.07);
    transition:box-shadow .15s,transform .15s;
    contain:content;
}
.fc-post-card:hover{
    box-shadow:0 4px 20px -4px rgba(16,185,129,.12),0 1px 6px rgba(0,0,0,.05);
    transform:translateY(-1px);
}

.fc-avatar-link{border-radius:.875rem;border:2px solid rgba(16,185,129,.2);transition:border-color .18s,transform .18s;display:block}
.fc-avatar-link:hover{border-color:#10b981;transform:scale(1.06)}

.fc-unread{
    position:absolute;top:-.3rem;right:-.3rem;
    min-width:1.15rem;height:1.15rem;padding:0 .25rem;
    background:#ef4444;color:#fff;font-size:.58rem;font-weight:900;
    border-radius:999px;border:2px solid #fff;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 2px 6px rgba(239,68,68,.4);
}

.fc-badge{display:inline-flex;align-items:center;gap:.2rem;padding:.18rem .5rem;border-radius:.4rem;font-size:.6rem;font-weight:900;letter-spacing:.07em;text-transform:uppercase}
.fc-badge-green{background:#ecfdf5;color:#059669;border:1px solid #a7f3d0}

.no-scrollbar::-webkit-scrollbar{display:none}
.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}

.fc-lightbox{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.88);backdrop-filter:blur(8px);cursor:zoom-out}

.fc-stat-box{text-align:center;border-radius:1rem;padding:.875rem .5rem}

/* Texto global preto */
.fc-wrap h1,.fc-wrap h2,.fc-wrap h3,.fc-wrap p:not(.fc-sub),.fc-wrap span:not([class*="text-"]):not([class*="fc-"]){color:#0f172a}
</style>
@endonce

{{-- ══════════════════════════════════════════════
     CABEÇALHO
══════════════════════════════════════════════ --}}
<header class="flex items-center justify-between gap-4 pt-6 md:pt-8 pb-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="size-11 rounded-[1.1rem] bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-500/25 shrink-0">
            <flux:icon name="globe-alt" variant="solid" class="size-6 text-white" />
        </div>
        <div class="min-w-0">
            <h1 class="text-xl sm:text-[1.6rem] font-black italic tracking-tight text-zinc-900 leading-none truncate">Finance Connect</h1>
            <p class="text-[9px] font-bold uppercase tracking-[.2em] text-zinc-400 mt-0.5 hidden sm:block">Rede de Progresso Financeiro</p>
        </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <div class="shrink-0"><livewire:social.chat-panel /></div>

        {{-- Notificações --}}
        <div class="relative shrink-0">
            <button
                @click="notifOpen = !notifOpen; if(notifOpen) $wire.markNotificationsRead()"
                class="relative size-10 rounded-[.875rem] bg-white border border-zinc-100 text-zinc-500 hover:text-emerald-600 hover:border-emerald-200 transition-all shadow-sm flex items-center justify-center"
            >
                <flux:icon name="bell" class="size-4.5" />
                @if($this->unreadNotificationsCount > 0)
                    <span class="fc-unread">{{ $this->unreadNotificationsCount > 9 ? '9+' : $this->unreadNotificationsCount }}</span>
                @endif
            </button>

            <div
                x-show="notifOpen" x-cloak @click.outside="notifOpen = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-80 fc-card z-50 overflow-hidden"
            >
                <div class="px-5 py-3 border-b border-zinc-50 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Notificações</span>
                    @if($this->unreadNotificationsCount > 0)
                        <span class="fc-badge fc-badge-green">{{ $this->unreadNotificationsCount }} novas</span>
                    @endif
                </div>
                <div class="max-h-80 overflow-y-auto fc-scroll divide-y divide-zinc-50">
                    @forelse($this->notifications as $notif)
                        <div class="flex items-start gap-3 px-4 py-3 hover:bg-emerald-50/50 transition-colors">
                            <flux:avatar src="{{ $notif->actor->avatarUrl() }}" initials="{{ substr($notif->actor->name, 0, 2) }}" class="size-8 rounded-xl border border-black/5 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] text-zinc-700 leading-snug">
                                    <span class="font-black text-zinc-900">{{ $notif->actor->name }}</span>
                                    @if($notif->type === 'like') gostou do teu post
                                    @elseif($notif->type === 'comment') comentou: <em class="text-zinc-500">{{ Str::limit($notif->preview, 35) }}</em>
                                    @else interagiu contigo
                                    @endif
                                </p>
                                <p class="text-[9px] text-emerald-600 font-bold uppercase mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notif->read_at)
                                <div class="size-2 rounded-full bg-emerald-500 shrink-0 mt-1 fc-dot-pulse"></div>
                            @endif
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <flux:icon name="bell-slash" class="size-8 text-zinc-200 mx-auto mb-2" />
                            <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Tudo em dia!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <a href="{{ route('social.profile', auth()->user()->username) }}" wire:navigate
            class="size-10 rounded-[.875rem] bg-white border border-zinc-100 text-zinc-500 hover:text-emerald-600 hover:border-emerald-200 transition-all shadow-sm flex items-center justify-center"
            title="O meu perfil">
            <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-7 rounded-lg" />
        </a>
    </div>
</header>

{{-- ══════════════════════════════════════════════
     BARRA: PESQUISA + TABS
══════════════════════════════════════════════ --}}
<div class="fc-card p-3 sm:p-4 mb-5">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative flex-1 min-w-0">
            <flux:icon name="magnifying-glass" class="size-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" />
            <input
                type="search"
                wire:model.live.debounce.350ms="search"
                placeholder="Pesquisar posts ou #hashtags..."
                class="fc-input w-full py-2.5 pl-10 pr-9 text-sm"
            >
            @if($search !== '')
                <button wire:click="$set('search', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700 transition-colors">
                    <flux:icon name="x-mark" class="size-4" />
                </button>
            @endif
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
            @foreach($filterTabs as $key => $tabInfo)
                <button type="button" wire:click="setFilter('{{ $key }}')"
                    class="fc-tab {{ $feedFilter === $key && !$tag ? 'fc-tab-active' : 'fc-tab-idle' }}">
                    <flux:icon name="{{ $tabInfo['icon'] }}" class="size-3.5" />
                    {{ $tabInfo['label'] }}
                </button>
            @endforeach

            @if($tag)
                <span class="fc-tab" style="background:#ecfdf5;color:#059669;border-color:#a7f3d0">
                    <flux:icon name="hashtag" class="size-3" />
                    {{ $tag }}
                    <button wire:click="clearTag" class="ml-1 hover:text-red-400 transition-colors">
                        <flux:icon name="x-mark" class="size-3" />
                    </button>
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Desafios: faixa compacta só em mobile --}}
@if(isset($activeChallenges) && $activeChallenges->isNotEmpty())
    <div class="lg:hidden -mt-2 mb-4 flex gap-2 overflow-x-auto no-scrollbar pb-1">
        @foreach($activeChallenges as $challenge)
            <div class="shrink-0 flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50/80 px-3 py-2">
                <flux:icon name="trophy" class="size-4 text-emerald-600 shrink-0" />
                <div class="min-w-0">
                    <p class="text-[11px] font-black text-zinc-900 truncate max-w-[140px]">{{ $challenge->title }}</p>
                    <p class="text-[9px] text-zinc-400 font-bold">{{ $challenge->participants_count }} participantes</p>
                </div>
                <button wire:click="joinChallenge({{ $challenge->id }})" class="shrink-0 text-[9px] font-black text-emerald-700 bg-white border border-emerald-200 px-2.5 py-1 rounded-lg">
                    Aderir
                </button>
            </div>
        @endforeach
    </div>
@endif

{{-- ══════════════════════════════════════════════
     GRELHA PRINCIPAL
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- FEED (8/12) --}}
    <div class="lg:col-span-8 space-y-4 min-w-0">

        {{-- COMPOSITOR --}}
        <div class="fc-card p-5">
            <form wire:submit.prevent="createPost" class="space-y-4">
                <div class="flex gap-3">
                    <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-10 rounded-xl border border-black/5 shrink-0" />
                    <div class="flex-1">
                        <textarea
                            wire:model="newPostContent"
                            maxlength="1000"
                            placeholder="Partilha uma conquista, dica ou ideia financeira..."
                            rows="3"
                            class="fc-input w-full p-3 text-sm font-medium resize-none fc-scroll"
                            style="color:#0f172a"
                        ></textarea>
                        <div class="flex justify-end mt-1">
                            <span class="text-[9px] font-bold {{ strlen($newPostContent) > 900 ? 'text-red-500' : 'text-zinc-400' }}">
                                {{ strlen($newPostContent) }}/1000
                            </span>
                        </div>
                    </div>
                </div>

                @if($mediaFile)
                    <div class="relative rounded-xl overflow-hidden border border-black/5 fc-anim-in">
                        <img src="{{ $mediaFile->temporaryUrl() }}" class="w-full max-h-64 object-cover">
                        <button type="button" wire:click="$set('mediaFile', null)"
                            class="absolute top-2 right-2 size-7 bg-black/60 hover:bg-red-500 text-white rounded-full flex items-center justify-center transition-colors">
                            <flux:icon name="x-mark" class="size-3.5 text-white" />
                        </button>
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-zinc-100">
                    <div class="flex items-center gap-2">
                        <label class="relative cursor-pointer size-9 rounded-xl bg-zinc-50 hover:bg-emerald-50 border border-zinc-100 hover:border-emerald-200 flex items-center justify-center text-zinc-400 hover:text-emerald-600 transition-all" title="Foto ou vídeo">
                            <flux:icon name="photo" class="size-4" />
                            <input type="file" wire:model="mediaFile" class="absolute inset-0 opacity-0 cursor-pointer w-full" accept="image/*,video/*">
                        </label>

                        <div class="flex items-center gap-1.5 bg-zinc-50 border border-zinc-100 rounded-xl px-3 py-2">
                            <flux:icon name="{{ $visibilityIcons[$visibility] ?? 'globe-alt' }}" class="size-3 text-emerald-600 shrink-0" />
                            <select wire:model.live="visibility"
                                class="bg-transparent border-0 p-0 text-[9px] font-black uppercase tracking-widest text-zinc-700 focus:ring-0 outline-none cursor-pointer">
                                @foreach($visibilityLabels as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-60"
                        class="fc-btn px-7 h-10">
                        <span wire:loading.remove wire:target="createPost">Publicar</span>
                        <span wire:loading wire:target="createPost" class="flex items-center gap-1.5">
                            <svg class="animate-spin size-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            A publicar...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- LISTA DE POSTS --}}
        <div class="space-y-4">
            @forelse($this->posts as $post)
                <article wire:key="post-{{ $post->id }}" class="fc-post-card p-5">

                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <a href="{{ route('social.profile', $post->user->username) }}" wire:navigate class="fc-avatar-link shrink-0">
                                <flux:avatar src="{{ $post->user->avatarUrl() }}" class="size-10 rounded-xl" />
                            </a>
                            <div class="min-w-0">
                                <a href="{{ route('social.profile', $post->user->username) }}" wire:navigate
                                   class="text-sm font-black text-zinc-900 uppercase tracking-tight hover:text-emerald-700 transition-colors truncate block">
                                    {{ $post->user->name }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[9px] text-zinc-400 font-bold">{{ $post->created_at->diffForHumans() }}</span>
                                    <span class="text-zinc-200 text-[10px]">·</span>
                                    <span class="flex items-center gap-1 text-[9px] font-bold text-emerald-600 uppercase">
                                        <flux:icon name="{{ $visibilityIcons[$post->visibility] ?? 'globe-alt' }}" class="size-2.5" />
                                        {{ $visibilityLabels[$post->visibility] ?? $post->visibility }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Menu 3 pontos --}}
                        <div class="relative shrink-0">
                            <button
                                @click="postMenuOpen === {{ $post->id }} ? postMenuOpen = null : postMenuOpen = {{ $post->id }}"
                                class="size-8 rounded-xl text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 flex items-center justify-center transition-all"
                            >
                                <flux:icon name="ellipsis-horizontal" class="size-4" />
                            </button>

                            <div
                                x-show="postMenuOpen === {{ $post->id }}"
                                x-cloak
                                @click.outside="postMenuOpen = null"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-xl z-50 border border-zinc-100 overflow-hidden"
                            >
                                <button wire:click="copyPostLink({{ $post->id }})" @click="postMenuOpen = null"
                                    class="w-full flex items-center gap-2 px-4 py-3 text-xs font-bold text-zinc-700 hover:bg-zinc-50 transition-colors text-left">
                                    <flux:icon name="link" class="size-3.5 text-emerald-600 shrink-0" />
                                    Copiar Link
                                </button>
                                @if($post->user_id !== auth()->id())
                                    <button wire:click="openReportModal({{ $post->id }})" @click="postMenuOpen = null"
                                        class="w-full flex items-center gap-2 px-4 py-3 text-xs font-bold text-red-500 hover:bg-red-50 transition-colors text-left border-t border-zinc-50">
                                        <flux:icon name="flag" class="size-3.5 shrink-0" />
                                        Reportar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Conteúdo texto --}}
                    @if($post->content)
                        <div class="text-sm leading-relaxed font-medium mb-3.5 break-words" style="color:#1e293b">
                            {!! $post->content_html !!}
                        </div>
                    @endif

                    {{-- Media --}}
                    @if($post->media_path)
                        <div class="rounded-xl overflow-hidden border border-black/5 mb-3.5 group/media relative">
                            @if($post->type === 'video')
                                <video src="{{ Storage::url($post->media_path) }}"
                                    controls preload="metadata"
                                    class="w-full max-h-[220px] object-cover bg-zinc-900">
                                </video>
                            @else
                                <img
                                    src="{{ Storage::url($post->media_path) }}"
                                    loading="lazy"
                                    @click="openLightbox('{{ Storage::url($post->media_path) }}')"
                                    class="w-full max-h-[220px] object-cover transition-transform duration-500 group-hover/media:scale-[1.015] cursor-zoom-in"
                                    alt="Imagem do post"
                                >
                            @endif
                        </div>
                    @endif

                    {{-- Barra interação --}}
                    <div class="flex items-center justify-between pt-3 border-t border-zinc-50">
                        <div class="flex items-center gap-1">
                            @php $userLiked = $post->likes->contains('user_id', auth()->id()); @endphp

                            <button wire:click="toggleLike({{ $post->id }})"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl transition-all {{ $userLiked ? 'text-red-500 bg-red-50 fc-liked' : 'text-zinc-500 hover:text-red-500 hover:bg-red-50' }}">
                                <flux:icon name="heart" variant="{{ $userLiked ? 'solid' : 'outline' }}" class="size-4 fc-hi" />
                                <span class="text-xs font-black" style="color:#374151">{{ $post->likes_count }}</span>
                            </button>

                            <button wire:click="setCommenting({{ $post->id }})"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl transition-all {{ $commentingPostId === $post->id ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                <flux:icon name="chat-bubble-left" class="size-4" />
                                <span class="text-xs font-black" style="color:#374151">{{ $post->comments_count }}</span>
                            </button>
                        </div>

                        <button wire:click="copyPostLink({{ $post->id }})"
                            class="p-2 rounded-xl text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all" title="Partilhar">
                            <flux:icon name="share" class="size-4" />
                        </button>
                    </div>

                    {{-- Comentários --}}
                    @if($commentingPostId === $post->id)
                        <div class="mt-4 pt-4 border-t border-emerald-50/80 space-y-3 fc-anim-in">
                            @if($post->comments->count() > 0)
                                <div class="space-y-2.5 max-h-52 overflow-y-auto fc-scroll pr-1">
                                    @foreach($post->comments as $comment)
                                        <div wire:key="cmt-{{ $comment->id }}" class="flex gap-2.5 items-start">
                                            <flux:avatar src="{{ $comment->user->avatarUrl() }}" initials="{{ substr($comment->user->name, 0, 2) }}" class="size-7 rounded-lg border border-black/5 shrink-0" />
                                            <div class="flex-1 bg-zinc-50 rounded-xl px-3.5 py-2.5 border border-zinc-100">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-[9px] font-black text-emerald-700 uppercase">{{ $comment->user->name }}</span>
                                                    <span class="text-[8px] text-zinc-400">{{ $comment->created_at->diffForHumans(null, true) }}</span>
                                                </div>
                                                <p class="text-xs font-medium leading-relaxed" style="color:#374151">{{ $comment->content }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-[9px] text-zinc-400 font-bold uppercase tracking-widest py-1.5">Sê o primeiro a comentar</p>
                            @endif

                            <form wire:submit.prevent="postComment({{ $post->id }})" class="flex items-center gap-2">
                                <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-7 rounded-lg border border-black/5 shrink-0" />
                                <input wire:model="commentContent" maxlength="280"
                                    placeholder="Escreve um comentário..."
                                    class="fc-input flex-1 py-2.5 px-3.5 text-xs font-medium"
                                    style="color:#0f172a"
                                    autofocus>
                                <button type="submit" wire:loading.attr="disabled"
                                    class="fc-btn px-4 h-9 text-[9px] shrink-0">
                                    <span wire:loading.remove wire:target="postComment">Enviar</span>
                                    <svg wire:loading wire:target="postComment" class="animate-spin size-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </button>
                            </form>
                        </div>
                    @endif

                </article>
            @empty
                <div class="py-20 text-center fc-card border-2 border-dashed border-zinc-100">
                    <flux:icon name="globe-alt" class="size-12 text-zinc-200 mx-auto mb-3" />
                    <p class="text-zinc-400 font-black uppercase tracking-[.35em] text-[10px]">Silêncio no horizonte</p>
                    <p class="text-zinc-300 text-xs mt-1.5">Publica algo ou segue outros membros</p>
                </div>
            @endforelse

            @if($this->posts->hasMorePages())
                <div x-intersect.once="$wire.loadMore()" class="flex flex-col items-center py-10 gap-2.5">
                    <div class="size-7 border-[3px] border-emerald-200 border-t-emerald-500 rounded-full animate-spin"></div>
                    <p class="text-[9px] font-black uppercase text-zinc-300 tracking-[.25em]">A carregar...</p>
                </div>
            @elseif($this->posts->count() > 0)
                <div class="py-8 text-center">
                    <p class="text-[9px] font-black uppercase text-zinc-300 tracking-[.25em]">Viste tudo por agora</p>
                </div>
            @endif
        </div>
    </div>

    {{-- SIDEBAR (4/12) --}}
    <aside class="lg:col-span-4 space-y-4 lg:sticky lg:top-5 lg:self-start">

        {{-- DESAFIOS COMUNITÁRIOS --}}
        @if(isset($activeChallenges) && $activeChallenges->isNotEmpty())
            <div class="fc-card overflow-hidden hidden lg:block">
                <div class="px-5 py-3.5 border-b border-zinc-50 flex items-center gap-2">
                    <flux:icon name="trophy" class="size-4 text-emerald-600" />
                    <h3 class="text-[9px] font-black uppercase tracking-[.25em] text-zinc-400">Desafios da Comunidade</h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($activeChallenges as $challenge)
                        <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-4">
                            <p class="font-black text-sm text-zinc-900 leading-snug">{{ $challenge->title }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-[10px] font-bold text-zinc-400">
                                    <flux:icon name="user-group" class="size-3 inline -mt-0.5" />
                                    {{ $challenge->participants_count }} participantes
                                </span>
                                <button wire:click="joinChallenge({{ $challenge->id }})" class="fc-btn px-4 py-1.5 text-[9px]">
                                    Aderir
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PARTILHAR METAS --}}
        @if(isset($myGoals) && $myGoals->isNotEmpty())
            <div class="fc-card p-5">
                <div class="flex items-center gap-2 mb-3.5">
                    <flux:icon name="flag" class="size-4 text-emerald-600" />
                    <h3 class="text-[9px] font-black uppercase tracking-[.25em] text-zinc-400">As Tuas Metas</h3>
                </div>
                <div class="space-y-2">
                    @foreach($myGoals as $goal)
                        @php $pct = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0; @endphp
                        <button wire:click="shareGoalProgress({{ $goal->id }})"
                            class="w-full text-left rounded-xl border border-zinc-100 hover:border-emerald-200 hover:bg-emerald-50/50 p-3 transition-all group">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-zinc-900 truncate">{{ $goal->name }}</span>
                                <span class="text-[10px] font-black text-emerald-600 shrink-0">{{ $pct }}%</span>
                            </div>
                            <div class="h-1 bg-zinc-100 rounded-full mt-2 overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width:{{ $pct }}%"></div>
                            </div>
                            <p class="text-[9px] font-bold text-zinc-400 mt-1.5 group-hover:text-emerald-600 transition-colors">
                                Partilhar progresso →
                            </p>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PERFIL RÁPIDO --}}
        <div class="fc-card p-5">
            <div class="flex items-center gap-3.5">
                <div class="relative shrink-0">
                    <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-13 rounded-2xl border-2 border-emerald-100" />
                    <div class="absolute -bottom-0.5 -right-0.5 size-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-zinc-900 uppercase tracking-tight truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-zinc-400 font-bold mt-0.5">{{ auth()->user()->username }}</p>
                    @if(auth()->user()->social_bio)
                        <p class="text-[10px] text-zinc-500 mt-1 leading-snug line-clamp-2">{{ auth()->user()->social_bio }}</p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 mt-4">
                <div class="fc-stat-box bg-zinc-50">
                    <p class="text-base font-black text-zinc-900">{{ auth()->user()->socialPosts()->where('is_story', false)->count() }}</p>
                    <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5">Posts</p>
                </div>
                <div class="fc-stat-box" style="background:#ecfdf5">
                    <p class="text-base font-black" style="color:#059669">{{ auth()->user()->following()->count() }}</p>
                    <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5">A Seguir</p>
                </div>
                <div class="fc-stat-box bg-zinc-50">
                    <p class="text-base font-black text-zinc-900">{{ auth()->user()->followers()->count() }}</p>
                    <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5">Seguidores</p>
                </div>
            </div>
            <button wire:click="openEditProfile"
                class="w-full mt-3.5 py-2.5 rounded-xl border border-emerald-200 text-emerald-700 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-50 transition-all">
                Editar Perfil
            </button>
        </div>

        {{-- TRENDING HASHTAGS --}}
        <div class="fc-card p-5">
            <div class="flex items-center gap-2 mb-3.5">
                <div class="size-2 rounded-full bg-emerald-500 fc-dot-pulse"></div>
                <h3 class="text-[9px] font-black uppercase tracking-[.3em] text-zinc-400">Finance Trends</h3>
            </div>

            <div class="space-y-0.5">
                @forelse($this->trendingHashtags as $i => $trend)
                    <button wire:click="filterByTag('{{ $trend['tag'] }}')"
                        class="w-full flex items-center justify-between rounded-xl px-3 py-2.5 hover:bg-emerald-50 transition-all group text-left {{ $tag === $trend['tag'] ? 'bg-emerald-50' : '' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="text-[9px] font-black text-zinc-300 w-4 tabular-nums text-right">{{ $i + 1 }}</span>
                            <span class="text-sm font-black text-emerald-600 group-hover:text-emerald-800 transition-colors">#{{ $trend['tag'] }}</span>
                        </div>
                        <span class="text-[8px] font-bold text-zinc-400 uppercase">{{ number_format($trend['count']) }}</span>
                    </button>
                @empty
                    <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest text-center py-5 italic">
                        Usa #hashtags nos teus posts
                    </p>
                @endforelse
            </div>
        </div>

        {{-- PESSOAS A SEGUIR --}}
        <div class="fc-card p-5">
            <div class="flex items-center justify-between mb-3.5">
                <h3 class="text-[9px] font-black uppercase tracking-[.25em] text-zinc-400">Pessoas a Seguir</h3>
                <button wire:click="$refresh" class="text-[8px] font-bold text-emerald-600 hover:text-emerald-800 uppercase tracking-widest transition-colors">
                    Atualizar
                </button>
            </div>

            <div class="space-y-3">
                @forelse($this->suggestedUsers as $suggested)
                    <div wire:key="sug-{{ $suggested->id }}" class="flex items-center justify-between gap-2.5 group/sug">
                        <a href="{{ route('social.profile', $suggested->username ?? '#') }}" wire:navigate class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="relative shrink-0">
                                <flux:avatar src="{{ $suggested->avatarUrl() }}" class="size-9 rounded-xl border border-black/5 group-hover/sug:scale-105 transition-transform" />
                                <div class="absolute -bottom-0.5 -right-0.5 size-2.5 bg-emerald-400 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-zinc-900 uppercase tracking-tight truncate">{{ explode(' ', $suggested->name)[0] }}</p>
                                <p class="text-[8px] text-zinc-400 font-bold">{{ $suggested->username }}</p>
                            </div>
                        </a>
                        <button wire:click="toggleFollow({{ $suggested->id }})"
                            class="shrink-0 size-8 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center"
                            title="Seguir">
                            <flux:icon name="user-plus" class="size-3.5" />
                        </button>
                    </div>
                @empty
                    <p class="text-[9px] text-zinc-400 font-bold uppercase text-center py-3">Conectado a todos!</p>
                @endforelse
            </div>
        </div>

        <p class="text-center text-[8px] font-bold text-zinc-300 uppercase tracking-[.25em] px-4">
            Finance Connect · {{ date('Y') }}
        </p>
    </aside>
</div>

{{-- LIGHTBOX --}}
<div x-show="lightboxOpen" x-cloak @click="closeLightbox()" class="fc-lightbox"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100">
    <img :src="lightboxSrc" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl" @click.stop alt="Lightbox">
    <button @click="closeLightbox()"
        class="absolute top-4 right-4 size-9 bg-white/10 hover:bg-white/20 text-white rounded-xl flex items-center justify-center transition-all">
        <flux:icon name="x-mark" class="size-4 text-white" />
    </button>
</div>

{{-- MODAL: EDITAR PERFIL --}}
<div
    x-data="{ open: false }"
    x-on:modal-show.window="if($event.detail.name === 'edit-profile') open = true"
    x-on:modal-close.window="if($event.detail.name === 'edit-profile') open = false"
>
    <div x-show="open" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div @click="open = false" class="absolute inset-0 bg-zinc-950/50 backdrop-blur-sm"
            x-transition:enter="transition duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
        </div>

        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative w-full max-w-lg bg-white rounded-[1.75rem] shadow-2xl overflow-hidden border border-zinc-100">

            <form wire:submit.prevent="saveProfile" class="flex flex-col max-h-[88vh]">
                <div class="px-7 pt-7 pb-5 border-b border-zinc-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-zinc-900 uppercase italic tracking-tight">Editar Perfil</h2>
                        <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-0.5">Finance Connect</p>
                    </div>
                    <button type="button" @click="open = false"
                        class="size-8 rounded-xl bg-zinc-50 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors">
                        <flux:icon name="x-mark" class="size-4" />
                    </button>
                </div>

                <div class="p-7 space-y-5 overflow-y-auto fc-scroll flex-1">
                    <div class="flex items-center gap-4">
                        <div class="relative size-18 rounded-2xl overflow-hidden border-2 border-emerald-100 shrink-0" style="width:4.5rem;height:4.5rem">
                            @if($editAvatarFile)
                                <img src="{{ $editAvatarFile->temporaryUrl() }}" class="size-full object-cover">
                            @else
                                <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-full" />
                            @endif
                        </div>
                        <div>
                            <label class="relative cursor-pointer fc-btn px-4 py-2 inline-flex text-[9px]">
                                <flux:icon name="camera" class="size-3.5" />
                                Alterar Foto
                                <input type="file" wire:model="editAvatarFile" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </label>
                            <p class="text-[8px] text-zinc-400 mt-1.5 uppercase font-bold">JPG, PNG — máx. 5MB</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Nome</label>
                            <input type="text" wire:model="editName" class="fc-input w-full py-3 px-4 text-sm font-semibold" style="color:#0f172a">
                            @error('editName') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Username</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 text-sm font-black select-none">@</span>
                                <input type="text" wire:model="editUsername" class="fc-input w-full py-3 pl-7 pr-4 text-sm font-semibold" style="color:#0f172a">
                            </div>
                            @error('editUsername') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Bio <span class="normal-case font-medium text-zinc-300">(opcional)</span></label>
                            <textarea wire:model="editBio" maxlength="160" rows="3"
                                placeholder="Fala sobre a tua jornada financeira..."
                                class="fc-input w-full py-3 px-4 text-sm font-medium resize-none" style="color:#0f172a"></textarea>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Visibilidade padrão</label>
                            <select wire:model="editDefaultVisibility" class="fc-input w-full py-3 px-4 text-sm font-semibold" style="color:#0f172a">
                                @foreach($visibilityLabels as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-7 py-5 bg-zinc-50/80 flex gap-3 border-t border-zinc-100">
                    <button type="button" @click="open = false"
                        class="flex-1 h-11 rounded-xl text-[9px] font-black uppercase text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-[2] h-11 fc-btn flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveProfile">Guardar Alterações</span>
                        <span wire:loading wire:target="saveProfile" class="flex items-center gap-1.5">
                            <svg class="animate-spin size-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            A guardar...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: REPORTAR POST --}}
@if($reportModal)
    <div class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-zinc-950/50 backdrop-blur-sm">
        <div class="w-full max-w-md bg-white rounded-[1.75rem] shadow-2xl border border-zinc-100 overflow-hidden fc-anim-pop">
            <div class="px-6 pt-6 pb-4 border-b border-zinc-50 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-zinc-900 uppercase italic">Reportar Post</h2>
                    <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-0.5">Segurança da comunidade</p>
                </div>
                <button wire:click="$set('reportModal', false)"
                    class="size-8 rounded-xl bg-zinc-50 text-zinc-400 hover:text-zinc-900 flex items-center justify-center">
                    <flux:icon name="x-mark" class="size-4" />
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-2 bg-zinc-50 rounded-xl px-3.5 py-2.5 border border-zinc-100">
                    <flux:icon name="document-text" class="size-3.5 text-zinc-400 shrink-0" />
                    <span class="text-xs font-bold text-zinc-600">Post #{{ $reportingPostId }}</span>
                </div>
                <div>
                    <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Motivo</label>
                    <textarea wire:model="reportReason" rows="4"
                        placeholder="Descreve o problema em detalhe..."
                        class="fc-input w-full py-3 px-4 text-sm font-medium resize-none" style="color:#0f172a"></textarea>
                    @error('reportReason') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('reportModal', false)"
                    class="flex-1 h-11 rounded-xl text-[9px] font-black uppercase text-zinc-500 hover:bg-zinc-100 transition-all">
                    Cancelar
                </button>
                <button wire:click="submitReport"
                    class="flex-[2] h-11 fc-btn flex items-center justify-center gap-2">
                    <flux:icon name="shield-check" class="size-4" />
                    Enviar Reporte
                </button>
            </div>
        </div>
    </div>
@endif

<script>
    window.addEventListener('copy-to-clipboard', e => {
        const text = e.detail && e.detail.text;
        if (!text) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const el = Object.assign(document.createElement('textarea'), {
                value: text,
                style: 'position:fixed;left:-9999px;top:-9999px'
            });
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            el.remove();
        }
    });
</script>

</div>
