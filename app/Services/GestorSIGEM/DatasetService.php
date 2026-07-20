<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\AuditoriaSgiem;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\CuadroCategoria;
use App\Models\SIGEM\CuadroDato;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class DatasetService
{
    public function __construct(
        private Cuadro $cuadro,
        private CuadroCategoria $categoria,
        private CuadroDato $dato,
        private AuditoriaSgiem $auditoria,
    ) {}

    public function obtenerEstado(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $verticales = $cuadro->categoriasVerticales()->orderBy('orden')->get();
        $horizontales = $cuadro->categoriasHorizontales()->orderBy('orden')->get();
        $datos = $cuadro->datos;

        $tieneDataset = $verticales->count() > 0 || $horizontales->count() > 0;

        $tabla = [];
        if ($tieneDataset) {
            $headerRow = [['tipo' => 'corner', 'valor' => '']];
            foreach ($horizontales as $h) {
                $headerRow[] = [
                    'tipo' => 'header',
                    'eje' => 'horizontal',
                    'categoria_id' => $h->categoria_id,
                    'valor' => $h->nombre,
                ];
            }
            $tabla[] = $headerRow;

            $mapa = [];
            foreach ($datos as $d) {
                $mapa[$d->fila][$d->columna] = $d;
            }

            foreach ($verticales as $f => $v) {
                $fila = [[
                    'tipo' => 'header',
                    'eje' => 'vertical',
                    'categoria_id' => $v->categoria_id,
                    'valor' => $v->nombre,
                ]];
                for ($c = 1; $c <= $horizontales->count(); $c++) {
                    $dato = $mapa[$f + 1][$c] ?? null;
                    $fila[] = [
                        'tipo' => 'celda',
                        'dato_id' => $dato?->dato_id,
                        'valor' => $dato?->valor ?? '',
                    ];
                }
                $tabla[] = $fila;
            }
        }

        return [
            'tiene_dataset' => $tieneDataset,
            'verticales' => $verticales->toArray(),
            'horizontales' => $horizontales->toArray(),
            'tabla' => $tabla,
            'max_filas' => $verticales->count(),
            'max_columnas' => $horizontales->count(),
        ];
    }

    public function generarGrilla(int $cuadro_id, int $filas, int $columnas): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->registrarAuditoria($cuadro_id, 'crear', [
            'filas' => $filas,
            'columnas' => $columnas,
        ]);

        $verticales = [];
        for ($f = 1; $f <= $filas; $f++) {
            $verticales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => 'vertical',
                'nombre' => "Fila $f",
                'orden' => $f,
                'tipo' => 'dato',
            ]);
        }

        $horizontales = [];
        for ($c = 1; $c <= $columnas; $c++) {
            $horizontales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => 'horizontal',
                'nombre' => "Columna $c",
                'orden' => $c,
                'tipo' => 'dato',
            ]);
        }

        foreach ($verticales as $f => $vCat) {
            foreach ($horizontales as $c => $hCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => '',
                    'fila' => $f + 1,
                    'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarFila(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => 'vertical',
            'nombre' => 'Fila ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $horizontales = $cuadro->categoriasHorizontales()->orderBy('orden')->get();

        foreach ($horizontales as $c => $hCat) {
            $this->dato->create([
                'cuadro_id' => $cuadro_id,
                'cat_horizontal_id' => $hCat->categoria_id,
                'cat_vertical_id' => $cat->categoria_id,
                'valor' => '',
                'fila' => $maxOrden + 1,
                'columna' => $c + 1,
            ]);
        }

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar fila vertical', 'nombre' => $cat->nombre]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarFila(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);

        if (!$cat || $cat->cuadro_id != $cuadro_id || $cat->eje !== 'vertical') {
            throw new \RuntimeException('Fila no encontrada');
        }

        $nombreFila = $cat->nombre;
        $this->dato->where('cuadro_id', $cuadro_id)
            ->where('cat_vertical_id', $categoria_id)->delete();

        $cat->delete();

        $this->reordenar($cuadro_id, 'vertical');

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Eliminar fila vertical', 'nombre' => $nombreFila]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarColumna(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => 'horizontal',
            'nombre' => 'Columna ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $verticales = $cuadro->categoriasVerticales()->orderBy('orden')->get();

        foreach ($verticales as $f => $vCat) {
            $this->dato->create([
                'cuadro_id' => $cuadro_id,
                'cat_horizontal_id' => $cat->categoria_id,
                'cat_vertical_id' => $vCat->categoria_id,
                'valor' => '',
                'fila' => $f + 1,
                'columna' => $maxOrden + 1,
            ]);
        }

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar columna horizontal', 'nombre' => $cat->nombre]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarColumna(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);

        if (!$cat || $cat->cuadro_id != $cuadro_id || $cat->eje !== 'horizontal') {
            throw new \RuntimeException('Columna no encontrada');
        }

        $nombreCol = $cat->nombre;
        $this->dato->where('cuadro_id', $cuadro_id)
            ->where('cat_horizontal_id', $categoria_id)->delete();

        $cat->delete();

        $this->reordenar($cuadro_id, 'horizontal');

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Eliminar columna horizontal', 'nombre' => $nombreCol]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function actualizarCelda(int $dato_id, string $valor): CuadroDato
    {
        $dato = $this->dato->find($dato_id);

        if (!$dato) {
            throw new \RuntimeException('Celda no encontrada');
        }

        $dato->update(['valor' => $valor, 'valor_crudo' => $valor]);

        $this->registrarAuditoria($dato->cuadro_id, 'actualizar', ['accion' => 'Editar celda', 'dato_id' => $dato_id, 'valor' => $valor]);

        return $dato;
    }

    public function renombrarCategoria(int $categoria_id, string $nombre): CuadroCategoria
    {
        $cat = $this->categoria->find($categoria_id);

        if (!$cat) {
            throw new \RuntimeException('Categoría no encontrada');
        }

        $cat->update(['nombre' => $nombre]);

        $this->registrarAuditoria($cat->cuadro_id, 'actualizar', ['accion' => 'Renombrar categoría', 'categoria_id' => $categoria_id, 'nombre' => $nombre]);

        return $cat;
    }

    public function pasteGrid(int $cuadro_id, array $grid): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        if (count($grid) < 2) {
            throw new \InvalidArgumentException('La tabla debe tener al menos 2 filas (encabezados + datos)');
        }

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Pegar grid desde portapapeles', 'filas' => count($grid) - 1, 'columnas' => count($grid[0]) - 1]);

        $headers = $grid[0];
        $numCols = count($headers);
        $numRows = count($grid) - 1;

        $verticales = [];
        for ($f = 0; $f < $numRows; $f++) {
            $nombreFila = $grid[$f + 1][0] ?? 'Fila ' . ($f + 1);
            $verticales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => 'vertical',
                'nombre' => $nombreFila,
                'orden' => $f + 1,
                'tipo' => 'dato',
            ]);
        }

        $horizontales = [];
        for ($c = 1; $c < $numCols; $c++) {
            $nombreCol = $headers[$c] ?? 'Columna ' . $c;
            $horizontales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => 'horizontal',
                'nombre' => $nombreCol,
                'orden' => $c,
                'tipo' => 'dato',
            ]);
        }

        foreach ($verticales as $f => $vCat) {
            foreach ($horizontales as $c => $hCat) {
                $valor = $grid[$f + 1][$c + 1] ?? '';
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => $valor,
                    'valor_crudo' => $valor,
                    'fila' => $f + 1,
                    'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    public function importarCsv(int $cuadro_id, UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            throw new \RuntimeException('No se pudo abrir el archivo');
        }

        $grid = [];

        while (($row = fgetcsv($handle)) !== false) {
            $grid[] = $row;
        }

        fclose($handle);

        if (empty($grid)) {
            throw new \InvalidArgumentException('El archivo CSV está vacío');
        }

        return $this->pasteGrid($cuadro_id, $grid);
    }

    public function limpiarDataset(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->registrarAuditoria($cuadro_id, 'eliminar', ['accion' => 'Limpiar dataset']);

        return [
            'tiene_dataset' => false,
            'verticales' => [],
            'horizontales' => [],
            'tabla' => [],
            'max_filas' => 0,
            'max_columnas' => 0,
        ];
    }

    private function registrarAuditoria(int $cuadro_id, string $accion, array $detalle = []): void
    {
        if (!Auth::check()) return;

        $this->auditoria->create([
            'user_id' => Auth::id(),
            'modelo' => 'Dataset',
            'modelo_id' => $cuadro_id,
            'accion' => $accion,
            'datos_nuevos' => $detalle,
        ]);
    }

    private function reordenar(int $cuadro_id, string $eje): void
    {
        $cats = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)
            ->orderBy('orden')
            ->get();

        foreach ($cats as $i => $cat) {
            $cat->update(['orden' => $i + 1]);
        }
    }
}
