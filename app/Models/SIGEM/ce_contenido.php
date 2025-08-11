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
        'tabla_datos' => 'array', // IMPORTANTE: Esto debe convertir JSON a array automáticamente
        'tabla_filas' => 'integer',
        'tabla_columnas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtener el subtema al que pertenece este contenido.
     */
    public function subtema()
    {
        return $this->belongsTo(ce_subtema::class, 'ce_subtema_id', 'ce_subtema_id');
    }
    
    /**
     * Renderizar tabla como HTML para vista
     */
    public function renderizarTabla($clase_css = 'table table-striped table-bordered table-hover')
    {
        if (!$this->tabla_datos || empty($this->tabla_datos)) {
            return '<div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Sin datos de tabla disponibles
                    </div>';
        }
        
        $html = '<div class="consulta-express-tabla">';
        
        // Título si existe
        if ($this->titulo_tabla) {
            $html .= '<div class="mb-3">
                        <h6 class="tabla-titulo text-primary mb-1">' . htmlspecialchars($this->titulo_tabla) . '</h6>';
            
            if ($this->pie_tabla) {
                $html .= '<small class="text-muted tabla-pie"><em>' . htmlspecialchars($this->pie_tabla) . '</em></small>';
            }
            
            $html .= '</div>';
        }
        
        // Tabla con estilo Bootstrap mejorado
        $html .= '<div class="table-responsive">
                    <table class="' . $clase_css . '">';
        
        foreach ($this->tabla_datos as $fila_index => $fila) {
            $html .= '<tr>';
            foreach ($fila as $col_index => $celda) {
                // Primera fila como encabezados si parece ser encabezados
                $es_encabezado = ($fila_index === 0 && $this->esFilaEncabezado($fila));
                $tag = $es_encabezado ? 'th' : 'td';
                
                // Agregar clases especiales
                $clases = '';
                if ($es_encabezado) {
                    $clases = ' class="table-primary text-center fw-bold"';
                } elseif ($col_index === 0 && !empty(trim($celda)) && !is_numeric(trim($celda))) {
                    // Primera columna con texto (categorías)
                    $clases = ' class="fw-semibold"';
                } elseif (is_numeric(trim($celda))) {
                    // Números alineados a la derecha
                    $clases = ' class="text-end"';
                }
                
                $contenido_celda = empty($celda) ? '<span class="text-muted">-</span>' : htmlspecialchars($celda);
                
                $html .= '<' . $tag . $clases . '>' . $contenido_celda . '</' . $tag . '>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table></div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Determinar si una fila parece ser encabezado
     */
    private function esFilaEncabezado($fila)
    {
        if (empty($fila)) return false;
        
        $celdas_con_texto = 0;
        $celdas_numericas = 0;
        $celdas_vacias = 0;
        
        foreach ($fila as $celda) {
            $celda_limpia = trim($celda);
            
            if (empty($celda_limpia)) {
                $celdas_vacias++;
            } elseif (is_numeric($celda_limpia)) {
                $celdas_numericas++;
            } else {
                $celdas_con_texto++;
            }
        }
        
        $total_celdas = count($fila);
        
        // Es encabezado si:
        // 1. Tiene más texto que números
        // 2. No tiene demasiadas celdas vacías
        // 3. Tiene al menos algo de contenido
        return ($celdas_con_texto > $celdas_numericas) && 
               ($celdas_vacias < $total_celdas * 0.5) &&
               ($celdas_con_texto + $celdas_numericas > 0);
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
     * Obtener contenidos más recientes
     */
    public static function obtenerRecientes($limit = 5)
    {
        return self::with(['subtema.tema'])
                 ->orderBy('created_at', 'desc')
                 ->limit($limit)
                 ->get();
    }
}