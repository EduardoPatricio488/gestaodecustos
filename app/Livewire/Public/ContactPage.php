<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

#[Layout('layouts.guest')]
class ContactPage extends Component
{
    public $name;

    public $email;

    public $message;

    public $privacyConsent = false;

    public $sent = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
        'privacyConsent' => 'accepted',
    ];

    public function send()
    {
        $key = 'contact:'.strtolower((string) $this->email).':'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Foram enviados demasiados pedidos. Tenta novamente mais tarde.');
            return;
        }
        RateLimiter::hit($key, 600);

        $this->validate();

        // Aqui podes adicionar lógica de envio de email real no futuro
        // Por agora, apenas simulamos o sucesso para o comprador ver
        $this->sent = true;
        $this->reset(['name', 'email', 'message', 'privacyConsent']);
    }

    public function render()
    {
        return view('livewire.public.contact-page');
    }
}
