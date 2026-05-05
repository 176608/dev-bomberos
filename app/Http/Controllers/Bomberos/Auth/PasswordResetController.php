<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\Bomberos\Auth;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\Bomberos\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PasswordResetController extends Controller
{
    public function showResetForm(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user || !in_array($user->log_in_status, [1,2])) {
            return redirect()->route('login')->with('error', 'No autorizado para cambiar contraseña.');
        }

        return view('auth.password-reset', compact('user'));
    }

    public function update(Request $request)
    {
        // ISO 25000: Validación de contraseña robusta
        $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/[A-Z]/',      // Mayúscula
                'regex:/[a-z]/',      // Minúscula
                'regex:/[0-9]/',      // Número
                'regex:/[^A-Za-z0-9]/', // Carácter especial
            ],
        ], [
            'password.min' => 'La contraseña debe tener mínimo 12 caracteres.',
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y caracteres especiales.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !in_array($user->log_in_status, [1,2])) {
            return redirect()->route('login')->with('error', 'No autorizado para cambiar contraseña.');
        }

        if ($user->log_in_status == 1) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);
            $user->name = $request->name;
        }
    // ISO 25000: Cifra la contraseña utilizando bcrypt (Hash::make) y no almacena contraseñas temporales
        $user->password = \Hash::make($request->password);
        $user->log_in_status = 0;
        $user->save();

        return redirect()->route('login')->with('success', 'Contraseña actualizada exitosamente. Inicia sesión.');
    }
}