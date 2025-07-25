<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar que tenga el rol correcto (admin o desarrollador)
        if (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador')) {
            return redirect()->route('dashboard');
        }

        $status = $request->get('status', 'active'); // default to active users
        
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
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista'])],
        ]);

        // Contraseña temporal por defecto
        $defaultPassword = 'CambiaMe123!';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            //'password' => Hash::make($request->password),
            'password' => bcrypt($defaultPassword),
            'role' => $request->role,
            'status' => 1,
            'log_in_status' => 1, // Nuevo usuario debe cambiar contraseña
        ]);

        return redirect()->route('admin.panel')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista'])],
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

}