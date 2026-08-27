<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ContactPage extends Component
{
    public $name;

    public $email;

    public $message;

    public $sent = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];

    public function send()
    {
        $this->validate();

        // Aqui podes adicionar lógica de envio de email real no futuro
        // Por agora, apenas simulamos o sucesso para o comprador ver
        $this->sent = true;
        $this->reset(['name', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.public.contact-page');
    }
}
