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

    private function detectarOrigen(): string
    {
        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, 'catalogo')) {
            return 'catalogo';
        }
        if (str_contains($referer, 'estadistica')) {
            return 'estadistica';
        }
        return '';
    }

    public function show(int $id)
    {
        $data = $this->datasetViewService->datosCuadro($id, true);
        if (!$data) {
            abort(404);
        }
        return view('VisorSIGEM.dataset_view.show', [
            'cuadro' => $data['cuadro'],
            'tabla' => $data['tabla'],
            'verticales' => $data['verticales'],
            'horizontales' => $data['horizontales'],
            'from' => $this->detectarOrigen(),
        ]);
    }

    public function cuadroApi(int $id)
    {
        $data = $this->datasetViewService->datosCuadro($id, true);
        if (!$data) {
            return response()->json(['error' => 'Cuadro no encontrado'], 404);
        }
        return response()->json($data);
    }
}
