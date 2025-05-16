<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
}