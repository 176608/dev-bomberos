<?php

namespace App\Http\Controllers;

use App\Models\Agregar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $registros = Agregar::orderBy('id', 'desc')->paginate(10);
        return view('dashboard', compact('registros'));
    }
}