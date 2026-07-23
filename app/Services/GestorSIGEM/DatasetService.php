<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\CuadroCategoria;
use App\Models\SIGEM\CuadroDato;
use App\Models\SIGEM\CuadroSeccion;
use Illuminate\Http\UploadedFile;

class DatasetService
{
    public function __construct(
        private Cuadro $cuadro,
        private CuadroCategoria $categoria,
        private CuadroDato $dato,
        private CuadroSeccion $seccion,
        private DatasetGridPresenter $presenter,
    ) {}

    public function obtenerEstado(int $cuadro_id, ?int $seccion_id = null): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $secciones = $this->seccion->where('cuadro_id', $cuadro_id)->orderBy('orden')->get();
        if ($secciones->isEmpty()) {
            $secciones = collect([$this->seccion->create([
                'cuadro_id' => $cuadro_id, 'nombre' => 'Serie única', 'orden' => 1,
            ])]);
        }
        if ($seccion_id === null || !$secciones->pluck('seccion_id')->contains($seccion_id)) {
            $seccionActiva = $secciones->first();
        } else {
            $seccionActiva = $secciones->firstWhere('seccion_id', $seccion_id);
        }

        $allVertical = $cuadro->categoriasVerticales()->orderBy('orden')->get();
        $allHorizontal = $cuadro->categoriasHorizontales()->orderBy('orden')->get();
        $datos = $cuadro->datos()->where('seccion_id', $seccionActiva->seccion_id)->get();

        $vertTree = $this->presenter->buildTree($allVertical);
        $horizTree = $this->presenter->buildTree($allHorizontal);

        $vertTreeSerialized = $this->presenter->serializeTree($vertTree);
        $horizTreeSerialized = $this->presenter->serializeTree($horizTree);

