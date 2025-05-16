<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Administrador') {
            return redirect()->route('dashboard');
        }

        $users = User::all();
        return view('roles.admin', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Analista'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.panel')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Analista'])],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.panel')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.panel')->with('error', 'No puedes eliminar tu propio usuario');
        }

        $user->delete();
        return redirect()->route('admin.panel')->with('success', 'Usuario eliminado exitosamente');
    }
}