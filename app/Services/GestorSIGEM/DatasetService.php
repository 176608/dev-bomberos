<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\AuditoriaSgiem;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\CuadroCategoria;
use App\Models\SIGEM\CuadroDato;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DatasetService
{
    private ?string $sesionId = null;

    public function __construct(
        private Cuadro $cuadro,
        private CuadroCategoria $categoria,
        private CuadroDato $dato,
        private AuditoriaSgiem $auditoria,
    ) {}

    public function setSesionId(?string $id): void
    {
        if ($id) $this->sesionId = $id;
    }

    public function obtenerEstado(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $allVertical = $cuadro->categoriasVerticales()->orderBy('orden')->get();
        $allHorizontal = $cuadro->categoriasHorizontales()->orderBy('orden')->get();
        $datos = $cuadro->datos;

        $vertTree = $this->buildTree($allVertical);
        $horizTree = $this->buildTree($allHorizontal);

        $vertTreeSerialized = $this->serializeTree($vertTree);
        $horizTreeSerialized = $this->serializeTree($horizTree);

        $labels = $this->buildEtiquetas($vertTreeSerialized);
        $numLabelCols = $labels ? max(array_map('count', $labels)) : 1;
        $headers = $this->buildEncabezados($horizTreeSerialized, $numLabelCols);

        $leafVIds = [];
        foreach ($labels as $row) {
            foreach ($row as $cell) {
                if ($cell['tipo'] === 'leaf') {
                    $leafVIds[] = $cell['categoria_id'];
                    break;
                }
            }
        }

        $leafHIds = [];
        if (!empty($headers)) {
            $leafCells = [];
            foreach ($headers as $row) {
                foreach ($row as $cell) {
                    if ($cell['tipo'] === 'leaf') {
                        $leafCells[] = $cell;
                    }
                }
            }
            usort($leafCells, fn($a, $b) => $a['col_index'] <=> $b['col_index']);
            $leafHIds = array_map(fn($c) => $c['categoria_id'], $leafCells);
        }

        $tieneDataset = !empty($leafVIds) || !empty($leafHIds);

        $vertMap = $allVertical->keyBy('categoria_id');
        $horizMap = $allHorizontal->keyBy('categoria_id');

        $verticales = [];
        foreach ($leafVIds as $id) {
            if ($vertMap->has($id)) $verticales[] = $vertMap[$id]->toArray();
        }
        $horizontales = [];
        foreach ($leafHIds as $id) {
            if ($horizMap->has($id)) $horizontales[] = $horizMap[$id]->toArray();
        }

        $dataGrid = [];
        if ($tieneDataset) {
            $mapa = [];
            foreach ($datos as $d) {
                $mapa[$d->cat_vertical_id][$d->cat_horizontal_id] = $d;
            }
            foreach ($leafVIds as $vId) {
                $row = [];
                foreach ($leafHIds as $hId) {
                    $dato = $mapa[$vId][$hId] ?? null;
                    $row[] = [
                        'dato_id' => $dato?->dato_id,
                        'valor' => $dato?->valor ?? '',
                        'cat_vertical_id' => $vId,
                        'cat_horizontal_id' => $hId,
                    ];
                }
                $dataGrid[] = $row;
            }
        }

        if (!$this->sesionId) $this->sesionId = (string) Str::uuid();

        return [
            'tiene_dataset' => $tieneDataset,
            'verticales' => $verticales,
            'horizontales' => $horizontales,
            'headers' => $headers,
            'labels' => $labels,
            'data' => $dataGrid,
            'max_filas' => count($leafVIds),
            'max_columnas' => count($leafHIds),
            'pivot_label' => $cuadro->pivot_label ?? 'PIVOTE',
            'sesion_token' => $this->sesionId,
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

        $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');

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

        $verticales = $this->getLeafCategories($cuadro_id, 'vertical');

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

        if ($padre->padre_id !== null) {
            throw new \RuntimeException('No se pueden agregar hijos a una categoría que ya es hija. Máximo 2 niveles de jerarquía.');
        }

        $hijosExistentes = $padre->hijos()->count();
        $maxOrden = $padre->hijos()->max('orden') ?? 0;

        $hijo = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $padre->eje,
            'padre_id' => $padre->categoria_id,
            'nombre' => 'Hijo ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        if ($padre->eje === 'vertical') {
            $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');
            foreach ($horizontales as $c => $hCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $hijo->categoria_id,
                    'valor' => '',
                    'fila' => $maxOrden + 1, 'columna' => $c + 1,
                ]);
            }
        } else {
            $verticales = $this->getLeafCategories($cuadro_id, 'vertical');
            foreach ($verticales as $f => $vCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'cat_horizontal_id' => $hijo->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => '',
                    'fila' => $f + 1, 'columna' => $maxOrden + 1,
                ]);
            }
        }

        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Agregar hijo', 'padre_id' => $padre_id]);
        return $this->obtenerEstado($cuadro_id);
    }

    public function actualizarCelda(int $dato_id, string $valor): CuadroDato
    {
        $dato = $this->dato->find($dato_id);
        if (!$dato) throw new \RuntimeException('Celda no encontrada');
        $dato->update(['valor' => $valor, 'valor_crudo' => $valor]);
        return $dato;
    }

    public function renombrarCategoria(int $categoria_id, string $nombre): CuadroCategoria
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat) throw new \RuntimeException('Categoría no encontrada');

        $nombre = trim($nombre);
        if ($nombre === '') throw new \RuntimeException('El nombre no puede estar vacío.');

        if ($cat->padre_id !== null) {
            $padre = $this->categoria->find($cat->padre_id);
            if ($padre && strcasecmp($nombre, trim($padre->nombre)) === 0) {
                throw new \RuntimeException('Un hijo no puede tener el mismo nombre que su padre.');
            }
        } else {
            $hijos = $this->categoria->where('padre_id', $categoria_id)->get();
            foreach ($hijos as $hijo) {
                if (strcasecmp($nombre, trim($hijo->nombre)) === 0) {
                    throw new \RuntimeException('Un padre no puede tener el mismo nombre que uno de sus hijos.');
                }
            }
        }

        // Auto-rename on sibling collision
        $base = $nombre;
        $contador = 0;
        $renombrado = false;
        while (true) {
            $existe = $this->categoria
                ->where('cuadro_id', $cat->cuadro_id)
                ->where('eje', $cat->eje)
                ->where('padre_id', $cat->padre_id)
                ->where('categoria_id', '!=', $categoria_id)
                ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])
                ->exists();
            if (!$existe) break;
            $contador++;
            $nombre = $base . ' (' . $contador . ')';
            $renombrado = true;
        }

        $cat->update(['nombre' => $nombre]);
        $cat->setAttribute('_renombrado', $renombrado);
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
        $verticales = $this->getLeafCategories($cuadro_id, 'vertical');
        $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');

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
            $this->renombrarCategoria($categorias[$idx]->categoria_id, trim($valor));
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
            'verticales' => [], 'horizontales' => [],
            'headers' => [], 'labels' => [], 'data' => [],
            'max_filas' => 0, 'max_columnas' => 0,
        ];
    }

    public function limpiarDatos(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $this->dato->where('cuadro_id', $cuadro_id)->update(['valor' => '', 'valor_crudo' => '']);
        $this->auditar($cuadro_id, 'actualizar', ['accion' => 'Limpiar datos']);

        return $this->obtenerEstado($cuadro_id);
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

    private function getLeaves($categories)
    {
        $idsWithChildren = $this->categoria
            ->whereIn('padre_id', $categories->pluck('categoria_id'))
            ->pluck('padre_id')
            ->unique();
        return $categories->reject(fn($c) => $idsWithChildren->contains($c->categoria_id))->values();
    }

    private function getLeafCategories(int $cuadro_id, string $eje)
    {
        $all = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)->orderBy('orden')->get();
        return $this->getLeaves($all);
    }

    private function countLeavesRecursive(array $node): int
    {
        if (empty($node['hijos'])) return 1;
        $count = 0;
        foreach ($node['hijos'] as $child) {
            $count += $this->countLeavesRecursive($child);
        }
        return $count;
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
                $item['span'] = $this->countLeavesRecursive($item);
            } else {
                $item['span'] = 1;
            }
            $result[] = $item;
        }
        return $result;
    }

    // ============ RENDERING HELPERS ============

    private function buildEncabezados(array $horizTree, int $numLabelCols): array
    {
        $rows = [];
        $colIdx = 0;
        $this->buildEncabezadosRecursive($horizTree, 0, $rows, $numLabelCols, $colIdx);
        $maxDepth = count($rows);
        if ($maxDepth > 0 && isset($rows[0][0]) && $rows[0][0]['tipo'] === 'corner') {
            $rows[0][0]['rowspan'] = $maxDepth;
        }
        return $rows;
    }

    private function buildEncabezadosRecursive(array $nodes, int $depth, array &$rows, int $numLabelCols, int &$colIdx): void
    {
        if (!isset($rows[$depth])) {
            $rows[$depth] = $depth === 0
                ? [['tipo' => 'corner', 'valor' => '', 'rowspan' => 1, 'colspan' => $numLabelCols]]
                : [];
        }
        foreach ($nodes as $node) {
            $hasChildren = !empty($node['hijos']);
            $esHijo = $depth > 0;
            if ($hasChildren) {
                $rows[$depth][] = [
                    'tipo' => 'parent',
                    'categoria_id' => $node['categoria_id'],
                    'nombre' => $node['nombre'],
                    'colspan' => $node['span'],
                    'es_hijo' => $esHijo,
                    'col_index' => $colIdx,
                ];
                $this->buildEncabezadosRecursive($node['hijos'], $depth + 1, $rows, $numLabelCols, $colIdx);
            } else {
                $rows[$depth][] = [
                    'tipo' => 'leaf',
                    'categoria_id' => $node['categoria_id'],
                    'nombre' => $node['nombre'],
                    'colspan' => 1,
                    'es_hijo' => $esHijo,
                    'col_index' => $colIdx,
                ];
                $colIdx++;
            }
        }
    }

    private function buildEtiquetas(array $vertTree): array
    {
        $rows = [];
        $rowIdx = 0;
        foreach ($vertTree as $node) {
            $hasChildren = !empty($node['hijos']);
            if ($hasChildren) {
                $span = $node['span'];
                $firstChild = true;
                foreach ($node['hijos'] as $child) {
                    $row = [];
                    if ($firstChild) {
                        $row[] = [
                            'tipo' => 'parent',
                            'categoria_id' => $node['categoria_id'],
                            'nombre' => $node['nombre'],
                            'rowspan' => $span,
                            'es_hijo' => false,
                            'row_index' => $rowIdx,
                        ];
                        $firstChild = false;
                    }
                    $row[] = [
                        'tipo' => 'leaf',
                        'categoria_id' => $child['categoria_id'],
                        'nombre' => $child['nombre'],
                        'rowspan' => 1,
                        'es_hijo' => true,
                        'row_index' => $rowIdx,
                    ];
                    $rows[] = $row;
                    $rowIdx++;
                }
            } else {
                $rows[] = [[
                    'tipo' => 'leaf',
                    'categoria_id' => $node['categoria_id'],
                    'nombre' => $node['nombre'],
                    'rowspan' => 1,
                    'colspan' => 2,
                    'es_hijo' => false,
                    'row_index' => $rowIdx,
                ]];
                $rowIdx++;
            }
        }
        return $rows;
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
        $entry = [
            'user_id' => Auth::id(),
            'modelo' => 'Dataset',
            'modelo_id' => $cuadro_id,
            'accion' => $accion,
            'datos_nuevos' => $detalle,
        ];
        if ($this->sesionId) {
            $entry['sesion_id'] = $this->sesionId;
        }
        $this->auditoria->create($entry);
    }
}
