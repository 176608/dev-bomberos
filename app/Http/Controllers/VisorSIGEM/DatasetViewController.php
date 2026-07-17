<?php

namespace App\Http\Controllers\VisorSIGEM;

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

    private function esDesarrollador(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Desarrollador') || auth()->user()->hasRole('Estadistico'));
    }

    public function show(int $id)
    {
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
