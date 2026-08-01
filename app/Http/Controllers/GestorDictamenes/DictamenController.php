<?php

namespace App\Http\Controllers\GestorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DictamenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dictamen::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                    ->orWhere('numero_oficio_raw', 'like', "%{$search}%")
                    ->orWhere('dependencia_empres', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%")
                    ->orWhere('nombre_puesto', 'like', "%{$search}%")
                    ->orWhere('revisado_por', 'like', "%{$search}%")
                    ->orWhere('observaciones', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estatus') && in_array($request->estatus, Dictamen::STATUSES, true)) {
            $query->where('estatus', $request->estatus);
        }

        $dictamenes = $query->orderByDesc('fecha')->get();

        $total = Dictamen::count();
        $conteoEstatus = [];
        foreach (Dictamen::STATUSES as $estatus) {
            $conteoEstatus[$estatus] = Dictamen::where('estatus', $estatus)->count();
        }
        $enviados = $conteoEstatus['ENVIADO'];

        $meses = [];
        $solicitudes = [];
        $diasHabiles = [];
        for ($i = 5; $i >= 0; $i--) {
            $fechaInicio = now()->subMonths($i)->startOfMonth();
            $fechaFin = now()->subMonths($i)->endOfMonth();

            $dictamenesMes = Dictamen::whereBetween('fecha', [$fechaInicio, $fechaFin])->get();

            $meses[] = $fechaInicio->format('M');
            $solicitudes[] = $dictamenesMes->count();

            $diasHabilesMes = 0;
            foreach ($dictamenesMes as $dictamen) {
                if ($dictamen->fecha && $dictamen->fecha_cierre) {
                    $fechaDictamen = \Carbon\Carbon::parse($dictamen->fecha);
                    $fechaCierre = \Carbon\Carbon::parse($dictamen->fecha_cierre);
                    $diasHabilesMes += $fechaDictamen->diffInBusinessDays($fechaCierre);
                }
            }
            $diasHabiles[] = $diasHabilesMes;
        }

        return view('gestor-dictamenes.index', compact(
            'dictamenes',
            'total',
            'conteoEstatus',
            'enviados',
            'meses',
            'solicitudes',
            'diasHabiles'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'numero_oficio' => 'required|string|max:255',
            'dependencia_empres' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(Dictamen::STATUSES)],
            'nombre_puesto' => 'nullable|string|max:255',
            'revisado_por' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $dictamen = Dictamen::create([
            'fecha' => $request->fecha,
            'numero_oficio' => $request->numero_oficio,
            'numero_oficio_raw' => $request->numero_oficio,
            'dependencia_empres' => $request->dependencia_empres,
            'asunto' => $request->asunto,
            'estatus' => $request->estatus,
            'nombre_puesto' => $request->nombre_puesto,
            'revisado_por' => $request->revisado_por,
            'observaciones' => $request->observaciones,
            'created_by' => auth()->id(),
        ]);
        $dictamen->refresh();

        $this->auditar($dictamen, 'CREAR');

        return back()->with('success', 'Dictamen creado correctamente.');
    }

    public function update(Request $request, Dictamen $dictamen)
    {
        $request->validate([
            'fecha' => 'required|date',
            'numero_oficio' => 'required|string|max:255',
            'dependencia_empres' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(Dictamen::STATUSES)],
            'nombre_puesto' => 'nullable|string|max:255',
            'revisado_por' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $dictamen->update([
            'fecha' => $request->fecha,
            'numero_oficio' => $request->numero_oficio,
            'numero_oficio_raw' => $request->numero_oficio,
            'dependencia_empres' => $request->dependencia_empres,
            'asunto' => $request->asunto,
            'estatus' => $request->estatus,
            'nombre_puesto' => $request->nombre_puesto,
            'revisado_por' => $request->revisado_por,
            'observaciones' => $request->observaciones,
            'updated_by' => auth()->id(),
        ]);
        $dictamen->refresh();

        $this->auditar($dictamen, 'MODIFICAR');

        return back()->with('success', 'Dictamen actualizado correctamente.');
    }

    public function destroy(Dictamen $dictamen)
    {
        $this->auditar($dictamen, 'ELIMINAR', deleted: true);
        $dictamen->delete();

        return back()->with('success', 'Dictamen eliminado correctamente.');
    }

    public function deletedDictamenes()
    {
        $dictamenes = DB::table('dictamenes_audit_log')
            ->whereNotNull('deleted_at')
            ->orderByDesc('deleted_at')
            ->get();

        return view('gestor-dictamenes.deleted', compact('dictamenes'));
    }

    public function restoreDeleted($id)
    {
        $audit = DB::table('dictamenes_audit_log')->where('id', $id)->whereNotNull('deleted_at')->first();

        if (!$audit) {
            return back()->with('error', 'Registro no encontrado o no eliminado.');
        }

        if (Dictamen::find($audit->dictamen_id)) {
            return back()->with('error', 'El dictamen ya existe en la tabla principal.');
        }

        Dictamen::create([
            'legacy_id' => $audit->legacy_id,
            'fecha' => $audit->fecha,
            'numero_oficio' => $audit->numero_oficio,
            'numero_oficio_raw' => $audit->numero_oficio_raw,
            'dependencia_empres' => $audit->dependencia_empres,
            'asunto' => $audit->asunto,
            'estatus' => $audit->estatus,
            'nombre_puesto' => $audit->nombre_puesto,
            'revisado_por' => $audit->revisado_por,
            'oficio' => $audit->oficio,
            'fecha_cierre' => $audit->fecha_cierre,
            'observaciones' => $audit->observaciones,
            'archivo' => $audit->archivo,
            'created_by' => auth()->id(),
        ]);

        DB::table('dictamenes_audit_log')->where('id', $id)->delete();

        return back()->with('success', 'Dictamen restaurado correctamente.');
    }

    private function auditar(Dictamen $dictamen, string $accion, bool $deleted = false)
    {
        DB::table('dictamenes_audit_log')->insert([
            'dictamen_id' => $dictamen->id,
            'anio' => $dictamen->anio,
            'dia' => $dictamen->dia,
            'mes' => $dictamen->mes,
            'fecha_raw' => $dictamen->fecha_raw,
            'oficio' => $dictamen->oficio,
            'nombre_puesto' => $dictamen->nombre_puesto,
            'dependencia_empres' => $dictamen->dependencia_empres,
            'asunto' => $dictamen->asunto,
            'estatus' => $dictamen->estatus,
            'numero_oficio_raw' => $dictamen->numero_oficio_raw,
            'archivo_raw' => $dictamen->archivo_raw,
            'revisado_por' => $dictamen->revisado_por,
            'observaciones' => $dictamen->observaciones,
            'fecha' => $dictamen->fecha,
            'numero_oficio' => $dictamen->numero_oficio,
            'archivo' => $dictamen->archivo,
            'fecha_cierre' => $dictamen->fecha_cierre,
            'legacy_id' => $dictamen->legacy_id,
            'created_by' => $dictamen->created_by,
            'updated_by' => $dictamen->updated_by,
            'deleted_by' => $deleted ? auth()->id() : null,
            'deleted_at' => $deleted ? now() : null,
        ]);
    }
}
