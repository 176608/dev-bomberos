<?php

namespace App\Http\Controllers;

use App\Models\Agregar;

class DashboardController extends Controller
{
    public function index()
    {
        $registros = Agregar::orderBy('id', 'desc')->paginate(10);
        return view('dashboard', compact('registros'));
    }
}