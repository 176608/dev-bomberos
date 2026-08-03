<?php

namespace App\Http\Controllers\Bomberos\Auth;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\SGU\User;
use App\Models\SIGEM\AuditoriaAcceso;
use App\Traits\HashIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class PasswordResetController extends Controller
{
    use HashIp;

    protected int $tokenExpiryMinutes = 15;

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

    public function showResetForm(Request $request)
    {
        $email = $request->input('email');

        \Log::info('PasswordReset showResetForm', [
            'email' => $email,
            'session_email' => $request->session()->get('email_for_reset'),
            'log_in_status' => $email ? User::where('email', $email)->first()?->log_in_status : null
        ]);

        if (!$email) {
            return redirect()->route('login')->with('error', 'Enlace inválido.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuario no encontrado.');
        }

        if (!in_array($user->log_in_status, [1,2])) {
            \Log::warning('Usuario no autorizado para reset', [
                'user_id' => $user->id,
                'log_in_status' => $user->log_in_status
            ]);
            return redirect()->route('login')->with('error', 'No autorizado para cambiar contraseña.');
        }

        $requirePin = $user->initial_token && session('pin_verificado') !== $user->id;

        return view('auth.password-reset', compact('user', 'requirePin'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'pin' => 'nullable|string|digits:10',
        ], [
            'password.min' => 'La contraseña debe tener mínimo 12 caracteres.',
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y caracteres especiales.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !in_array($user->log_in_status, [1,2])) {
            return redirect()->route('login')->with('error', 'No autorizado para cambiar contraseña.');
        }

        if ($user->initial_token && session('pin_verificado') !== $user->id) {
            if (!$request->filled('pin')) {
                return back()->withInput()->withErrors(['pin' => 'Ingresa el PIN proporcionado por el administrador.']);
            }

            if (!Hash::check($request->pin, $user->initial_token)) {
                $this->registrarAcceso($user->id, 'intento_fallido', $request->ip(), 'pin_incorrecto', true);
                return back()->withInput()->withErrors(['pin' => 'PIN incorrecto.']);
            }
        }

        if ($user->log_in_status == 1) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);
            $user->name = $request->name;
        }

        $user->password = Hash::make($request->password);
        $user->log_in_status = 0;
        $user->initial_token = null;
        $user->save();

        $request->session()->forget('pin_verificado');

        $this->registrarAcceso($user->id, 'restauracion_password', $request->ip());

        return redirect()->route('login')->with('success', 'Contraseña actualizada exitosamente. Inicia sesión.');
    }
}