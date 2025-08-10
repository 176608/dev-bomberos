<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller; // NOTA: Controller siempre debe ser esta dirección
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\CuadroEstadistico;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class AdminController extends Controller
{
    public function index()
    {
        // Verificar permisos de administrador
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && !$user->hasRole('Desarrollador')) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        return view('roles.sigem_admin');
    }

    public function mapas()
    {
        $mapas = Mapa::obtenerTodos();
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_mapa',
            'mapas' => $mapas
        ]);
    }

    public function temas()
    {
        $temas = Tema::orderBy('tema_titulo', 'asc')->get(); // Corregido: tema_titulo en lugar de nombre_tema
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_tema',
            'temas' => $temas
        ]);
    }

    public function subtemas()
    {
        $subtemas = Subtema::with('tema')->orderBy('subtema_titulo', 'asc')->get(); // Corregido: subtema_titulo
        $temas = Tema::orderBy('tema_titulo', 'asc')->get(); // Para el select de agregar/editar
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_subtema',
            'subtemas' => $subtemas,
            'temas' => $temas
        ]);
    }

    public function cuadros()
    {
        $cuadros = CuadroEstadistico::with(['subtema.tema'])->orderBy('cuadro_estadistico_titulo', 'asc')->get();
        $temas = Tema::orderBy('tema_titulo', 'asc')->get(); // Para el select de agregar/editar
        $subtemas = Subtema::with('tema')->orderBy('subtema_titulo', 'asc')->get(); // Para el select de agregar/editar
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_cuadro',
            'cuadros' => $cuadros,
            'temas' => $temas,
            'subtemas' => $subtemas
        ]);
    }

    public function consultas()
    {
        $ce_temas = ce_tema::obtenerTodos(); // Usar el método del modelo
        $ce_subtemas = ce_subtema::with('tema')->orderBy('ce_subtema_id', 'asc')->get(); // Corregir nombre de campo
        $ce_contenidos = ce_contenido::with(['subtema.tema'])->orderBy('created_at', 'desc')->get(); // Con relaciones
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_consultas',
            'ce_temas' => $ce_temas,
            'ce_subtemas' => $ce_subtemas,
            'ce_contenidos' => $ce_contenidos
        ]);
    }

    /**
     * Crear nuevo mapa
     */
    public function crearMapa(Request $request)
    {
        try {
            // Validar datos
            $request->validate([
                'nombre_mapa' => 'required|string|max:255',
                'nombre_seccion' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'enlace' => 'nullable|url',
                'codigo_mapa' => 'nullable|string|max:50',
                'icono' => 'nullable|file|mimes:png|max:2048' // 2MB max
            ]);

            $datos = $request->only([
                'nombre_mapa', 
                'nombre_seccion', 
                'descripcion', 
                'enlace', 
                'codigo_mapa'
            ]);

            // Manejar upload de icono
            if ($request->hasFile('icono')) {
                $archivo = $request->file('icono');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                
                // Mover archivo a la carpeta img/SIGEM_mapas/
                $archivo->move(public_path('img/SIGEM_mapas'), $nombreArchivo);
                $datos['icono'] = $nombreArchivo;
            }

            // Crear mapa usando el método del modelo
            $mapa = Mapa::crear($datos);

            return redirect()
                ->route('sigem.admin.mapas')
                ->with('success', 'Mapa creado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.mapas')
                ->with('error', 'Error al crear el mapa: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar mapa existente
     */
    public function actualizarMapa(Request $request, $id)
    {
        try {
            $mapa = Mapa::obtenerPorId($id);
            
            if (!$mapa) {
                return redirect()
                    ->route('sigem.admin.mapas')
                    ->with('error', 'Mapa no encontrado');
            }

            // Validar datos
            $request->validate([
                'nombre_mapa' => 'required|string|max:255',
                'nombre_seccion' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'enlace' => 'nullable|url',
                'codigo_mapa' => 'nullable|string|max:50',
                'icono' => 'nullable|file|mimes:png|max:2048'
            ]);

            $datos = $request->only([
                'nombre_mapa', 
                'nombre_seccion', 
                'descripcion', 
                'enlace', 
                'codigo_mapa'
            ]);

            // Manejar upload de nuevo icono
            if ($request->hasFile('icono')) {
                // Eliminar icono anterior si existe
                if ($mapa->icono && file_exists(public_path('img/SIGEM_mapas/' . $mapa->icono))) {
                    unlink(public_path('img/SIGEM_mapas/' . $mapa->icono));
                }

                $archivo = $request->file('icono');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move(public_path('img/SIGEM_mapas'), $nombreArchivo);
                $datos['icono'] = $nombreArchivo;
            }

            // Actualizar mapa
            $mapa->actualizar($datos);

            return redirect()
                ->route('sigem.admin.mapas')
                ->with('success', 'Mapa actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.mapas')
                ->with('error', 'Error al actualizar el mapa: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar mapa
     */
    public function eliminarMapa($id)
    {
        try {
            $mapa = Mapa::obtenerPorId($id);
            
            if (!$mapa) {
                return redirect()
                    ->route('sigem.admin.mapas')
                    ->with('error', 'Mapa no encontrado');
            }

            // Eliminar archivo de icono si existe
            if ($mapa->icono && file_exists(public_path('img/SIGEM_mapas/' . $mapa->icono))) {
                unlink(public_path('img/SIGEM_mapas/' . $mapa->icono));
            }

            // Guardar nombre para el mensaje
            $nombreMapa = $mapa->nombre_mapa;

            // Eliminar mapa
            $mapa->eliminar();

            return redirect()
                ->route('sigem.admin.mapas')
                ->with('success', "Mapa '{$nombreMapa}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.mapas')
                ->with('error', 'Error al eliminar el mapa: ' . $e->getMessage());
        }
    }

    // ============ MÉTODOS CRUD PARA TEMAS ============
    public function crearTema(Request $request)
    {
        try {
            // Validar datos
            $request->validate([
                'tema_titulo' => 'required|string|max:255',
                'clave_tema' => 'nullable|string|max:10|unique:tema,clave_tema',
                'orden_indice' => 'nullable|integer|min:0|max:999'
            ]);

            $datos = $request->only([
                'tema_titulo',
                'clave_tema', 
                'orden_indice'
            ]);

            // Si no se proporciona orden, usar el siguiente disponible
            if (!$datos['orden_indice']) {
                $maxOrden = Tema::max('orden_indice') ?? 0;
                $datos['orden_indice'] = $maxOrden + 1;
            }

            // Crear tema
            $tema = Tema::crear($datos);

            return redirect()
                ->route('sigem.admin.temas')
                ->with('success', "Tema '{$tema->tema_titulo}' creado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sigem.admin.temas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.temas')
                ->with('error', 'Error al crear el tema: ' . $e->getMessage());
        }
    }

    public function actualizarTema(Request $request, $id)
    {
        try {
            $tema = Tema::obtenerPorId($id);
            
            if (!$tema) {
                return redirect()
                    ->route('sigem.admin.temas')
                    ->with('error', 'Tema no encontrado');
            }

            // Validar datos (excluir el ID actual de la validación de clave única)
            $request->validate([
                'tema_titulo' => 'required|string|max:255',
                'clave_tema' => 'nullable|string|max:10|unique:tema,clave_tema,' . $id . ',tema_id',
                'orden_indice' => 'nullable|integer|min:0|max:999'
            ]);

            $datos = $request->only([
                'tema_titulo',
                'clave_tema',
                'orden_indice'
            ]);

            // Actualizar tema
            $tema->actualizar($datos);

            return redirect()
                ->route('sigem.admin.temas')
                ->with('success', "Tema '{$tema->tema_titulo}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sigem.admin.temas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.temas')
                ->with('error', 'Error al actualizar el tema: ' . $e->getMessage());
        }
    }

    public function eliminarTema($id)
    {
        try {
            $tema = Tema::obtenerPorId($id);
            
            if (!$tema) {
                return redirect()
                    ->route('sigem.admin.temas')
                    ->with('error', 'Tema no encontrado');
            }

            // Verificar si tiene subtemas asociados
            $subtemasCount = $tema->subtemas()->count();
            
            if ($subtemasCount > 0) {
                return redirect()
                    ->route('sigem.admin.temas')
                    ->with('error', "No se puede eliminar el tema '{$tema->tema_titulo}' porque tiene {$subtemasCount} subtema(s) asociado(s). Elimine o reasigne los subtemas primero.");
            }

            // Guardar nombre para el mensaje
            $nombreTema = $tema->tema_titulo;

            // Eliminar tema
            $tema->eliminar();

            return redirect()
                ->route('sigem.admin.temas')
                ->with('success', "Tema '{$nombreTema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.temas')
                ->with('error', 'Error al eliminar el tema: ' . $e->getMessage());
        }
    }

    // ============ MÉTODOS CRUD PARA SUBTEMAS ============
    public function crearSubtema(Request $request)
    {
        try {
            // Validar datos
            $request->validate([
                'subtema_titulo' => 'required|string|max:255',
                'tema_id' => 'required|integer|exists:tema,tema_id',
                'clave_subtema' => 'nullable|string|max:15',
                'orden_indice' => 'nullable|integer|min:0|max:999',
                'imagen' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            $datos = $request->only([
                'subtema_titulo',
                'tema_id',
                'clave_subtema'
            ]);

            // Si no se proporciona orden, obtener el siguiente disponible para el tema específico
            if ($request->filled('orden_indice')) {
                $datos['orden_indice'] = $request->orden_indice;
            } else {
                $datos['orden_indice'] = Subtema::siguienteOrden($request->tema_id);
            }

            // Manejar upload de imagen
            if ($request->hasFile('imagen')) {
                $archivo = $request->file('imagen');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                
                // Crear directorio si no existe
                $directorioImagenes = public_path('imagenes/subtemas_u');
                if (!file_exists($directorioImagenes)) {
                    mkdir($directorioImagenes, 0755, true);
                }
                
                $archivo->move($directorioImagenes, $nombreArchivo);
                $datos['imagen'] = $nombreArchivo;
            }

            // Crear subtema
            $subtema = Subtema::crear($datos);

            return redirect()
                ->route('sigem.admin.subtemas')
                ->with('success', "Subtema '{$subtema->subtema_titulo}' creado exitosamente en orden {$subtema->orden_indice}");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sigem.admin.subtemas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.subtemas')
                ->with('error', 'Error al crear el subtema: ' . $e->getMessage());
        }
    }

    public function actualizarSubtema(Request $request, $id)
    {
        try {
            $subtema = Subtema::obtenerPorId($id);
            
            if (!$subtema) {
                return redirect()
                    ->route('sigem.admin.subtemas')
                    ->with('error', 'Subtema no encontrado');
            }

            // Validar datos
            $request->validate([
                'subtema_titulo' => 'required|string|max:255',
                'tema_id' => 'required|integer|exists:tema,tema_id',
                'clave_subtema' => 'nullable|string|max:15',
                'orden_indice' => 'nullable|integer|min:0|max:999',
                'imagen' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            $datos = $request->only([
                'subtema_titulo',
                'tema_id',
                'clave_subtema'
            ]);

            // Si cambió el tema, recalcular el orden
            if ($request->tema_id != $subtema->tema_id) {
                // Cambió de tema, asignar siguiente orden del nuevo tema
                $datos['orden_indice'] = Subtema::siguienteOrden($request->tema_id);
            } else {
                // Mismo tema, conservar orden o usar el proporcionado
                $datos['orden_indice'] = $request->filled('orden_indice') 
                    ? $request->orden_indice 
                    : $subtema->orden_indice;
            }

            // Manejar upload de nueva imagen
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($subtema->imagen && file_exists(public_path('imagenes/subtemas_u/' . $subtema->imagen))) {
                    unlink(public_path('imagenes/subtemas_u/' . $subtema->imagen));
                }

                $archivo = $request->file('imagen');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                
                $directorioImagenes = public_path('imagenes/subtemas_u');
                if (!file_exists($directorioImagenes)) {
                    mkdir($directorioImagenes, 0755, true);
                }
                
                $archivo->move($directorioImagenes, $nombreArchivo);
                $datos['imagen'] = $nombreArchivo;
            }

            // Actualizar subtema
            $subtema->actualizar($datos);

            return redirect()
                ->route('sigem.admin.subtemas')
                ->with('success', "Subtema '{$subtema->subtema_titulo}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sigem.admin.subtemas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.subtemas')
                ->with('error', 'Error al actualizar el subtema: ' . $e->getMessage());
        }
    }

    public function eliminarSubtema($id)
    {
        try {
            $subtema = Subtema::obtenerPorId($id);
            
            if (!$subtema) {
                return redirect()
                    ->route('sigem.admin.subtemas')
                    ->with('error', 'Subtema no encontrado');
            }

            // Verificar si hay cuadros estadísticos asociados
            $cuadrosCount = CuadroEstadistico::where('subtema_id', $id)->count();
            
            if ($cuadrosCount > 0) {
                return redirect()
                    ->route('sigem.admin.subtemas')
                    ->with('error', "No se puede eliminar el subtema '{$subtema->subtema_titulo}' porque tiene {$cuadrosCount} cuadro(s) estadístico(s) asociado(s). Elimine o reasigne los cuadros primero.");
            }

            // Eliminar archivo de imagen si existe
            if ($subtema->imagen && file_exists(public_path('imagenes/subtemas_u/' . $subtema->imagen))) {
                unlink(public_path('imagenes/subtemas_u/' . $subtema->imagen));
            }

            // Guardar datos para el mensaje
            $nombreSubtema = $subtema->subtema_titulo;
            $nombreTema = $subtema->tema ? $subtema->tema->tema_titulo : 'Sin tema';

            // Eliminar subtema
            $subtema->eliminar();

            return redirect()
                ->route('sigem.admin.subtemas')
                ->with('success', "Subtema '{$nombreSubtema}' del tema '{$nombreTema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.subtemas')
                ->with('error', 'Error al eliminar el subtema: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Obtener siguiente orden para un tema específico
     */
    public function obtenerSiguienteOrdenTema($tema_id)
    {
        try {
            $siguienteOrden = Subtema::siguienteOrden($tema_id);
            return response()->json(['siguiente_orden' => $siguienteOrden]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el orden'], 500);
        }
    }

    // ============ MÉTODOS CRUD PARA CUADROS ============
    public function crearCuadro(Request $request)
    {
        try {
            // Validar datos
            $request->validate([
                'codigo_cuadro' => 'required|string|max:50|unique:cuadro_estadistico,codigo_cuadro',
                'cuadro_estadistico_titulo' => 'required|string|max:255',
                'cuadro_estadistico_subtitulo' => 'nullable|string|max:500',
                'subtema_id' => 'required|integer|exists:subtema,subtema_id',
                'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120', // 5MB max
                'pdf_file' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
                'permite_grafica' => 'nullable|boolean',
                'tipo_grafica_permitida' => 'nullable|string|in:bar,line,pie,doughnut',
                'eje_vertical_mchart' => 'nullable|string|max:100',
                'pie_pagina' => 'nullable|string|max:500',
                'invertir_eje_vertical_horizontal' => 'nullable|boolean'
            ]);

            $datos = $request->only([
                'codigo_cuadro',
                'cuadro_estadistico_titulo',
                'cuadro_estadistico_subtitulo',
                'subtema_id',
                'eje_vertical_mchart',
                'pie_pagina'
            ]);

            // Manejar checkboxes
            $datos['permite_grafica'] = $request->has('permite_grafica');
            $datos['invertir_eje_vertical_horizontal'] = $request->has('invertir_eje_vertical_horizontal');
            $datos['tipo_grafica_permitida'] = $datos['permite_grafica'] ? $request->tipo_grafica_permitida : null;

            // Crear directorios si no existen
            $directorioExcel = public_path('archivos/cuadros_estadisticos/excel');
            $directorioPdf = public_path('archivos/cuadros_estadisticos/pdf');
            
            if (!file_exists($directorioExcel)) {
                mkdir($directorioExcel, 0755, true);
            }
            if (!file_exists($directorioPdf)) {
                mkdir($directorioPdf, 0755, true);
            }

            // Manejar upload de archivo Excel
            if ($request->hasFile('excel_file')) {
                $archivo = $request->file('excel_file');
                $nombreArchivo = $datos['codigo_cuadro'] . '_' . time() . '.' . $archivo->getClientOriginalExtension();
                $archivo->move($directorioExcel, $nombreArchivo);
                $datos['excel_file'] = $nombreArchivo;
            }

            // Manejar upload de archivo PDF
            if ($request->hasFile('pdf_file')) {
                $archivo = $request->file('pdf_file');
                $nombreArchivo = $datos['codigo_cuadro'] . '_' . time() . '.' . $archivo->getClientOriginalExtension();
                $archivo->move($directorioPdf, $nombreArchivo);
                $datos['pdf_file'] = $nombreArchivo;
            }

            // Crear cuadro
            $cuadro = CuadroEstadistico::crear($datos);

            return redirect()
                ->route('sigem.admin.cuadros')
                ->with('success', "Cuadro estadístico '{$cuadro->cuadro_estadistico_titulo}' creado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sigem.admin.cuadros')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.cuadros')
                ->with('error', 'Error al crear el cuadro estadístico: ' . $e->getMessage());
        }
    }

    public function actualizarCuadro(Request $request, $id)
    {
        try {
            $cuadro = CuadroEstadistico::obtenerPorId($id);
            
            if (!$cuadro) {
                return redirect()
                    ->route('sigem.admin.cuadros')
                    ->with('error', 'Cuadro estadístico no encontrado');
            }

            // Validar datos (excluir el ID actual de la validación de código único)
            $request->validate([
                'codigo_cuadro' => 'required|string|max:50|unique:cuadro_estadistico,codigo_cuadro,' . $id . ',cuadro_estadistico_id',
                'cuadro_estadistico_titulo' => 'required|string|max:255',
                'cuadro_estadistico_subtitulo' => 'nullable|string|max:500',
                'subtema_id' => 'required|integer|exists:subtema,subtema_id',
                'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120',
                'pdf_file' => 'nullable|file|mimes:pdf|max:5120',
                'permite_grafica' => 'nullable|boolean',
                'tipo_grafica_permitida' => 'nullable|string|in:bar,line,pie,doughnut',
                'eje_vertical_mchart' => 'nullable|string|max:100',
                'pie_pagina' => 'nullable|string|max:500',
                'invertir_eje_vertical_horizontal' => 'nullable|boolean'
            ]);

            $datos = $request->only([
                'codigo_cuadro',
                'cuadro_estadistico_titulo',
                'cuadro_estadistico_subtitulo',
                'subtema_id',
                'eje_vertical_mchart',
                'pie_pagina'
            ]);

            // Manejar checkboxes
            $datos['permite_grafica'] = $request->has('permite_grafica');
            $datos['invertir_eje_vertical_horizontal'] = $request->has('invertir_eje_vertical_horizontal');
            $datos['tipo_grafica_permitida'] = $datos['permite_grafica'] ? $request->tipo_grafica_permitida : null;

            // Directorios para archivos
            $directorioExcel = public_path('archivos/cuadros_estadisticos/excel');
            $directorioPdf = public_path('archivos/cuadros_estadisticos/pdf');

            // Manejar upload de nuevo archivo Excel
            if ($request->hasFile('excel_file')) {
                // Eliminar archivo anterior si existe
                if ($cuadro->excel_file && file_exists($directorioExcel . '/' . $cuadro->excel_file)) {
                    unlink($directorioExcel . '/' . $cuadro->excel_file);
                }

                $archivo = $request->file('excel_file');
                $nombreArchivo = $datos['codigo_cuadro'] . '_' . time() . '.' . $archivo->getClientOriginalExtension();
                $archivo->move($directorioExcel, $nombreArchivo);
                $datos['excel_file'] = $nombreArchivo;
            }

            // Manejar upload de nuevo archivo PDF
            if ($request->hasFile('pdf_file')) {
                // Eliminar archivo anterior si existe
                if ($cuadro->pdf_file && file_exists($directorioPdf . '/' . $cuadro->pdf_file)) {
                    unlink($directorioPdf . '/' . $cuadro->pdf_file);
                }

                $archivo = $request->file('pdf_file');
                $nombreArchivo = $datos['codigo_cuadro'] . '_' . time() . '.' . $archivo->getClientOriginalExtension();
                $archivo->move($directorioPdf, $nombreArchivo);
                $datos['pdf_file'] = $nombreArchivo;
            }

            // Actualizar cuadro
            $cuadro->actualizar($datos);

            return redirect()
                ->route('sigem.admin.cuadros')
                ->with('success', "Cuadro estadístico '{$cuadro->cuadro_estadistico_titulo}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sigem.admin.cuadros')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.cuadros')
                ->with('error', 'Error al actualizar el cuadro estadístico: ' . $e->getMessage());
        }
    }

    public function eliminarCuadro($id)
    {
        try {
            $cuadro = CuadroEstadistico::obtenerPorId($id);
            
            if (!$cuadro) {
                return redirect()
                    ->route('sigem.admin.cuadros')
                    ->with('error', 'Cuadro estadístico no encontrado');
            }

            // Eliminar archivos físicos si existen
            if ($cuadro->excel_file && file_exists(public_path('archivos/cuadros_estadisticos/excel/' . $cuadro->excel_file))) {
                unlink(public_path('archivos/cuadros_estadisticos/excel/' . $cuadro->excel_file));
            }

            if ($cuadro->pdf_file && file_exists(public_path('archivos/cuadros_estadisticos/pdf/' . $cuadro->pdf_file))) {
                unlink(public_path('archivos/cuadros_estadisticos/pdf/' . $cuadro->pdf_file));
            }

            // Guardar datos para el mensaje
            $nombreCuadro = $cuadro->cuadro_estadistico_titulo;
            $codigoCuadro = $cuadro->codigo_cuadro;

            // Eliminar cuadro
            $cuadro->eliminar();

            return redirect()
                ->route('sigem.admin.cuadros')
                ->with('success', "Cuadro estadístico '{$nombreCuadro}' (código: {$codigoCuadro}) eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sigem.admin.cuadros')
                ->with('error', 'Error al eliminar el cuadro estadístico: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Obtener subtemas por tema
     */
    public function obtenerSubtemasPorTema($tema_id)
    {
        try {
            $subtemas = Subtema::where('tema_id', $tema_id)
                              ->orderBy('orden_indice', 'asc')
                              ->orderBy('subtema_titulo', 'asc')
                              ->get(['subtema_id', 'subtema_titulo']);
            
            return response()->json(['subtemas' => $subtemas]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener subtemas'], 500);
        }
    }

    // ============ MÉTODOS CRUD PARA CONSULTAS EXPRESS ============
    public function crearTemaCE(Request $request)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.consultas')->with('error', 'Método no implementado aún');
    }

    public function crearSubtemaCE(Request $request)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.consultas')->with('error', 'Método no implementado aún');
    }

    public function crearContenidoCE(Request $request)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.consultas')->with('error', 'Método no implementado aún');
    }
}
