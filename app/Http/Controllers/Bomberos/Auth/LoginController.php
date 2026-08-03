<?php

namespace App\Http\Controllers\Bomberos\Auth;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\SGU\User;
use App\Models\SIGEM\AuditoriaAcceso;
use App\Traits\HashIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use HashIp;

    protected $redirectTo = '/dashboard';

    private function registrarAcceso(?int $userId, string $accion, string $ip, ?string $detalle = null, bool $ipBruta = false): void
    {
        AuditoriaAcceso::create([
            'user_id'    => $userId,
            'accion'     => $accion,
            'detalle'    => $detalle,
            'ip'         => $this->hashIp($ip),
            'ip_bruta'   => $ipBruta ? $ip : null,
            'created_at' => now(),
        ]);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');
        $ip = $request->ip();

        $key = 'login-email:' . $email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Log::warning('Login bloqueado por email', compact('email', 'ip'));
            $this->registrarAcceso(null, 'intento_fallido', $ip, 'bloqueado_rate_limit', true);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Demasiados intentos. Intenta de nuevo en ' . ceil($seconds / 60) . ' minuto(s).']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            RateLimiter::hit($key, 300);
            Log::info('Login: usuario no encontrado', compact('email', 'ip'));
            $this->registrarAcceso(null, 'intento_fallido', $ip, 'usuario_no_encontrado', true);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Usuario no encontrado.']);
        }

        if (in_array($user->log_in_status, [1, 2])) {
            RateLimiter::clear($key);
            $this->registrarAcceso($user->id, 'primer_acceso', $ip, 'requiere_restauracion_password');

            return redirect()->route('password.reset.form', ['email' => $email])
                ->with('message', 'Crea tu contraseña segura.');
        }

        $request->validate([
            'password' => ['required'],
        ]);

        $credentials = compact('email') + ['password' => $request->input('password')];

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($key, 300);
            Log::info('Login: contraseña incorrecta', compact('email', 'ip'));
            $this->registrarAcceso($user->id, 'intento_fallido', $ip, 'contrasena_incorrecta', true);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Contraseña incorrecta.']);
        }

        RateLimiter::clear($key);

        $user = Auth::user();

        if (!$user->status) {
            Auth::logout();
            Log::warning('Login: cuenta desactivada', compact('email', 'ip'));
            $this->registrarAcceso($user->id, 'intento_fallido', $ip, 'cuenta_desactivada', true);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        $request->session()->regenerate();

        $this->registrarAcceso($user->id, 'login', $ip);

        return $this->redirectBasedOnRole();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $ip = $request->ip();

        if ($user) {
            $this->registrarAcceso($user->id, 'logout', $ip);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        RateLimiter::clear('login-flood:' . $ip);

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

        $email = $request->email;
        $ip = $request->ip();

        $key = 'check-email:' . $email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('checkEmail bloqueado por email', compact('email', 'ip'));
            return response()->json([
                'message' => 'Demasiadas solicitudes. Intenta de nuevo más tarde.'
            ], 429);
        }

        RateLimiter::hit($key, 300);

        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::info('checkEmail: email no encontrado', compact('email', 'ip'));
            return response()->json([
                'exists' => false,
                'log_in_status' => null,
            ]);
        }

        $requiresPin = in_array($user->log_in_status, [1, 2]);
        $requiresPassword = $user->log_in_status === 0;

        return response()->json([
            'exists' => true,
            'log_in_status' => $user->log_in_status,
            'requires_pin' => $requiresPin,
            'requires_password' => $requiresPassword
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
            return redirect()->route('gestor-dictamenes.index');
        } elseif ($user->role === 'Editor Dictamenes') {
            return redirect()->route('gestor-dictamenes.index');
        } elseif ($user->role === 'Estadistico') {
            return redirect()->route('sgiem.admin.index');
        }

        return redirect()->route('dashboard');
    }
}