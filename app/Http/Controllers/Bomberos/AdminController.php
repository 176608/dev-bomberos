<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\Bomberos;

use App\Models\Bomberos\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Bomberos\Controller;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador')) {
            return redirect()->route('dashboard');
        }

        $status = $request->get('status', 'active');
        
        $users = User::when($status !== 'all', function($query) use ($status) {
            return $query->where('status', $status === 'active' ? 1 : 0);
        })->get();

        return view('roles.admin', compact('users', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista', 'Registrador', 'Administrador Dictamenes', 'Editor Dictamenes'])],
        ]);

        $randomPassword = bin2hex(random_bytes(16));
        $initialToken = $this->generateInitialToken();
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($randomPassword),
            'role' => $request->role,
            'status' => 1,
            'log_in_status' => 1,
            'initial_token' => Hash::make($initialToken),
        ]);

        return redirect()->route('admin.panel')->with('success', "Usuario creado exitosamente. PIN de acceso: {$initialToken}");
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista', 'Registrador', 'Administrador Dictamenes', 'Editor Dictamenes'])],
                'status' => ['required', 'boolean'],
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
            ];

            if ($request->has('reset_password')) {
                $updateData['log_in_status'] = 2;
                $updateData['initial_token'] = Hash::make($this->generateInitialToken());
            }

            $user->update($updateData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente',
                    'data' => $user
                ]);
            }

            return redirect()->route('admin.panel')
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar usuario: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.panel')
                ->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    public function generarPin(Request $request, User $user)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Solicitud inválida'], 400);
        }

        if (!in_array($user->log_in_status, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no requiere PIN de acceso.'
            ], 422);
        }

        $newPin = $this->generateInitialToken();
        
        $user->update([
            'initial_token' => Hash::make($newPin)
        ]);

        return response()->json([
            'success' => true,
            'pin' => $newPin,
            'message' => 'PIN generado exitosamente'
        ]);
    }

    public function verificarPin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'pin' => ['required', 'string', 'digits:10'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if (!in_array($user->log_in_status, [1, 2])) {
            return response()->json([
                'valid' => false,
                'message' => 'El usuario no requiere PIN'
            ], 400);
        }

        if (!$user->initial_token) {
            return response()->json([
                'valid' => false,
                'message' => 'PIN no configurado. Contacta al administrador.'
            ], 400);
        }

        if (!Hash::check($request->pin, $user->initial_token)) {
            return response()->json([
                'valid' => false,
                'message' => 'PIN incorrecto'
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'message' => 'PIN válido'
        ]);
    }

    protected function generateInitialToken(): string
    {
        return str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
    }
}