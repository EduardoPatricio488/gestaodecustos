<?php

namespace App\Livewire;

use App\Models\AiActionLog;
use App\Models\AiConversation;
use App\Models\Workspace;
use App\Services\AI\AiBrainService;
use App\Services\AI\ContextEngine;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AiCopilot extends Component
{
    public bool $isOpen = false;
    public string $input = '';
    public array $messages = [];
    public array $pendingActions = [];
    public array $conversations = [];
    public array $pageContext = [];
    public ?int $conversationId = null;
    public bool $isLoading = false;
    private ?int $loadedWorkspaceId = null;

    public function mount(ContextEngine $contextEngine): void
    {
        $user = Auth::user();
        $workspace = $contextEngine->resolveWorkspace($user);

        $this->pageContext = [
            'module' => request()->route()?->getName(),
            'route' => request()->route()?->getName(),
            'path' => request()->path(),
            'period' => request()->query('period'),
            'offline_status' => 'online',
        ];

        $this->loadWorkspaceConversation($user->id, $workspace);
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function setPageContext(array $context): void
    {
        $allowedKeys = [
            'module', 'route', 'path', 'entity', 'entity_type', 'action', 'filters',
            'period', 'state', 'offline_status', 'pending_offline_items',
        ];

        $this->pageContext = array_merge(
            $this->pageContext,
            array_intersect_key($context, array_flip($allowedKeys))
        );
    }

    public function sendMessage(AiBrainService $brain): void
    {
        $input = trim($this->input);
        if ($input === '' || $this->isLoading) return;

        $this->isLoading = true;

        try {
            $user = Auth::user();
            $workspace = app(ContextEngine::class)->resolveWorkspace($user);
            if (! $workspace) throw new \RuntimeException('Workspace inválido.');

            if ($this->loadedWorkspaceId !== $workspace->id) {
                $this->loadWorkspaceConversation($user->id, $workspace);
            }

            $conversation = $this->conversation($brain, $workspace);
            $result = $brain->chat($user, $input, $conversation, $this->pageContext);
            $this->conversationId = $result['conversation_id'];
            $this->pendingActions = $result['pending_actions'] ?? [];
            $this->loadMessages($conversation->fresh());
            $this->refreshConversations();
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
        if ($this->isLoading) return;

        $this->isLoading = true;

        try {
            $result = $brain->confirm(Auth::user(), $actionId);
            $message = (string) ($result['message'] ?? 'Registo criado e guardado com sucesso na aplicação.');

            $this->messages[] = [
                'role' => 'assistant',
                'content' => '✅ '.$message,
            ];

            $this->pendingActions = array_values(array_filter(
                $this->pendingActions,
                fn (array $action) => (int) ($action['id'] ?? 0) !== $actionId
            ));

            $this->dispatch('finance-pro-data-changed', actionId: $actionId);
            $this->refreshConversations();
        } catch (\Throwable $e) {
            report($e);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => '❌ Não consegui executar o registo: '.$e->getMessage(),
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
        $this->input = '';
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = AiConversation::query()
            ->whereKey($conversationId)
            ->where('user_id', Auth::id())
            ->where('workspace_id', $this->loadedWorkspaceId)
            ->first();

        if (! $conversation) return;

        $this->conversationId = $conversation->id;
        $this->loadMessages($conversation);
        $this->input = '';
    }

    public function archiveConversation(): void
    {
        if (! $this->conversationId) return;

        AiConversation::query()
            ->whereKey($this->conversationId)
            ->where('user_id', Auth::id())
            ->where('workspace_id', $this->loadedWorkspaceId)
            ->update(['archived_at' => now()]);

        $this->newConversation();
        $this->refreshConversations();
    }

    private function conversation(AiBrainService $brain, Workspace $workspace): AiConversation
    {
        return $brain->conversation(Auth::user(), $workspace, $this->conversationId);
    }

    private function loadWorkspaceConversation(int $userId, ?Workspace $workspace): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->pendingActions = [];
        $this->conversations = [];
        $this->loadedWorkspaceId = $workspace?->id;

        if (! $workspace) return;

        $conversation = AiConversation::query()
            ->where('user_id', $userId)
            ->where('workspace_id', $workspace->id)
            ->whereNull('archived_at')
            ->latest('last_activity_at')
            ->first();

        if ($conversation) {
            $this->conversationId = $conversation->id;
            $this->loadMessages($conversation);
        }

        $this->refreshConversations();
    }

    private function refreshConversations(): void
    {
        if (! $this->loadedWorkspaceId) {
            $this->conversations = [];
            return;
        }

        $this->conversations = AiConversation::query()
            ->where('user_id', Auth::id())
            ->where('workspace_id', $this->loadedWorkspaceId)
            ->whereNull('archived_at')
            ->withCount('messages')
            ->latest('last_activity_at')
            ->limit(30)
            ->get()
            ->map(fn (AiConversation $conversation) => [
                'id' => $conversation->id,
                'title' => $conversation->title ?: 'Nova conversa',
                'messages_count' => $conversation->messages_count,
                'last_activity_at' => optional($conversation->last_activity_at)->format('d/m H:i'),
            ])
            ->values()
            ->all();
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

        $this->pendingActions = AiActionLog::query()
            ->where('user_id', Auth::id())
            ->where('workspace_id', $this->loadedWorkspaceId)
            ->where('status', 'awaiting_confirmation')
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn (AiActionLog $action) => [
                'id' => $action->id,
                'token' => $action->confirmation_token,
                'tool' => $action->tool_name,
                'title' => $action->result_payload['title'] ?? 'Confirmar ação',
                'summary' => $action->result_payload['summary'] ?? $action->tool_name,
                'details' => $action->result_payload['details'] ?? [],
            ])->values()->all();
    }

    public function render()
    {
        return view('livewire.ai-copilot');
    }
}
