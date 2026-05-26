<?php

namespace App\Http\Controllers\VisorSIGEM;

use Illuminate\Http\Request;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Catalogo;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class SIGEMV2Controller extends Controller
{
    /* ================================================================
     *  🟢 PROTECCIÓN TEMPORAL: Solo usuarios con rol Desarrollador
     *  🔴 ELIMINAR este constructor cuando el V2 reemplace al visor
     *     público. En ese momento se agrega:
     *     Route::permanentRedirect('/sigem', '/sigem-v2')
     *     en routes/web.php
     * ================================================================ */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('Desarrollador')) {
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
        $estructura = Catalogo::obtenerEstructuraCatalogoConClaves();
        return view('VisorSIGEM.catalogo', compact('estructura'));
    }

    public function estadistica()
    {
        $temas = Tema::withCount('subtemas')
            ->orderBy('orden_indice')
            ->get();

        return view('VisorSIGEM.estadistica', compact('temas'));
    }

    public function estadisticaTema($tema_id)
    {
        $tema = Tema::with(['subtemas' => function ($q) {
            $q->orderBy('orden_indice');
        }])->findOrFail($tema_id);

        $temas = Tema::orderBy('orden_indice')->get();

        return view('VisorSIGEM.estadistica_tema', compact('tema', 'temas'));
    }

    public function verIndicador($id)
    {
        // TODO: Implementar cuando existan IndicadorEstadistico y ValorIndicador
        // Por ahora redirige a la vista de estadística
        return redirect()->route('sigem.v2.estadistica');
    }

    public function datosIndicadorJson($id)
    {
        // TODO: Implementar cuando exista ValorIndicador
        return response()->json(['success' => false, 'message' => 'No implementado']);
    }

    public function cartografia()
    {
        $mapas = Mapa::obtenerParaCartografia();
        return view('VisorSIGEM.cartografia', compact('mapas'));
    }

    public function productos()
    {
        return view('VisorSIGEM.productos');
    }

    public function consultaExpress()
    {
        $temas = ce_tema::with('subtemas')->orderBy('ce_tema_id')->get();
        return view('VisorSIGEM.consulta_express', compact('temas'));
    }

    public function consultaExpressContenido($subtema_id)
    {
        $subtema = ce_subtema::with('tema')->findOrFail($subtema_id);
        $contenido = ce_contenido::where('ce_subtema_id', $subtema_id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('VisorSIGEM.consulta_express_contenido', compact('subtema', 'contenido'));
    }
}
