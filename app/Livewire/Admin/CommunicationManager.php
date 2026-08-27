<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CommunicationManager extends Component
{
    public $title;

    public $message;

    public $type = 'info';

    public $expires_at;

    /**
     * Envia o aviso para a base de dados
     */
    public function send()
    {
        $this->validate([
            'title' => 'required|min:3',
            'message' => 'required',
            'type' => 'required',
        ]);

        DB::table('site_announcements')->insert([
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'expires_at' => $this->expires_at,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->reset(['title', 'message', 'expires_at']);
        $this->dispatch('toast', text: 'Aviso global publicado com sucesso!');
    }

    /**
     * Apaga um aviso
     */
    public function delete($id)
    {
        DB::table('site_announcements')->where('id', $id)->delete();
        $this->dispatch('toast', text: 'Aviso removido.');
    }

    public function render()
    {
        return view('livewire.admin.communication-manager', [
            'announcements' => DB::table('site_announcements')->latest()->get(),
        ]);
    }
}
