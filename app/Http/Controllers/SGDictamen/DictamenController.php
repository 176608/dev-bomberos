<?php
namespace App\Http\Controllers\SGDictamen;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\SGDictamen\Dictamen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DictamenController extends Controller
{
    public function index(Request $request)
    {
        // Búsqueda
        $query = Dictamen::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('oficio', 'like', "%$search%")
                  ->orWhere('nombre_puesto', 'like', "%$search%")
                  ->orWhere('asunto', 'like', "%$search%")
                  ->orWhere('revisado_por', 'like', "%$search%")
                  ->orWhere('observaciones', 'like', "%$search%");
        }

        $dictamenes = $query->orderBy('fecha', 'DESC')->get();

        // Estadísticas
        $total = Dictamen::count();
        $nuevo = Dictamen::where('estatus', 'ENVIADO')->count();

        // Gráfica: conteo por mes
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $solicitudes = array_fill(0, 12, 0);
        $registros = Dictamen::select('fecha')->get();

        foreach ($registros as $r) {
            if ($r->fecha) {
                $carbon = Carbon::parse($r->fecha);
                $mesIdx = $carbon->month - 1;
                if ($mesIdx >= 0 && $mesIdx < 12) {
                    $solicitudes[$mesIdx]++;
                }
            }
        }

        // Días hábiles (últimos 12 meses)
        $diasHabiles = array_fill(0, 12, 0);
        $conteoPorMes = array_fill(0, 12, 0);
        $inicioRango = now()->subMonths(12);

        foreach ($dictamenes as $d) {
            if ($d->fecha) {
                $fechaDictamen = Carbon::parse($d->fecha);
                if ($fechaDictamen->gte($inicioRango)) {
                    $inicio = $fechaDictamen;
                    $fin = $d->fecha_cierre ? Carbon::parse($d->fecha_cierre) : now();
                    $dias = 0;
                    $current = clone $inicio;
                    while ($current->lessThan($fin)) {
                        if ($current->isWeekday()) $dias++;
                        $current->addDay();
                    }
                    $mesIdx = $inicio->month - 1;
                    if ($mesIdx >= 0 && $mesIdx < 12) {
                        $diasHabiles[$mesIdx] += $dias;
                        $conteoPorMes[$mesIdx]++;
                    }
                }
            }
        }

        for ($i = 0; $i < 12; $i++) {
            if ($conteoPorMes[$i] > 0) {
                $diasHabiles[$i] = round($diasHabiles[$i] / $conteoPorMes[$i]);
            } else {
                $diasHabiles[$i] = 0;
            }
        }

