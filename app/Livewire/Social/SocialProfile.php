<?php

namespace App\Livewire\Social;

use Livewire\Component;
use App\Models\{User, SocialPost, SocialFollow, SocialLike, SocialNotification, SocialComment};
use Livewire\WithFileUploads;
use Livewire\Attributes\{Layout, Computed, On};
use Illuminate\Support\Str;

class SocialProfile extends Component
{
    use WithFileUploads;

    public string $username;

    // --- ESTADO DE EDIÇÃO ---
    public $editName = '';
    public $editUsername = '';
    public $editBio = '';
    public $editAvatarFile = null;
    public $editDefaultVisibility = 'public';
    public $editIsPrivate = false;

    // --- ESTADO DE REPORTE ---
    public $reportModal = false;
    public $reportingPostId = null;
    public $reportReason = '';

    // --- ESTADO DE INTERAÇÃO ---
    public ?int $commentingPostId = null;
    public $commentContent = '';
    public int $perPage = 12;

    public function mount(string $username)
    {
        $this->username = $username;

        if (auth()->user()->username === $username) {
            $user = auth()->user();
            $this->editName = $user->name;
            $this->editUsername = $user->username;
            $this->editBio = $user->social_bio;
            $this->editDefaultVisibility = $user->default_post_visibility ?? 'public';
            $this->editIsPrivate = (bool) $user->is_profile_private;
        }
    }

    /**
     * DADOS DO DONO DO PERFIL
     */
    #[Computed]
    public function profileUser()
    {
        return User::where('username', $this->username)->firstOrFail();
    }

    /**
     * POST ATIVO NO MODAL
     */
    #[Computed]
    public function commentingPost()
    {
        if (!$this->commentingPostId) return null;

        return SocialPost::with([
                'user:id,name,username,avatar_path',
                'comments.user:id,name,username,avatar_path',
                'likes:id,post_id,user_id',
            ])
            ->withCount(['likes', 'comments'])
            ->find($this->commentingPostId);
    }

    /**
     * ESTATÍSTICAS
     */
    #[Computed]
    public function followersCount(): int { return SocialFollow::where('following_id', $this->profileUser->id)->count(); }

    #[Computed]
    public function followingCount(): int { return SocialFollow::where('follower_id', $this->profileUser->id)->count(); }

    #[Computed]
    public function totalLikesReceived(): int
    {
        return SocialLike::whereIn('post_id', SocialPost::where('user_id', $this->profileUser->id)->pluck('id'))->count();
    }

    #[Computed]
    public function isFollowing(): bool
    {
        return SocialFollow::where('follower_id', auth()->id())
            ->where('following_id', $this->profileUser->id)
            ->exists();
    }

    #[Computed]
    public function isOwnProfile(): bool
    {
        return $this->profileUser->id === auth()->id();
    }

    /**
     * MOTOR DA GRELHA (PAGINAÇÃO)
     * O #[Computed] deve estar aqui colado à função!
     */
    #[Computed]
    public function posts()
    {
        $viewerId = auth()->id();
        $workspaceId = auth()->user()->current_workspace_id ?? null;
        $isOwn = $this->profileUser->id === $viewerId;
        $amFollowing = $this->isFollowing;

        $query = SocialPost::with([
                'user:id,name,username,avatar_path',
                'likes:id,post_id,user_id',
            ])
            ->withCount(['likes', 'comments'])
            ->where('user_id', $this->profileUser->id)
            ->where('is_story', false);

        if (!$isOwn) {
            $query->where(function ($q) use ($amFollowing, $workspaceId) {
                $q->where('visibility', 'public')
                  ->orWhere(function ($q2) use ($workspaceId) {
                      $q2->where('visibility', 'workspace')->where('workspace_id', $workspaceId);
                  });

                if ($amFollowing) {
                    $q->orWhere('visibility', 'followers');
                }
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    /**
     * AÇÕES DO PERFIL
     */
    public function loadMore() { $this->perPage += 6; }

    public function toggleFollow()
    {
        if ($this->isOwnProfile) return;
        SocialFollow::toggle(auth()->id(), $this->profileUser->id);
    }

    public function toggleLike($postId)
    {
        $like = SocialLike::where('user_id', auth()->id())->where('post_id', $postId)->first();
        if ($like) {
            $like->delete();
        } else {
            SocialLike::create(['user_id' => auth()->id(), 'post_id' => $postId]);
            $post = SocialPost::find($postId);
            SocialNotification::notify($post->user_id, auth()->id(), 'like', $postId);
        }
    }

    public function postComment($postId = null)
    {
        $postId ??= $this->commentingPostId;

        if (!$postId) {
            return;
        }

        $this->validate(['commentContent' => 'required|min:1|max:280']);

        SocialComment::create([
            'user_id' => auth()->id(),
            'post_id' => $postId,
            'content' => $this->commentContent
        ]);

        $post = SocialPost::find($postId);
        if ($post) {
            SocialNotification::notify($post->user_id, auth()->id(), 'comment', $postId, $this->commentContent);
        }
        $this->commentContent = '';
        $this->dispatch('toast', text: 'Comentário enviado! 🟢');
    }

    public function submitReport()
{
    $this->validate(['reportReason' => 'required|min:10|max:500']);

    \App\Models\SocialReport::create([
        'user_id' => auth()->id(),
        'social_post_id' => $this->reportingPostId,
        'reason' => $this->reportReason,
        'status' => 'pending',
    ]);

    $this->reset(['reportModal', 'reportReason', 'reportingPostId']);
    $this->dispatch('toast', text: 'Denúncia enviada para moderação. 🟢');
}

/**
 * GESTÃO DE PERFIL
 */
public function saveProfile()
{
    if (!$this->isOwnProfile) return;

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
    $this->username = $this->editUsername;
    $this->dispatch('close-edit-profile-modal');
}

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
    return view('livewire.social.social-profile');
}
}
