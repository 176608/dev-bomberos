<?php

namespace App\Services\GestorSIGEM;

class DatasetGridPresenter
{
    public function buildTree($categories): array
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

    public function serializeTree(array $nodes): array
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
                $item['num_hijos'] = $hijos->count();
            } else {
                $item['span'] = 1;
                $item['num_hijos'] = 0;
            }
            $result[] = $item;
        }
        return $result;
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

    public function buildEncabezados(array $horizTree, int $numLabelCols): array
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
                    'num_hijos' => $node['num_hijos'] ?? 0,
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

    public function buildEtiquetas(array $vertTree): array
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
                            'num_hijos' => $node['num_hijos'] ?? 0,
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
}
