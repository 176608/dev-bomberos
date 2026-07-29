<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Services\VisorSIGEM\DatasetViewService;

class DatasetViewController extends Controller
{
    public function __construct(
        private DatasetViewService $datasetViewService,
    ) {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->hasRole('Desarrollador') && !$user->hasRole('Administrador') && !$user->hasRole('Estadistico')) {
                return redirect('/sigem');
            }
            return $next($request);
        });
    }

    public function show(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if ($cuadro && $cuadro->tipo_mapa_pdf) {
            return redirect()->route('sigem.v2.cuadro.mapa', $id);
        }

        $data = $this->datasetViewService->datosCuadro($id, $this->esDesarrollador());
        if (!$data) {
            abort(404);
        }
        return view('VisorSIGEM.dataset_view.show', [
            'cuadro' => $data['cuadro'],
            'tabla' => $data['tabla'],
            'verticales' => $data['verticales'],
            'horizontales' => $data['horizontales'],
            'tema' => $data['tema'],
            'subtema' => $data['subtema'],
        ]);
    }

    public function cuadroApi(int $id)
    {
        $data = $this->datasetViewService->datosCuadro($id, $this->esDesarrollador());
        if (!$data) {
            return response()->json(['error' => 'Cuadro no encontrado'], 404);
        }
        return response()->json($data);
    }
}
