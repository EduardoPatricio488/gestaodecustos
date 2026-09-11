<?php

namespace App\Livewire;

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

            // Force the current page/components to refresh their server-side data
            // after the AI writes to the database.
            $this->dispatch('finance-pro-data-changed', actionId: $actionId);
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

        $latest = $conversation->messages()
            ->where('role', 'assistant')
            ->latest('id')
            ->first();

        $metadataActions = $latest?->metadata['pending_actions'] ?? [];
        if (is_array($metadataActions)) {
            $this->pendingActions = array_values($metadataActions);
        }
    }

    public function render()
    {
        return view('livewire.ai-copilot');
    }
}
