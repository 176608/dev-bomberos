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
                'nombre_archivo' => 'nullable|string|max:255',
                'orden_indice' => 'nullable|integer|min:0|max:999'
            ]);

            $datos = $request->only([
                'tema_titulo',
                'clave_tema', 
                'nombre_archivo',
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
                'nombre_archivo' => 'nullable|string|max:255',
                'orden_indice' => 'nullable|integer|min:0|max:999'
            ]);

            $datos = $request->only([
                'tema_titulo',
                'clave_tema',
                'nombre_archivo', 
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
        // TODO: Implementar método
        return redirect()->route('sigem.admin.subtemas')->with('error', 'Método no implementado aún');
    }

    public function actualizarSubtema(Request $request, $id)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.subtemas')->with('error', 'Método no implementado aún');
    }

    public function eliminarSubtema($id)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.subtemas')->with('error', 'Método no implementado aún');
    }

    // ============ MÉTODOS CRUD PARA CUADROS ============
    public function crearCuadro(Request $request)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.cuadros')->with('error', 'Método no implementado aún');
    }

    public function actualizarCuadro(Request $request, $id)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.cuadros')->with('error', 'Método no implementado aún');
    }

    public function eliminarCuadro($id)
    {
        // TODO: Implementar método
        return redirect()->route('sigem.admin.cuadros')->with('error', 'Método no implementado aún');
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
