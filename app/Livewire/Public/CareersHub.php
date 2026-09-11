<?php

namespace App\Livewire\Public;

use App\Models\Candidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CareersHub extends Component
{
    public $email;

    public $password;

    public $name;

    public $isRegistering = true;

    #[Layout('layouts.guest')]
    public function authenticate()
    {
        $guard = Auth::guard('candidate');

        if ($this->isRegistering) {
            $this->validate([
                'name' => 'required|string|min:3|max:120',
                'email' => 'required|email|max:255|unique:candidates,email',
                'password' => 'required|string|min:8',
            ]);

            $candidate = Candidate::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $guard->login($candidate);
        } else {
            $this->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (! $guard->attempt(['email' => $this->email, 'password' => $this->password])) {
                session()->flash('error', 'Credenciais inválidas.');

                return;
            }
        }

        $this->reset(['password']);

        return redirect()->route('careers.apply');
    }

    public function logout()
    {
        Auth::guard('candidate')->logout();

        return redirect('/');
    }

    public function render()
    {
        if (Auth::guard('candidate')->check()) {
            return view('livewire.public.candidate-portal');
        }

        return view('livewire.public.careers-hub');
    }
}
