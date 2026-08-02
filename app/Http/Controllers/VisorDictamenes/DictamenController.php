<?php

namespace App\Http\Controllers\VisorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;

class DictamenController extends Controller
{
    private const MESES_ESP = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    public function publicIndex(Request $request)
    {
        $query = Dictamen::where('estatus', 'ENVIADO');

        if ($request->filled('anio') && preg_match('/^\d{4}$/', $request->anio)) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('revisado_por')) {
            $query->where('revisado_por', $request->revisado_por);
        }

        if ($request->filled('dependencia')) {
            $query->where('dependencia_empres', $request->dependencia);
        }

        if ($request->filled('nombre_puesto')) {
            $query->where('nombre_puesto', $request->nombre_puesto);
        }

        $dictamenes = $query->orderByDesc('fecha')->get();

        $total = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)->count();
        $enviados = Dictamen::where('estatus', 'ENVIADO')->count();

        $anios = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('anio')
            ->distinct()
            ->orderBy('anio')
            ->pluck('anio');

        $revisadosPor = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('revisado_por')->where('revisado_por', '!=', '')
            ->distinct()->orderBy('revisado_por')->pluck('revisado_por');

        $dependencias = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('dependencia_empres')->where('dependencia_empres', '!=', '')
            ->distinct()->orderBy('dependencia_empres')->pluck('dependencia_empres');

        $nombresPuestos = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('nombre_puesto')->where('nombre_puesto', '!=', '')
            ->distinct()->orderBy('nombre_puesto')->pluck('nombre_puesto');

        $sanitizar = fn ($v) => is_string($v)
            ? preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $v)
            : $v;

        $datosGrafica = Dictamen::where('estatus', 'ENVIADO')
            ->get(['fecha', 'revisado_por', 'dependencia_empres', 'nombre_puesto'])
            ->map(fn ($d) => [
                'f' => $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : null,
                'r' => $sanitizar($d->revisado_por),
                'd' => $sanitizar($d->dependencia_empres),
                'n' => $sanitizar($d->nombre_puesto),
            ])->values();

        return view('visor-dictamenes.public', compact(
            'dictamenes',
            'total',
            'enviados',
            'anios',
            'revisadosPor',
            'dependencias',
            'nombresPuestos',
            'datosGrafica'
        ));
    }
}
