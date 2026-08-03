<?php

namespace App\Http\Controllers\SGU;

use App\Models\SIGEM\PubVisitante;
use App\Models\SIGEM\PubVisita;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController
{
    public const ETIQUETAS_ACCION = [
        'vista' => 'Vistas de página',
        'dataset' => 'Consulta de dataset',
        'grafica' => 'Consulta de gráfica',
        'grafica_tipo' => 'Tipo de gráfica',
        'grafica_png' => 'Descarga PNG',
        'mapa' => 'Consulta de mapa',
        'mapa_pdf' => 'Descarga mapa PDF',
        'excel' => 'Descarga Excel',
        'consulta_express' => 'Consulta express',
        'inicio_card' => 'Tarjeta de inicio',
        'cartografia_link' => 'Enlace cartografía',
        'producto_link' => 'Enlace productos',
    ];

    public function index(Request $request): View
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        if (!$desde && !$hasta) {
            $desde = now()->subDays(30)->format('Y-m-d');
            $hasta = now()->format('Y-m-d');
        }

        $visitas = PubVisita::query()
            ->when($desde, fn ($q) => $q->whereDate('pub_visita.created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('pub_visita.created_at', '<=', $hasta));

        $eventos = (clone $visitas)->count();

        $eventosPorDia = (clone $visitas)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $topAcciones = (clone $visitas)
            ->selectRaw('accion, COUNT(*) as total')
            ->groupBy('accion')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($a) => [
                'label' => self::ETIQUETAS_ACCION[$a->accion] ?? $a->accion,
                'total' => (int) $a->total,
            ]);

        $topCuadros = (clone $visitas)
            ->selectRaw('pub_visita.cuadro_id, cuadro_v2.codigo_cuadro, cuadro_v2.c_titulo, COUNT(*) as total')
            ->leftJoin('cuadro_v2', 'cuadro_v2.cuadro_id', '=', 'pub_visita.cuadro_id')
            ->whereNotNull('pub_visita.cuadro_id')
            ->groupBy('pub_visita.cuadro_id', 'cuadro_v2.codigo_cuadro', 'cuadro_v2.c_titulo')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $origenes = (clone $visitas)
            ->selectRaw('origen, COUNT(*) as total')
            ->whereNotNull('origen')
            ->groupBy('origen')
            ->orderByDesc('total')
            ->get();

        $tiposGrafica = (clone $visitas)
            ->where('accion', 'grafica_tipo')
            ->whereNotNull('detalle')
            ->selectRaw('detalle, COUNT(*) as total')
            ->groupBy('detalle')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $paginas = (clone $visitas)
            ->where('accion', 'vista')
            ->whereNotNull('detalle')
            ->selectRaw('detalle, COUNT(*) as total')
            ->groupBy('detalle')
            ->orderByDesc('total')
            ->get()
            ->map(function ($p) {
                $label = ['inicio' => 'Inicio', 'catalogo' => 'Catálogo', 'estadistica' => 'Estadística',
                          'cartografia' => 'Cartografía', 'productos' => 'Productos'][$p->detalle] ?? $p->detalle;
                if (str_starts_with($p->detalle, 'estadistica_tema:')) {
                    $label = 'Tema ' . explode(':', $p->detalle)[1];
                }
                return ['label' => $label, 'total' => (int) $p->total];
            });

        $pngDescargas = (clone $visitas)->where('accion', 'grafica_png')->count();

        $visitantesTotales = PubVisitante::count();
        $visitantesNuevos = PubVisitante::whereDate('primer_visita', '>=', $desde)
            ->whereDate('primer_visita', '<=', $hasta)->count();
        $visitantesActivos = PubVisitante::whereDate('ultima_visita', '>=', $desde)
            ->whereDate('ultima_visita', '<=', $hasta)->count();
        $bots = PubVisitante::where('es_bot', true)->count();
        $humanos = PubVisitante::where('es_bot', false)->count();

        $ultimasVisitas = (clone $visitas)->with(['visitante', 'cuadro'])
            ->orderByDesc('created_at')->limit(50)->get();

        return view('sgu.admin.dashboard', compact(
            'desde', 'hasta', 'eventos', 'visitantesTotales', 'visitantesNuevos',
            'visitantesActivos', 'bots', 'humanos', 'eventosPorDia', 'topAcciones',
            'topCuadros', 'origenes', 'tiposGrafica', 'pngDescargas', 'paginas', 'ultimasVisitas'
        ));
    }
}
