<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\AuditoriaSgiem;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\CuadroCategoria;
use App\Models\SIGEM\CuadroDato;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $arbolVertical = $this->buildTree($verticales);
        $arbolHorizontal = $this->buildTree($horizontales);

        $hojasVerticales = $this->getLeaves($arbolVertical);
        $hojasHorizontales = $this->getLeaves($arbolHorizontal);

        $pivoteVertical = $this->findPivote($verticales, 'vertical');
        $pivoteHorizontal = $this->findPivote($horizontales, 'horizontal');

        $mapa = [];
        foreach ($datos as $d) {
            $mapa[$d->cat_vertical_id][$d->cat_horizontal_id] = $d;
        }

        $tabla = $this->buildGrid(
            $arbolHorizontal, $hojasHorizontales,
            $arbolVertical, $hojasVerticales,
            $mapa, $pivoteVertical, $pivoteHorizontal
        );

        $tabla_headers = $this->buildMultiLevelHeaders($arbolHorizontal);

        return [
            'tiene_dataset' => $tieneDataset,
            'vertical' => [
                'arbol' => $arbolVertical,
                'hojas' => $hojasVerticales,
                'pivote' => $pivoteVertical ? [
                    'categoria_id' => $pivoteVertical->categoria_id,
                    'nombre' => $pivoteVertical->nombre,
                ] : null,
            ],
            'horizontal' => [
                'arbol' => $arbolHorizontal,
                'hojas' => $hojasHorizontales,
                'pivote' => $pivoteHorizontal ? [
                    'categoria_id' => $pivoteHorizontal->categoria_id,
                    'nombre' => $pivoteHorizontal->nombre,
                ] : null,
            ],
            'tabla' => $tabla,
            'tabla_headers' => $tabla_headers,
            'max_filas' => count($hojasVerticales),
            'max_columnas' => count($hojasHorizontales),
        ];
    }

    private function buildTree($categorias): array
    {
        $map = [];
        $roots = [];
        foreach ($categorias as $cat) {
            $map[$cat->categoria_id] = [
                'categoria_id' => $cat->categoria_id,
                'nombre' => $cat->nombre,
                'eje' => $cat->eje,
                'padre_id' => $cat->padre_id,
                'orden' => $cat->orden,
                'tipo' => $cat->tipo,
                'hijos' => [],
            ];
        }
        foreach ($map as &$node) {
            if ($node['padre_id'] && isset($map[$node['padre_id']])) {
                $map[$node['padre_id']]['hijos'][] = &$node;
            } else {
                $roots[] = &$node;
            }
        }
        unset($node);

        usort($roots, fn($a, $b) => $a['orden'] - $b['orden']);
        foreach ($map as &$node) {
            usort($node['hijos'], fn($a, $b) => $a['orden'] - $b['orden']);
        }
        unset($node);

        return $roots;
    }

    private function getLeaves(array $tree): array
    {
        $leaves = [];
        foreach ($tree as $node) {
            if (empty($node['hijos']) && $node['tipo'] !== 'pivote') {
                $leaves[] = $node;
            } else if (!empty($node['hijos'])) {
                $leaves = array_merge($leaves, $this->getLeaves($node['hijos']));
            }
        }
        return $leaves;
    }

    private function findPivote($categorias, string $eje): ?CuadroCategoria
    {
        return $categorias->firstWhere('tipo', 'pivote');
    }

    private function buildMultiLevelHeaders(array $arbolH): array
    {
        $rows = [];
        $maxDepth = $this->maxDepth($arbolH);
        if ($maxDepth === 0) return $rows;
        for ($depth = 0; $depth < $maxDepth; $depth++) {
            $row = [['tipo' => 'corner', 'valor' => '', 'rowspan' => $maxDepth, 'colspan' => 1]];
            $this->buildHeaderRowAtDepth($arbolH, $depth, $row);
            $rows[] = $row;
        }
        return $rows;
    }

    private function buildGrid(
        array $arbolH, array $hojasH,
        array $arbolV, array $hojasV,
        array $mapa,
        ?CuadroCategoria $pivoteV,
        ?CuadroCategoria $pivoteH
    ): array {
        $tabla = [];

        // ---- Flat header: horizontal leaf names ----
        $headerRow = [['tipo' => 'corner', 'valor' => '']];
        foreach ($hojasH as $h) {
            $headerRow[] = [
                'tipo' => 'header',
                'eje' => 'horizontal',
                'categoria_id' => $h['categoria_id'],
                'valor' => $h['nombre'],
                'tipo_cat' => $h['tipo'],
                'colspan' => 1,
                'rowspan' => 1,
            ];
        }
        $tabla[] = $headerRow;

        // ---- Multi-level headers (rich) ----
        $tabla_headers = [];
        $maxDepthH = $this->maxDepth($arbolH);
        if ($maxDepthH > 0) {
            for ($depth = 0; $depth < $maxDepthH; $depth++) {
                $hRow = [['tipo' => 'corner', 'valor' => '', 'rowspan' => $maxDepthH, 'colspan' => 1]];
                $this->buildHeaderRowAtDepth($arbolH, $depth, $hRow);
                $tabla_headers[] = $hRow;
            }
        }

        // ---- Data rows ----
        $this->buildDataRowsFlat($arbolV, $hojasH, $mapa, $tabla);

        // ---- Pivot row ----
        if ($pivoteV) {
            $pivotRow = [['tipo' => 'header', 'eje' => 'vertical', 'categoria_id' => $pivoteV->categoria_id, 'valor' => $pivoteV->nombre, 'tipo_cat' => 'pivote']];
            foreach ($hojasH as $h) {
                $dato = $mapa[$pivoteV->categoria_id][$h['categoria_id']] ?? null;
                $pivotRow[] = [
                    'tipo' => 'celda',
                    'dato_id' => $dato?->dato_id,
                    'valor' => $dato?->valor ?? '',
                    'cat_vertical_id' => $pivoteV->categoria_id,
                    'cat_horizontal_id' => $h['categoria_id'],
                ];
            }
            $tabla[] = $pivotRow;
        }

        return $tabla;
    }

    private function maxDepth(array $tree): int
    {
        $max = 0;
        foreach ($tree as $node) {
            $depth = 1 + (empty($node['hijos']) ? 0 : $this->maxDepth($node['hijos']));
            if ($depth > $max) $max = $depth;
        }
        return $max;
    }

    private function buildHeaderRowAtDepth(array $tree, int $targetDepth, array &$row, int $currentDepth = 0): void
    {
        foreach ($tree as $node) {
            if ($currentDepth === $targetDepth) {
                if (empty($node['hijos'])) {
                    $row[] = [
                        'tipo' => 'header',
                        'eje' => 'horizontal',
                        'categoria_id' => $node['categoria_id'],
                        'valor' => $node['nombre'],
                        'tipo_cat' => $node['tipo'],
                        'colspan' => 1,
                        'rowspan' => 1,
                    ];
                } else {
                    $leafCount = count($this->getLeaves([$node]));
                    $row[] = [
                        'tipo' => 'header',
                        'eje' => 'horizontal',
                        'categoria_id' => $node['categoria_id'],
                        'valor' => $node['nombre'],
                        'tipo_cat' => $node['tipo'],
                        'colspan' => $leafCount,
                        'rowspan' => 1,
                    ];
                }
            } else if (!empty($node['hijos'])) {
                $this->buildHeaderRowAtDepth($node['hijos'], $targetDepth, $row, $currentDepth + 1);
            }
        }
    }

    private function buildDataRowsFlat(array $tree, array $hojasH, array $mapa, array &$tabla, int $profundidad = 0): void
    {
        foreach ($tree as $node) {
            if (empty($node['hijos'])) {
                if ($node['tipo'] === 'pivote') continue;
                $row = [[
                    'tipo' => 'header',
                    'eje' => 'vertical',
                    'categoria_id' => $node['categoria_id'],
                    'valor' => $node['nombre'],
                    'tipo_cat' => $node['tipo'],
                    'profundidad' => $profundidad,
                ]];
                foreach ($hojasH as $h) {
                    $dato = $mapa[$node['categoria_id']][$h['categoria_id']] ?? null;
                    $row[] = [
                        'tipo' => 'celda',
                        'dato_id' => $dato?->dato_id,
                        'valor' => $dato?->valor ?? '',
                        'cat_vertical_id' => $node['categoria_id'],
                        'cat_horizontal_id' => $h['categoria_id'],
                    ];
                }
                $tabla[] = $row;
            } else {
                $this->buildDataRowsFlat($node['hijos'], $hojasH, $mapa, $tabla, $profundidad + 1);
            }
        }
    }

    // ============ TREE CRUD ============

    public function agregarRaiz(int $cuadro_id, string $eje, string $nombre = '', string $tipo = 'dato'): array
    {
        $this->validateEje($eje);

        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)
            ->whereNull('padre_id')
            ->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $eje,
            'nombre' => $nombre ?: ($eje === 'vertical' ? 'Categoría ' . ($maxOrden + 1) : 'Columna ' . ($maxOrden + 1)),
            'orden' => $maxOrden + 1,
            'tipo' => $tipo,
        ]);

        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar raíz', 'eje' => $eje, 'nombre' => $cat->nombre]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarHijo(int $cuadro_id, int $padre_id): array
    {
        $padre = $this->categoria->find($padre_id);
        if (!$padre || $padre->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría padre no encontrada');
        }

        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('padre_id', $padre_id)
            ->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $padre->eje,
            'padre_id' => $padre_id,
            'nombre' => 'Hijo ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar hijo', 'padre_id' => $padre_id, 'nombre' => $cat->nombre]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarHermano(int $cuadro_id, int $categoria_id): array
    {
        $ref = $this->categoria->find($categoria_id);
        if (!$ref || $ref->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría de referencia no encontrada');
        }

        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $ref->eje)
            ->where('padre_id', $ref->padre_id)
            ->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $ref->eje,
            'padre_id' => $ref->padre_id,
            'nombre' => $ref->nombre . ' (copia)',
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar hermano', 'referencia_id' => $categoria_id, 'nombre' => $cat->nombre]);

        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarCategoria(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat || $cat->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría no encontrada');
        }

        $nombre = $cat->nombre;

        DB::transaction(function () use ($cuadro_id, $cat) {
            $this->deleteCascade($cat);
        });

        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Eliminar categoría', 'nombre' => $nombre]);

        return $this->obtenerEstado($cuadro_id);
    }

    private function deleteCascade(CuadroCategoria $cat): void
    {
        $children = $this->categoria->where('padre_id', $cat->categoria_id)->get();
        foreach ($children as $child) {
            $this->deleteCascade($child);
        }
        CuadroDato::where('cuadro_id', $cat->cuadro_id)
            ->where(function ($q) use ($cat) {
                $q->where('cat_vertical_id', $cat->categoria_id)
                  ->orWhere('cat_horizontal_id', $cat->categoria_id);
            })->delete();
        $cat->delete();
    }

    public function renombrarCategoria(int $categoria_id, string $nombre): CuadroCategoria
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat) throw new \RuntimeException('Categoría no encontrada');
        $cat->update(['nombre' => $nombre]);

        $this->registrarAuditoria($cat->cuadro_id, 'actualizar', ['accion' => 'Renombrar categoría', 'categoria_id' => $categoria_id, 'nombre' => $nombre]);

        return $cat;
    }

    public function cambiarTipoCategoria(int $categoria_id, string $tipo): CuadroCategoria
    {
        $validos = ['dato', 'pivote', 'total', 'promedio', 'porcentual'];
        if (!in_array($tipo, $validos)) {
            throw new \InvalidArgumentException("Tipo inválido: debe ser uno de: " . implode(', ', $validos));
        }

        $cat = $this->categoria->find($categoria_id);
        if (!$cat) throw new \RuntimeException('Categoría no encontrada');
        $cat->update(['tipo' => $tipo]);

        $this->registrarAuditoria($cat->cuadro_id, 'actualizar', ['accion' => 'Cambiar tipo categoría', 'categoria_id' => $categoria_id, 'tipo' => $tipo]);

        return $cat;
    }

    public function reordenar(int $cuadro_id, int $categoria_id, int $nueva_posicion): array
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat || $cat->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría no encontrada');
        }

        $hermanos = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $cat->eje)
            ->where('padre_id', $cat->padre_id)
            ->where('categoria_id', '!=', $categoria_id)
            ->orderBy('orden')
            ->get();

        $cat->update(['orden' => $nueva_posicion]);

        $all = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $cat->eje)
            ->where('padre_id', $cat->padre_id)
            ->orderBy('orden')
            ->get();

        foreach ($all as $i => $c) {
            $c->update(['orden' => $i + 1]);
        }

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Reordenar categoría', 'categoria_id' => $categoria_id, 'nueva_posicion' => $nueva_posicion]);

        return $this->obtenerEstado($cuadro_id);
    }

    // ============ DATA CELLS ============

    private function syncDataCells(int $cuadro_id): void
    {
        $verticales = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->where('tipo', 'dato')->get();
        $horizontales = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->where('tipo', 'dato')->get();

        // Remove orphans (data where either axis category no longer exists)
        CuadroDato::where('cuadro_id', $cuadro_id)
            ->where(function ($q) use ($verticales, $horizontales) {
                $q->whereNotIn('cat_vertical_id', $verticales->pluck('categoria_id'))
                  ->orWhereNotIn('cat_horizontal_id', $horizontales->pluck('categoria_id'));
            })
            ->delete();

        // Ensure every leaf×leaf has a record
        $existing = CuadroDato::where('cuadro_id', $cuadro_id)->get()
            ->keyBy(fn($d) => $d->cat_vertical_id . '-' . $d->cat_horizontal_id);

        $fila = 1;
        foreach ($verticales as $v) {
            $columna = 1;
            foreach ($horizontales as $h) {
                $key = $v->categoria_id . '-' . $h->categoria_id;
                if (!isset($existing[$key])) {
                    CuadroDato::create([
                        'cuadro_id' => $cuadro_id,
                        'cat_horizontal_id' => $h->categoria_id,
                        'cat_vertical_id' => $v->categoria_id,
                        'valor' => '',
                        'valor_crudo' => '',
                        'fila' => $fila,
                        'columna' => $columna,
                    ]);
                }
                $columna++;
            }
            $fila++;
        }
    }

    public function actualizarCelda(int $dato_id, string $valor): CuadroDato
    {
        $dato = $this->dato->find($dato_id);
        if (!$dato) throw new \RuntimeException('Celda no encontrada');

        $dato->update(['valor' => $valor, 'valor_crudo' => $valor]);

        $this->registrarAuditoria($dato->cuadro_id, 'actualizar', ['accion' => 'Editar celda', 'dato_id' => $dato_id, 'valor' => $valor]);

        return $dato;
    }

    public function actualizarCeldaPorCruze(int $cuadro_id, int $cat_vertical_id, int $cat_horizontal_id, string $valor): CuadroDato
    {
        $dato = CuadroDato::where('cuadro_id', $cuadro_id)
            ->where('cat_vertical_id', $cat_vertical_id)
            ->where('cat_horizontal_id', $cat_horizontal_id)
            ->first();

        if (!$dato) {
            $dato = CuadroDato::create([
                'cuadro_id' => $cuadro_id,
                'cat_horizontal_id' => $cat_horizontal_id,
                'cat_vertical_id' => $cat_vertical_id,
                'valor' => $valor,
                'valor_crudo' => $valor,
                'fila' => 0,
                'columna' => 0,
            ]);
        } else {
            $dato->update(['valor' => $valor, 'valor_crudo' => $valor]);
        }

        return $dato;
    }

    // ============ PASTE ============

    public function pasteGrid(int $cuadro_id, array $grid): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        if (count($grid) < 2) {
            throw new \InvalidArgumentException('La tabla debe tener al menos 2 filas (encabezados + datos)');
        }

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Pegar grid desde portapapeles', 'filas' => count($grid) - 1, 'columnas' => count($grid[0]) - 1]);

        $headers = $grid[0];
        $numCols = count($headers);
        $numRows = count($grid) - 1;

        // Create root vertical categories for each data row
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

        // Create root horizontal categories for each data column
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

        // Create data cells
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

    public function pasteDataRange(int $cuadro_id, array $range): array
    {
        // Paste data starting from a specific position
        // $range = { startRow: cat_vertical_id, startCol: cat_horizontal_id, data: [[val1, val2, ...], ...] }
        $startVId = $range['startRow'] ?? null;
        $startHId = $range['startCol'] ?? null;
        $data = $range['data'] ?? [];

        if (!$startVId || !$startHId || empty($data)) {
            throw new \InvalidArgumentException('Rango de pegado inválido');
        }

        $verticales = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->where('tipo', 'dato')->orderBy('orden')->get();
        $horizontales = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->where('tipo', 'dato')->orderBy('orden')->get();

        $vIndex = $verticales->search(fn($v) => $v->categoria_id === $startVId);
        $hIndex = $horizontales->search(fn($h) => $h->categoria_id === $startHId);

        if ($vIndex === false || $hIndex === false) {
            throw new \InvalidArgumentException('Posición inicial no encontrada en el grid');
        }

        foreach ($data as $ri => $row) {
            foreach ($row as $ci => $val) {
                $vCat = $verticales[$vIndex + $ri] ?? null;
                $hCat = $horizontales[$hIndex + $ci] ?? null;
                if (!$vCat || !$hCat) continue;

                $this->actualizarCeldaPorCruze($cuadro_id, $vCat->categoria_id, $hCat->categoria_id, (string)$val);
            }
        }

        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Pegar rango de datos']);

        return $this->obtenerEstado($cuadro_id);
    }

    // ============ GENERATE / IMPORT ============

    public function generarGrilla(int $cuadro_id, int $filas, int $columnas): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->registrarAuditoria($cuadro_id, 'crear', [
            'filas' => $filas,
            'columnas' => $columnas,
        ]);

        for ($f = 1; $f <= $filas; $f++) {
            $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => 'vertical',
                'nombre' => "Fila $f",
                'orden' => $f,
                'tipo' => 'dato',
            ]);
        }

        for ($c = 1; $c <= $columnas; $c++) {
            $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => 'horizontal',
                'nombre' => "Columna $c",
                'orden' => $c,
                'tipo' => 'dato',
            ]);
        }

        $this->syncDataCells($cuadro_id);

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarFila(int $cuadro_id): array
    {
        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->whereNull('padre_id')->max('orden') ?? 0;

        $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => 'vertical',
            'nombre' => 'Fila ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar fila']);

        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarFila(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat || $cat->cuadro_id != $cuadro_id || $cat->eje !== 'vertical') {
            throw new \RuntimeException('Fila no encontrada');
        }

        $this->deleteCascade($cat);
        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Eliminar fila']);

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarColumna(int $cuadro_id): array
    {
        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->whereNull('padre_id')->max('orden') ?? 0;

        $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => 'horizontal',
            'nombre' => 'Columna ' . ($maxOrden + 1),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Agregar columna']);

        return $this->obtenerEstado($cuadro_id);
    }

    public function eliminarColumna(int $cuadro_id, int $categoria_id): array
    {
        $cat = $this->categoria->find($categoria_id);
        if (!$cat || $cat->cuadro_id != $cuadro_id || $cat->eje !== 'horizontal') {
            throw new \RuntimeException('Columna no encontrada');
        }

        $this->deleteCascade($cat);
        $this->syncDataCells($cuadro_id);
        $this->registrarAuditoria($cuadro_id, 'actualizar', ['accion' => 'Eliminar columna']);

        return $this->obtenerEstado($cuadro_id);
    }

    public function importarCsv(int $cuadro_id, UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) throw new \RuntimeException('No se pudo abrir el archivo');

        $grid = [];
        while (($row = fgetcsv($handle)) !== false) {
            $grid[] = $row;
        }
        fclose($handle);

        if (empty($grid)) throw new \InvalidArgumentException('El archivo CSV está vacío');

        return $this->pasteGrid($cuadro_id, $grid);
    }

    public function limpiarDataset(int $cuadro_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $cuadro->datos()->delete();
        $cuadro->categorias()->delete();

        $this->registrarAuditoria($cuadro_id, 'eliminar', ['accion' => 'Limpiar dataset']);

        return [
            'tiene_dataset' => false,
            'vertical' => ['arbol' => [], 'hojas' => [], 'pivote' => null],
            'horizontal' => ['arbol' => [], 'hojas' => [], 'pivote' => null],
            'tabla' => [],
            'tabla_headers' => [],
            'max_filas' => 0,
            'max_columnas' => 0,
        ];
    }

    // ============ HELPERS ============

    private function validateEje(string $eje): void
    {
        if (!in_array($eje, ['vertical', 'horizontal'])) {
            throw new \InvalidArgumentException('Eje inválido. Use "vertical" o "horizontal".');
        }
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
}
