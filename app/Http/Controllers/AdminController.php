<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->role !== 'Administrador') {
            return redirect()->route('dashboard');
        }

        $users = User::all();
        return view('roles.admin', compact('users'));
    }
}