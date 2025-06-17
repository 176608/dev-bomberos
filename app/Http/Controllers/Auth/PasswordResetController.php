<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->log_in_status = 0;
        $user->save();

        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada exitosamente. Por favor, inicia sesión nuevamente.');
    }
}