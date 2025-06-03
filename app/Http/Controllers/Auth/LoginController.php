<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        logout as protected traitLogout;
    }

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
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
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('Error en logout:', [
                'message' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al cerrar sesión');
        }
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