        return view('sg-dictamen.index', compact(
            'dictamenes', 'total', 'nuevo', 'meses', 'solicitudes', 'diasHabiles'
        ));
    }

    public function edit(Dictamen $dictamen)
    {
        return response()->json([
            'fecha' => $dictamen->fecha ? Carbon::parse($dictamen->fecha)->format('Y-m-d') : null,
            'oficio' => $dictamen->oficio,
            'nombre_puesto' => $dictamen->nombre_puesto,
            'dependencia_empres' => $dictamen->dependencia_empres,
            'asunto' => $dictamen->asunto,
            'numero_oficio' => $dictamen->numero_oficio,
            'revisado_por' => $dictamen->revisado_por,
            'estatus' => $dictamen->estatus,
            'observaciones' => $dictamen->observaciones,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date', 'oficio' => 'nullable|string',
            'nombre_puesto' => 'nullable|string', 'dependencia_empres' => 'nullable|string',
            'asunto' => 'nullable|string', 'numero_oficio' => 'nullable|string',
            'revisado_por' => 'nullable|string', 'observaciones' => 'nullable|string',
            'estatus' => 'required|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        Dictamen::create($validated);
        return redirect()->route('sg-dictamen.index')->with('success', 'Dictamen creado exitosamente.');
    }

    public function update(Request $request, Dictamen $dictamen)
    {
        $validated = $request->validate([
            'fecha' => 'required|date', 'oficio' => 'nullable|string',
            'nombre_puesto' => 'nullable|string', 'dependencia_empres' => 'nullable|string',
            'asunto' => 'nullable|string', 'numero_oficio' => 'nullable|string',
            'revisado_por' => 'nullable|string', 'observaciones' => 'nullable|string',
            'estatus' => 'required|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $dictamen->update($validated);
        return redirect()->route('sg-dictamen.index')->with('success', 'Dictamen actualizado exitosamente.');
    }

    public function destroy(Dictamen $dictamen)
{
    // Guardar TODOS los campos en la tabla de auditoría ANTES de borrar
    \DB::table('dictamen_audit_log')->insert([
        'dictamen_id' => $dictamen->id,
        'fecha' => $dictamen->fecha,
        'oficio' => $dictamen->oficio,
        'nombre_puesto' => $dictamen->nombre_puesto,
        'dependencia_empres' => $dictamen->dependencia_empres,
        'asunto' => $dictamen->asunto,
        'numero_oficio' => $dictamen->numero_oficio,
        'revisado_por' => $dictamen->revisado_por,
        'observaciones' => $dictamen->observaciones,
        'estatus' => $dictamen->estatus,
        'deleted_by' => auth()->id(),
        'deleted_at' => now()
    ]);

    // Eliminar el registro de la tabla original
    $dictamen->delete();

    return redirect()->route('sg-dictamen.index')->with('success', 'Dictamen eliminado exitosamente.');
}

public function restoreDeleted($id)
{
    // 1. Buscar el registro en la tabla de auditoría
    $auditRecord = \DB::table('dictamen_audit_log')->where('id', $id)->first();

    if (!$auditRecord) {
        return redirect()->back()->with('error', 'El registro no existe.');
    }

    // 2. Preparar los datos para volver a la tabla principal
    $data = [
        'fecha' => $auditRecord->fecha,
        'oficio' => $auditRecord->oficio,
        'nombre_puesto' => $auditRecord->nombre_puesto,
        'dependencia_empres' => $auditRecord->dependencia_empres,
        'asunto' => $auditRecord->asunto,
        'numero_oficio' => $auditRecord->numero_oficio,
        'revisado_por' => $auditRecord->revisado_por,
        'observaciones' => $auditRecord->observaciones,
        'estatus' => $auditRecord->estatus,
        // Opcional: Si quieres que mantenga la fecha de creación original, descomenta esto:
        // 'created_at' => $auditRecord->deleted_at, 
    ];

    // 3. Insertar de nuevo en la tabla principal
    Dictamen::create($data);

    // 4. Eliminar el registro de la tabla de auditoría (ya que ya no está "borrado")
    \DB::table('dictamen_audit_log')->where('id', $id)->delete();

    return redirect()->route('sg-dictamen.deleted')->with('success', 'Dictamen restaurado exitosamente.');
}
    // Vista pública
    public function publicIndex()
    {
        // Obtener los dictamenes 
        $dictamenes = Dictamen::orderBy('fecha', 'DESC')->get();

        // Estadísticas 
        $total = Dictamen::count();  // 89 - Total de TODOS
        $nuevo = Dictamen::where('estatus', 'ENVIADO')->count();  // 54 - Solo ENVIADO

        // Gráfica: conteo por mes 
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $solicitudes = array_fill(0, 12, 0);
        $registros = Dictamen::select('fecha')->get();

        foreach ($registros as $r) {
            if ($r->fecha) {
                $carbon = Carbon::parse($r->fecha);
                $mesIdx = $carbon->month - 1;
                if ($mesIdx >= 0 && $mesIdx < 12) {
                    $solicitudes[$mesIdx]++;
                }
            }
        }

        // Días hábiles 
        $diasHabiles = array_fill(0, 12, 0);
        $conteoPorMes = array_fill(0, 12, 0);
        $inicioRango = now()->subMonths(12);

        foreach ($dictamenes as $d) {
            if ($d->fecha) {
                $fechaDictamen = Carbon::parse($d->fecha);
                if ($fechaDictamen->gte($inicioRango)) {
                    $inicio = $fechaDictamen;
                    $fin = $d->fecha_cierre ? Carbon::parse($d->fecha_cierre) : now();
                    $dias = 0;
                    $current = clone $inicio;
                    while ($current->lessThan($fin)) {
                        if ($current->isWeekday()) $dias++;
                        $current->addDay();
                    }
                    $mesIdx = $inicio->month - 1;
                    if ($mesIdx >= 0 && $mesIdx < 12) {
                        $diasHabiles[$mesIdx] += $dias;
                        $conteoPorMes[$mesIdx]++;
                    }
                }
            }
        }

        for ($i = 0; $i < 12; $i++) {
            if ($conteoPorMes[$i] > 0) {
                $diasHabiles[$i] = round($diasHabiles[$i] / $conteoPorMes[$i]);
            } else {
                $diasHabiles[$i] = 0;
            }
        }

        return view('sg-dictamen.public', compact(
            'dictamenes', 'total', 'nuevo', 'meses', 'solicitudes', 'diasHabiles'
        ));
    }
    public function deletedDictamenes()
{
    // Obtenemos los registros de la tabla de auditoría ordenados por fecha
    $deletedLogs = \DB::table('dictamen_audit_log')->orderBy('deleted_at', 'DESC')->get();
    
    return view('sg-dictamen.deleted', compact('deletedLogs'));
}
}