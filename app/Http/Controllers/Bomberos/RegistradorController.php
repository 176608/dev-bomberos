<?php

namespace App\Http\Controllers\Bomberos;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\Bomberos\Colonias;
use App\Models\Bomberos\Calles;
use App\Models\Bomberos\CatalogoCalle;
use Yajra\DataTables\Facades\DataTables;

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