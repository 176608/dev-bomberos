<?php

namespace App\Http\Controllers\VisorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DictamenController extends Controller
{
    public function publicIndex(Request $request)
    {
        $query = Dictamen::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                    ->orWhere('numero_oficio_raw', 'like', "%{$search}%")
                    ->orWhere('dependencia_empres', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%")
                    ->orWhere('nombre_puesto', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estatus') && in_array($request->estatus, Dictamen::STATUSES, true)) {
            $query->where('estatus', $request->estatus);
        }

        $dictamenes = $query->orderByDesc('fecha')->get();

        $total = Dictamen::count();
        $enviados = Dictamen::where('estatus', 'ENVIADO')->count();

        $meses = [];
        $solicitudes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fechaInicio = now()->subMonths($i)->startOfMonth();
            $fechaFin = now()->subMonths($i)->endOfMonth();
            $meses[] = $fechaInicio->format('M');
            $solicitudes[] = Dictamen::whereBetween('fecha', [$fechaInicio, $fechaFin])->count();
        }

        return view('visor-dictamenes.public', compact(
            'dictamenes',
            'total',
            'enviados',
            'meses',
            'solicitudes'
        ));
    }
}
