<div
    x-data="{ editOpen: false, lightboxSrc: null, lightboxOpen: false, openLightbox(s){ this.lightboxSrc=s; this.lightboxOpen=true; } }"
    x-on:close-edit-profile-modal.window="editOpen = false"
    @keydown.escape.window="editOpen = false; lightboxOpen = false"
    class="fc-profile w-full max-w-7xl mx-auto pb-24 px-3 md:px-5"
>
<livewire:social.chat-panel :show-launcher="false" />

@once
<style>
[x-cloak]{display:none!important}
.fc-profile *{box-sizing:border-box}

/* Reutiliza o sistema fc-* do hub mas isolado no .fc-profile */
.fc-profile .fcp-card{
    background:#fff;
    border:1px solid rgba(0,0,0,.06);
    border-radius:1.5rem;
    box-shadow:0 1px 12px -2px rgba(0,0,0,.08);
}
.fc-profile .fcp-input{
    background:rgba(0,0,0,.03);
    border:1.5px solid rgba(0,0,0,.07);
    border-radius:.875rem;
    outline:none;
    color:#0f172a;
    transition:border-color .18s,box-shadow .18s,background .18s;
}
.fc-profile .fcp-input:focus{
    background:#fff;
    border-color:#10b981;
    box-shadow:0 0 0 3px rgba(16,185,129,.1);
}
.fc-profile .fcp-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:.375rem;
    background:linear-gradient(135deg,#059669,#10b981);
    color:#fff!important;border:none;border-radius:.875rem;
    font-weight:800;font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;
    cursor:pointer;transition:filter .15s,transform .1s,box-shadow .15s;
    box-shadow:0 4px 14px -2px rgba(16,185,129,.3);
}
.fc-profile .fcp-btn:hover{filter:brightness(1.07)}
.fc-profile .fcp-btn:active{transform:scale(.96)}
.fc-profile .fcp-btn:disabled{opacity:.4;pointer-events:none}

.fc-profile .fcp-scroll::-webkit-scrollbar{width:3px}
.fc-profile .fcp-scroll::-webkit-scrollbar-thumb{background:#10b981;border-radius:10px}

.no-scrollbar::-webkit-scrollbar{display:none}
.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}

@keyframes fcp-up{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@keyframes fcp-pop{0%{transform:scale(.93);opacity:0}60%{transform:scale(1.02)}100%{transform:scale(1);opacity:1}}
.fcp-in{animation:fcp-up .3s ease both}
.fcp-pop{animation:fcp-pop .28s ease both}

.fc-profile .fcp-grid-item{border-radius:1rem;overflow:hidden;aspect-ratio:1;cursor:pointer;border:1px solid rgba(0,0,0,.05);transition:transform .2s,box-shadow .2s}
.fc-profile .fcp-grid-item:hover{transform:scale(1.02);box-shadow:0 8px 24px -4px rgba(0,0,0,.15)}

.fcp-lightbox{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.88);cursor:zoom-out}
</style>
@endonce

{{-- VOLTAR --}}
<div class="pt-6 md:pt-8 pb-2">
    <a href="{{ route('social.hub') }}" wire:navigate
        class="inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-[.2em] text-zinc-400 hover:text-emerald-600 transition-colors group">
        <div class="size-7 rounded-xl bg-white border border-zinc-100 flex items-center justify-center group-hover:border-emerald-200 transition-colors shadow-sm">
            <flux:icon name="arrow-left" class="size-3.5" />
        </div>
        Voltar ao Finance Connect
    </a>
</div>

