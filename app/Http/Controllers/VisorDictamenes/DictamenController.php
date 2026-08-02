<?php

namespace App\Http\Controllers\VisorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Http\Controllers\Bomberos\Controller;

class DictamenController extends Controller
{
    public function publicIndex()
    {
        $dictamenes = Dictamen::where('estatus', 'ENVIADO')->orderByDesc('fecha')->get();

        $total = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)->count();
        $enviados = Dictamen::where('estatus', 'ENVIADO')->count();

        $datosGrafica = Dictamen::where('estatus', 'ENVIADO')
            ->get(['fecha'])
            ->map(fn ($d) => [
                'f' => $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : null,
            ])->values();

        return view('visor-dictamenes.public', compact('dictamenes', 'total', 'enviados', 'datosGrafica'));
    }
}
