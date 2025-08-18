<?php

namespace App\Http\Controllers\Bomberos;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\Bomberos\Colonias;
use App\Models\Bomberos\Calles;

class RegistradorController extends Controller
{
    /**
     * Mostrar el panel principal del registrador
     */
    public function index()
    {
        return view('roles.registrador');
    }
}