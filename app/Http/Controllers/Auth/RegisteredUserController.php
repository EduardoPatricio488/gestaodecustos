<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyAccountMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $verificationCode = (string) random_int(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => null,
            'verification_code_hash' => hash('sha256', $verificationCode),
            'verification_code_expires_at' => now()->addMinutes(10),
            'verification_code_attempts' => 0,
        ]);

        Auth::login($user);

        try {
            Mail::to($user->email)->send(new VerifyAccountMail($verificationCode));
        } catch (\Throwable $e) {
            report($e);
            $user->update([
                'verification_code_hash' => null,
                'verification_code_expires_at' => null,
                'verification_code_attempts' => 0,
            ]);

            return redirect()->back()->withErrors([
                'email' => 'Não foi possível enviar a verificação. Tenta novamente.',
            ]);
        }

        return redirect()->route('verification.notice');
    }
}
