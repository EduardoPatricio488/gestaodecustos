<?php

namespace App\Livewire\Public;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

class CareersHub extends Component
{
    public $email, $password, $name;
    public $isRegistering = true;

    #[Layout('layouts.guest')]
    public function authenticate()
    {
        if ($this->isRegistering) {
            $this->validate([
                'name' => 'required|min:3',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
            ]);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'candidate', // Define uma role para saberes que é candidato
            ]);

            Auth::login($user);
        } else {
            $this->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                return redirect()->route('careers.portal');
            }

            session()->flash('error', 'Credenciais inválidas.');
            return;
        }

        return redirect()->route('careers.portal');
    }

    public function render()
    {
        return view('livewire.public.careers-hub');
    }
}
