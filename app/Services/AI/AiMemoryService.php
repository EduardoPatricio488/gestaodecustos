<?php

namespace App\Services\AI;

use App\Models\AiMemory;
use App\Models\User;
use App\Models\Workspace;

final class AiMemoryService
{
    public function remember(User $user, Workspace $workspace, string $type, string $key, string $value, int $importance = 50, ?string $expiresAt = null): AiMemory
    {
        $this->assertWorkspaceAccess($user, $workspace);

        return AiMemory::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'key' => $key,
            ],
            [
                'type' => $type,
                'value' => $value,
                'importance' => max(0, min(100, $importance)),
                'expires_at' => $expiresAt,
                'last_used_at' => now(),
            ]
        );
    }

    public function relevant(User $user, Workspace $workspace, ?string $type = null, int $limit = 20)
    {
        $this->assertWorkspaceAccess($user, $workspace);

        return AiMemory::query()
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->when($type, fn ($query) => $query->where('type', $type))
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('importance')
            ->orderByDesc('last_used_at')
            ->limit($limit)
            ->get();
    }

    public function forget(User $user, Workspace $workspace, int $memoryId): bool
    {
        $this->assertWorkspaceAccess($user, $workspace);

        return (bool) AiMemory::query()
            ->whereKey($memoryId)
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->delete();
    }

    private function assertWorkspaceAccess(User $user, Workspace $workspace): void
    {
        abort_unless(
            $workspace->users()->whereKey($user->id)->exists(),
            403,
            'Sem acesso a este workspace.'
        );
    }
}
