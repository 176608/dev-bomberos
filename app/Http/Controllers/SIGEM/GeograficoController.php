<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeograficoItem;

class GeograficoController extends Controller
{
    public function index()
    {
        $items = GeograficoItem::all(); // ← Obtiene todo de la tabla geografico_items
        return view('geografico', compact('items')); // ← Manda los datos a la vista geografico.blade.php
    }
}
