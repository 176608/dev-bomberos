<?php

namespace App\Http\Controllers;

use App\Models\Hidrante;

class DashboardController extends Controller
{
    public function index()
    {
        $registros = Hidrante::all();
        return view('dashboard', compact('registros'));
    }
}