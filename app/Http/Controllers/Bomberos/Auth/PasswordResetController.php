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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

        $user->password = \Hash::make($request->password);
        $user->log_in_status = 0;
        $user->save();

        return redirect()->route('login')->with('success', 'Contraseña actualizada exitosamente. Inicia sesión.');
    }
}