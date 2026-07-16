<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Services\VisorSIGEM\CatalogoService;
use App\Services\VisorSIGEM\EstadisticaService;
use App\Services\VisorSIGEM\ConsultaExpressService;

class SIGEMV2Controller extends Controller
{
    public function __construct(
        private CatalogoService $catalogoService,
        private EstadisticaService $estadisticaService,
        private ConsultaExpressService $consultaExpressService,
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

    public function index()
    {
        return view('VisorSIGEM.inicio');
    }

    public function catalogo()
    {
        $data = $this->catalogoService->obtenerCatalogo($this->esDesarrollador());
        return view('VisorSIGEM.catalogo', $data);
    }

    public function estadistica()
    {
        $data = $this->estadisticaService->obtenerTemas($this->esDesarrollador());
        return view('VisorSIGEM.estadistica', $data);
    }

    public function estadisticaTema($tema_id)
    {
        $data = $this->estadisticaService->obtenerDatosTema($tema_id, $this->esDesarrollador());
        return view('VisorSIGEM.estadistica_tema', $data);
    }

    public function verIndicador($id)
    {
        return redirect()->route('sigem.v2.estadistica');
    }

    public function datosIndicadorJson($id)
    {
        return response()->json(['success' => false, 'message' => 'No implementado']);
    }

    public function cartografia()
    {
        return view('VisorSIGEM.cartografia');
    }

    public function ajaxCuadrosV2($subtema_id)
    {
        $data = $this->catalogoService->cuadrosPorSubtema($subtema_id, $this->esDesarrollador());
        return response()->json($data);
    }

    public function productos()
    {
        return view('VisorSIGEM.productos');
    }

    public function consultaExpress()
    {
        $temas = $this->consultaExpressService->obtenerTemas();
        return view('VisorSIGEM.consulta_express', compact('temas'));
    }

    public function ajaxSubtemas($tema_id)
    {
        $data = $this->consultaExpressService->subtemasPorTema($tema_id);
        return response()->json($data);
    }

    public function ajaxContenido($subtema_id)
    {
        $data = $this->consultaExpressService->obtenerContenido($subtema_id);
        return response()->json($data);
    }
}
