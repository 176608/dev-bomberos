@php
    function esc($s) {
        if ($s === null || $s === false) return '';
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }

    function hexToIntensity($hex, $intensity) {
        $hex = ltrim((string)$hex, '#');
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) return null;
        $mix = 1 - (float)$intensity;
        $rgb = '#';
        for ($i = 0; $i < 6; $i += 2) {
            $c = hexdec(substr($hex, $i, 2));
            $rgb .= str_pad(dechex((int)round($c * $intensity + 255 * $mix)), 2, '0', STR_PAD_LEFT);
        }
        return $rgb;
    }

    $maxCols = 0;
    foreach ($seccionesData as $sd) {
        $est = $sd['estado'];
        $h = $est['horizontales'] ?? [];
        $hdrs = $est['headers'] ?? [];
        $nl = 1;
        if (!empty($hdrs) && !empty($hdrs[0]) && ($hdrs[0][0]['tipo'] ?? '') === 'corner') {
            $nl = $hdrs[0][0]['colspan'] ?? 1;
        }
        $cols = $nl + count($h);
        if ($cols > $maxCols) $maxCols = $cols;
    }
    if ($maxCols < 1) $maxCols = 100;
@endphp

@if($mostrarLogo)
<table>
    <tr><td></td><td style="height:45px"></td></tr>
    <tr><td></td><td style="height:16pt"></td></tr>
    <tr><td></td><td style="height:16pt"></td></tr>
</table>
@endif

@php
    $titleFull = $codigoCuadro . ' — ' . $tituloCuadro;
    $titleLines = max(1, ceil(mb_strlen($titleFull) / 80)) + 1;
    $titleH = ($titleLines * 18) . 'pt';
    $subH = $subtituloCuadro ? max(1, ceil(mb_strlen($subtituloCuadro) / 90)) * 14 + 4 . 'pt' : 0;
@endphp
<table>
    <tr><td></td><td colspan="{{ $maxCols }}" style="height:8pt"></td></tr>
    <tr>
        <td></td>
        <td colspan="{{ $maxCols }}" style="font-size:14pt;font-weight:bold;white-space:normal;word-wrap:break-word;height:{{ $titleH }};">
            {{ $titleFull }}
        </td>
    </tr>
    @if($subtituloCuadro)
    <tr>
        <td></td>
        <td colspan="{{ $maxCols }}" style="font-size:10pt;color:#666;white-space:normal;word-wrap:break-word;height:{{ $subH }};">{{ $subtituloCuadro }}</td>
    </tr>
    @endif
</table>

@foreach($seccionesData as $secIdx => $secData)
@php
    $estado = $secData['estado'];
    $seccion = $secData['seccion'];
    $headers = $estado['headers'] ?? [];
    $labels = $estado['labels'] ?? [];
    $data = $estado['data'] ?? [];
    $horizontales = $estado['horizontales'] ?? [];
    $verticales = $estado['verticales'] ?? [];
    $temaColor = $estado['tema_color'] ?? '';
    $themeOk = is_string($temaColor) && preg_match('/^#[0-9a-fA-F]{6}$/', $temaColor) === 1;
    $pivotBg = $themeOk ? $temaColor : '#e8edf2';
    $pivotBottom = $themeOk ? 'border-bottom:medium ' . $temaColor . ';' : '';
    $hParentBg = $themeOk ? hexToIntensity($temaColor, 0.75) : '#d4e6f1';
    $hLeafBg = $themeOk ? hexToIntensity($temaColor, 0.5) : '#eaf2f8';
    $vParentBg = $themeOk ? hexToIntensity($temaColor, 0.75) : '#d5f5e3';
    $vLeafBg = $themeOk ? hexToIntensity($temaColor, 0.5) : '#fef9e7';
    $vChildBg = $themeOk ? hexToIntensity($temaColor, 0.25) : '#fef9e7';
    $stripedBg = $themeOk ? hexToIntensity($temaColor, 0.125) : '#f2f2f2';
    $totalBg = $themeOk ? hexToIntensity($temaColor, 0.6) : '#bfbfbf';
    $pivotLabel = $estado['pivot_label'] ?? 'PIVOTE';
    $numLabelCols = 1;
    if (!empty($headers) && !empty($headers[0]) && ($headers[0][0]['tipo'] ?? '') === 'corner') {
        $numLabelCols = $headers[0][0]['colspan'] ?? 1;
    }

    $visVIds = $estado['_visibleVerticalIds'] ?? null;
    $visHIds = $estado['_visibleHorizontalIds'] ?? null;

    $visVIdx = [];
    foreach ($verticales as $i => $v) {
        if ($visVIds === null || in_array($v['categoria_id'], $visVIds)) {
            $visVIdx[] = $i;
        }
    }
    $visHIdx = [];
    foreach ($horizontales as $i => $h) {
        if ($visHIds === null || in_array($h['categoria_id'], $visHIds)) {
            $visHIdx[] = $i;
        }
    }

    $totalCols = $numLabelCols + count($visHIdx);

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
@endphp

