<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;

class AuxController extends Controller
{
    public function index()
    {
        try {
            // Intenta establecer la conexión
            $connected = DB::connection('sqlsrv_aux')->getPdo();
            $message = "¡Conexión establecida exitosamente!";
            $status = "success";
            
        } catch (Exception $e) {
            $message = "Error de conexión: " . $e->getMessage();
            $status = "error";
        }

        return view('VistaAux', compact('message', 'status'));
    }
}