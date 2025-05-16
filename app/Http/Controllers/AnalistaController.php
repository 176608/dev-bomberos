<?php

namespace App\Http\Controllers;

use App\Models\Agregar;
use Illuminate\Http\Request;

class AnalistaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->role !== 'Analista') {
            return redirect()->route('dashboard');
        }

        $registros = Agregar::all();
        return view('roles.analista', compact('registros'));
    }
}