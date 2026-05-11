<?php

namespace App\Http\Controllers\Bomberos\Auth;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\Bomberos\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

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

        $key = 'login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Demasiados intentos. Intenta de nuevo en ' . ceil($seconds / 60) . ' minuto(s).']);
        }

        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && in_array($user->log_in_status, [1, 2])) {
            if (!Hash::check($credentials['password'], $user->password)) {
                RateLimiter::hit($key, 300);
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => 'Credenciales incorrectas.']);
            }

            if (!$user->initial_token) {
                RateLimiter::clear($key);
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => 'Tu cuenta requiere configuración. Contacta al administrador para generar un PIN de acceso.']);
            }

            RateLimiter::clear($key);
            
            session([
                'email_for_reset' => $credentials['email'],
                'pin_verified_for_reset' => true,
                'pin_verified_user_id' => $user->id
            ]);
            
            return redirect()->route('password.reset.form')
                ->with('email', $credentials['email'])
                ->with('require_pin', true)
                ->with('message', 'Ingresa el PIN proporcionado por el administrador.');
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($key, 300);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Contraseña incorrecta.']);
        }

        RateLimiter::clear($key);

        $user = Auth::user();

        if (!$user->status) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        $request->session()->regenerate();
        return $this->redirectBasedOnRole();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        RateLimiter::clear('login:' . $request->ip());
        
        return redirect()->route('login')
            ->with('success', 'Sesión cerrada exitosamente')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $key = 'check-email:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'message' => 'Demasiadas solicitudes. Intenta de nuevo más tarde.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'exists' => false,
                'log_in_status' => null,
            ]);
        }

        $requiresPin = in_array($user->log_in_status, [1, 2]) && $user->initial_token;

        return response()->json([
            'exists' => true,
            'log_in_status' => $user->log_in_status,
            'requires_pin' => $requiresPin
        ]);
    }

    protected function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if ($user->role === 'Administrador') {
            return redirect()->route('sigem.admin.index');
        } elseif ($user->role === 'Desarrollador') {
            return redirect()->route('admin.panel');
        } elseif ($user->role === 'Capturista') {
            return redirect()->route('capturista.panel');
        } elseif ($user->role === 'Registrador') {
            return redirect()->route('registrador.panel');
        }
        elseif ($user->role === 'Administrador Dictamenes') {
            return redirect()->route('sg-dictamen.index');
        } elseif ($user->role === 'Editor Dictamenes') {
            return redirect()->route('sg-dictamen.index');
        }

        return redirect()->route('dashboard');
    }
}