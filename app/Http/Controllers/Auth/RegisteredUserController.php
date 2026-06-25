<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerifyAccountMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
   public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    $verificationCode = rand(100000, 999999);

   $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'verification_code' => $verificationCode,
    ]);

    Auth::login($user);

    try {
        // Tenta enviar o e-mail
        Mail::to($user->email)->send(new VerifyAccountMail($verificationCode));
    } catch (\Exception $e) {
        // Se falhar, ele vai mostrar o erro no ecrã em vez de ficar parado
        dd("O e-mail falhou! Erro: " . $e->getMessage());
    }

    return redirect()->route('verification.notice');
}
}
