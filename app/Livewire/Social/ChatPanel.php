<?php

namespace App\Livewire\Social;

use Livewire\Component;
use App\Models\User;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class ChatPanel extends Component
{
    public bool $open = false;

    public bool $showLauncher = true;

    public ?int $activeConversationId = null;

    public string $newMessage = '';

    public string $userSearch = '';

    public bool $showNewChat = false;

    public array $selectedUserIds = [];

    public string $groupName = '';

    public function mount(bool $showLauncher = true): void
    {
        $this->showLauncher = $showLauncher;
    }

    /**
     * Conversas do utilizador atual, ordenadas pela mais recente atividade
     */
    #[Computed]
    public function conversations()
    {
        return auth()->user()->chatConversations()
            ->with(['users', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->get()
            ->sortByDesc(fn ($c) => optional($c->messages->first())->created_at ?? $c->created_at)
            ->values();
    }

    #[Computed]
    public function activeConversation()
    {
        if (!$this->activeConversationId) {
            return null;
        }

        return ChatConversation::with(['users', 'messages.user'])
            ->find($this->activeConversationId);
    }
#[On('open-chat-with')]
public function openDirectChatWith($userId)
{
    $this->open = true;      // Ativa no servidor
    $this->showNewChat = false;
    $this->startDirectChat($userId); // Abre a conversa com a pessoa
}
    #[Computed]
public function conversationMessages()
{
    if (!$this->activeConversation) {
        return collect();
    }

    return $this->activeConversation->messages()->with('user')->oldest()->get();
}

    /**
     * Resultados de pesquisa de utilizadores para iniciar novo chat
     */
    #[Computed]
    public function searchResults()
    {
        if (trim($this->userSearch) === '') {
            return collect();
        }

        return User::where('id', '!=', auth()->id())
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->userSearch.'%')
                  ->orWhere('username', 'like', '%'.$this->userSearch.'%');
            })
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function totalUnreadCount(): int
    {
        $total = 0;

        foreach ($this->conversations as $conversation) {
            $lastMessage = $conversation->messages->first();
            if (!$lastMessage) {
                continue;
            }

            $pivot = $conversation->users->firstWhere('id', auth()->id())?->pivot;
            $lastRead = $pivot?->last_read_at;

            if ($lastMessage->user_id !== auth()->id() && (!$lastRead || $lastMessage->created_at->gt($lastRead))) {
                $total++;
            }
        }

        return $total;
    }

    public function toggleOpen()
    {
        $this->open = !$this->open;
    }

    public function openConversation(int $conversationId)
    {
        $this->activeConversationId = $conversationId;
        $this->showNewChat = false;
        $this->markAsRead();
    }

    public function backToList()
    {
        $this->activeConversationId = null;
    }

    public function markAsRead()
    {
        if (!$this->activeConversation) {
            return;
        }

        $this->activeConversation->users()->updateExistingPivot(auth()->id(), [
            'last_read_at' => now(),
        ]);
    }

    /**
     * Iniciar (ou abrir, se já existir) uma conversa 1-para-1
     */
    public function startDirectChat(int $userId)
    {
        $existing = ChatConversation::findDirectBetween(auth()->id(), $userId);

        if ($existing) {
            $this->openConversation($existing->id);
            return;
        }

        $conversation = ChatConversation::create([
            'is_group'   => false,
            'created_by' => auth()->id(),
        ]);

        $conversation->users()->attach([auth()->id(), $userId]);

        $this->openConversation($conversation->id);
    }

    /**
     * Iniciar um novo grupo com os utilizadores selecionados
     */
    public function startGroupChat()
    {
        $this->validate([
            'groupName'       => 'required|string|max:50',
            'selectedUserIds' => 'required|array|min:1',
        ]);

        $conversation = ChatConversation::create([
            'name'       => $this->groupName,
            'is_group'   => true,
            'created_by' => auth()->id(),
        ]);

        $userIds = array_unique(array_merge($this->selectedUserIds, [auth()->id()]));
        $conversation->users()->attach($userIds);

        $this->reset(['groupName', 'selectedUserIds', 'userSearch']);
        $this->openConversation($conversation->id);
    }

    public function toggleSelectedUser(int $userId)
    {
        if (in_array($userId, $this->selectedUserIds)) {
            $this->selectedUserIds = array_values(array_diff($this->selectedUserIds, [$userId]));
        } else {
            $this->selectedUserIds[] = $userId;
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        if (!$this->activeConversation) {
            return;
        }

        ChatMessage::create([
            'chat_conversation_id' => $this->activeConversationId,
            'user_id'              => auth()->id(),
            'content'              => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->markAsRead();
    }

    public function render()
    {
        return view('livewire.social.chat-panel');
    }

}