{{-- ══════════════════════════════════════════════
     CABEÇALHO DO PERFIL
══════════════════════════════════════════════ --}}
<div class="fcp-card fcp-in overflow-hidden">
    {{-- Banner decorativo --}}
    <div class="h-32 md:h-44 relative overflow-hidden" style="background:linear-gradient(135deg,#059669 0%,#10b981 45%,#34d399 70%,#6ee7b7 100%)">
        <div class="absolute inset-0" style="background-image:url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>
        <div class="absolute bottom-0 left-0 right-0 h-16" style="background:linear-gradient(to top,rgba(255,255,255,.15),transparent)"></div>
        <div class="absolute -bottom-16 -right-16 size-48 bg-white/5 rounded-full"></div>
        <div class="absolute -top-16 -left-16 size-48 bg-white/5 rounded-full"></div>
    </div>

    <div class="px-6 md:px-8 pb-6">
        {{-- Avatar sobreposto ao banner --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 -mt-12 md:-mt-16">
            <div class="flex items-end gap-4 md:gap-5">
                <div class="relative shrink-0">
                    <div class="size-24 md:size-28 rounded-2xl border-4 border-white shadow-xl overflow-hidden bg-zinc-100">
                        <flux:avatar src="{{ $this->profileUser->avatarUrl() }}" initials="{{ $this->profileUser->initials() }}" class="size-full" />
                    </div>
                    @if(!$this->profileUser->is_profile_private)
                        <div class="absolute -bottom-1 -right-1 size-6 bg-emerald-500 border-[3px] border-white rounded-xl flex items-center justify-center shadow">
                            <flux:icon name="check" class="size-3 text-white" />
                        </div>
                    @endif
                </div>

                <div class="pb-1">
                    <h2 class="text-2xl md:text-3xl font-black text-zinc-900 tracking-tight leading-tight">{{ $this->profileUser->name }}</h2>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="text-[10px] font-bold text-emerald-600">&#64;{{ $this->profileUser->username }}</span>
                        @if($this->profileUser->is_profile_private)
                            <span class="flex items-center gap-1 text-[8px] font-black uppercase px-2 py-0.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                                <flux:icon name="lock-closed" class="size-2.5" /> Privado
                            </span>
                        @else
                            <span class="flex items-center gap-1 text-[8px] font-black uppercase px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <flux:icon name="globe-alt" class="size-2.5" /> Público
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex items-center gap-2 pb-1 flex-wrap">
                @if($this->isOwnProfile)
                    <button @click="editOpen = true"
                        class="flex items-center gap-2 px-5 h-10 rounded-xl bg-white border border-zinc-200 text-zinc-700 font-black text-[9px] uppercase tracking-widest hover:border-emerald-300 hover:text-emerald-700 hover:bg-emerald-50 transition-all shadow-sm">
                        <flux:icon name="pencil-square" class="size-4" />
                        Editar Perfil
                    </button>
                @else
                    <button wire:click="startConversation({{ $this->profileUser->id }})"
                        class="flex items-center gap-2 px-5 h-10 rounded-xl bg-white border border-zinc-200 text-zinc-700 font-black text-[9px] uppercase tracking-widest hover:border-emerald-300 hover:bg-emerald-50 transition-all shadow-sm">
                        <flux:icon name="chat-bubble-left-right" class="size-4 text-emerald-600" />
                        Mensagem
                    </button>
                    <button wire:click="toggleFollow"
                        class="flex items-center gap-2 px-5 h-10 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all shadow-sm
                            {{ $this->isFollowing
                                ? 'bg-white border border-zinc-200 text-zinc-600 hover:border-red-200 hover:text-red-500 hover:bg-red-50'
                                : 'fcp-btn' }}">
                        <flux:icon name="{{ $this->isFollowing ? 'user-minus' : 'user-plus' }}" class="size-4" />
                        {{ $this->isFollowing ? 'A Seguir' : 'Seguir' }}
                    </button>
                    <button wire:click="copyProfileLink('{{ $this->profileUser->username }}')"
                        class="size-10 rounded-xl bg-white border border-zinc-200 text-zinc-500 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm"
                        title="Partilhar perfil">
                        <flux:icon name="share" class="size-4" />
                    </button>
                @endif
            </div>
        </div>

        @if($this->profileUser->social_bio)
            <p class="mt-4 text-sm text-zinc-600 font-medium leading-relaxed max-w-lg">{{ $this->profileUser->social_bio }}</p>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════
     ESTATÍSTICAS
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-3 mt-4 fcp-in" style="animation-delay:.05s">
    <div class="fcp-card p-5 text-center">
        <p class="text-2xl font-black text-zinc-900">{{ number_format($this->followersCount) }}</p>
        <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-1">Seguidores</p>
    </div>
    <div class="fcp-card p-5 text-center">
        <p class="text-2xl font-black text-zinc-900">{{ number_format($this->followingCount) }}</p>
        <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-1">A Seguir</p>
    </div>
    <div class="fcp-card p-5 text-center">
        <p class="text-2xl font-black text-emerald-600">{{ number_format($this->totalLikesReceived) }}</p>
        <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-1">Likes Totais</p>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     GRELHA DE PUBLICAÇÕES
══════════════════════════════════════════════ --}}
<div
    x-data="{
        modalOpen: false,
        postContent: '',
        postMedia: '',
        postType: '',
        postDate: '',
        postId: null,
        openPost(id, content, media, type, date) {
            this.postId = id; this.postContent = content;
            this.postMedia = media; this.postType = type; this.postDate = date;
            this.modalOpen = true;
            $wire.set('commentingPostId', id);
        }
    }"
    class="space-y-4 mt-4 fcp-in" style="animation-delay:.1s"
