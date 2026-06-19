@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'SIGEM v2 — Catálogo de Indicadores')

@section('visor_content')
@php
    $totalTemas = $temas->count();

    function hexToRgba($hex, $alpha) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    }
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

.indice-tema-header.opacity-50 {
    opacity: 0.5;
}

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
        <p class="text-center lead">Son {{ $totalTemas }} temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

        <div class="row mt-4 catalogo-row">
            <div class="col-lg-4">
                <div class="card bg-light h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Estructura de Índice</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="indice-container">
                            @forelse($temas as $temaIdx => $tema)
                                @php
                                    $bgColor = $tema->color ?? '#8FBC8F';
                                    $claseNoPublicado = (!$tema->publicado && $esDesarrollador) ? 'opacity-50' : '';
                                @endphp
                                <div class="indice-tema-container">
                                    <div class="indice-tema-header {{ $claseNoPublicado }}" style="background-color: {{ $bgColor }};" onclick="document.getElementById('tema-{{ $tema->tema_id }}')?.scrollIntoView({behavior:'smooth', block:'start'});">
                                        {{ $temaIdx + 1 }}. {{ $tema->tema_titulo }}
                                        @if($tema->icono)
                                            <i class="{{ $tema->icono }} ms-2"></i>
                                        @endif
                                        @if(!$tema->publicado && $esDesarrollador)
                                            <span class="badge bg-warning text-dark ms-2"><i class="bi bi-eye-slash"></i></span>
                                        @endif
                                    </div>
                                    @forelse($tema->subtemas as $stIdx => $subtema)
                                        @php
                                            $claveEfectiva = $subtema->obtenerClaveEfectiva() ?? ($tema->clave_tema ?? 'N/A');
                                            $stClaseNoPublicado = (!$subtema->publicado && $esDesarrollador) ? 'opacity-50' : '';
                                        @endphp
                                        <div class="indice-subtema-row {{ $stClaseNoPublicado }}" style="background-color: {{ $stIdx % 2 === 0 ? hexToRgba($tema->color ?? '#8FBC8F', 0.12) : hexToRgba($tema->color ?? '#8FBC8F', 0.06) }};" onclick="document.getElementById('subtema-{{ $tema->tema_id }}-{{ $subtema->subtema_id }}')?.scrollIntoView({behavior:'smooth', block:'start'});">
                                            <div style="min-width:40px;text-align:center;font-weight:600;color:#2a6e48;padding:8px;">{{ $claveEfectiva }}</div>
                                            <div style="flex:1;padding:8px 8px 8px 0;">{{ $subtema->subtema_titulo }}</div>
                                            @if(!$subtema->publicado && $esDesarrollador)
                                                <div style="padding:8px;"><span class="badge bg-warning text-dark" style="font-size:0.6rem;"><i class="bi bi-eye-slash"></i></span></div>
                                            @endif
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
                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Cuadros Estadísticos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="indicadores-container">
                            @forelse($temas as $temaIdx => $tema)
                                @php
                                    $bgColor = $tema->color ?? '#8FBC8F';
                                @endphp
                                <div id="tema-{{ $tema->tema_id }}" style="margin-bottom:16px;">
                                    <div class="p-2 fw-bold" style="background-color: {{ $bgColor }};">
                                        {{ $temaIdx + 1 }}. {{ mb_strtoupper($tema->tema_titulo) }}
                                        @if($tema->icono)
                                            <i class="{{ $tema->icono }} ms-2"></i>
                                        @endif
                                        @if(!$tema->publicado && $esDesarrollador)
                                            <span class="badge bg-warning text-dark ms-2"><i class="bi bi-eye-slash"></i> No publicado</span>
                                        @endif
                                    </div>

                                    @forelse($tema->subtemas as $stIdx => $subtema)
                                        @php
                                            $indicadoresSubtema = $indicadores->where('subtema_id', $subtema->subtema_id);
                                        @endphp
                                        <div id="subtema-{{ $tema->tema_id }}-{{ $subtema->subtema_id }}">
                                            <div class="fw-bold px-2 py-1" style="background:{{ hexToRgba($tema->color ?? '#8FBC8F', 0.12) }};border-left:4px solid {{ $tema->color ?? '#8FBC8F' }};font-size:0.9rem;{{ !$subtema->publicado && $esDesarrollador ? 'opacity:0.5;' : '' }}">
                                                <i class="bi bi-bookmark-fill me-1 text-success"></i>{{ $subtema->subtema_titulo }}
                                                @if(!$subtema->publicado && $esDesarrollador)
                                                    <span class="badge bg-warning text-dark ms-1"><i class="bi bi-eye-slash"></i></span>
                                                @endif
                                            </div>

                                            @forelse($indicadoresSubtema as $indIdx => $indicador)
                                                @php
                                                    $indClase = (!$indicador->publicado && $esDesarrollador) ? 'opacity-50' : '';
                                                    $indBorde = (!$indicador->publicado && $esDesarrollador) ? 'border-left: 3px solid #ffc107;' : '';
                                                @endphp
                                                <div class="indicador-fila {{ $indClase }}" style="background:{{ $indIdx % 2 === 0 ? '#ffffff' : '#f8f9fa' }};{{ $indBorde }}" onclick="alert('ID del Indicador: {{ $indicador->cuadro_id }}')">
                                                    <span class="codigo">{{ $indicador->codigo_cuadro }}</span>
                                                    <span class="titulo">
                                                        <strong>{{ $indicador->c_titulo }}</strong>
                                                        @if($indicador->c_subtitulo)
                                                            <div class="subtitulo">{{ $indicador->c_subtitulo }}</div>
                                                        @endif
                                                    </span>
                                                    @if(!$indicador->publicado && $esDesarrollador)
                                                        <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;"><i class="bi bi-eye-slash"></i></span>
                                                    @endif
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