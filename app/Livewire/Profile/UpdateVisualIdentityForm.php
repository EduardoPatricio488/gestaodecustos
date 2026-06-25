<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UpdateVisualIdentityForm extends Component
{
    public $profile_emoji;
    public $profile_color;
    public $custom_emoji = '';

    public function mount()
    {
        $user = Auth::user();
        $this->profile_emoji = $user->profile_emoji ?? '😎';
        $this->profile_color = $user->profile_color ?? '#6366f1';
    }

    public function applyCustomEmoji()
    {
        $emoji = trim($this->custom_emoji);

        if ($emoji === '') {
            return;
        }

        $this->updateIdentity('emoji', $emoji);
        $this->custom_emoji = '';
    }

    public function updateIdentity($type, $value)
    {
        $user = Auth::user();

        if ($type === 'emoji') {
            $this->profile_emoji = $value;
            $user->update(['profile_emoji' => $value]);
        }

        if ($type === 'color') {
            $this->profile_color = $value;
            $user->update(['profile_color' => $value]);
        }

        $this->dispatch('toast', text: 'Identidade visual atualizada!');
    }

    public function render()
    {
        return view('livewire.profile.update-visual-identity-form');
    }
}