>
    <div class="flex items-center gap-2 px-1">
        <div class="size-2 rounded-full bg-emerald-500"></div>
        <h3 class="text-[10px] font-black uppercase tracking-[.3em] text-zinc-400">Publicações</h3>
        <span class="text-[9px] font-bold text-zinc-300">({{ $this->posts->total() }})</span>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        @forelse($this->posts as $post)
            <div
                wire:key="gp-{{ $post->id }}"
                @click="openPost(
                    {{ $post->id }},
                    '{{ str_replace(["'", "\r", "\n"], ["\'", ' ', ' '], strip_tags($post->content ?? '')) }}',
                    '{{ $post->media_path ? Storage::url($post->media_path) : '' }}',
                    '{{ $post->type }}',
                    '{{ $post->created_at->format('d/m/Y \à\s H\hi') }}'
                )"
                class="fcp-grid-item group relative bg-zinc-100"
            >
                @if($post->media_path)
                    @if($post->type === 'video')
                        <video src="{{ Storage::url($post->media_path) }}" class="w-full h-full object-cover pointer-events-none" preload="none"></video>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="size-10 bg-black/50 rounded-full flex items-center justify-center">
                                <flux:icon name="play" variant="solid" class="size-5 text-white" />
                            </div>
                        </div>
                    @else
                        <img src="{{ Storage::url($post->media_path) }}" loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            alt="Post">
                    @endif
                @else
                    <div class="w-full h-full bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center p-4">
                        <p class="text-xs font-bold text-zinc-700 text-center line-clamp-4 italic leading-relaxed">"{{ strip_tags($post->content ?? '') }}"</p>
                    </div>
                @endif

                {{-- Overlay hover --}}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center gap-4 opacity-0 group-hover:opacity-100">
                    <div class="flex items-center gap-1.5 text-white">
                        <flux:icon name="heart" variant="solid" class="size-4" />
                        <span class="text-xs font-black">{{ $post->likes_count }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-white">
                        <flux:icon name="chat-bubble-left" variant="solid" class="size-4" />
                        <span class="text-xs font-black">{{ $post->comments_count }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full fcp-card py-20 text-center border-2 border-dashed border-zinc-100">
                <div class="size-16 rounded-2xl bg-zinc-50 flex items-center justify-center mx-auto mb-4">
                    <flux:icon name="photo" class="size-8 text-zinc-300" />
                </div>
                <p class="text-zinc-400 font-black uppercase tracking-[.3em] text-[10px]">Sem publicações ainda</p>
                @if($this->isOwnProfile)
                    <a href="{{ route('social.hub') }}" wire:navigate
                        class="inline-flex items-center gap-1.5 mt-4 text-[9px] font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-800 transition-colors">
                        <flux:icon name="plus-circle" class="size-3.5" />
                        Criar o teu primeiro post
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Infinite scroll --}}
    @if($this->posts->hasMorePages())
        <div x-intersect.once="$wire.loadMore()" class="flex justify-center py-8">
            <div class="size-7 border-[3px] border-emerald-200 border-t-emerald-500 rounded-full animate-spin"></div>
        </div>
    @endif

    {{-- MODAL DETALHE DO POST (estilo Instagram) --}}
    <div x-show="modalOpen" x-cloak
        @click.self="modalOpen = false"
        class="fixed inset-0 z-[300] flex items-center justify-center p-3 md:p-8 bg-zinc-950/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <div @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-5xl bg-white rounded-[1.75rem] overflow-hidden shadow-2xl flex flex-col md:flex-row fcp-pop"
            style="max-height:90vh"
        >
            {{-- Fechar --}}
            <button @click="modalOpen = false"
                class="absolute top-3 right-3 z-20 size-8 bg-black/40 hover:bg-black/60 text-white rounded-xl flex items-center justify-center transition-all md:hidden">
                <flux:icon name="x-mark" class="size-4 text-white" />
            </button>

            {{-- Media --}}
            <div class="w-full md:w-3/5 bg-zinc-900 flex items-center justify-center min-h-64 md:min-h-0 relative shrink-0">
                <template x-if="postMedia && postType !== 'video'">
                    <img :src="postMedia" class="max-w-full max-h-full object-contain" style="max-height:90vh" alt="Post">
                </template>
                <template x-if="postMedia && postType === 'video'">
                    <video :src="postMedia" controls class="max-w-full max-h-full" style="max-height:90vh"></video>
                </template>
                <template x-if="!postMedia">
                    <div class="p-10 text-center">
                        <p class="text-emerald-400 text-xl font-bold italic leading-relaxed" x-text="'&ldquo;' + postContent + '&rdquo;'"></p>
                    </div>
                </template>
            </div>

            {{-- Info + Comentários --}}
            <div class="flex flex-col bg-white flex-1" style="max-height:90vh">
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-100 shrink-0">
                    <div class="flex items-center gap-3">
                        <flux:avatar src="{{ $this->profileUser->avatarUrl() }}" class="size-9 rounded-xl border border-black/5" />
                        <div>
                            <p class="text-sm font-black text-zinc-900">{{ $this->profileUser->name }}</p>
                            <p class="text-[9px] text-emerald-600 font-bold" x-text="postDate"></p>
                        </div>
                    </div>
                    <button @click="modalOpen = false"
                        class="size-8 rounded-xl bg-zinc-50 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors hidden md:flex">
                        <flux:icon name="x-mark" class="size-4" />
                    </button>
                </div>

                {{-- Conteúdo + Comentários --}}
                <div class="flex-1 overflow-y-auto fcp-scroll p-5 space-y-4 bg-zinc-50/30">
                    <template x-if="postContent && postContent.length > 0">
                        <p class="text-sm text-zinc-700 font-medium leading-relaxed pb-4 border-b border-zinc-100" x-text="postContent"></p>
                    </template>

                    @if($this->commentingPost)
                        @forelse($this->commentingPost->comments as $comment)
                            <div wire:key="mc-{{ $comment->id }}" class="flex gap-2.5 items-start fcp-in">
                                <flux:avatar src="{{ $comment->user->avatarUrl() }}" initials="{{ substr($comment->user->name, 0, 2) }}" class="size-7 rounded-lg border border-black/5 shrink-0" />
                                <div class="flex-1 bg-white rounded-xl px-3.5 py-2.5 shadow-sm border border-zinc-100">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[9px] font-black text-emerald-700 uppercase">{{ $comment->user->name }}</span>
                                        <span class="text-[8px] text-zinc-400">{{ $comment->created_at->diffForHumans(null, true) }}</span>
                                    </div>
                                    <p class="text-xs text-zinc-700 font-medium leading-relaxed">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">Sê o primeiro a comentar</p>
                            </div>
                        @endforelse
                    @endif
                </div>

                {{-- Ações --}}
                <div class="p-4 border-t border-zinc-100 bg-white shrink-0">
                    @if($this->commentingPost)
                        <div class="flex items-center gap-3 mb-3.5">
                            <button wire:click="toggleLike({{ $this->commentingPost->id }})"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl transition-all
                                    {{ $this->commentingPost->likes->contains('user_id', auth()->id())
                                        ? 'text-red-500 bg-red-50'
                                        : 'text-zinc-500 hover:text-red-500 hover:bg-red-50' }}">
                                <flux:icon name="heart" variant="{{ $this->commentingPost->likes->contains('user_id', auth()->id()) ? 'solid' : 'outline' }}" class="size-4" />
                                <span class="text-xs font-black text-zinc-700">{{ $this->commentingPost->likes_count }}</span>
                            </button>
                        </div>
                    @endif

                    <form wire:submit.prevent="postComment({{ $this->commentingPost?->id ?? 'null' }})" class="flex items-center gap-2">
                        <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-7 rounded-lg border border-black/5 shrink-0" />
                        <input wire:model="commentContent" maxlength="280"
                            placeholder="Adiciona um comentário..."
                            class="fcp-input flex-1 py-2.5 px-3.5 text-xs font-medium">
                        <button type="submit" wire:loading.attr="disabled"
                            class="fcp-btn size-9 shrink-0 text-[9px]">
                            <flux:icon name="paper-airplane" class="size-4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: EDITAR PERFIL
