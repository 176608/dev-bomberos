<?php

namespace App\Http\Controllers;

use App\Models\Agregar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalistaController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Analista') {
            return redirect()->route('dashboard');
        }

        $registros = Agregar::all();
        return view('roles.analista', compact('registros'));
    }
}