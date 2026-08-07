<?php
/* <!-- Archivo SGU - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SGU;

use App\Models\SGU\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GestorController
{
    public function usuarios(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador')) {
            return redirect()->route('sgu.admin.index');
        }

        $status = $request->get('status', 'active');

        $users = User::when($status !== 'all', function ($query) use ($status) {
            return $query->where('status', $status === 'active' ? 1 : 0);
        })->get();

        return view('sgu.gestor.usuarios', compact('users', 'status'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista', 'Registrador', 'Administrador Dictamenes', 'Editor Dictamenes', 'Estadistico'])],
        ]);

        $randomPassword = bin2hex(random_bytes(16));
        $initialToken = $this->generateInitialToken();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($randomPassword),
            'role' => $validated['role'],
            'status' => 1,
            'log_in_status' => 1,
            'initial_token' => Hash::make($initialToken),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Usuario creado exitosamente. PIN de acceso: {$initialToken}",
                'pin' => $initialToken,
            ]);
        }

        return redirect()->route('sgu.admin.gestor.usuarios')
            ->with('success', "Usuario creado exitosamente. PIN de acceso: {$initialToken}");
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista', 'Registrador', 'Administrador Dictamenes', 'Editor Dictamenes', 'Estadistico'])],
                'status' => ['required', 'boolean'],
            ]);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'status' => $validated['status'],
            ];

            $newPin = null;
            if ($request->has('reset_password')) {
                $newPin = $this->generateInitialToken();
                $updateData['log_in_status'] = 2;
                $updateData['initial_token'] = Hash::make($newPin);
            }

            $user->update($updateData);

            $pinGenerado = $newPin;

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $request->has('reset_password')
                        ? 'Usuario actualizado y contraseña reseteada. Se generó un nuevo PIN.'
                        : 'Usuario actualizado exitosamente',
                    'pin' => $pinGenerado,
                    'data' => $user,
                ]);
            }

            if ($pinGenerado) {
                return redirect()->route('sgu.admin.gestor.usuarios')
                    ->with('success', "Usuario actualizado. PIN de acceso: {$pinGenerado}");
            }

            return redirect()->route('sgu.admin.gestor.usuarios')
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            \Log::error('SGU: error al actualizar usuario', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar usuario.',
                ], 422);
            }

            return redirect()->route('sgu.admin.gestor.usuarios')
                ->with('error', 'Error al actualizar usuario.');
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
                'message' => 'El usuario no requiere PIN de acceso. Estado actual: ' . $user->log_in_status,
            ], 422);
        }

        $newPin = $this->generateInitialToken();

        $user->update([
            'initial_token' => Hash::make($newPin),
        ]);

        return response()->json([
            'success' => true,
            'pin' => $newPin,
            'message' => 'PIN generado exitosamente',
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
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        if (!in_array($user->log_in_status, [1, 2])) {
            return response()->json([
                'valid' => false,
                'message' => 'El usuario no requiere PIN',
            ], 400);
        }

        if (!$user->initial_token) {
            return response()->json([
                'valid' => false,
                'message' => 'PIN no configurado. Contacta al administrador.',
            ], 400);
        }

        if (!Hash::check($request->pin, $user->initial_token)) {
            return response()->json([
                'valid' => false,
                'message' => 'PIN incorrecto',
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'message' => 'PIN válido',
        ]);
    }

    protected function generateInitialToken(): string
    {
        return str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
    }
}