══════════════════════════════════════════════ --}}
<div x-show="editOpen" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4">
    <div @click="editOpen = false" class="absolute inset-0 bg-zinc-950/50 backdrop-blur-sm"
        x-transition:enter="transition duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
    </div>

    <div x-show="editOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="relative w-full max-w-lg bg-white rounded-[1.75rem] shadow-2xl border border-zinc-100 overflow-hidden fcp-pop"
    >
        <div wire:loading wire:target="saveProfile"
            class="absolute inset-0 z-50 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center gap-4">
            <div class="size-10 border-[3px] border-emerald-200 border-t-emerald-500 rounded-full animate-spin"></div>
            <p class="text-[10px] text-emerald-600 font-black uppercase tracking-widest">A guardar...</p>
        </div>

        <form wire:submit.prevent="saveProfile" class="flex flex-col max-h-[88vh]">
            <div class="px-7 pt-7 pb-5 border-b border-zinc-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-zinc-900 uppercase italic tracking-tight">Editar Perfil</h2>
                    <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-0.5">Finance Connect</p>
                </div>
                <button type="button" @click="editOpen = false"
                    class="size-8 rounded-xl bg-zinc-50 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors">
                    <flux:icon name="x-mark" class="size-4" />
                </button>
            </div>

            <div class="p-7 space-y-5 overflow-y-auto fcp-scroll flex-1">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0" style="width:4.5rem;height:4.5rem">
                        <div class="size-full rounded-2xl overflow-hidden border-2 border-emerald-100">
                            @if($editAvatarFile)
                                <img src="{{ $editAvatarFile->temporaryUrl() }}" class="size-full object-cover">
                            @else
                                <flux:avatar src="{{ auth()->user()->avatarUrl() }}" class="size-full" />
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="relative cursor-pointer fcp-btn px-4 py-2 inline-flex text-[9px]">
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
                        <input type="text" wire:model="editName" class="fcp-input w-full py-3 px-4 text-sm font-semibold">
                        @error('editName') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Username</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black select-none text-sm">@</span>
                            <input type="text" wire:model="editUsername" class="fcp-input w-full py-3 pl-7 pr-4 text-sm font-semibold">
                        </div>
                        @error('editUsername') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase text-zinc-400 ml-1 block mb-1.5">Bio</label>
                        <textarea wire:model="editBio" maxlength="160" rows="3"
                            placeholder="Fala sobre a tua jornada financeira..."
                            class="fcp-input w-full py-3 px-4 text-sm font-medium resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-zinc-50 rounded-2xl border border-zinc-100">
                        <div class="flex items-center gap-3">
                            <flux:icon name="lock-closed" class="size-4 text-amber-500 shrink-0" />
                            <div>
                                <p class="text-xs font-black text-zinc-800 uppercase tracking-tight">Perfil Privado</p>
                                <p class="text-[9px] text-zinc-400 mt-0.5">Apenas aprovados veem os teus posts</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="editIsPrivate" class="sr-only peer">
                            <div class="w-10 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 peer-focus:ring-2 peer-focus:ring-emerald-300"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-7 py-5 bg-zinc-50/80 flex gap-3 border-t border-zinc-100">
                <button type="button" @click="editOpen = false"
                    class="flex-1 h-11 rounded-xl text-[9px] font-black uppercase text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition-all">
                    Cancelar
                </button>
                <button type="submit" wire:loading.attr="disabled"
                    class="flex-[2] h-11 fcp-btn flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="saveProfile">Guardar</span>
                    <span wire:loading wire:target="saveProfile" class="flex items-center gap-1.5">
                        <svg class="animate-spin size-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        A guardar...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script clipboard --}}
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
