<?php

namespace App\Livewire\Social;

use Livewire\Component;
use App\Models\{SocialPost, SocialFollow, SocialLike, SocialComment, SocialNotification, User};
use Livewire\WithFileUploads;
use Livewire\Attributes\{Layout, Computed, Url, On};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SocialHub extends Component
{
    use WithFileUploads;

    // --- PUBLICADOR DE CONTEÚDO ---
    public $newPostContent = '';
    public $mediaFile;
    public $visibility = 'public'; // public, followers, workspace, private

    // --- INTERAÇÕES ---
    public $commentingPostId = null;
    public $commentContent = '';

    // --- EDIÇÃO DE PERFIL (Mantido exatamente como tinhas) ---
    public $editName = '';
    public $reportModal = false;
public $reportingPostId = null;
public $reportReason = '';
    public $editUsername = '';
    public $editBio = '';
    public $editAvatarFile = null;
    public $editDefaultVisibility = 'public';
    public $editIsPrivate = false;

    // --- FILTROS E PESQUISA (Refletidos no URL para navegação fluida) ---
    #[Url(as: 'filtro')]
    public string $feedFilter = 'todos'; // todos, seguir, workspace

    #[Url(as: 'tag')]
    public ?string $tag = null;

    #[Url(as: 'q')]
    public string $search = '';

    // --- PAGINAÇÃO PARA PERFORMANCE ---
    public int $perPage = 10;

    /**
     * MOTOR DO FEED: Filtra quem pode ver o quê em tempo real.
     * Implementa lógica de visibilidade complexa (Público, Seguidores, Workspace).
     */
    #[Computed]
    public function posts()
    {
        $userId = auth()->id();
        $workspaceId = auth()->user()->current_workspace_id;
        $followingIds = SocialFollow::where('follower_id', $userId)->pluck('following_id');

        $query = SocialPost::with([
                'user:id,name,username,avatar_path',
                'likes:id,post_id,user_id',
                'comments.user:id,name,username,avatar_path',
            ])
            ->withCount(['likes', 'comments'])
            ->where('is_story', false) // Foco total em posts reais (stories removidos)
            ->where(function ($query) use ($userId, $workspaceId, $followingIds) {
                $query->where('visibility', 'public') // Visível a todos
                    ->orWhere('user_id', $userId) // Próprio autor vê sempre os seus
                    ->orWhere(function ($q) use ($followingIds) { // Só para seguidores
                        $q->where('visibility', 'followers')->whereIn('user_id', $followingIds);
                    })
                    ->orWhere(function ($q) use ($workspaceId) { // Só para membros do workspace
                        $q->where('visibility', 'workspace')->where('workspace_id', $workspaceId);
                    });
            });

        // Filtro de Tabs (Navegação Superior)
        if ($this->feedFilter === 'seguir') {
            $query->whereIn('user_id', $followingIds->push($userId));
        } elseif ($this->feedFilter === 'workspace') {
            $query->where('workspace_id', $workspaceId);
        }

        // Filtros Ativos (Hashtags e Pesquisa Livre)
        if ($this->tag) {
            $query->withHashtag($this->tag);
        }

        if ($this->search !== '') {
            $query->where('content', 'like', '%' . $this->search . '%');
        }

        // Retorna paginado para permitir carregamento infinito fluido
        return $query->latest()->paginate($this->perPage);
    }

    /**
     * HASHTAGS EM TENDÊNCIA: Calculadas a partir dos posts recentes
     */
    #[Computed]
    public function trendingHashtags()
    {
        return Cache::remember('trending_hashtags', 600, function () {
            $recentPosts = SocialPost::where('is_story', false)
                ->where('created_at', '>=', now()->subDays(14))
                ->whereNotNull('content')
                ->latest()
                ->limit(300)
                ->pluck('content');

            $counts = [];
            foreach ($recentPosts as $content) {
                preg_match_all('/#([\p{L}0-9_]+)/u', $content, $matches);
                foreach ($matches[1] ?? [] as $tag) {
                    $key = Str::lower($tag);
                    $counts[$key] = ($counts[$key] ?? 0) + 1;
                }
            }

            arsort($counts);

            return collect($counts)->take(6)->map(fn ($count, $tag) => [
                'tag' => $tag,
                'count' => $count,
            ])->values()->all();
        });
    }

    /**
     * SUGESTÕES: Utilizadores que ainda não segues
     */
    #[Computed]
    public function suggestedUsers()
    {
        $userId = auth()->id();
        $followingIds = SocialFollow::where('follower_id', $userId)->pluck('following_id');

        return User::select('id', 'name', 'username', 'avatar_path')
            ->where('id', '!=', $userId)
            ->whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->take(3)
            ->get();
    }

    /**
     * NOTIFICAÇÕES: Últimos alertas do utilizador
     */
    #[Computed]
    public function notifications()
    {
        return SocialNotification::with('actor', 'post')
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(15)
            ->get();
    }

    #[Computed]
    public function unreadNotificationsCount()
    {
        return SocialNotification::where('user_id', auth()->id())->unread()->count();
    }

    /**
     * PERFIL: Carrega os dados atuais para o modal de edição.
     */
    public function openEditProfile()
    {
        $user = auth()->user();
        $this->editName = $user->name;
        $this->editUsername = $user->username;
        $this->editBio = $user->social_bio;
        $this->editDefaultVisibility = $user->default_post_visibility ?? 'public';
        $this->editIsPrivate = (bool) $user->is_profile_private;
        $this->editAvatarFile = null;

        $this->dispatch('modal-show', name: 'edit-profile');
    }

    /**
     * PERFIL: Valida e guarda as alterações do perfil.
     */
    public function saveProfile()
    {
        $user = auth()->user();
        $this->validate([
            'editName'      => 'required|string|max:60',
            'editUsername'  => 'required|alpha_dash|max:30|unique:users,username,'.$user->id,
            'editBio'       => 'nullable|string|max:160',
            'editAvatarFile'=> 'nullable|image|max:5120',
            'editDefaultVisibility' => 'required|in:public,followers,workspace,private',
            'editIsPrivate' => 'boolean',
        ]);

        $data = [
            'name'      => $this->editName,
            'username'  => $this->editUsername,
            'social_bio'=> $this->editBio,
            'default_post_visibility' => $this->editDefaultVisibility,
            'is_profile_private'      => $this->editIsPrivate,
        ];

        if ($this->editAvatarFile) {
            $data['avatar_path'] = $this->editAvatarFile->store('social/avatars', 'public');
        }

        $user->update($data);
        $this->dispatch('modal-close', name: 'edit-profile');
        $this->dispatch('toast', text: 'Perfil atualizado com sucesso! ✨');
    }

    /**
     * PUBLICAÇÃO: Cria um novo post (Texto, Imagem ou Vídeo).
     */
    public function createPost()
    {
        $this->validate([
            'newPostContent' => 'required_without:mediaFile|max:1000',
            'mediaFile'      => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:30720', // Limite de 30MB
        ]);

        $path = null;
        $type = 'text';

        if ($this->mediaFile) {
            $path = $this->mediaFile->store('social/posts', 'public');
            $type = Str::contains($this->mediaFile->getMimeType(), 'video') ? 'video' : 'image';
        }

        SocialPost::create([
            'user_id'      => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'type'         => $type,
            'content'      => $this->newPostContent,
            'media_path'   => $path,
            'visibility'   => $this->visibility,
            'is_story'     => false,
        ]);

        $this->reset(['newPostContent', 'mediaFile']);
        $this->dispatch('post-created'); // Trigger para animações Liquid Glass no Blade
        $this->dispatch('toast', text: 'Publicado no Finance Connect! 🌍');
    }

    /**
     * INTERAÇÃO: Gostar/Retirar gosto e notificar o autor.
     */
    public function toggleLike($postId)
    {
        $like = SocialLike::where('user_id', auth()->id())->where('post_id', $postId)->first();

        if ($like) {
            $like->delete();
        } else {
            SocialLike::create(['user_id' => auth()->id(), 'post_id' => $postId]);
            $post = SocialPost::find($postId);
            if ($post) {
                SocialNotification::notify($post->user_id, auth()->id(), 'like', $postId);
            }
        }
    }

    /**
     * INTERAÇÃO: Publicar um comentário e notificar o autor.
     */
    public function postComment($postId)
    {
        $this->validate(['commentContent' => 'required|min:1|max:280']);

        SocialComment::create([
            'user_id' => auth()->id(),
            'post_id' => $postId,
            'content' => $this->commentContent,
        ]);

        $post = SocialPost::find($postId);
        if ($post) {
            SocialNotification::notify($post->user_id, auth()->id(), 'comment', $postId, $this->commentContent);
        }

        $this->reset(['commentContent', 'commentingPostId']);
        $this->dispatch('toast', text: 'Comentário enviado! ✨');
    }

    /**
     * NAVEGAÇÃO: Carregar mais posts (Infinite Scroll).
     * Essencial para manter a fluidez "Liquid" sem recarregar a página.
     */
    public function loadMore()
    {
        $this->perPage += 10;
    }

    /**
     * NAVEGAÇÃO: Define o filtro principal (Para Ti, A Seguir, Workspace).
     */
    public function setFilter(string $filter)
    {
        $this->feedFilter = $filter;
        $this->tag = null;
        $this->perPage = 10; // Reset da paginação para velocidade
    }

    /**
     * NAVEGAÇÃO: Filtra o feed por uma Hashtag específica.
     */
    public function filterByTag(string $tag)
    {
        $this->tag = $tag;
        $this->feedFilter = 'todos';
        $this->perPage = 10;
    }

    public function clearTag()
    {
        $this->tag = null;
    }

    /**
     * SOCIAL: Seguir ou Deixar de seguir um utilizador.
     */
    public function toggleFollow(int $userId)
    {
        SocialFollow::toggle(auth()->id(), $userId);
        $this->dispatch('refresh-suggested'); // Atualiza a lista lateral
    }

    /**
     * UTILITÁRIOS: Copiar link e Reportar.
     */
    public function copyPostLink($postId)
    {
        $url = route('social.hub') . '?post=' . $postId;
        $this->dispatch('copy-to-clipboard', text: $url);
        $this->dispatch('toast', text: 'Link copiado para a área de transferência! 🔗');
    }

    public function openReportModal($postId)
{
    $this->reportingPostId = $postId;
    $this->reportModal = true;
}

public function submitReport()
{
    $this->validate(['reportReason' => 'required|min:10|max:500']);

    // GRAVA A DENÚNCIA PARA O ADMIN VER
    \App\Models\SocialReport::create([
        'user_id' => auth()->id(),            // Quem denuncia
        'social_post_id' => $this->reportingPostId, // O post denunciado
        'reason' => $this->reportReason,      // O motivo escrito
        'status' => 'pending',                // Estado inicial
    ]);

    $this->reset(['reportModal', 'reportReason', 'reportingPostId']);
    $this->dispatch('toast', text: 'Denúncia enviada com sucesso. A nossa equipa irá analisar. 🛡️');
}

    /**
     * UI: Ativa/Desativa a caixa de comentário de um post específico.
     */


    public function setCommenting($postId)
    {
        $this->commentingPostId = ($this->commentingPostId === $postId) ? null : $postId;
        if ($this->commentingPostId) {
            $this->commentContent = '';
        }
    }

    /**
     * NOTIFICAÇÕES: Marca todas como lidas ao abrir o painel.
     */
    public function markNotificationsRead()
    {
        SocialNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * RENDERIZAÇÃO: Liga o componente ao layout principal.
     */
/**
 * PERFIL: Iniciar conversa com o utilizador do perfil.
 */
public function startConversation(int $userId)
{
    $this->dispatch('open-chat-with', userId: $userId);
}

/**
 * PERFIL: Copiar link do perfil.
 */
public function copyProfileLink(string $username)
{
    $url = route('social.profile', $username);
    $this->dispatch('copy-to-clipboard', text: $url);
    $this->dispatch('toast', text: 'Link do perfil copiado! 🔗');
}


    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.social.social-hub');
    }
}
