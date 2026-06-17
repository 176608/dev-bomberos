<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Controllers\GestorSIGEM\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\CuadroEstadistico;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;
use App\Services\FileContentValidator;
use App\Services\SecureFileUpload;

class AdminController extends Controller
{
    protected SecureFileUpload $fileUploader;
    protected FileContentValidator $fileValidator;

    public function __construct()
    {
        $this->fileUploader = new SecureFileUpload();
        $this->fileValidator = $this->fileUploader->getValidator();
    }

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

        return view('GestorSIGEM.layout');
    }

    public function mapas()
    {
        return redirect()->route('sgiem.admin.index')
            ->with('error', 'Mapas no disponibles en SGIEM.');
    }

    public function temas()
    {
        $temas = TemaV2::orderBy('tema_titulo', 'asc')->get(); // Corregido: tema_titulo en lugar de nombre_tema
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_tema',
            'temas' => $temas
        ]);
    }

    public function subtemas()
    {
        $subtemas = SubtemaV2::with('tema')->orderBy('subtema_titulo', 'asc')->get(); // Corregido: subtema_titulo
        $temas = TemaV2::orderBy('tema_titulo', 'asc')->get(); // Para el select de agregar/editar
        
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_subtema',
            'subtemas' => $subtemas,
            'temas' => $temas
        ]);
    }

    public function cuadros()
    {
        $cuadros = CuadroEstadistico::with(['subtema.tema'])->orderBy('cuadro_estadistico_titulo', 'asc')->get();
        $temas = TemaV2::orderBy('orden_indice', 'asc')->get(); // Para el select de agregar/editar
        $subtemas = SubtemaV2::with('tema')->orderBy('subtema_titulo', 'asc')->get(); // Para el select de agregar/editar
        
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_cuadro',
            'cuadros' => $cuadros,
            'temas' => $temas,
            'subtemas' => $subtemas
        ]);
    }

    public function consultas()
    {
        $ce_temas = ce_tema::obtenerTodos(); // Usar el método del modelo
        $ce_subtemas = ce_subtema::obtenerTodos(); // Usar el método del modelo
        $ce_contenidos = ce_contenido::with(['subtema.tema'])->orderBy('created_at', 'desc')->get(); // Con relaciones
        
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_consultas',
            'ce_temas' => $ce_temas,
            'ce_subtemas' => $ce_subtemas,
            'ce_contenidos' => $ce_contenidos
        ]);
    }

    // Mapas eliminado de SGIEM (disponible solo en SIGEM legacy)

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
                $maxOrden = TemaV2::max('orden_indice') ?? 0;
                $datos['orden_indice'] = $maxOrden + 1;
            }

            // Crear tema
            $tema = TemaV2::crear($datos);

            return redirect()
                ->route('sgiem.admin.temas')
                ->with('success', "Tema '{$tema->tema_titulo}' creado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', 'Error al crear el tema: ' . $e->getMessage());
        }
    }

    public function actualizarTema(Request $request, $id)
    {
        try {
            $tema = TemaV2::obtenerPorId($id);
            
            if (!$tema) {
                return redirect()
                    ->route('sgiem.admin.temas')
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
                ->route('sgiem.admin.temas')
                ->with('success', "Tema '{$tema->tema_titulo}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', 'Error al actualizar el tema: ' . $e->getMessage());
        }
    }

    public function eliminarTema($id)
    {
        try {
            $tema = TemaV2::obtenerPorId($id);
            
            if (!$tema) {
                return redirect()
                    ->route('sgiem.admin.temas')
                    ->with('error', 'Tema no encontrado');
            }

            // Verificar si tiene subtemas asociados
            $subtemasCount = $tema->subtemas()->count();
            
            if ($subtemasCount > 0) {
                return redirect()
                    ->route('sgiem.admin.temas')
                    ->with('error', "No se puede eliminar el tema '{$tema->tema_titulo}' porque tiene {$subtemasCount} subtema(s) asociado(s). Elimine o reasigne los subtemas primero.");
            }

            // Guardar nombre para el mensaje
            $nombreTema = $tema->tema_titulo;

            // Eliminar tema
            $tema->eliminar();

            return redirect()
                ->route('sgiem.admin.temas')
                ->with('success', "Tema '{$nombreTema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', 'Error al eliminar el tema: ' . $e->getMessage());
        }
    }

    // ============ MÉTODOS CRUD PARA SUBTEMAS ============
    public function crearSubtema(Request $request)
    {
        try {
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

            if ($request->filled('orden_indice')) {
                $datos['orden_indice'] = $request->orden_indice;
            } else {
                $datos['orden_indice'] = SubtemaV2::siguienteOrden($request->tema_id);
            }

            if ($request->hasFile('imagen')) {
                try {
                    $datos['imagen'] = $this->fileUploader->uploadImage($request->file('imagen'));
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.subtemas')
                        ->with('error', 'Error de seguridad en el archivo: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $subtema = SubtemaV2::crear($datos);

            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('success', "Subtema '{$subtema->subtema_titulo}' creado exitosamente en orden {$subtema->orden_indice}");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error al crear el subtema: ' . $e->getMessage());
        }
    }

    public function actualizarSubtema(Request $request, $id)
    {
        try {
            $subtema = SubtemaV2::obtenerPorId($id);
            
            if (!$subtema) {
                return redirect()
                    ->route('sgiem.admin.subtemas')
                    ->with('error', 'Subtema no encontrado');
            }

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

            if ($request->tema_id != $subtema->tema_id) {
                $datos['orden_indice'] = SubtemaV2::siguienteOrden($request->tema_id);
            } else {
                $datos['orden_indice'] = $request->filled('orden_indice') 
                    ? $request->orden_indice 
                    : $subtema->orden_indice;
            }

            if ($request->has('remove_imagen') && $request->remove_imagen == '1') {
                if ($subtema->imagen) {
                    $this->fileUploader->deleteFile($subtema->imagen, 'imagenes/subtemas_u');
                }
                $datos['imagen'] = null;
            }
            elseif ($request->hasFile('imagen')) {
                try {
                    $datos['imagen'] = $this->fileUploader->uploadImage(
                        $request->file('imagen'),
                        $subtema->imagen
                    );
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.subtemas')
                        ->with('error', 'Error de seguridad en el archivo: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $subtema->actualizar($datos);

            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('success', "Subtema '{$subtema->subtema_titulo}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error al actualizar el subtema: ' . $e->getMessage());
        }
    }

    public function eliminarSubtema($id)
    {
        try {
            $subtema = SubtemaV2::obtenerPorId($id);
            
            if (!$subtema) {
                return redirect()
                    ->route('sgiem.admin.subtemas')
                    ->with('error', 'Subtema no encontrado');
            }

            $cuadrosCount = CuadroEstadistico::where('subtema_id', $id)->count();
            
            if ($cuadrosCount > 0) {
                return redirect()
                    ->route('sgiem.admin.subtemas')
                    ->with('error', "No se puede eliminar el subtema '{$subtema->subtema_titulo}' porque tiene {$cuadrosCount} cuadro(s) estadístico(s) asociado(s). Elimine o reasigne los cuadros primero.");
            }

            if ($subtema->imagen) {
                $this->fileUploader->deleteFile($subtema->imagen, 'imagenes/subtemas_u');
            }

            $nombreSubtema = $subtema->subtema_titulo;
            $nombreTema = $subtema->tema ? $subtema->tema->tema_titulo : 'Sin tema';

            $subtema->eliminar();

            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('success', "Subtema '{$nombreSubtema}' del tema '{$nombreTema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error al eliminar el subtema: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Obtener siguiente orden para un tema específico
     */
    public function obtenerSiguienteOrdenTema($tema_id)
    {
        try {
            $siguienteOrden = SubtemaV2::siguienteOrden($tema_id);
            return response()->json(['siguiente_orden' => $siguienteOrden]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el orden'], 500);
        }
    }

    /**
     * AJAX: Obtener subtemas por tema
     */
    public function obtenerSubtemasPorTema($tema_id)
    {
        try {
            $subtemas = SubtemaV2::where('tema_id', $tema_id)
                              ->orderBy('orden_indice', 'asc')
                              ->orderBy('subtema_titulo', 'asc')
                              ->get(['subtema_id', 'subtema_titulo']);
            
            return response()->json(['subtemas' => $subtemas]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener subtemas'], 500);
        }
    }

    // ============ MÉTODOS CRUD PARA CUADROS ============
    public function crearCuadro(Request $request)
    {
        try {
            $request->validate([
                'codigo_cuadro' => 'required|string|max:50|unique:cuadro_estadistico,codigo_cuadro',
                'cuadro_estadistico_titulo' => 'required|string|max:255',
                'cuadro_estadistico_subtitulo' => 'nullable|string|max:500',
                'subtema_id' => 'required|integer|exists:subtema,subtema_id',
                'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120|prohibited_if:tipo_mapa_pdf,1',
                'pdf_file' => 'nullable|file|mimes:pdf|max:5120',
                'excel_formated_file' => 'nullable|file|mimes:xlsx,xls|max:5120|prohibited_if:tipo_mapa_pdf,1',
                'permite_grafica' => 'nullable|boolean|prohibited_if:tipo_mapa_pdf,1',
                'tipo_mapa_pdf' => 'nullable|boolean',
                'pie_pagina' => 'nullable|string|max:50000'
            ]);

            $datos = $request->only([
                'codigo_cuadro',
                'cuadro_estadistico_titulo',
                'cuadro_estadistico_subtitulo',
                'subtema_id',
                'pie_pagina'
            ]);

            $isMapaPdf = $request->has('tipo_mapa_pdf') && $request->tipo_mapa_pdf == '1';
            $datos['permite_grafica'] = $isMapaPdf ? false : $request->has('permite_grafica');
            $datos['tipo_mapa_pdf'] = $isMapaPdf ? 1 : 0;

            $fecha = date('d_m_Y');

            if (!$isMapaPdf && $request->hasFile('excel_file')) {
                try {
                    $datos['excel_file'] = $this->fileUploader->uploadExcel($request->file('excel_file'));
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.cuadros')
                        ->with('error', 'Error de seguridad en archivo Excel: ' . $e->getMessage())
                        ->withInput();
                }
            }

            if ($request->hasFile('pdf_file')) {
                try {
                    $datos['pdf_file'] = $this->fileUploader->uploadPDF($request->file('pdf_file'));
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.cuadros')
                        ->with('error', 'Error de seguridad en archivo PDF: ' . $e->getMessage())
                        ->withInput();
                }
            }

            if (!$isMapaPdf && $request->hasFile('excel_formated_file')) {
                try {
                    $datos['excel_formated_file'] = $this->fileUploader->uploadExcelFormated($request->file('excel_formated_file'));
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.cuadros')
                        ->with('error', 'Error de seguridad en archivo Excel: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $cuadro = CuadroEstadistico::crear($datos);

            return redirect()
                ->route('sgiem.admin.cuadros')
                ->with('success', "Cuadro estadístico '{$cuadro->cuadro_estadistico_titulo}' (código: {$cuadro->codigo_cuadro}) creado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.cuadros')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.cuadros')
                ->with('error', 'Error al crear el cuadro estadístico: ' . $e->getMessage());
        }
    }

    public function actualizarCuadro(Request $request, $id)
    {
        try {
            $cuadro = CuadroEstadistico::obtenerPorId($id);
            
            if (!$cuadro) {
                return redirect()
                    ->route('sgiem.admin.cuadros')
                    ->with('error', 'Cuadro estadístico no encontrado');
            }

            $request->validate([
                'codigo_cuadro' => 'required|string|max:50|unique:cuadro_estadistico,codigo_cuadro,' . $id . ',cuadro_estadistico_id',
                'cuadro_estadistico_titulo' => 'required|string|max:255',
                'cuadro_estadistico_subtitulo' => 'nullable|string|max:500',
                'subtema_id' => 'required|integer|exists:subtema,subtema_id',
                'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120',
                'pdf_file' => 'nullable|file|mimes:pdf|max:5120',
                'excel_formated_file' => 'nullable|file|mimes:xlsx,xls|max:5120',
                'permite_grafica' => 'nullable|boolean',
                'pie_pagina' => 'nullable|string|max:50000'
            ]);

            $datos = $request->only([
                'codigo_cuadro',
                'cuadro_estadistico_titulo',
                'cuadro_estadistico_subtitulo',
                'subtema_id',
                'pie_pagina'
            ]);

            $isMapaPdf = $request->has('tipo_mapa_pdf') && $request->tipo_mapa_pdf == '1';
            $datos['permite_grafica'] = $isMapaPdf ? false : $request->has('permite_grafica');
            $datos['tipo_mapa_pdf'] = $isMapaPdf ? 1 : 0;

            if ($isMapaPdf) {
                if ($cuadro->excel_file) {
                    $this->fileUploader->deleteFile($cuadro->excel_file, 'u_excel');
                }
                if ($cuadro->excel_formated_file) {
                    $this->fileUploader->deleteFile($cuadro->excel_formated_file, 'u_xlsx_formated');
                }
                $datos['excel_file'] = null;
                $datos['excel_formated_file'] = null;
            } else {
                if ($request->has('remove_excel') && $request->remove_excel == '1') {
                    if ($cuadro->excel_file) {
                        $this->fileUploader->deleteFile($cuadro->excel_file, 'u_excel');
                    }
                    $datos['excel_file'] = null;
                }
                elseif ($request->hasFile('excel_file')) {
                    try {
                        $datos['excel_file'] = $this->fileUploader->uploadExcel(
                            $request->file('excel_file'),
                            $cuadro->excel_file
                        );
                    } catch (\InvalidArgumentException $e) {
                        return redirect()
                            ->route('sgiem.admin.cuadros')
                            ->with('error', 'Error de seguridad en archivo Excel: ' . $e->getMessage())
                            ->withInput();
                    }
                }
            }

            if ($request->has('remove_pdf') && $request->remove_pdf == '1') {
                if ($cuadro->pdf_file) {
                    $this->fileUploader->deleteFile($cuadro->pdf_file, 'u_pdf');
                }
                $datos['pdf_file'] = null;
            }
            elseif ($request->hasFile('pdf_file')) {
                if ($isMapaPdf) {
                    if ($cuadro->excel_file) {
                        $this->fileUploader->deleteFile($cuadro->excel_file, 'u_excel');
                    }
                    if ($cuadro->excel_formated_file) {
                        $this->fileUploader->deleteFile($cuadro->excel_formated_file, 'u_xlsx_formated');
                    }
                    $datos['excel_file'] = null;
                    $datos['excel_formated_file'] = null;
                    $datos['permite_grafica'] = false;
                }
                try {
                    $datos['pdf_file'] = $this->fileUploader->uploadPDF(
                        $request->file('pdf_file'),
                        $cuadro->pdf_file
                    );
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.cuadros')
                        ->with('error', 'Error de seguridad en archivo PDF: ' . $e->getMessage())
                        ->withInput();
                }
            }

            if ($request->has('remove_excel_formated') && $request->remove_excel_formated == '1') {
                if ($cuadro->excel_formated_file) {
                    $this->fileUploader->deleteFile($cuadro->excel_formated_file, 'u_xlsx_formated');
                }
                $datos['excel_formated_file'] = null;
            }
            elseif (!$isMapaPdf && $request->hasFile('excel_formated_file')) {
                try {
                    $datos['excel_formated_file'] = $this->fileUploader->uploadExcelFormated(
                        $request->file('excel_formated_file'),
                        $cuadro->excel_formated_file
                    );
                } catch (\InvalidArgumentException $e) {
                    return redirect()
                        ->route('sgiem.admin.cuadros')
                        ->with('error', 'Error de seguridad en archivo Excel: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $cuadro->actualizar($datos);

            return redirect()
                ->route('sgiem.admin.cuadros')
                ->with('success', "Cuadro estadístico '{$cuadro->cuadro_estadistico_titulo}' (código: {$cuadro->codigo_cuadro}) actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.cuadros')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.cuadros')
                ->with('error', 'Error al actualizar el cuadro estadístico: ' . $e->getMessage());
        }
    }

      public function eliminarCuadro($id)
    {
        try {
            $cuadro = CuadroEstadistico::obtenerPorId($id);
            
            if (!$cuadro) {
                return redirect()
                    ->route('sgiem.admin.cuadros')
                    ->with('error', 'Cuadro estadístico no encontrado');
            }

            if ($cuadro->excel_file) {
                $this->fileUploader->deleteFile($cuadro->excel_file, 'u_excel');
            }

            if ($cuadro->pdf_file) {
                $this->fileUploader->deleteFile($cuadro->pdf_file, 'u_pdf');
            }

            if ($cuadro->excel_formated_file) {
                $this->fileUploader->deleteFile($cuadro->excel_formated_file, 'u_xlsx_formated');
            }

            $nombreCuadro = $cuadro->cuadro_estadistico_titulo;
            $codigoCuadro = $cuadro->codigo_cuadro;

            $cuadro->eliminar();

            return redirect()
                ->route('sgiem.admin.cuadros')
                ->with('success', "Cuadro estadístico '{$nombreCuadro}' (código: {$codigoCuadro}) eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.cuadros')
                ->with('error', 'Error al eliminar el cuadro estadístico: ' . $e->getMessage());
        }
    }

    public function obtenerCuadroParaEdicion($id)
    {
        try {
            $cuadro = CuadroEstadistico::obtenerPorId($id);

            if (!$cuadro) {
                return response()->json(['error' => 'Cuadro no encontrado'], 404);
            }

            return response()->json([
                'codigo_cuadro' => $cuadro->codigo_cuadro,
                'cuadro_estadistico_titulo' => $cuadro->cuadro_estadistico_titulo,
                'cuadro_estadistico_subtitulo' => $cuadro->cuadro_estadistico_subtitulo,
                'subtema_id' => $cuadro->subtema_id,
                'excel_file' => $cuadro->excel_file,
                'pdf_file' => $cuadro->pdf_file,
                'excel_formated_file' => $cuadro->excel_formated_file,
                'tipo_mapa_pdf' => isset($cuadro->tipo_mapa_pdf) ? (int)$cuadro->tipo_mapa_pdf : 0,
                'permite_grafica' => $cuadro->permite_grafica,
                'pie_pagina' => $cuadro->pie_pagina,
                'subtema' => $cuadro->subtema ? [
                    'subtema_id' => $cuadro->subtema->subtema_id,
                    'tema' => $cuadro->subtema->tema ? [
                        'tema_id' => $cuadro->subtema->tema->tema_id
                    ] : null
                ] : null
            ]);

        } catch (\Exception $e) {
            \Log::error('obtenerCuadroParaEdicion error', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // ============ MÉTODOS CRUD PARA CONSULTAS EXPRESS ============
    
    /**
     * Crear nuevo tema CE
     */
    public function crearTemaCE(Request $request)
    {
        try {
            // Validar datos
            $request->validate([
                'tema' => 'required|string|max:255|unique:consulta_express_tema,tema'
            ]);

            // Crear tema CE usando el método del modelo
            $temaCE = ce_tema::crear([
                'tema' => $request->tema
            ]);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Tema CE '{$temaCE->tema}' creado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al crear el tema CE: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar tema CE
     */
    public function actualizarTemaCE(Request $request, $id)
    {
        try {
            $temaCE = ce_tema::obtenerPorId($id);
            
            if (!$temaCE) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', 'Tema CE no encontrado');
            }

            // Validar datos (excluir el ID actual de la validación única)
            $request->validate([
                'tema' => 'required|string|max:255|unique:consulta_express_tema,tema,' . $id . ',ce_tema_id'
            ]);

            // Actualizar tema CE usando el método del modelo
            $temaCE->actualizar([
                'tema' => $request->tema
            ]);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Tema CE actualizado a '{$temaCE->tema}' exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al actualizar el tema CE: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar tema CE
     */
    public function eliminarTemaCE($id)
    {
        try {
            $temaCE = ce_tema::obtenerPorId($id);
            
            if (!$temaCE) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', 'Tema CE no encontrado');
            }

            // Verificar si tiene subtemas asociados
            $subtemasCount = $temaCE->subtemas()->count();
            
            if ($subtemasCount > 0) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', "No se puede eliminar el tema CE '{$temaCE->tema}' porque tiene {$subtemasCount} subtema(s) asociado(s). Elimine o reasigne los subtemas primero.");
            }

            // Guardar nombre para el mensaje
            $nombreTema = $temaCE->tema;

            // Eliminar tema CE usando el método del modelo
            $temaCE->eliminar();

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Tema CE '{$nombreTema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al eliminar el tema CE: ' . $e->getMessage());
        }
    }

    /**
     * Crear nuevo subtema CE
     */
    public function crearSubtemaCE(Request $request)
    {
        try {
            // Validar datos
            $request->validate([
                'ce_tema_id' => 'required|integer|exists:consulta_express_tema,ce_tema_id',
                'ce_subtema' => 'required|string|max:255'
            ]);

            // Crear subtema CE usando el método del modelo
            $subtemaCE = ce_subtema::crear([
                'ce_tema_id' => $request->ce_tema_id,
                'ce_subtema' => $request->ce_subtema
            ]);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Subtema CE '{$subtemaCE->ce_subtema}' creado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al crear el subtema CE: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar subtema CE
     */
    public function actualizarSubtemaCE(Request $request, $id)
    {
        try {
            $subtemaCE = ce_subtema::obtenerPorId($id);
            
            if (!$subtemaCE) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', 'Subtema CE no encontrado');
            }

            // Validar datos
            $request->validate([
                'ce_tema_id' => 'required|integer|exists:consulta_express_tema,ce_tema_id',
                'ce_subtema' => 'required|string|max:255'
            ]);

            // Actualizar subtema CE usando el método del modelo
            $subtemaCE->actualizar([
                'ce_tema_id' => $request->ce_tema_id,
                'ce_subtema' => $request->ce_subtema
            ]);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Subtema CE '{$subtemaCE->ce_subtema}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al actualizar el subtema CE: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar subtema CE
     */
    public function eliminarSubtemaCE($id)
    {
        try {
            $subtemaCE = ce_subtema::obtenerPorId($id);
            
            if (!$subtemaCE) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', 'Subtema CE no encontrado');
            }

            // Verificar si tiene contenidos asociados
            $contenidosCount = $subtemaCE->contenidos()->count();
            
            if ($contenidosCount > 0) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', "No se puede eliminar el subtema CE '{$subtemaCE->ce_subtema}' porque tiene {$contenidosCount} contenido(s) asociado(s). Elimine los contenidos primero.");
            }

            // Guardar datos para el mensaje
            $nombreSubtema = $subtemaCE->ce_subtema;
            $nombreTema = $subtemaCE->tema ? $subtemaCE->tema->tema : 'Sin tema';

            // Eliminar subtema CE usando el método del modelo
            $subtemaCE->eliminar();

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Subtema CE '{$nombreSubtema}' del tema '{$nombreTema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al eliminar el subtema CE: ' . $e->getMessage());
        }
    }

    /**
     * Crear nuevo contenido CE
     */
    public function crearContenidoCE(Request $request)
    {
        try {
            // Validar datos básicos
            $request->validate([
                'ce_subtema_id' => 'required|integer|exists:consulta_express_subtema,ce_subtema_id',
                'titulo_tabla' => 'required|string|max:255',
                'pie_tabla' => 'nullable|string|max:500',
                'tabla_filas' => 'required|integer|min:1|max:50',
                'tabla_columnas' => 'required|integer|min:1|max:20'
            ]);

            $filas = (int) $request->tabla_filas;
            $columnas = (int) $request->tabla_columnas;
            
            // Validar estructura de tabla
            ce_contenido::validarTabla($filas, $columnas, $request->all());
            
            // Crear estructura de datos de tabla
            $estructura_tabla = ce_contenido::crearEstructuraTabla($filas, $columnas, $request->all());

            // Crear contenido CE
            $contenidoCE = ce_contenido::create([
                'ce_subtema_id' => $request->ce_subtema_id,
                'titulo_tabla' => $request->titulo_tabla,
                'pie_tabla' => $request->pie_tabla,
                'tabla_filas' => $filas,
                'tabla_columnas' => $columnas,
                'tabla_datos' => $estructura_tabla
            ]);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Contenido CE '{$contenidoCE->titulo_tabla}' creado exitosamente con tabla de {$filas}x{$columnas}");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al crear el contenido CE: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Actualizar contenido CE
     */
    public function actualizarContenidoCE(Request $request, $id)
    {
        try {
            $contenidoCE = ce_contenido::find($id);
            
            if (!$contenidoCE) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', 'Contenido CE no encontrado');
            }

            // Validar datos básicos
            $request->validate([
                'ce_subtema_id' => 'required|integer|exists:consulta_express_subtema,ce_subtema_id',
                'titulo_tabla' => 'required|string|max:255',
                'pie_tabla' => 'nullable|string|max:500',
                'tabla_filas' => 'required|integer|min:1|max:50',
                'tabla_columnas' => 'required|integer|min:1|max:20'
            ]);

            $filas = (int) $request->tabla_filas;
            $columnas = (int) $request->tabla_columnas;
            
            // Validar estructura de tabla
            ce_contenido::validarTabla($filas, $columnas, $request->all());
            
            // Crear estructura de datos de tabla
            $estructura_tabla = ce_contenido::crearEstructuraTabla($filas, $columnas, $request->all());

            // Actualizar contenido CE
            $contenidoCE->update([
                'ce_subtema_id' => $request->ce_subtema_id,
                'titulo_tabla' => $request->titulo_tabla,
                'pie_tabla' => $request->pie_tabla,
                'tabla_filas' => $filas,
                'tabla_columnas' => $columnas,
                'tabla_datos' => $estructura_tabla
            ]);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Contenido CE '{$contenidoCE->titulo_tabla}' actualizado exitosamente");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al actualizar el contenido CE: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar contenido CE
     */
    public function eliminarContenidoCE($id)
    {
        try {
            $contenidoCE = ce_contenido::find($id);
            
            if (!$contenidoCE) {
                return redirect()
                    ->route('sgiem.admin.consultas')
                    ->with('error', 'Contenido CE no encontrado');
            }

            // Guardar datos para el mensaje
            $tituloTabla = $contenidoCE->titulo_tabla ?: 'Tabla sin título';
            $nombreSubtema = $contenidoCE->subtema ? $contenidoCE->subtema->ce_subtema : 'Sin subtema';
            $dimensiones = "{$contenidoCE->tabla_filas}x{$contenidoCE->tabla_columnas}";

            // Eliminar contenido CE
            $contenidoCE->delete();

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Contenido CE '{$tituloTabla}' ({$dimensiones}) del subtema '{$nombreSubtema}' eliminado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al eliminar el contenido CE: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Obtener subtemas CE por tema
     */
    public function obtenerSubtemasCEPorTema($tema_id)
    {
        try {
            $subtemas = ce_subtema::where('ce_tema_id', $tema_id)
                                  ->orderBy('ce_subtema_id', 'asc')
                                  ->get(['ce_subtema_id', 'ce_subtema']);
            
            return response()->json(['subtemas' => $subtemas]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener subtemas CE'], 500);
        }
    }

    /**
     * AJAX: Obtener contenido CE completo para vista
     */
    public function obtenerContenidoCE($id)
    {
        try {
            $contenido = ce_contenido::with(['subtema.tema'])->find($id);
            
            if (!$contenido) {
                return response()->json(['error' => 'Contenido no encontrado'], 404);
            }
            
            // Debug: Log para verificar datos
            \Log::info('Contenido CE cargado:', [
                'id' => $contenido->ce_contenido_id,
                'titulo' => $contenido->titulo_tabla,
                'subtema_id' => $contenido->ce_subtema_id,
                'subtema_nombre' => $contenido->subtema ? $contenido->subtema->ce_subtema : null,
                'tema_id' => $contenido->subtema && $contenido->subtema->tema ? $contenido->subtema->tema->ce_tema_id : null,
                'tema_nombre' => $contenido->subtema && $contenido->subtema->tema ? $contenido->subtema->tema->tema : null,
                'datos' => $contenido->tabla_datos,
                'filas' => $contenido->tabla_filas,
                'columnas' => $contenido->tabla_columnas
            ]);
            
            $tablaHtml = $contenido->renderizarTabla();
            
            return response()->json([
                'success' => true,
                'contenido' => [
                    'ce_contenido_id' => $contenido->ce_contenido_id,
                    'titulo_tabla' => $contenido->titulo_tabla,
                    'pie_tabla' => $contenido->pie_tabla,
                    'tabla_filas' => $contenido->tabla_filas,
                    'tabla_columnas' => $contenido->tabla_columnas,
                    'tabla_datos' => $contenido->tabla_datos,
                    'ce_subtema_id' => $contenido->ce_subtema_id, // Asegurar que esto esté incluido
                    'created_at' => $contenido->created_at,
                    'subtema' => $contenido->subtema ? [
                        'ce_subtema_id' => $contenido->subtema->ce_subtema_id,
                        'ce_subtema' => $contenido->subtema->ce_subtema,
                        'tema' => $contenido->subtema->tema ? [
                            'ce_tema_id' => $contenido->subtema->tema->ce_tema_id,
                            'tema' => $contenido->subtema->tema->tema
                        ] : null
                    ] : null
                ],
                'tabla_html' => $tablaHtml,
                'resumen' => $contenido->resumen_tabla ?? null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener contenido CE:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
