<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PasswordResetController extends Controller
{
    public function showResetForm()
    {
        if (Auth::user()->log_in_status === 0) {
            return redirect()->route('dashboard');
        }
        return view('auth.password-reset');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        if ($user->log_in_status === 1) {
            // Validación para nuevos usuarios
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
        } else {
            // Validación solo para cambio de contraseña
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        }

        //$user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->log_in_status = 0;
        $user->save();

        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Información actualizada exitosamente. Por favor, inicie sesión nuevamente.');
    }
}