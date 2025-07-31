<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    /**
     * No usa tabla propia, es un modelo para operaciones del catálogo
     */
    protected $table = null;
    
    /**
     * No maneja timestamps
     */
    public $timestamps = false;
    
    /**
     * Obtener todos los temas con sus subtemas para el catálogo
     */
    public static function obtenerTemasConSubtemas()
    {
        return Tema::with(['subtemas' => function($query) {
            $query->orderBy('orden_indice', 'asc');
        }])
        ->orderBy('orden_indice', 'asc')
        ->get();
    }
    
    /**
     * Obtener estructura completa del catálogo organizada
     */
    public static function obtenerEstructuraCatalogo()
    {
        $temas = self::obtenerTemasConSubtemas();
        $estructura = [];
        $totalSubtemas = 0;
        
        foreach ($temas as $tema) {
            $estructura[$tema->tema_titulo] = [];
            
            foreach ($tema->subtemas as $subtema) {
                $estructura[$tema->tema_titulo][] = [
                    'subtema_id' => $subtema->subtema_id,
                    'nombre' => $subtema->subtema_titulo,
                    'imagen' => $subtema->imagen,
                    'orden' => $subtema->orden_indice
                ];
                $totalSubtemas++;
            }
        }
        
        return [
            'estructura' => $estructura,
            'total_temas' => count($temas),
            'total_subtemas' => $totalSubtemas,
            'temas_detalle' => $temas
        ];
    }
    
    /**
     * Obtener estructura completa del catálogo organizada CON CLAVES
     */
    public static function obtenerEstructuraCatalogoConClaves()
    {
        $temas = Tema::obtenerTodosConSubtemasYClaves(); // CAMBIO: usar nueva función
        $estructura = [];
        $totalSubtemas = 0;
        
        foreach ($temas as $tema) {
            $estructura[$tema->tema_titulo] = [];
            
            foreach ($tema->subtemas as $subtema) {
                $infoClave = $subtema->obtenerInfoClave(); // AGREGAR: obtener info de clave
                
                $estructura[$tema->tema_titulo][] = [
                    'subtema_id' => $subtema->subtema_id,
                    'nombre' => $subtema->subtema_titulo,
                    'imagen' => $subtema->imagen,
                    'orden' => $subtema->orden_indice,
                    'clave_original' => $subtema->clave_subtema, // AGREGAR
                    'clave_efectiva' => $infoClave['clave_efectiva'], // AGREGAR
                    'origen_clave' => $infoClave['origen'], // AGREGAR
                    'clave_tema' => $infoClave['clave_tema'] // AGREGAR
                ];
                $totalSubtemas++;
            }
        }
        
        return [
            'estructura' => $estructura,
            'total_temas' => count($temas),
            'total_subtemas' => $totalSubtemas,
            'temas_detalle' => $temas->map(function($tema) {
                return [
                    'tema_id' => $tema->tema_id,
                    'titulo' => $tema->tema_titulo,
                    'clave_tema' => $tema->clave_tema, // AGREGAR
                    'subtemas' => $tema->subtemas->map(function($subtema) {
                        $infoClave = $subtema->obtenerInfoClave();
                        return [
                            'subtema_id' => $subtema->subtema_id,
                            'titulo' => $subtema->subtema_titulo,
                            'clave_original' => $subtema->clave_subtema,
                            'clave_efectiva' => $infoClave['clave_efectiva'],
                            'origen_clave' => $infoClave['origen']
                        ];
                    })
                ];
            })
        ];
    }
    
    /**
     * Obtener resumen estadístico del catálogo
     */
    public static function obtenerResumen()
    {
        $temas = Tema::withCount('subtemas')->orderBy('orden_indice', 'asc')->get();
        
        $resumen = [
            'total_temas' => $temas->count(),
            'total_subtemas' => $temas->sum('subtemas_count'),
            'temas_detallados' => []
        ];
        
        foreach ($temas as $tema) {
            $resumen['temas_detallados'][] = [
                'tema_id' => $tema->tema_id,
                'titulo' => $tema->tema_titulo,
                'cantidad_subtemas' => $tema->subtemas_count,
                'orden' => $tema->orden_indice
            ];
        }
        
        return $resumen;
    }
    
    /**
     * Buscar en el catálogo por término
     */
    public static function buscar($termino)
    {
        $temas = Tema::where('tema_titulo', 'LIKE', "%{$termino}%")
                    ->with('subtemas')
                    ->get();
                    
        $subtemas = Subtema::where('subtema_titulo', 'LIKE', "%{$termino}%")
                          ->with('tema')
                          ->get();
        
        return [
            'temas_encontrados' => $temas,
            'subtemas_encontrados' => $subtemas,
            'total_resultados' => $temas->count() + $subtemas->count()
        ];
    }
    
    /**
     * Obtener tema específico con sus subtemas
     */
    public static function obtenerTemaPorId($tema_id)
    {
        return Tema::with('subtemas')->find($tema_id);
    }
    
    /**
     * Obtener subtemas de un tema específico
     */
    public static function obtenerSubtemasPorTema($tema_id)
    {
        return Subtema::where('tema_id', $tema_id)
                     ->orderBy('orden_indice', 'asc')
                     ->get();
    }
}
