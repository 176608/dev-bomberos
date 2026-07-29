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

    $visHIdx = [];
    foreach ($horizontales as $i => $h) {
        $visHIdx[] = $i;
    }
    $visVIdx = [];
    foreach ($verticales as $i => $v) {
        $visVIdx[] = $i;
    }

    // ─── Parent groups from original labels ───
    $parentGroups = [];
    $parentGroupOfIdx = [];
    foreach ($labels as $ri => $rowCells) {
        $pc = null;
        foreach ($rowCells as $c) { if ($c['tipo'] === 'parent') $pc = $c; }
        if ($pc) {
            $parentGroups[] = ['parentId' => $pc['categoria_id'], 'cell' => $pc, 'visibleCount' => 0];
        }
        $parentGroupOfIdx[$ri] = !empty($parentGroups) ? $parentGroups[count($parentGroups) - 1]['parentId'] : null;
    }

    // ─── Filter label rows, strip parent cells ───
    $visLabelRows = [];
    $seenParent = [];
    $parentSpan = [];
    foreach ($labels as $ri => $rowCells) {
        $leaf = null;
        foreach ($rowCells as $c) { if ($c['tipo'] === 'leaf') $leaf = $c; }
        if (!$leaf) continue;
        if (!in_array($leaf['row_index'], $visVIdx)) continue;
        $stripped = [];
        foreach ($rowCells as $c) { if ($c['tipo'] !== 'parent') $stripped[] = $c; }
        $visLabelRows[] = $stripped;
        $pid = $parentGroupOfIdx[$ri] ?? null;
        if ($pid !== null) {
            foreach ($parentGroups as &$pg) {
                if ($pg['parentId'] === $pid) {
                    $pg['visibleCount']++;
                    if (!isset($seenParent[$pid])) $seenParent[$pid] = count($visLabelRows) - 1;
                    break;
                }
            }
            unset($pg);
        }
    }

    // ─── Inject parent cell at first visible row of each group ───
    foreach ($parentGroups as $pg) {
        if ($pg['visibleCount'] > 0) {
            $parentSpan[$pg['parentId']] = $pg['visibleCount'];
            $insertAt = $seenParent[$pg['parentId']] ?? null;
            if ($insertAt !== null) {
                array_unshift($visLabelRows[$insertAt], [
                    'tipo' => 'parent', 'categoria_id' => $pg['parentId'], 'nombre' => $pg['cell']['nombre']
                ]);
            }
        }
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

    @foreach ($visLabelRows as $rowCells)
        <tr>
            @foreach ($rowCells as $cell)
                @if ($cell['tipo'] === 'parent')
                    <th rowspan="{{ $parentSpan[$cell['categoria_id']] ?? 1 }}" style="text-align:left;font-weight:bold;background:#d5f5e3;border:1px solid #000;">
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
