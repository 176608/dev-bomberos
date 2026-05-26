@extends('VisorSIGEM.layouts.visor')

@section('title', 'Inicio — SIGEM v2')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="row mb-4 align-items-center">
            <div class="col-lg-8">
                <h2 class="text-success">SIGEM <small class="text-muted fs-6">v2 — Visor Estadístico</small></h2>
                <p class="lead">
                    Sistema de Información Geográfica y Estadística Municipal.
                    Consulta cuadros estadísticos, mapas y productos de información
                    categorizados por temas y subtemas.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="text-success mb-0">{{ $totalTemas }}</h3>
                        <small class="text-muted">Temas</small>
                    </div>
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="text-success mb-0">{{ $totalSubtemas }}</h3>
                        <small class="text-muted">Subtemas</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <img src="{{ asset('imagenes/estadgde.png') }}" alt="Estadística" class="img-fluid rounded shadow-sm">
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h4><i class="bi bi-list-task me-2"></i>Explora por tema:</h4>
                <div class="temas-grid-container mt-3">
                    @forelse($temas as $index => $tema)
                        @php
                            $colores = [
                                'background-color: #8FBC8F;',
                                'background-color: #87CEEB;',
                                'background-color: #DDA0DD;',
                                'background-color: #F0E68C;',
                                'background-color: #FFA07A;',
                                'background-color: #98FB98;',
                            ];
                            $color = $colores[$index % count($colores)];
                        @endphp
                        <a href="{{ route('sigem.v2.estadistica.tema', $tema) }}"
                           class="text-decoration-none">
                            <div class="tema-card" style="{{ $color }}">
                                <div class="enlace-completo">
                                    <div class="row-icono">
                                        @switch($tema->orden_indice)
                                            @case(1) <i class="bi bi-globe"></i> @break
                                            @case(2) <i class="bi bi-leaf"></i> @break
                                            @case(3) <i class="bi bi-person-bounding-box"></i> @break
                                            @case(4) <i class="bi bi-archive-fill"></i> @break
                                            @case(5) <i class="bi bi-cash-coin"></i> @break
                                            @case(6) <i class="bi bi-bank2"></i> @break
                                            @default <i class="bi bi-file-earmark-text"></i>
                                        @endswitch
                                    </div>
                                    <div class="row-titulo">
                                        <h5 class="titulo-tema mb-3">
                                            {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="alert alert-warning">No hay temas disponibles.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .header-logos {
        display: flex; width: 100%; min-height: 100px;
        border-bottom: 4px solid #ffd700; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .logo-section {
        flex: 1; display: flex; align-items: center; justify-content: center;
        background-color: white; margin: 10px 5px; border-radius: 8px; padding: 15px;
    }
    .logo-section img { max-width: 100%; max-height: 80px; object-fit: contain; }
    .main-menu {
        background-color: #48887B !important;
        border-bottom: 3px solid #ffd700; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%; margin: 0; padding: 0;
    }
    .main-menu .nav-container {
        display: flex; justify-content: center; align-items: center;
        max-width: 1200px; margin: 0 auto; padding: 0;
    }
    .main-menu a {
        color: white; text-decoration: none; padding: 12px 20px;
        font-weight: bold; font-size: 14px; border-radius: 0;
        transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;
    }
    .main-menu a:hover { background-color: rgba(255,255,255,0.1); color: #ffd700; }
    .main-menu a.active {
        background-color: #0b584fff; color: #ffd700; font-weight: bold;
    }
    .main-menu a.active::after {
        content: ''; position: absolute; bottom: 0; left: 0;
        width: 100%; height: 3px; background-color: #ffd700;
    }
    .temas-grid-container {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; padding: 0.5rem;
    }
    .tema-card {
        border-radius: 16px; min-height: 110px;
        transition: all 0.3s ease; box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden; border: none; cursor: pointer;
    }
    .tema-card:hover {
        transform: translateY(-4px) scale(1.01); filter: brightness(0.9);
        background-color: rgba(0,0,0,0.85) !important;
    }
    .tema-card:hover .enlace-completo { background-color: #15412cff !important; }
    .tema-card:hover .titulo-tema { color: #ffffffff !important; }
    .enlace-completo {
        display: flex; flex-direction: column; justify-content: space-between;
        align-items: center; height: 100%; width: 100%;
        color: #3b3b3bff !important; text-decoration: none !important;
        padding: 1rem 0.75rem !important;
    }
    .row-icono { font-size: 2rem; display: flex; justify-content: center; margin-bottom: 0.3rem; }
    .row-titulo { text-align: center; }
    .titulo-tema { color: #3b3b3bff !important; font-size: 0.95rem; font-weight: 700; margin: 0; }
    @media (max-width: 991px) { .temas-grid-container { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 767px) { .temas-grid-container { grid-template-columns: 1fr; } }
</style>
@endpush
