<table>
    <tr>
        <td colspan="100" style="font-size:14pt;font-weight:bold;">
            {{ $codigoCuadro }} — {{ $tituloCuadro }}
        </td>
    </tr>
    @if($subtituloCuadro)
    <tr>
        <td colspan="100" style="font-size:10pt;color:#666;">{{ $subtituloCuadro }}</td>
    </tr>
    @endif
    <tr><td colspan="100"></td></tr>
</table>

@php
    $headers = $estado['headers'] ?? [];
    $labels = $estado['labels'] ?? [];
    $data = $estado['data'] ?? [];
    $horizontales = $estado['horizontales'] ?? [];
    $verticales = $estado['verticales'] ?? [];
    $pivotLabel = $estado['pivot_label'] ?? 'PIVOTE';
    $numLabelCols = 1;
    if (!empty($headers) && !empty($headers[0]) && ($headers[0][0]['tipo'] ?? '') === 'corner') {
        $numLabelCols = $headers[0][0]['colspan'] ?? 1;
    }

    $childrenOfH = [];
    foreach ($horizontales as $h) {
        if (!empty($h['padre_id'])) {
            $childrenOfH[$h['padre_id']][] = $h;
        }
    }
    $childrenOfV = [];
    foreach ($verticales as $v) {
        if (!empty($v['padre_id'])) {
            $childrenOfV[$v['padre_id']][] = $v;
        }
    }

    $visHIdx = [];
    foreach ($horizontales as $i => $h) {
        $visHIdx[] = $i;
    }
    $visVIdx = [];
    foreach ($verticales as $i => $v) {
        $visVIdx[] = $i;
    }

    $visLabelRows = [];
    $parentSpan = [];
    $parentGroupOfIdx = [];
    $parentGroups = [];
    foreach ($labels as $ri => $rowCells) {
        $leaf = null;
        $pc = null;
        foreach ($rowCells as $c) {
            if ($c['tipo'] === 'leaf') $leaf = $c;
            if ($c['tipo'] === 'parent') $pc = $c;
        }
        if (!$leaf) continue;
        if (!in_array($leaf['row_index'], $visVIdx)) continue;
        $stripped = [];
        foreach ($rowCells as $c) {
            if ($c['tipo'] !== 'parent') $stripped[] = $c;
        }
        $visLabelRows[] = $stripped;
        if ($pc) {
            if (!isset($parentSpan[$pc['categoria_id']])) $parentSpan[$pc['categoria_id']] = 0;
            $parentSpan[$pc['categoria_id']]++;
            if (!isset($parentGroupOfIdx[$ri])) $parentGroupOfIdx[$ri] = $pc['categoria_id'];
            if (!isset($seenParent[$pc['categoria_id']])) $seenParent[$pc['categoria_id']] = count($visLabelRows) - 1;
        }
    }

    $parentSpanFinal = [];
    $seenParent = [];
    foreach ($labels as $ri => $rowCells) {
        $leaf = null;
        $pc = null;
        foreach ($rowCells as $c) {
            if ($c['tipo'] === 'leaf') $leaf = $c;
            if ($c['tipo'] === 'parent') $pc = $c;
        }
        if (!$leaf || !in_array($leaf['row_index'], $visVIdx)) continue;
        if ($pc) {
            $pid = $pc['categoria_id'];
            if (!isset($parentSpanFinal[$pid])) $parentSpanFinal[$pid] = 0;
            $parentSpanFinal[$pid]++;
            if (!isset($seenParent[$pid])) $seenParent[$pid] = count($visLabelRows) - 1;
        }
    }

    $finalLabelRows = [];
    foreach ($visLabelRows as $idx => $rowCells) {
        $finalRow = [];
        $hasParent = false;
        foreach ($rowCells as $c) {
            if ($c['tipo'] === 'parent') { $hasParent = true; break; }
        }
        if (!$hasParent) {
            foreach ($parentSpanFinal as $pid => $span) {
                if (isset($seenParent[$pid]) && $seenParent[$pid] === $idx) {
                    array_unshift($rowCells, ['tipo' => 'parent', 'categoria_id' => $pid, 'nombre' => '']);
                    $pidNombre = '';
                    foreach ($labels as $r2) {
                        foreach ($r2 as $c2) {
                            if ($c2['tipo'] === 'parent' && $c2['categoria_id'] == $pid) {
                                $pidNombre = $c2['nombre'];
                                break 2;
                            }
                        }
                    }
                    $rowCells[0]['nombre'] = $pidNombre;
                    break;
                }
            }
        }
        $finalLabelRows[] = $rowCells;
    }

    function esc($s) {
        if ($s === null || $s === false) return '';
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
@endphp

<table>
    @php
        $hDepth = count($headers);
    @endphp
    @for ($ri = 0; $ri < $hDepth; $ri++)
        <tr>
            @foreach ($headers[$ri] as $cell)
                @if ($cell['tipo'] === 'corner')
                    <th rowspan="{{ $cell['rowspan'] ?? $hDepth }}" colspan="{{ $numLabelCols }}" style="text-align:center;font-weight:bold;background:#e8edf2;border:1px solid #000;">
                        {{ esc($pivotLabel) }}
                    </th>
                @elseif ($cell['tipo'] === 'parent')
                    @php
                        $start = $cell['col_index'];
                        $end = $start + $cell['colspan'];
                        $cnt = 0;
                        foreach ($visHIdx as $hidx) {
                            if ($hidx >= $start && $hidx < $end) $cnt++;
                        }
                        if ($cnt === 0) continue;
                    @endphp
                    <th colspan="{{ $cnt }}" style="text-align:center;font-weight:bold;background:#d4e6f1;border:1px solid #000;">
                        {{ esc($cell['nombre']) }}
                    </th>
                @elseif ($cell['tipo'] === 'leaf')
                    @php
                        if (!in_array($cell['col_index'], $visHIdx)) continue;
                    @endphp
                    <th style="text-align:center;font-weight:bold;background:#eaf2f8;border:1px solid #000;white-space:nowrap;">
                        {{ esc($cell['nombre']) }}
                    </th>
                @endif
            @endforeach
        </tr>
    @endfor

    @foreach ($finalLabelRows as $rowCells)
        <tr>
            @foreach ($rowCells as $cell)
                @if ($cell['tipo'] === 'parent')
                    <th rowspan="{{ $parentSpanFinal[$cell['categoria_id']] ?? 1 }}" style="text-align:left;font-weight:bold;background:#d5f5e3;border:1px solid #000;">
                        {{ esc($cell['nombre']) }}
                    </th>
                @elseif ($cell['tipo'] === 'leaf')
                    @php
                        $hasParent = false;
                        foreach ($rowCells as $rc) { if ($rc['tipo'] === 'parent') { $hasParent = true; break; } }
                        $cs = $hasParent && !empty($cell['colspan']) ? ' colspan="'.$cell['colspan'].'"' : '';
                    @endphp
                    <th{{ $cs }} style="text-align:left;font-weight:bold;background:#fef9e7;border:1px solid #000;">
                        {{ esc($cell['nombre']) }}
                    </th>
                @endif
            @endforeach
            @foreach ($visHIdx as $hidx)
                @php
                    $val = '';
                    if (!empty($data)) {
                        $vertIdx = null;
                        foreach ($rowCells as $c) { if ($c['tipo'] === 'leaf') $vertIdx = $c['row_index']; }
                        if ($vertIdx !== null && isset($data[$vertIdx][$hidx])) {
                            $cel = $data[$vertIdx][$hidx];
                            $val = ($cel['valor'] !== '' && $cel['valor'] !== null) ? $cel['valor'] : '';
                        }
                    }
                @endphp
                <td style="text-align:right;border:1px solid #000;">{{ esc($val) }}</td>
            @endforeach
        </tr>
    @endforeach
</table>

@if($piePagina)
<table>
    <tr><td colspan="100" style="border-top:1px solid #ccc;padding-top:8px;font-size:9pt;color:#555;">{!! $piePagina !!}</td></tr>
</table>
@endif