@php $showSecHeader = ($seccion['nombre'] ?? '') !== 'Serie única'; @endphp
@if($showSecHeader)
    @if($secIdx > 0)
    <table><tr><td></td><td colspan="{{ $totalCols }}" style="border-top:1px solid #999;height:4px"></td></tr></table>
    @endif
    <table>
        <tr><td></td><td colspan="{{ $totalCols }}" style="font-size:11pt;font-weight:bold;color:#333;background:#f0f0f0;padding:4px 8px;text-align:center;">{!! esc($seccion['nombre'] ?? ('Sección ' . ($secIdx + 1))) !!}</td></tr>
    </table>
@endif

<table>
    @php $hDepth = count($headers); @endphp
    @for ($ri = 0; $ri < $hDepth; $ri++)
        <tr>
            <td></td>
            @foreach ($headers[$ri] as $cell)
                @if ($cell['tipo'] === 'corner')
                    <th rowspan="{{ $cell['rowspan'] ?? $hDepth }}" colspan="{{ $numLabelCols }}" style="text-align:center;vertical-align:middle;font-weight:bold;background:{{ $pivotBg }};border:1px solid #000;{{ $pivotBottom }}">
                        {!! esc($pivotLabel) !!}
                    </th>
                @elseif ($cell['tipo'] === 'parent')
                    @php
                        $start = $cell['col_index'];
                        $end = $start + $cell['colspan'];
                        $cnt = 0;
                        foreach ($visHIdx as $hidx) { if ($hidx >= $start && $hidx < $end) $cnt++; }
                        if ($cnt === 0) continue;
                    @endphp
                    <th colspan="{{ $cnt }}" style="text-align:center;vertical-align:middle;font-weight:bold;background:{{ $hParentBg }};border:1px solid #000;">
                        {!! esc($cell['nombre']) !!}
                    </th>
                @elseif ($cell['tipo'] === 'leaf')
                    @php if (!in_array($cell['col_index'], $visHIdx)) continue; @endphp
                    <th style="text-align:center;vertical-align:middle;font-weight:bold;background:{{ $hLeafBg }};border:1px solid #000;white-space:nowrap;">
                        {!! esc($cell['nombre']) !!}
                    </th>
                @endif
            @endforeach
        </tr>
    @endfor

    @foreach ($visLabelRows as $ri => $rowCells)
        @php
            $isEven = $ri % 2 === 0;
            $rowLeaf = null;
            foreach ($rowCells as $c) { if ($c['tipo'] === 'leaf') $rowLeaf = $c; }
            $totalName = $rowLeaf ? ($rowLeaf['nombre'] ?? '') : '';
            $isTotal = preg_match('/^(Total|Totales|Sumatoria|Preliminar|Acumulado)$/', $totalName);
            $dataBg = $isTotal ? $totalBg : ($isEven ? $stripedBg : '#ffffff');
            $rowHasParent = false;
            foreach ($rowCells as $c) { if ($c['tipo'] === 'parent') { $rowHasParent = true; break; } }
            $groupTop = ($rowHasParent && $ri > 0)
                ? 'border-top:medium ' . ($themeOk ? $temaColor : '#adb5bd') . ';'
                : '';
            $dataStyle = 'text-align:right;vertical-align:middle;border:1px solid #000;' . $groupTop . 'background:' . $dataBg . ';' . ($isTotal ? 'font-weight:700;' : '');
        @endphp
        <tr>
            <td></td>
            @foreach ($rowCells as $cell)
                @if ($cell['tipo'] === 'parent')
                    <th rowspan="{{ $parentSpan[$cell['categoria_id']] ?? 1 }}" style="text-align:left;vertical-align:middle;font-weight:bold;background:{{ $vParentBg }};border:1px solid #000;{{ $groupTop }}">
                        {!! esc($cell['nombre']) !!}
                    </th>
                @elseif ($cell['tipo'] === 'leaf')
                    @php
                        $hasParent = false;
                        foreach ($rowCells as $rc) { if ($rc['tipo'] === 'parent') { $hasParent = true; break; } }
                        $cs = $hasParent && !empty($cell['colspan']) ? ' colspan="'.$cell['colspan'].'"' : '';
                        $vBg = !empty($cell['es_hijo']) ? $vChildBg : $vLeafBg;
                    @endphp
                    <th{{ $cs }} style="text-align:left;vertical-align:middle;font-weight:bold;background:{{ $vBg }};border:1px solid #000;{{ $groupTop }}">
                        {!! esc($cell['nombre']) !!}
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
                <td style="{{ $dataStyle }}">{!! esc($val) !!}</td>
            @endforeach
        </tr>
    @endforeach
</table>
@endforeach

@if($piePagina)
<table>
    <tr><td></td><td colspan="{{ $maxCols }}" style="border-top:1px solid #ccc;padding-top:8px;font-size:9pt;color:#555;">{!! App\Services\HtmlSanitizer::sanitize($piePagina) !!}</td></tr>
</table>
@endif