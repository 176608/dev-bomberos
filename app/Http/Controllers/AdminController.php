<?php

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
        if (!auth()->check() || auth()->user()->role !== 'Administrador') {
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
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 1,
        ]);

        return redirect()->route('admin.panel')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Administrador', 'Desarrollador', 'Capturista'])],
            'status' => ['required', 'boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.panel')->with('success', 'Usuario actualizado exitosamente');
    }

}