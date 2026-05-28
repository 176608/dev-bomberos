@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'SIGEM v2 — Catálogo de Indicadores')

@section('visor_content')
@php
    $coloresTema = ['#8FBC8F', '#87CEEB', '#DDA0DD', '#F0E68C', '#FFA07A', '#98FB98'];
    $temasDetalle = $estructura['temas_detalle'] ?? [];
    $totalTemas = $estructura['total_temas'] ?? count($temasDetalle);
    $totalSubtemas = $estructura['total_subtemas'] ?? 0;
@endphp

<style>
.indice-tema-container {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.indice-tema-container:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.indice-tema-header {
    text-align: center;
    font-weight: bold;
    padding: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.indice-tema-header:hover {
    transform: translateY(-1px);
}
.indice-subtema-row {
    display: flex;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.3s ease;
    align-items: center;
}
.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px) !important;
}
.indice-subtema-row:last-child {
    border-bottom: none;
}
.highlight-focus {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
    animation: pulseHighlight 1s ease-in-out;
    position: relative;
    z-index: 10;
}
@keyframes pulseHighlight {
    0% { transform: scale(1); box-shadow: 0 0 15px rgba(255, 193, 7, 0.5); }
    50% { transform: scale(1.02); box-shadow: 0 0 25px rgba(255, 193, 7, 0.8); }
    100% { transform: scale(1); box-shadow: 0 0 15px rgba(255, 193, 7, 0.5); }
}
.catalogo-row {
    align-items: stretch;
}
.catalogo-row .card {
    height: 100%;
}
.catalogo-row .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}
#indice-container::-webkit-scrollbar,
#cuadros-container::-webkit-scrollbar {
    width: 8px;
}
#indice-container::-webkit-scrollbar-track,
#cuadros-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
#indice-container::-webkit-scrollbar-thumb,
#cuadros-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
#indice-container::-webkit-scrollbar-thumb:hover,
#cuadros-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
.indicador-row {
    display: flex;
    align-items: baseline;
    gap: 10px;
    padding: 7px 12px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.15s ease;
    cursor: pointer;
}
.indicador-row:hover {
    background-color: #e8f4f8;
}
.indicador-row:last-child {
    border-bottom: none;
}
.indicador-codigo {
    flex-shrink: 0;
    min-width: 65px;
    font-weight: 700;
    color: #1e6b3b;
    font-size: 0.82rem;
    font-family: Consolas, "Courier New", monospace;
    text-align: right;
}
.indicador-titulo {
    flex: 1;
    min-width: 0;
    overflow-wrap: break-word;
    word-break: break-word;
    line-height: 1.35;
    font-size: 0.9rem;
}
.indicador-titulo .text-muted {
    font-size: 0.8rem;
}
@media (max-width: 768px) {
    #indice-container > div,
    #cuadros-container > div {
        max-height: 400px !important;
    }
    .indicador-row {
        padding: 6px 8px;
        gap: 6px;
    }
    .indicador-codigo {
        min-width: 50px;
        font-size: 0.75rem;
    }
    .indicador-titulo {
        font-size: 0.85rem;
    }
}
@media (max-width: 576px) {
    #indice-container > div,
    #cuadros-container > div {
        max-height: 300px !important;
    }
    .indicador-codigo {
        min-width: 44px;
        font-size: 0.7rem;
    }
    .indicador-titulo {
        font-size: 0.82rem;
    }
}
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes indicadores que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de indicador estadístico.
        </div>

        <div class="row">
            <div class="text-center mb-4">
                <img src="{{ asset('imagenes/ejem.png') }}" alt="Catálogo Ejemplo" class="img-fluid rounded shadow-sm">
            </div>
        </div>
        <div class="row">
            <div class="text-center mb-4">
                <p>El indicador de "Población por Municipio" se encuentra dentro del Tema 3. Sociodemográfico en el subtema de Población</p>
            </div>
        </div>

        <p class="text-center lead">Son {{ $totalTemas }} temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los indicadores estadísticos.</p>

        <div class="row mt-4 catalogo-row">
            <div class="col-lg-4">
                <div class="card bg-light h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Estructura de Índice
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="indice-container" style="max-height: 600px; overflow-y: auto;">
                            @forelse($temasDetalle as $temaIdx => $tema)
                                @php
                                    $color = $coloresTema[$temaIdx % count($coloresTema)];
                                    $temaId = $tema['tema_id'] ?? $temaIdx;
                                    $temaTitulo = $tema['titulo'] ?? key($estructura['estructura'] ?? []);
                                @endphp
                                <div class="indice-tema-container">
                                    <div class="indice-tema-header" style="background-color: {{ $color }};" onclick="document.getElementById('tema-indicadores-{{ $temaId }}')?.scrollIntoView({behavior:'smooth', block:'start'});">
                                        {{ $temaIdx + 1 }}. {{ $temaTitulo }}
                                    </div>
                                    @php $subtemas = $tema['subtemas'] ?? []; @endphp
                                    @forelse($subtemas as $stIdx => $subtema)
                                        @php
                                            $stId = $subtema['subtema_id'] ?? $stIdx;
                                            $claveEfectiva = $subtema['clave_efectiva'] ?? ($tema['clave_tema'] ?? 'N/A');
                                        @endphp
                                        <div class="indice-subtema-row" onclick="document.getElementById('subtema-indicadores-{{ $temaId }}-{{ $stId }}')?.scrollIntoView({behavior:'smooth', block:'start'});" style="background-color: {{ $stIdx % 2 === 0 ? '#ffffff' : '#f8f9fa' }};">
                                            <div style="min-width: 40px; text-align: center; font-weight: 600; color: #2a6e48; padding: 8px;">
                                                {{ $claveEfectiva }}
                                            </div>
                                            <div style="flex: 1; padding: 8px 8px 8px 0;">
                                                {{ $subtema['titulo'] ?? $subtema['nombre'] ?? '' }}
                                            </div>
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
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>Indicadores Estadísticos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="cuadros-container" style="max-height: 600px; overflow-y: auto;">
                            @forelse($temasDetalle as $temaIdx => $tema)
                                @php
                                    $temaId = $tema['tema_id'] ?? $temaIdx;
                                    $temaTitulo = $tema['titulo'] ?? '';
                                    $color = $coloresTema[$temaIdx % count($coloresTema)];
                                @endphp
                                <div id="tema-indicadores-{{ $temaId }}" style="margin-bottom: 20px;">
                                    <div class="d-flex align-items-center p-3 text-black fw-bold" style="background: linear-gradient(135deg, {{ $color }} 0%, #2a6e48 100%);">
                                        <span class="fs-5">{{ $temaIdx + 1 }}. {{ mb_strtoupper($temaTitulo) }}</span>
                                    </div>

                                    @php $subtemas = $tema['subtemas'] ?? []; @endphp
                                    @forelse($subtemas as $stIdx => $subtema)
                                        @php
                                            $stId = $subtema['subtema_id'] ?? $stIdx;
                                            $subtemaTitulo = $subtema['titulo'] ?? $subtema['nombre'] ?? '';
                                            $indicadoresSubtema = $indicadores->where('subtema_id', $stId);
                                        @endphp
                                        <div id="subtema-indicadores-{{ $temaId }}-{{ $stId }}" style="border-bottom: 2px solid #ddd;">
                                            <div class="fw-bold p-2" style="background-color: #e8f5e9; border-left: 4px solid #2a6e48;">
                                                <i class="bi bi-bookmark-fill me-1 text-success"></i>{{ $subtemaTitulo }}
                                            </div>

                                            @forelse($indicadoresSubtema as $indicador)
                                                <div class="indicador-row" onclick="alert('ID del Indicador: {{ $indicador->cuadro_estadistico_id }}')">
                                                    <div class="indicador-codigo">{{ $indicador->codigo_cuadro }}</div>
                                                    <div class="indicador-titulo">
                                                        <strong>{{ $indicador->cuadro_estadistico_titulo }}</strong>
                                                        @if($indicador->cuadro_estadistico_subtitulo)
                                                            <div class="text-muted">{{ $indicador->cuadro_estadistico_subtitulo }}</div>
                                                        @endif
                                                    </div>
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