<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Services\VisorSIGEM\CatalogoService;
use App\Services\VisorSIGEM\EstadisticaService;
use App\Services\VisorSIGEM\ConsultaExpressService;
use Illuminate\Http\Request;

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

    public function index()
    {
        return view('VisorSIGEM.inicio');
    }

    public function catalogo()
    {
        $data = $this->catalogoService->obtenerCatalogo($this->esDesarrollador());
        $this->registrarEvento('vista', 'catalogo');
        return view('VisorSIGEM.catalogo', $data);
    }

    public function estadistica()
    {
        $data = $this->estadisticaService->obtenerTemas($this->esDesarrollador());
        $this->registrarEvento('vista', 'estadistica');
        return view('VisorSIGEM.estadistica', $data);
    }

    public function estadisticaTema($tema_id)
    {
        $data = $this->estadisticaService->obtenerDatosTema($tema_id, $this->esDesarrollador());
        return view('VisorSIGEM.estadistica_tema', $data);
    }

    public function verCuadroRedirect($id)
    {
        return redirect()->route('sigem.v2.cuadro.dataset', ['id' => $id]);
    }

    public function datosCuadroJson($id)
    {
        return response()->json(['success' => false, 'message' => 'No implementado']);
    }

    public function cartografia()
    {
        $this->registrarEvento('vista', 'cartografia');
        return view('VisorSIGEM.cartografia');
    }

    public function ajaxCuadrosV2($subtema_id)
    {
        $data = $this->catalogoService->cuadrosPorSubtema($subtema_id, $this->esDesarrollador());
        return response()->json($data);
    }

    public function productos()
    {
        $this->registrarEvento('vista', 'productos');
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
        if ($data['success'] ?? false) {
            $this->registrarEvento('consulta_express', 'subtema:' . $subtema_id);
        }
        return response()->json($data);
    }

    public function trackEvent(Request $request)
    {
        $accion = $request->input('accion');
        $detalle = $request->input('detalle');

        $accionesPermitidas = ['inicio_card', 'cartografia_link', 'producto_link'];
        if (!in_array($accion, $accionesPermitidas)) {
            return response()->json(['success' => false, 'message' => 'Acción no válida'], 422);
        }

        if (is_string($detalle) && mb_strlen($detalle) > 255) {
            $detalle = mb_substr($detalle, 0, 255);
        }

        $this->registrarEvento($accion, is_string($detalle) ? $detalle : null);

        return response()->json(['success' => true]);
    }
}
