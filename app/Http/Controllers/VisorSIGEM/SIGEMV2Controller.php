<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\Catalogo;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class SIGEMV2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->hasRole('Desarrollador') && !$user->hasRole('Administrador')) {
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
        $indicadores = \App\Models\SIGEM\CuadroEstadistico::obtenerTodos();
        return view('VisorSIGEM.catalogo', compact('estructura', 'indicadores'));
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

        $tema_subtemas = $tema->subtemas;
        $temas = Tema::orderBy('orden_indice')->get();

        $indicadores = collect([]);
        $subtema_seleccionado = null;

        if ($tema_subtemas && $tema_subtemas->count() > 0) {
            $subtema_seleccionado = $tema_subtemas->first();
            $indicadores = \App\Models\SIGEM\CuadroEstadistico::where('subtema_id', $subtema_seleccionado->subtema_id)
                ->orderBy('codigo_cuadro')
                ->get();
        }

        return view('VisorSIGEM.estadistica_tema', compact('tema', 'temas', 'tema_subtemas', 'subtema_seleccionado', 'indicadores'));
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
        return view('VisorSIGEM.cartografia');
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

    public function ajaxSubtemas($tema_id)
    {
        try {
            $subtemas = ce_subtema::where('ce_tema_id', $tema_id)
                ->orderBy('ce_subtema')
                ->get();

            return response()->json([
                'success' => true,
                'subtemas' => $subtemas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar subtemas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajaxContenido($subtema_id)
    {
        try {
            $contenido = ce_contenido::where('ce_subtema_id', $subtema_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$contenido) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró contenido para este subtema'
                ]);
            }

            $subtema = ce_subtema::with('tema')->find($subtema_id);

            return response()->json([
                'success' => true,
                'message' => 'Contenido cargado exitosamente',
                'contenido' => [
                    'ce_contenido_id' => $contenido->ce_contenido_id,
                    'titulo_tabla' => $contenido->titulo_tabla,
                    'pie_tabla' => $contenido->pie_tabla,
                    'tabla_filas' => $contenido->tabla_filas,
                    'tabla_columnas' => $contenido->tabla_columnas,
                    'tabla_datos' => $contenido->tabla_datos,
                    'created_at' => $contenido->created_at,
                    'updated_at' => $contenido->updated_at
                ],
                'subtema' => $subtema ? [
                    'ce_subtema_id' => $subtema->ce_subtema_id,
                    'ce_subtema' => $subtema->ce_subtema,
                    'tema' => $subtema->tema ? [
                        'ce_tema_id' => $subtema->tema->ce_tema_id,
                        'tema' => $subtema->tema->tema
                    ] : null
                ] : null,
                'actualizado' => $contenido->updated_at ? $contenido->updated_at->format('d/m/Y H:i:s') : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar contenido: ' . $e->getMessage()
            ], 500);
        }
    }
}
