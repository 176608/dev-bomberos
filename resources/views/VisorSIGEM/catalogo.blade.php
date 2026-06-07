@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'SIGEM v2 — Catálogo de Indicadores')

@section('visor_content')
@php
    $coloresTema = ['#8FBC8F', '#87CEEB', '#DDA0DD', '#F0E68C', '#FFA07A', '#98FB98'];
    $temasDetalle = $estructura['temas_detalle'] ?? [];
    $totalTemas = $estructura['total_temas'] ?? count($temasDetalle);
@endphp

<style>
.catalogo-row .card-body { padding: 0 !important; }
#indice-container, #indicadores-container {
    height: 800px; overflow-y: auto;
}

.indice-tema-container {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.indice-tema-container:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.indice-tema-header {
    text-align: center; font-weight: bold; padding: 10px;
    cursor: pointer; transition: all 0.3s ease;
}
.indice-tema-header:hover { transform: translateY(-1px); }
.indice-subtema-row {
    display: flex; border-bottom: 1px solid #eee;
    cursor: pointer; transition: all 0.3s ease; align-items: center;
}
.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px) !important;
}
.indice-subtema-row:last-child { border-bottom: none; }

#indice-container::-webkit-scrollbar,
#indicadores-container::-webkit-scrollbar { width: 8px; }
#indice-container::-webkit-scrollbar-track,
#indicadores-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
#indice-container::-webkit-scrollbar-thumb,
#indicadores-container::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
#indice-container::-webkit-scrollbar-thumb:hover,
#indicadores-container::-webkit-scrollbar-thumb:hover { background: #555; }

.indicador-fila {
    display: flex;
    align-items: center;
    padding: 7px 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.15s ease;
    gap: 8px;
}
.indicador-fila:hover { background-color: #e8f4f8 !important; }
.indicador-fila:last-child { border-bottom: none; }
.indicador-fila .codigo {
    flex-shrink: 0;
    min-width: 70px;
    font-weight: 700;
    color: #1e6b3b;
    font-size: 0.82rem;
}
.indicador-fila .titulo {
    flex: 1;
    min-width: 0;
    font-size: 0.9rem;
    line-height: 1.35;
    color: #212529;
    overflow-wrap: break-word;
    word-break: break-word;
}
.indicador-fila .subtitulo {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 1px;
}

@media (max-width: 768px) {
    #indice-container, #indicadores-container { height: 500px; }
    #indice-container { margin-bottom: 16px; }
}
@media (max-width: 576px) {
    #indice-container, #indicadores-container { height: 400px; }
    .indicador-fila { padding: 6px 8px; gap: 6px; }
    .indicador-fila .codigo { min-width: 54px; font-size: 0.75rem; }
    .indicador-fila .titulo { font-size: 0.84rem; }
}
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            
        </div>
        <div class="row">
            
        </div>

        <p class="text-center lead">Son {{ $totalTemas }} temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los indicadores estadísticos.</p>

        <div class="row mt-4 catalogo-row">
            <div class="col-lg-4">
                <div class="card bg-light h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Estructura de Índice</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="indice-container">
                            @forelse($temasDetalle as $temaIdx => $tema)
                                @php
                                    $color = $coloresTema[$temaIdx % count($coloresTema)];
                                    $temaId = $tema['tema_id'] ?? $temaIdx;
                                    $temaTitulo = $tema['titulo'] ?? key($estructura['estructura'] ?? []);
                                @endphp
                                <div class="indice-tema-container">
                                    <div class="indice-tema-header" style="background-color: {{ $color }};" onclick="document.getElementById('tema-{{ $temaId }}')?.scrollIntoView({behavior:'smooth', block:'start'});">
                                        {{ $temaIdx + 1 }}. {{ $temaTitulo }}
                                    </div>
                                    @php $subtemas = $tema['subtemas'] ?? []; @endphp
                                    @forelse($subtemas as $stIdx => $subtema)
                                        @php
                                            $stId = $subtema['subtema_id'] ?? $stIdx;
                                            $claveEfectiva = $subtema['clave_efectiva'] ?? ($tema['clave_tema'] ?? 'N/A');
                                        @endphp
                                        <div class="indice-subtema-row" style="background-color: {{ $stIdx % 2 === 0 ? '#ffffff' : '#f8f9fa' }};" onclick="document.getElementById('subtema-{{ $temaId }}-{{ $stId }}')?.scrollIntoView({behavior:'smooth', block:'start'});">
                                            <div style="min-width:40px;text-align:center;font-weight:600;color:#2a6e48;padding:8px;">{{ $claveEfectiva }}</div>
                                            <div style="flex:1;padding:8px 8px 8px 0;">{{ $subtema['titulo'] ?? $subtema['nombre'] ?? '' }}</div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted p-3">Sin subtemas</div>
                                    @endforelse
                                </div>
                            @empty
                                <div class="text-center text-muted p-4">No hay temas disponibles</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card bg-light h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Indicadores Estadísticos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="indicadores-container">
                            @forelse($temasDetalle as $temaIdx => $tema)
                                @php
                                    $temaId = $tema['tema_id'] ?? $temaIdx;
                                    $temaTitulo = $tema['titulo'] ?? '';
                                    $color = $coloresTema[$temaIdx % count($coloresTema)];
                                @endphp
                                <div id="tema-{{ $temaId }}" style="margin-bottom:16px;">
                                    <div class="p-2 fw-bold" style="background-color: {{ $color }};">
                                        {{ $temaIdx + 1 }}. {{ mb_strtoupper($temaTitulo) }}
                                    </div>

                                    @php $subtemas = $tema['subtemas'] ?? []; @endphp
                                    @forelse($subtemas as $stIdx => $subtema)
                                        @php
                                            $stId = $subtema['subtema_id'] ?? $stIdx;
                                            $subtemaTitulo = $subtema['titulo'] ?? $subtema['nombre'] ?? '';
                                            $indicadoresSubtema = $indicadores->where('subtema_id', $stId);
                                        @endphp
                                        <div id="subtema-{{ $temaId }}-{{ $stId }}">
                                            <div class="fw-bold px-2 py-1" style="background:#e8f5e9;border-left:4px solid #2a6e48;font-size:0.9rem;">
                                                <i class="bi bi-bookmark-fill me-1 text-success"></i>{{ $subtemaTitulo }}
                                            </div>

                                            @forelse($indicadoresSubtema as $indIdx => $indicador)
                                                <div class="indicador-fila" style="background:{{ $indIdx % 2 === 0 ? '#ffffff' : '#f8f9fa' }};" onclick="alert('ID del Indicador: {{ $indicador->cuadro_estadistico_id }}')">
                                                    <span class="codigo">{{ $indicador->codigo_cuadro }}</span>
                                                    <span class="titulo">
                                                        <strong>{{ $indicador->cuadro_estadistico_titulo }}</strong>
                                                        @if($indicador->cuadro_estadistico_subtitulo)
                                                            <div class="subtitulo">{{ $indicador->cuadro_estadistico_subtitulo }}</div>
                                                        @endif
                                                    </span>
                                                </div>
                                            @empty
                                                <div class="text-center text-muted small p-2">Sin indicadores en este subtema</div>
                                            @endforelse
                                        </div>
                                    @empty
                                        <div class="text-center text-muted p-3">Sin subtemas en este tema</div>
                                    @endforelse
                                </div>
                            @empty
                                <div class="text-center text-muted p-4">No hay temas disponibles</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection