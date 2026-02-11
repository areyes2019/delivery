<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();
        // 🔐 Solo ciertos roles pueden entrar
        if (!in_array($user->rol, [
            UserRole::DESPACHADOR,
            UserRole::ADMIN_CLIENTE,
            UserRole::SUPER_ADMIN,
            UserRole::DRIVER,
            ])) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'No tienes acceso al dashboard',
            ]);
        }
        
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
