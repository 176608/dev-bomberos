<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    /*public function __construct()
    {
        // Corregimos la sintaxis del middleware
        $this->middleware('guest')->except('logout');
    }*/

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        if (!$user->status) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta está desactivada.',
            ]);
        }

        $request->session()->regenerate();
        return $this->redirectBasedOnRole();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if ($user->role === 'Administrador') {
            return redirect()->route('admin.panel');
        } elseif ($user->role === 'Desarrollador') {
            return redirect()->route('dev.panel');
        } elseif ($user->role === 'Analista') {
            return redirect()->route('analista.panel');
        }

        return redirect()->route('dashboard');
    }
}