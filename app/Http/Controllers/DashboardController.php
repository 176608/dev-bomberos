<?php

namespace App\Http\Controllers;

use App\Models\Agregar;

class DashboardController extends Controller
{
    public function index()
    {
        $registros = Agregar::all();
        return view('dashboard', compact('registros'));
    }
}