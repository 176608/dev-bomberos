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
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $verticales = $cuadro->categoriasVerticales()->orderBy('orden')->get();
        $horizontales = $cuadro->categoriasHorizontales()->orderBy('orden')->get();
        $datos = $cuadro->datos;

        $tieneDataset = $verticales->count() > 0 || $horizontales->count() > 0;

        $tabla = [];
        if ($tieneDataset) {
            $headerRow = [['tipo' => 'corner', 'valor' => '']];
            foreach ($horizontales as $h) {
                $headerRow[] = [
                    'tipo' => 'header', 'eje' => 'horizontal',
                    'categoria_id' => $h->categoria_id, 'valor' => $h->nombre,
                ];
            }
            $tabla[] = $headerRow;

            $mapa = [];
            foreach ($datos as $d) {
                $mapa[$d->cat_vertical_id][$d->cat_horizontal_id] = $d;
            }

            foreach ($verticales as $v) {
                $row = [[
                    'tipo' => 'header', 'eje' => 'vertical',
                    'categoria_id' => $v->categoria_id, 'valor' => $v->nombre,
                ]];
                foreach ($horizontales as $h) {
                    $dato = $mapa[$v->categoria_id][$h->categoria_id] ?? null;
                    $row[] = [
                        'tipo' => 'celda',
                        'dato_id' => $dato?->dato_id,
                        'valor' => $dato?->valor ?? '',
                        'cat_vertical_id' => $v->categoria_id,
                        'cat_horizontal_id' => $h->categoria_id,
                    ];
                }
                $tabla[] = $row;
            }
        }

        $vertTree = $this->buildTree($verticales);
        $horizTree = $this->buildTree($horizontales);

        return [
            'tiene_dataset' => $tieneDataset,
            'verticales' => $verticales->toArray(),
            'horizontales' => $horizontales->toArray(),
            'tabla' => $tabla,
            'max_filas' => $verticales->count(),
            'max_columnas' => $horizontales->count(),
            'vertical_tree' => $this->serializeTree($vertTree),
            'horizontal_tree' => $this->serializeTree($horizTree),
        ];
    }

    public function generarGrilla(int $cuadro_id, int $filas, int $columnas): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->auditar($cuadro_id, 'crear', ['filas' => $filas, 'columnas' => $columnas]);

        $verticales = [];
        for ($f = 1; $f <= $filas; $f++) {
            $verticales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id, 'eje' => 'vertical',
                'nombre' => "Fila $f", 'orden' => $f, 'tipo' => 'dato',
            ]);
        }

        $horizontales = [];
        for ($c = 1; $c <= $columnas; $c++) {
            $horizontales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id, 'eje' => 'horizontal',
                'nombre' => "Columna $c", 'orden' => $c, 'tipo' => 'dato',
            ]);
        }

        foreach ($verticales as $f => $vCat) {
            foreach ($horizontales as $c => $hCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => '', 'fila' => $f + 1, 'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarFila(int $cuadro_id): array
    {
        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id, 'eje' => 'vertical',
            'nombre' => 'Fila ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1, 'tipo' => 'dato',
        ]);

        $horizontales = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->orderBy('orden')->get();

        foreach ($horizontales as $c => $hCat) {
            $this->dato->create([
                'cuadro_id' => $cuadro_id,
                'cat_horizontal_id' => $hCat->categoria_id,
                'cat_vertical_id' => $cat->categoria_id,
                'valor' => '', 'fila' => $maxOrden + 1, 'columna' => $c + 1,
            ]);
        }

        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Agregar fila']);
        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarFila(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat || $cat->cuadro_id != $cuadro_id || $cat->eje !== 'vertical') {
            throw new \RuntimeException('Fila no encontrada');
        }

        $this->dato->where('cuadro_id', $cuadro_id)
            ->where('cat_vertical_id', $categoria_id)->delete();
        $this->categoria->where('padre_id', $categoria_id)->delete();
        $cat->delete();

        $this->reordenar($cuadro_id, 'vertical');
        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Eliminar fila']);
        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarColumna(int $cuadro_id): array
    {
        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id, 'eje' => 'horizontal',
            'nombre' => 'Columna ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1, 'tipo' => 'dato',
        ]);

        $verticales = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->orderBy('orden')->get();

        foreach ($verticales as $f => $vCat) {
            $this->dato->create([
                'cuadro_id' => $cuadro_id,
                'cat_horizontal_id' => $cat->categoria_id,
                'cat_vertical_id' => $vCat->categoria_id,
                'valor' => '', 'fila' => $f + 1, 'columna' => $maxOrden + 1,
            ]);
        }

        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Agregar columna']);
        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarColumna(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat || $cat->cuadro_id != $cuadro_id || $cat->eje !== 'horizontal') {
            throw new \RuntimeException('Columna no encontrada');
        }

        $this->dato->where('cuadro_id', $cuadro_id)
            ->where('cat_horizontal_id', $categoria_id)->delete();
        $this->categoria->where('padre_id', $categoria_id)->delete();
        $cat->delete();

        $this->reordenar($cuadro_id, 'horizontal');
        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Eliminar columna']);
        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarHijo(int $cuadro_id, int $padre_id): array
    {
        $padre = $this->categoria->find($padre_id);
        if (!$padre || $padre->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría padre no encontrada');
        }

        $maxOrden = $padre->hijos()->max('orden') ?? 0;

        $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $padre->eje,
            'padre_id' => $padre->categoria_id,
            'nombre' => 'Hijo ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Agregar hijo', 'padre_id' => $padre_id]);
        return $this->obtenerEstado($cuadro_id);
    }

    public function actualizarCelda(int $dato_id, string $valor): CuadroDato
    {
        $dato = $this->dato->find($dato_id);
        if (!$dato) throw new \RuntimeException('Celda no encontrada');
        $dato->update(['valor' => $valor, 'valor_crudo' => $valor]);
        $this->auditar($dato->cuadro_id, 'actualizar', ['accion' => 'Editar celda', 'dato_id' => $dato_id]);
        return $dato;
    }

    public function renombrarCategoria(int $categoria_id, string $nombre): CuadroCategoria
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat) throw new \RuntimeException('Categoría no encontrada');
        $cat->update(['nombre' => $nombre]);
        $this->auditar($cat->cuadro_id, 'actualizar', ['accion' => 'Renombrar', 'categoria_id' => $categoria_id]);
        return $cat;
    }

    public function pasteGrid(int $cuadro_id, array $grid, ?int $startVerticalId = null, ?int $startHorizontalId = null): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        if ($startVerticalId !== null && $startHorizontalId !== null) {
            return $this->pastePartial($cuadro_id, $cuadro, $grid, $startVerticalId, $startHorizontalId);
        }

        if (count($grid) < 2) throw new \InvalidArgumentException('Debe tener al menos 2 filas');

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();
        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Pegar grid']);

        $headers = $grid[0];
        $numCols = count($headers);
        $numRows = count($grid) - 1;

        $verticales = [];
        for ($f = 0; $f < $numRows; $f++) {
            $nombre = $grid[$f + 1][0] ?? 'Fila ' . ($f + 1);
            $verticales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id, 'eje' => 'vertical',
                'nombre' => $nombre, 'orden' => $f + 1, 'tipo' => 'dato',
            ]);
        }

        $horizontales = [];
        for ($c = 1; $c < $numCols; $c++) {
            $nombre = $headers[$c] ?? 'Columna ' . $c;
            $horizontales[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id, 'eje' => 'horizontal',
                'nombre' => $nombre, 'orden' => $c, 'tipo' => 'dato',
            ]);
        }

        foreach ($verticales as $f => $vCat) {
            foreach ($horizontales as $c => $hCat) {
                $valor = $grid[$f + 1][$c + 1] ?? '';
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => $valor, 'valor_crudo' => $valor,
                    'fila' => $f + 1, 'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    private function pastePartial(int $cuadro_id, Cuadro $cuadro, array $grid, int $startVerticalId, int $startHorizontalId): array
    {
        $verticales = $cuadro->categoriasVerticales()->orderBy('orden')->get();
        $horizontales = $cuadro->categoriasHorizontales()->orderBy('orden')->get();

        $vIdx = $verticales->search(fn($v) => $v->categoria_id === $startVerticalId);
        $hIdx = $horizontales->search(fn($h) => $h->categoria_id === $startHorizontalId);

        if ($vIdx === false || $hIdx === false) {
            throw new \RuntimeException('Posición inicial no encontrada en la grilla');
        }

        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Pegar parcial']);

        foreach ($grid as $f => $row) {
            $vPos = $vIdx + $f;
            if ($vPos >= $verticales->count()) break;
            $vCat = $verticales[$vPos];

            foreach ($row as $c => $valor) {
                $hPos = $hIdx + $c;
                if ($hPos >= $horizontales->count()) break;
                $hCat = $horizontales[$hPos];

                $this->dato->updateOrCreate(
                    ['cuadro_id' => $cuadro_id, 'cat_vertical_id' => $vCat->categoria_id, 'cat_horizontal_id' => $hCat->categoria_id],
                    ['valor' => $valor, 'valor_crudo' => $valor]
                );
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    public function importarCsv(int $cuadro_id, UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) throw new \RuntimeException('No se pudo abrir el archivo');

        $grid = [];
        while (($row = fgetcsv($handle)) !== false) $grid[] = $row;
        fclose($handle);

        if (empty($grid)) throw new \InvalidArgumentException('Archivo vacío');
        return $this->pasteGrid($cuadro_id, $grid);
    }

    public function pasteCategorias(int $cuadro_id, string $eje, int $startCategoriaId, array $valores): array
    {
        $categorias = $this->categoria
            ->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)
            ->orderBy('orden')
            ->get();

        $startIdx = $categorias->search(fn($c) => $c->categoria_id === $startCategoriaId);
        if ($startIdx === false) throw new \RuntimeException('Categoría inicial no encontrada');

        foreach ($valores as $i => $valor) {
            $idx = $startIdx + $i;
            if ($idx >= $categorias->count()) break;
            $categorias[$idx]->update(['nombre' => trim($valor)]);
        }

        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Pegar en categorías', 'eje' => $eje]);
        return $this->obtenerEstado($cuadro_id);
    }

    public function limpiarDataset(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->auditar($cuadro_id, 'eliminar', ['accion' => 'Limpiar dataset']);
        return [
            'tiene_dataset' => false,
            'verticales' => [], 'horizontales' => [], 'tabla' => [],
            'max_filas' => 0, 'max_columnas' => 0,
        ];
    }

    // ============ TREE HELPERS ============

    private function buildTree($categories): array
    {
        $byParent = [];
        foreach ($categories as $cat) {
            $byParent[$cat->padre_id ?? 0][] = $cat;
        }

        $result = [];
        foreach ($byParent[0] ?? [] as $root) {
            $this->attachChildren($root, $byParent);
            $result[] = $root;
        }
        return $result;
    }

    private function attachChildren($node, array &$byParent): void
    {
        $children = $byParent[$node->categoria_id] ?? [];
        foreach ($children as $child) {
            $this->attachChildren($child, $byParent);
        }
        $node->setRelation('hijos', collect($children));
    }

    private function serializeTree(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $item = [
                'categoria_id' => $node->categoria_id,
                'nombre' => $node->nombre,
                'tipo' => $node->tipo,
            ];
            $hijos = $node->hijos ?? collect();
            if ($hijos->isNotEmpty()) {
                $item['hijos'] = $this->serializeTree($hijos->all());
            }
            $result[] = $item;
        }
        return $result;
    }

    private function reordenar(int $cuadro_id, string $eje): void
    {
        $cats = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)->orderBy('orden')->get();
        foreach ($cats as $i => $cat) {
            $cat->update(['orden' => $i + 1]);
        }
    }

    private function auditar(int $cuadro_id, string $accion, array $detalle = []): void
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
}
