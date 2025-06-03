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

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                if (!Auth::user()->status) {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => 'Tu cuenta está desactivada.',
                    ]);
                }

                $request->session()->regenerate();
                return $this->redirectBasedOnRole();
            }

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en login:', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            throw ValidationException::withMessages([
                'email' => 'Error al iniciar sesión. Por favor intente de nuevo.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/'); // Redirigimos a la página principal en lugar de login
        } catch (\Exception $e) {
            \Log::error('Error en logout:', [
                'message' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Error al cerrar sesión');
        }
    }

    protected function redirectBasedOnRole()
    {
        if (Auth::user()->role === 'Administrador') {
            return redirect()->route('admin.panel');
        } elseif (Auth::user()->role === 'Desarrollador') {
            return redirect()->route('dev.panel');
        } elseif (Auth::user()->role === 'Analista') {
            return redirect()->route('analista.panel');
        }
        
        return redirect()->route('dashboard');
    }
}