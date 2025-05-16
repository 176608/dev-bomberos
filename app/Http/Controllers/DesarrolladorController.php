<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DesarrolladorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->role !== 'Desarrollador') {
            return redirect()->route('dashboard');
        }

        $users = User::all();
        return view('roles.desarrollador', compact('users'));
    }
}