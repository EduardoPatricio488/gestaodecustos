<?php

namespace App\Livewire\Profile;

use App\Models\AiMemory;
use App\Services\AI\ContextEngine;
use Livewire\Component;

class AiMemorySettings extends Component
{
    public ?string $memoryError = null;

    public function forgetMemory(int $id): void
    {
        $deleted = AiMemory::query()->whereKey($id)->where('user_id', auth()->id())->delete();
        $this->memoryError = $deleted ? null : 'Não foi possível encontrar esta memória.';
        if ($deleted) {
            $this->dispatch('toast', variant: 'success', text: 'Memória apagada.');
        }
    }

    public function clearMemories(): void
    {
        $deleted = AiMemory::query()->where('user_id', auth()->id())->delete();
        $this->memoryError = $deleted ? null : 'Não existem memórias para apagar.';
        if ($deleted) {
            $this->dispatch('toast', variant: 'success', text: 'Toda a memória da IA foi apagada.');
        }
    }

    public function render()
    {
        $workspace = app(ContextEngine::class)->resolveWorkspace(auth()->user());
        $memories = AiMemory::query()->active()->where('user_id', auth()->id())->where(function ($query) use ($workspace) {
            $query->whereNull('workspace_id');
            if ($workspace) {
                $query->orWhere('workspace_id', $workspace->id);
            }
        })->latest()->limit(30)->get();

        $hasMemories = AiMemory::query()->where('user_id', auth()->id())->exists();

        return view('livewire.profile.ai-memory-settings', compact('memories', 'hasMemories'));
    }
}