        $labels = $this->presenter->buildEtiquetas($vertTreeSerialized);
        $numLabelCols = $labels ? max(array_map('count', $labels)) : 1;
        $headers = $this->presenter->buildEncabezados($horizTreeSerialized, $numLabelCols);

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
            'tema_color' => $cuadro->tema?->color ?? null,
            'secciones' => $secciones->toArray(),
            'seccion_activa_id' => $seccionActiva->seccion_id,
        ];
    }

    public function generarGrilla(int $cuadro_id, int $filas, int $columnas): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $this->seccion->where('cuadro_id', $cuadro_id)->delete();
        $cuadro->datos()->delete();
        $this->deleteCategoriasSafe($cuadro);

        $seccion = $this->seccion->create([
            'cuadro_id' => $cuadro_id, 'nombre' => 'Serie única', 'orden' => 1,
        ]);

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
                    'seccion_id' => $seccion->seccion_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => '', 'fila' => $f + 1, 'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarFila(int $cuadro_id, ?string $nombre = null): array
    {
        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'vertical')->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id, 'eje' => 'vertical',
            'nombre' => $nombre ?? ('Fila ' . ($maxOrden + 1)),
            'orden' => $maxOrden + 1, 'tipo' => 'dato',
        ]);

        $secciones = $this->seccion->where('cuadro_id', $cuadro_id)->get();
        $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');

        foreach ($secciones as $seccion) {
            foreach ($horizontales as $c => $hCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'seccion_id' => $seccion->seccion_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $cat->categoria_id,
                    'valor' => '', 'fila' => $maxOrden + 1, 'columna' => $c + 1,
                ]);
            }
        }

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
        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarColumna(int $cuadro_id, ?string $nombre = null): array
    {
        $maxOrden = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', 'horizontal')->max('orden') ?? 0;

        $cat = $this->categoria->create([
            'cuadro_id' => $cuadro_id, 'eje' => 'horizontal',
            'nombre' => $nombre ?? ('Columna ' . ($maxOrden + 1)),
            'orden' => $maxOrden + 1, 'tipo' => 'dato',
        ]);

        $secciones = $this->seccion->where('cuadro_id', $cuadro_id)->get();
        $verticales = $this->getLeafCategories($cuadro_id, 'vertical');

        foreach ($secciones as $seccion) {
            foreach ($verticales as $f => $vCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'seccion_id' => $seccion->seccion_id,
                    'cat_horizontal_id' => $cat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => '', 'fila' => $f + 1, 'columna' => $maxOrden + 1,
                ]);
            }
        }

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
        return $this->obtenerEstado($cuadro_id);
    }

    public function agregarHijo(int $cuadro_id, int $padre_id, ?string $nombre = null): array
    {
        $padre = $this->categoria->find($padre_id);
        if (!$padre || $padre->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría padre no encontrada');
        }

        if ($padre->padre_id !== null) {
            throw new \RuntimeException('No se pueden agregar hijos a una categoría que ya es hija. Máximo 2 niveles de jerarquía.');
        }

        $maxOrden = $padre->hijos()->max('orden') ?? 0;

        $hijo = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $padre->eje,
            'padre_id' => $padre->categoria_id,
            'nombre' => $nombre ?? ('Hijo ' . ($maxOrden + 1)),
            'orden' => $maxOrden + 1,
            'tipo' => 'dato',
        ]);

        $secciones = $this->seccion->where('cuadro_id', $cuadro_id)->get();

        if ($padre->eje === 'vertical') {
            $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');
            foreach ($secciones as $seccion) {
                foreach ($horizontales as $c => $hCat) {
                    $this->dato->create([
                        'cuadro_id' => $cuadro_id,
                        'seccion_id' => $seccion->seccion_id,
                        'cat_horizontal_id' => $hCat->categoria_id,
                        'cat_vertical_id' => $hijo->categoria_id,
                        'valor' => '',
                        'fila' => $maxOrden + 1, 'columna' => $c + 1,
                    ]);
                }
            }
        } else {
            $verticales = $this->getLeafCategories($cuadro_id, 'vertical');
            foreach ($secciones as $seccion) {
                foreach ($verticales as $f => $vCat) {
                    $this->dato->create([
                        'cuadro_id' => $cuadro_id,
                        'seccion_id' => $seccion->seccion_id,
                        'cat_horizontal_id' => $hijo->categoria_id,
                        'cat_vertical_id' => $vCat->categoria_id,
                        'valor' => '',
                        'fila' => $f + 1, 'columna' => $maxOrden + 1,
                    ]);
                }
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    public function clonarCategoria(int $cuadro_id, int $categoria_id): array
    {
        $padre = $this->categoria->find($categoria_id);
        if (!$padre || $padre->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Categoría no encontrada');
        }
        if ($padre->padre_id !== null) {
            throw new \RuntimeException('No se puede clonar una categoría hija');
        }

        $hijos = $padre->hijos()->orderBy('orden')->get();
        if ($hijos->count() < 2) {
            throw new \RuntimeException('La categoría debe tener al menos 2 hijos para clonar');
        }

        $eje = $padre->eje;

        $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)
            ->whereNull('padre_id')
            ->where('orden', '>', $padre->orden)
            ->increment('orden');

        $baseNombre = $padre->nombre . ' Clon';
        $nombre = $baseNombre;
        $contador = 0;
        while ($this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)
            ->whereNull('padre_id')
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])
            ->exists()
        ) {
            $contador++;
            $nombre = $baseNombre . '(' . $contador . ')';
        }

        $clon = $this->categoria->create([
            'cuadro_id' => $cuadro_id,
            'eje' => $eje,
            'nombre' => $nombre,
            'orden' => $padre->orden + 1,
            'tipo' => 'dato',
        ]);

        $nuevosHijos = [];
        foreach ($hijos as $hijo) {
            $nuevosHijos[] = $this->categoria->create([
                'cuadro_id' => $cuadro_id,
                'eje' => $eje,
                'padre_id' => $clon->categoria_id,
                'nombre' => $hijo->nombre,
                'orden' => $hijo->orden,
                'tipo' => 'dato',
            ]);
        }

        $secciones = $this->seccion->where('cuadro_id', $cuadro_id)->get();

        if ($eje === 'vertical') {
            $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');
            foreach ($secciones as $seccion) {
                foreach ($nuevosHijos as $nh) {
                    foreach ($horizontales as $c => $hCat) {
                        $this->dato->create([
                            'cuadro_id' => $cuadro_id,
                            'seccion_id' => $seccion->seccion_id,
                            'cat_horizontal_id' => $hCat->categoria_id,
                            'cat_vertical_id' => $nh->categoria_id,
                            'valor' => '',
                            'fila' => $nh->orden, 'columna' => $c + 1,
                        ]);
                    }
                }
            }
        } else {
            $verticales = $this->getLeafCategories($cuadro_id, 'vertical');
            foreach ($secciones as $seccion) {
                foreach ($verticales as $f => $vCat) {
                    foreach ($nuevosHijos as $nh) {
                        $this->dato->create([
                            'cuadro_id' => $cuadro_id,
                            'seccion_id' => $seccion->seccion_id,
                            'cat_horizontal_id' => $nh->categoria_id,
                            'cat_vertical_id' => $vCat->categoria_id,
                            'valor' => '',
                            'fila' => $f + 1, 'columna' => $nh->orden,
                        ]);
                    }
                }
            }
        }

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

    public function pasteGrid(int $cuadro_id, array $grid, ?int $startVerticalId = null, ?int $startHorizontalId = null, ?array $vertOrder = null, ?array $horizOrder = null, ?int $seccionId = null): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        if ($startVerticalId !== null && $startHorizontalId !== null) {
            return $this->pastePartial($cuadro_id, $cuadro, $grid, $startVerticalId, $startHorizontalId, $vertOrder, $horizOrder, $seccionId);
        }

        if (count($grid) < 2) throw new \InvalidArgumentException('Debe tener al menos 2 filas');

        $this->seccion->where('cuadro_id', $cuadro_id)->delete();
        $cuadro->datos()->delete();
        $this->deleteCategoriasSafe($cuadro);

        $seccion = $this->seccion->create([
            'cuadro_id' => $cuadro_id, 'nombre' => 'Serie única', 'orden' => 1,
        ]);

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
                    'seccion_id' => $seccion->seccion_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'valor' => $valor, 'valor_crudo' => $valor,
                    'fila' => $f + 1, 'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id);
    }

    private function pastePartial(int $cuadro_id, Cuadro $cuadro, array $grid, int $startVerticalId, int $startHorizontalId, ?array $vertOrder = null, ?array $horizOrder = null, ?int $seccionId = null): array
    {
        $verticales = collect();
        $horizontales = collect();

        if ($vertOrder !== null && $horizOrder !== null) {
            $vIdx = array_search($startVerticalId, $vertOrder);
            $hIdx = array_search($startHorizontalId, $horizOrder);
        } else {
            $verticales = $this->getLeafCategories($cuadro_id, 'vertical');
            $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');
            $vIdx = $verticales->search(fn($v) => $v->categoria_id === $startVerticalId);
            $hIdx = $horizontales->search(fn($h) => $h->categoria_id === $startHorizontalId);
        }

        if ($vIdx === false || $hIdx === false) {
            throw new \RuntimeException('Posición inicial no encontrada en la grilla');
        }

        if ($seccionId === null) {
            $seccion = $this->seccion->where('cuadro_id', $cuadro_id)->orderBy('orden')->first();
            $seccionId = $seccion?->seccion_id ?? throw new \RuntimeException('No hay secciones');
        }

        $getVId = function($pos) use ($vertOrder, $verticales) {
            return $vertOrder !== null ? $vertOrder[$pos] : $verticales[$pos]->categoria_id;
        };
        $getHId = function($pos) use ($horizOrder, $horizontales) {
            return $horizOrder !== null ? $horizOrder[$pos] : $horizontales[$pos]->categoria_id;
        };
        $countV = $vertOrder !== null ? count($vertOrder) : $verticales->count();
        $countH = $horizOrder !== null ? count($horizOrder) : $horizontales->count();

        foreach ($grid as $f => $row) {
            $vPos = $vIdx + $f;
            if ($vPos >= $countV) break;
            $vCatId = $getVId($vPos);
            foreach ($row as $c => $valor) {
                $hPos = $hIdx + $c;
                if ($hPos >= $countH) break;
                $hCatId = $getHId($hPos);
                $this->dato->updateOrCreate(
                    ['cuadro_id' => $cuadro_id, 'seccion_id' => $seccionId, 'cat_vertical_id' => $vCatId, 'cat_horizontal_id' => $hCatId],
                    ['valor' => $valor, 'valor_crudo' => $valor]
                );
            }
        }

        return $this->obtenerEstado($cuadro_id, $seccionId);
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

        return $this->obtenerEstado($cuadro_id);
    }

    public function limpiarDatos(int $cuadro_id, int $seccion_id): array
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);
        if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

        $this->dato->where('cuadro_id', $cuadro_id)
            ->where('seccion_id', $seccion_id)
            ->update(['valor' => '', 'valor_crudo' => '']);
        return $this->obtenerEstado($cuadro_id, $seccion_id);
    }

    // ============ SECCIONES ============

    public function agregarSeccion(int $cuadro_id, string $nombre, ?string $header = null, ?string $footer = null): array
    {
        $maxOrden = $this->seccion->where('cuadro_id', $cuadro_id)->max('orden') ?? 0;

        $seccion = $this->seccion->create([
            'cuadro_id' => $cuadro_id,
            'nombre' => $nombre,
            'orden' => $maxOrden + 1,
            'header' => $header,
            'footer' => $footer,
        ]);

        $verticales = $this->getLeafCategories($cuadro_id, 'vertical');
        $horizontales = $this->getLeafCategories($cuadro_id, 'horizontal');

        foreach ($verticales as $f => $vCat) {
            foreach ($horizontales as $c => $hCat) {
                $this->dato->create([
                    'cuadro_id' => $cuadro_id,
                    'seccion_id' => $seccion->seccion_id,
                    'cat_vertical_id' => $vCat->categoria_id,
                    'cat_horizontal_id' => $hCat->categoria_id,
                    'valor' => '',
                    'valor_crudo' => '',
                    'fila' => $f + 1,
                    'columna' => $c + 1,
                ]);
            }
        }

        return $this->obtenerEstado($cuadro_id, $seccion->seccion_id);
    }

    public function actualizarSeccion(int $seccion_id, string $nombre, ?string $header = null, ?string $footer = null): array
    {
        $seccion = $this->seccion->findOrFail($seccion_id);
        $seccion->update([
            'nombre' => $nombre,
            'header' => $header,
            'footer' => $footer,
        ]);
        return $this->obtenerEstado($seccion->cuadro_id, $seccion_id);
    }

    public function eliminarSeccion(int $seccion_id): array
    {
        $seccion = $this->seccion->findOrFail($seccion_id);
        $cuadro_id = $seccion->cuadro_id;

        $count = $this->seccion->where('cuadro_id', $cuadro_id)->count();
        if ($count <= 1) throw new \RuntimeException('No se puede eliminar la única sección');

        $seccion->delete();

        $primera = $this->seccion->where('cuadro_id', $cuadro_id)->orderBy('orden')->first();
        return $this->obtenerEstado($cuadro_id, $primera->seccion_id);
    }

    public function switchSeccion(int $cuadro_id, int $seccion_id): array
    {
        return $this->obtenerEstado($cuadro_id, $seccion_id);
    }

    public function reordenarSeccion(int $seccion_id, string $direccion): array
    {
        $seccion = $this->seccion->findOrFail($seccion_id);
        $cuadro_id = $seccion->cuadro_id;

        $ordenActual = $seccion->orden;
        $ordenObjetivo = $direccion === 'up' ? $ordenActual - 1 : $ordenActual + 1;

        if ($ordenObjetivo < 1) throw new \RuntimeException('La sección ya está en la primera posición');

        $vecina = $this->seccion->where('cuadro_id', $cuadro_id)
            ->where('orden', $ordenObjetivo)->first();
        if (!$vecina) throw new \RuntimeException('No hay sección adyacente en esa dirección');

        $seccion->update(['orden' => $ordenObjetivo]);
        $vecina->update(['orden' => $ordenActual]);

        return $this->obtenerEstado($cuadro_id, $seccion_id);
    }

    // ============ UTILITY HELPERS ============

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

    private function reordenar(int $cuadro_id, string $eje): void
    {
        $roots = $this->categoria->where('cuadro_id', $cuadro_id)
            ->where('eje', $eje)
            ->whereNull('padre_id')
            ->orderBy('orden')->get();
        foreach ($roots as $i => $cat) {
            $cat->update(['orden' => $i + 1]);
        }
    }

    private function deleteCategoriasSafe($cuadro): void
    {
        $allCats = $cuadro->categorias()->get();
        $childIds = $allCats->whereNotNull('padre_id')->pluck('categoria_id')->toArray();
        $parentIds = $allCats->whereNull('padre_id')->pluck('categoria_id')->toArray();
        if (!empty($childIds)) {
            $this->categoria->whereIn('categoria_id', $childIds)->delete();
        }
        if (!empty($parentIds)) {
            $this->categoria->whereIn('categoria_id', $parentIds)->delete();
        }
    }
}
