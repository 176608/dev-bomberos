<?php

namespace App\Http\Controllers\Bomberos\Auth;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\Bomberos\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class PasswordResetController extends Controller
{
    protected int $tokenExpiryMinutes = 15;

    public function showResetForm(Request $request)
    {
        $email = $request->input('email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Enlace inválido.');
        }

        $user = User::where('email', $email)->first();

        if (!$user || !in_array($user->log_in_status, [1,2])) {
            return redirect()->route('login')->with('error', 'No autorizado para cambiar contraseña.');
        }

        $requirePin = $request->session()->get('pin_verified_for_reset') && 
                      $request->session()->get('pin_verified_user_id') == $user->id;

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

        $pinVerified = $request->session()->get('pin_verified_for_reset') && 
                       $request->session()->get('pin_verified_user_id') == $user->id;

        if ($user->initial_token && !$pinVerified) {
            if (!$request->filled('pin')) {
                return back()->withInput()->withErrors(['pin' => 'Ingresa el PIN proporcionado por el administrador.']);
            }

            if (!Hash::check($request->pin, $user->initial_token)) {
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

        $request->session()->forget('pin_verified_for_reset');
        $request->session()->forget('pin_verified_user_id');
        
        return redirect()->route('login')->with('success', 'Contraseña actualizada exitosamente. Inicia sesión.');
    }
}