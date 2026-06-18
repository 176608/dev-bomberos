<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\Catalogo;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class SIGEMV2Controller extends Controller
{
    private function esDesarrollador(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Desarrollador');
    }

    public function __construct()
    {
        // No se bloquea a usuarios no autenticados; el filtro de publicado se aplica por query
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
        $query = Cuadro::query();
        if (!$this->esDesarrollador()) {
            $query->where('publicado', true);
        }
        $indicadores = $query->get();

        $indicadores = $indicadores->sort(function ($a, $b) {
            $aParts = explode('.', $a->codigo_cuadro);
            $bParts = explode('.', $b->codigo_cuadro);
            $len = max(count($aParts), count($bParts));
            for ($i = 0; $i < $len; $i++) {
                $aVal = (int)($aParts[$i] ?? 0);
                $bVal = (int)($bParts[$i] ?? 0);
                if ($aVal !== $bVal) return $aVal <=> $bVal;
            }
            return 0;
        });

        return view('VisorSIGEM.catalogo', compact('estructura', 'indicadores'));
    }

    public function estadistica()
    {
        $query = TemaV2::withCount('subtemas')
            ->orderBy('orden_indice');

        if (!$this->esDesarrollador()) {
            $query->where('publicado', true);
        }

        $temas = $query->get();
        $esDesarrollador = $this->esDesarrollador();

        return view('VisorSIGEM.estadistica', compact('temas', 'esDesarrollador'));
    }

    public function estadisticaTema($tema_id)
    {
        $esDesarrollador = $this->esDesarrollador();

        $tema = TemaV2::with(['subtemas' => function ($q) use ($esDesarrollador) {
            $q->orderBy('orden_indice');
            if (!$esDesarrollador) {
                $q->where('publicado', true);
            }
        }])->findOrFail($tema_id);

        if (!$esDesarrollador && !$tema->publicado) {
            abort(404);
        }

        $tema_subtemas = $tema->subtemas;
        $temas = TemaV2::orderBy('orden_indice')->get();

        $indicadores = collect([]);
        $subtema_seleccionado = null;

        if ($tema_subtemas && $tema_subtemas->count() > 0) {
            $subtema_seleccionado = $tema_subtemas->first();
            $query = Cuadro::where('subtema_id', $subtema_seleccionado->subtema_id)
                ->orderBy('codigo_cuadro');
            if (!$esDesarrollador) {
                $query->where('publicado', true);
            }
            $indicadores = $query->get();
        }

        return view('VisorSIGEM.estadistica_tema', compact('tema', 'temas', 'tema_subtemas', 'subtema_seleccionado', 'indicadores', 'esDesarrollador'));
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

    public function ajaxCuadrosV2($subtema_id)
    {
        try {
            $query = Cuadro::where('subtema_id', $subtema_id)
                ->orderBy('codigo_cuadro');

            if (!$this->esDesarrollador()) {
                $query->where('publicado', true);
            }

            $cuadros = $query->get()
                ->map(function($cuadro) {
                    return [
                        'cuadro_id' => $cuadro->cuadro_id,
                        'codigo_cuadro' => $cuadro->codigo_cuadro,
                        'c_titulo' => $cuadro->c_titulo,
                        'c_subtitulo' => $cuadro->c_subtitulo,
                        'tipo_mapa_pdf' => $cuadro->tipo_mapa_pdf,
                        'permite_grafica' => $cuadro->permite_grafica,
                        'publicado' => $cuadro->publicado,
                    ];
                });

            return response()->json([
                'success' => true,
                'cuadros' => $cuadros,
                'total_cuadros' => $cuadros->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar cuadros: ' . $e->getMessage(),
                'cuadros' => []
            ]);
        }
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
