<?php

namespace App\Livewire;

use App\Models\AiConversation;
use App\Services\AI\AiBrainService;
use App\Services\AI\ContextEngine;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class AiCopilot extends Component
{
    public bool $isOpen = false;

    public string $input = '';

    public array $messages = [];

    public array $pendingActions = [];

    public array $pageContext = [];

    public ?int $conversationId = null;

    public bool $isLoading = false;

    public function mount(ContextEngine $contextEngine): void
    {
        $user = Auth::user();
        $workspace = $contextEngine->resolveWorkspace($user);

        $this->pageContext = [
            'module' => request()->route()?->getName(),
            'route' => request()->route()?->getName(),
            'period' => request()->query('period'),
        ];

        if (! $workspace) {
            return;
        }

        $conversation = AiConversation::query()
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->whereNull('archived_at')
            ->latest('last_activity_at')
            ->first();

        if ($conversation) {
            $this->conversationId = $conversation->id;
            $this->loadMessages($conversation);
        }
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function setPageContext(array $context): void
    {
        $this->pageContext = array_merge($this->pageContext, array_intersect_key($context, array_flip([
            'module', 'route', 'entity', 'entity_type', 'action', 'filters', 'period', 'state',
        ])));
    }

    public function sendMessage(AiBrainService $brain): void
    {
        $input = trim($this->input);
        if ($input === '' || $this->isLoading) {
            return;
        }

        $this->isLoading = true;

        try {
            $conversation = $this->conversation($brain);
            $result = $brain->chat(Auth::user(), $input, $conversation, $this->pageContext);
            $this->conversationId = $result['conversation_id'];
            $this->pendingActions = $result['pending_actions'] ?? [];
            $this->loadMessages($conversation->fresh());
            $this->input = '';
        } catch (\Throwable $e) {
            report($e);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Não consegui concluir o pedido neste momento. Não alterei os teus dados.',
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function confirmAction(int $actionId, AiBrainService $brain): void
    {
        $this->isLoading = true;

        try {
            $result = $brain->confirm(Auth::user(), $actionId);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => ! empty($result['success'])
                    ? 'Ação executada com sucesso. Os dados foram atualizados. ✅'
                    : 'A ação terminou sem confirmação de sucesso.',
            ];

            $this->pendingActions = array_values(array_filter(
                $this->pendingActions,
                fn (array $action) => (int) ($action['id'] ?? 0) !== $actionId
            ));
        } catch (\Throwable $e) {
            report($e);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Não consegui executar esta ação. O estado dos teus dados foi mantido.',
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->pendingActions = [];
    }

    public function archiveConversation(): void
    {
        if (! $this->conversationId) {
            return;
        }

        AiConversation::query()
            ->whereKey($this->conversationId)
            ->where('user_id', Auth::id())
            ->update(['archived_at' => now()]);

        $this->newConversation();
    }

    #[On('ai-page-context')]
    public function receivePageContext(array $context): void
    {
        $this->setPageContext($context);
    }

    private function conversation(AiBrainService $brain): AiConversation
    {
        $workspace = app(ContextEngine::class)->resolveWorkspace(Auth::user());
        return $brain->conversation(Auth::user(), $workspace, $this->conversationId);
    }

    private function loadMessages(AiConversation $conversation): void
    {
        $this->messages = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->oldest('id')
            ->limit(60)
            ->get()
            ->map(fn ($message) => [
                'id' => $message->id,
                'role' => $message->role,
                'content' => $message->content,
                'metadata' => $message->metadata,
            ])->values()->all();

        $metadataActions = $conversation->messages()
            ->where('role', 'assistant')
            ->latest('id')
            ->first()?->metadata['pending_actions'] ?? [];

        if (is_array($metadataActions)) {
            $this->pendingActions = array_values($metadataActions);
        }
    }

    public function render()
    {
        return view('livewire.ai-copilot');
    }
}
