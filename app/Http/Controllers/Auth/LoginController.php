<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    /*public function __construct()
    {
        // Corregimos la sintaxis del middleware
        $this->middleware('guest')->except('logout');
    }*/

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

        if (!Auth::attempt($credentials)) {
            // Redirige de vuelta con error y email
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Contraseña incorrecta.']);
        }

        $user = Auth::user();

        if (!$user->status) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        // Check if user needs to reset password
        if ($user->log_in_status > 0) {
            return redirect()->route('password.reset.form')
                ->with('message', $user->log_in_status === 1 ? 
                    'Por favor, establece tu contraseña para continuar.' : 
                    'Por favor, cambia tu contraseña para continuar.');
        }

        $request->session()->regenerate();
        return $this->redirectBasedOnRole();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['exists' => false], 200);
        }

        return response()->json([
            'exists' => true,
            'log_in_status' => $user->log_in_status,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    protected function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if ($user->role === 'Administrador') {
            return redirect()->route('admin.panel');
        } elseif ($user->role === 'Desarrollador') {
            return redirect()->route('dev.panel');
        } elseif ($user->role === 'Capturista') {
            return redirect()->route('capturista.panel');
        }

        return redirect()->route('dashboard');
    }
}