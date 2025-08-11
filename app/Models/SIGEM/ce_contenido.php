<?php
/* <!-- -AGREGADO 04/08/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class ce_contenido extends Model
{
    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'consulta_express_contenido';
    
    /**
     * La clave primaria asociada con la tabla.
     */
    protected $primaryKey = 'ce_contenido_id';
    
    /**
     * Indica si los IDs del modelo son auto-incrementales.
     */
    public $incrementing = true;
    
    /**
     * Indica si el modelo debe tener marcas de tiempo.
     */
    public $timestamps = true;
    
    /**
     * Los atributos que son asignables en masa.
     */
    protected $fillable = [
        'ce_subtema_id',
        'tabla_datos', // JSON con la estructura de la tabla
        'tabla_filas',  // Número de filas
        'tabla_columnas', // Número de columnas
        'titulo_tabla',   // Título descriptivo de la tabla
        'pie_tabla'       // Nota o fuente de la tabla
    ];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'tabla_datos' => 'array', // Automáticamente convierte JSON a array
        'tabla_filas' => 'integer',
        'tabla_columnas' => 'integer'
    ];
    
    /**
     * Obtener el subtema al que pertenece este contenido.
     */
    public function subtema()
    {
        return $this->belongsTo(ce_subtema::class, 'ce_subtema_id', 'ce_subtema_id');
    }
    
    /**
     * Obtener contenido por subtema
     */
    public static function obtenerPorSubtema($subtemaId)
    {
        return self::where('ce_subtema_id', $subtemaId)
                 ->orderBy('created_at', 'desc')
                 ->get();
    }
    
    /**
     * Obtener contenidos más recientes
     */
    public static function obtenerRecientes($limit = 5)
    {
        return self::with(['subtema', 'subtema.tema'])
                 ->orderBy('created_at', 'desc')
                 ->limit($limit)
                 ->get();
    }
    
    /**
     * Crear estructura de tabla desde datos de formulario
     */
    public static function crearEstructuraTabla($filas, $columnas, $datos)
    {
        $estructura = [];
        
        for ($fila = 0; $fila < $filas; $fila++) {
            $estructura[$fila] = [];
            for ($col = 0; $col < $columnas; $col++) {
                $clave = "celda_{$fila}_{$col}";
                $estructura[$fila][$col] = isset($datos[$clave]) ? trim($datos[$clave]) : '';
            }
        }
        
        return $estructura;
    }
    
    /**
     * Validar estructura de tabla
     */
    public static function validarTabla($filas, $columnas, $datos)
    {
        // Validaciones básicas
        if ($filas < 1 || $filas > 50) {
            throw new \Exception('El número de filas debe estar entre 1 y 50');
        }
        
        if ($columnas < 1 || $columnas > 20) {
            throw new \Exception('El número de columnas debe estar entre 1 y 20');
        }
        
        // Validar que al menos hay algunos datos
        $celdas_vacias = 0;
        $total_celdas = $filas * $columnas;
        
        for ($fila = 0; $fila < $filas; $fila++) {
            for ($col = 0; $col < $columnas; $col++) {
                $clave = "celda_{$fila}_{$col}";
                if (empty($datos[$clave])) {
                    $celdas_vacias++;
                }
            }
        }
        
        // Si más del 80% de las celdas están vacías, advertir
        if (($celdas_vacias / $total_celdas) > 0.8) {
            throw new \Exception('La tabla parece estar mayormente vacía. Completa al menos el 20% de las celdas.');
        }
        
        return true;
    }
    
    /**
     * Renderizar tabla como HTML para vista
     */
    public function renderizarTabla($clase_css = 'table table-striped table-bordered table-sm')
    {
        if (!$this->tabla_datos || empty($this->tabla_datos)) {
            return '<p class="text-muted">Sin datos de tabla disponibles</p>';
        }
        
        $html = '<div class="consulta-express-tabla">';
        
        // Título si existe
        if ($this->titulo_tabla) {
            $html .= '<h6 class="tabla-titulo mb-2">' . htmlspecialchars($this->titulo_tabla) . '</h6>';
        }
        
        // Tabla
        $html .= '<table class="' . $clase_css . '">';
        
        foreach ($this->tabla_datos as $fila_index => $fila) {
            $html .= '<tr>';
            foreach ($fila as $celda) {
                // Primera fila como encabezados si parece ser encabezados
                $tag = ($fila_index === 0 && $this->esFilaEncabezado($fila)) ? 'th' : 'td';
                $html .= '<' . $tag . '>' . htmlspecialchars($celda) . '</' . $tag . '>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        // Pie de tabla si existe
        if ($this->pie_tabla) {
            $html .= '<small class="text-muted tabla-pie">' . htmlspecialchars($this->pie_tabla) . '</small>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Determinar si una fila parece ser encabezado
     */
    private function esFilaEncabezado($fila)
    {
        // Si todas las celdas tienen texto y no hay números puros, probablemente es encabezado
        $tieneTexto = true;
        $soloNumeros = 0;
        
        foreach ($fila as $celda) {
            if (empty(trim($celda))) {
                return false; // Encabezados no deberían estar vacíos
            }
            
            if (is_numeric(trim($celda))) {
                $soloNumeros++;
            }
        }
        
        // Si más del 70% son números puros, probablemente no es encabezado
        return ($soloNumeros / count($fila)) < 0.7;
    }
    
    /**
     * Obtener resumen de la tabla para listados
     */
    public function getResumenTablaAttribute()
    {
        if (!$this->tabla_datos) {
            return 'Tabla vacía';
        }
        
        $filas = count($this->tabla_datos);
        $cols = $filas > 0 ? count($this->tabla_datos[0]) : 0;
        
        $titulo = $this->titulo_tabla ? $this->titulo_tabla : 'Tabla sin título';
        
        return "{$titulo} ({$filas}x{$cols})";
    }
}