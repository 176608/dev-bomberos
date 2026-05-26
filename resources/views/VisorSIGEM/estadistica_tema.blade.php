@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'SIGEM v2 — ' . ($tema->tema_titulo ?? 'Estadística'))

@section('visor_content')
<style>
.subtema-nav-item {
    display: block;
    padding: 10px 15px;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}
.subtema-nav-item:hover {
    background-color: #e8f4f8;
    transform: translateX(3px);
}
.subtema-nav-item.active {
    background-color: #d4edda;
    border-left: 4px solid #28a745;
}
.subtema-nav-item .subtema-image {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 8px;
}
.subtema-nav-item .subtema-texto h6 {
    font-size: 0.85rem;
    margin-bottom: 0;
    line-height: 1.2;
}
.sidebar-content {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}
.btn-toggle-sidebar-fixed {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    border-radius: 4px;
    padding: 4px 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
}
.btn-toggle-sidebar-fixed:hover {
    background: rgba(255,255,255,0.3);
}
.transition-width {
    transition: all 0.3s ease;
}
.sidebar-mini {
    width: 60px !important;
    min-width: 60px !important;
    flex: 0 0 60px !important;
}
.sidebar-mini .sidebar-title,
.sidebar-mini .subtema-texto,
.sidebar-mini .subtema-image-container {
    display: none;
}
.sidebar-mini .subtema-nav-item {
    padding: 10px 8px;
    text-align: center;
}
.sidebar-mini .subtema-nav-item .subtema-image {
    width: 35px;
    height: 35px;
}
.content-expanded {
    flex: 0 0 calc(100% - 60px) !important;
    max-width: calc(100% - 60px) !important;
}
.cuadro-item {
    transition: all 0.2s ease;
    cursor: pointer;
}
.cuadro-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #28a745 !important;
    background-color: #f0fff0;
}
.cuadros-lista {
    max-height: 70vh;
    overflow-y: auto;
}
@media (max-width: 768px) {
    .sidebar-mini {
        display: none;
    }
    .content-expanded {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
}
</style>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="row g-0 min-vh-75">
            <div class="col-md-4 bg-light border-end transition-width" id="sidebar-subtemas">
                <div class="d-flex flex-column h-100">
                    <div class="p-3 text-white position-relative" style="background-color: #0b584fff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 sidebar-title">
                                <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $tema->tema_titulo }}
                            </h6>
                        </div>
                        <div class="toggle-button-container">
                            <button class="btn-toggle-sidebar-fixed" id="toggle-sidebar" title="Expandir/Colapsar panel">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex-fill overflow-auto sidebar-content" id="subtemas-navegacion">
                        @if(isset($tema_subtemas) && $tema_subtemas->count() > 0)
                            @foreach($tema_subtemas as $tema_subtema)
                                <a href="javascript:void(0)"
                                   onclick="cargarIndicadores({{ $tema_subtema->subtema_id }}); return false;"
                                   class="subtema-nav-item text-decoration-none text-dark {{ isset($subtema_seleccionado) && $tema_subtema->subtema_id == $subtema_seleccionado->subtema_id ? 'active' : '' }}">
                                    <div class="row g-0 w-100 align-items-center">
                                        <div class="col-3 subtema-image-container">
                                            @if($tema_subtema->imagen)
                                                <img src="{{ asset('imagenes/subtemas_u/'.$tema_subtema->imagen) }}"
                                                     alt="{{ $tema_subtema->subtema_titulo }}"
                                                     class="subtema-image img-fluid">
                                            @else
                                                <div class="no-image-placeholder d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-collection text-success fs-3"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-9 subtema-texto">
                                            <div class="d-flex align-items-center justify-content-between w-100">
                                                <h6 class="mb-1">{{ $tema_subtema->subtema_titulo }}</h6>
                                                <i class="bi bi-chevron-right ms-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="p-3 text-center text-muted">
                                <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay subtemas disponibles</p>
                                <a href="{{ route('sigem.v2.estadistica') }}" class="btn btn-outline-secondary btn-sm mt-3">
                                    <i class="bi bi-arrow-left me-1"></i>Volver a temas estadísticos
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8 transition-width" id="contenido-principal">
                <div class="d-flex flex-column h-100">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-success me-3 d-none" id="show-sidebar" title="Mostrar panel de subtemas">
                                <i class="bi bi-list"></i>
                            </button>

                            <div style="min-width: 250px;">
                                <select class="form-select" id="tema-selector" onchange="cambiarTema(this.value)">
                                    @foreach($temas as $t)
                                        <option value="{{ $t->tema_id }}" {{ $tema->tema_id == $t->tema_id ? 'selected' : '' }}>
                                            {{ $t->orden_indice }}. {{ $t->tema_titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <a href="{{ route('sigem.v2.estadistica') }}" class="btn btn-sm btn-outline-secondary d-none d-md-inline">
                            <i class="bi bi-arrow-left me-1"></i>Volver a temas
                        </a>
                    </div>

                    <div class="p-3 border-bottom" id="subtema-header">
                        @if(isset($subtema_seleccionado))
                            <h5 class="mb-0">{{ $subtema_seleccionado->subtema_titulo }}</h5>
                        @else
                            <h5 class="mb-0">{{ $tema->tema_titulo }}</h5>
                            <p class="text-muted small mb-0">Seleccione un subtema para ver sus indicadores</p>
                        @endif
                    </div>

                    <div class="flex-fill overflow-auto p-3" id="indicadores-container">
                        @php
                            function extraerNumeroIndice($codigo) {
                                if (empty($codigo)) return PHP_FLOAT_MAX;
                                if (preg_match('/\.(\d+(?:\.\d+)*)$/', $codigo, $m)) return floatval($m[1]);
                                if (preg_match('/(\d+(?:\.\d+)*)$/', $codigo, $m)) return floatval($m[1]);
                                return PHP_FLOAT_MAX;
                            }
                            if (isset($indicadores) && $indicadores->count() > 0) {
                                $indicadoresArr = $indicadores->toArray();
                                usort($indicadoresArr, function($a, $b) {
                                    return extraerNumeroIndice($a['codigo_cuadro'] ?? '') <=> extraerNumeroIndice($b['codigo_cuadro'] ?? '');
                                });
                                $indicadores = collect($indicadoresArr);
                            }
                        @endphp

                        @if(isset($indicadores) && $indicadores->count() > 0 && isset($subtema_seleccionado))
                            <div class="indicadores-lista">
                                @foreach($indicadores as $indicador)
                                    <a href="javascript:void(0)"
                                       onclick="alert('ID del Indicador: {{ $indicador['cuadro_estadistico_id'] }}')"
                                       class="cuadro-item p-3 mb-3 border rounded text-decoration-none d-block">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="mb-1 d-block text-dark">
                                                    <span class="fw-bold text-success">
                                                        {{ $indicador['codigo_cuadro'] ?? 'N/A' }}
                                                    </span>
                                                    {{ $indicador['cuadro_estadistico_titulo'] ?? 'Sin título' }}
                                                </span>
                                                @if(!empty($indicador['cuadro_estadistico_subtitulo']))
                                                    <small class="text-muted d-block">{{ $indicador['cuadro_estadistico_subtitulo'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-table" style="font-size: 3rem;"></i>
                                <p class="mt-3">Seleccione un subtema para ver los indicadores disponibles.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('visor_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();

    document.getElementById('toggle-sidebar')?.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var sidebar = document.getElementById('sidebar-subtemas');
        var content = document.getElementById('contenido-principal');
        if (sidebar.classList.contains('sidebar-mini')) {
            sidebar.classList.remove('sidebar-mini');
            content.classList.remove('content-expanded');
            localStorage.setItem('subtemas-sidebar-collapsed', 'false');
            this.setAttribute('title', 'Colapsar panel');
        } else {
            sidebar.classList.add('sidebar-mini');
            content.classList.add('content-expanded');
            localStorage.setItem('subtemas-sidebar-collapsed', 'true');
            this.setAttribute('title', 'Expandir panel');
        }
    });

    var sidebarCollapsed = localStorage.getItem('subtemas-sidebar-collapsed');
    if (sidebarCollapsed === null || sidebarCollapsed === 'true') {
        document.getElementById('sidebar-subtemas')?.classList.add('sidebar-mini');
        document.getElementById('contenido-principal')?.classList.add('content-expanded');
    }
});

function cambiarTema(tema_id) {
    window.location.href = '{{ url("/sigem-v2/estadistica/tema") }}/' + tema_id;
}

function cargarIndicadores(subtema_id) {
    var container = document.getElementById('indicadores-container');
    container.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-success"><span class="visually-hidden">Cargando...</span></div><p class="mt-3">Cargando indicadores...</p></div>';

    document.querySelectorAll('#subtemas-navegacion .subtema-nav-item').forEach(function (item) {
        item.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    var subtemaTitle = event.currentTarget.querySelector('.subtema-texto h6')?.innerText || 'Subtema';
    document.getElementById('subtema-header').innerHTML = '<h5 class="mb-0">' + subtemaTitle + '</h5>';

    fetch('/sigem/obtener-cuadros-estadistica/' + subtema_id)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                actualizarInfoSubtema(subtema_id);
                renderizarIndicadores(data.cuadros);
            } else {
                container.innerHTML = '<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle me-2"></i>' + (data.message || 'Error al cargar indicadores') + '</div>';
            }
        })
        .catch(function () {
            container.innerHTML = '<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle me-2"></i>Error de conexión al cargar indicadores</div>';
        });
}

function actualizarInfoSubtema(subtema_id) {
    fetch('/sigem/obtener-info-subtema/' + subtema_id)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                document.getElementById('subtema-header').innerHTML = '<h5 class="mb-0">' + data.subtema.subtema_titulo + '</h5><p class="text-muted small mb-0">' + (data.subtema.tema ? data.subtema.tema.tema_titulo : '') + '</p>';
            }
        })
        .catch(function () {});
}

function renderizarIndicadores(indicadores) {
    var container = document.getElementById('indicadores-container');
    if (!indicadores || indicadores.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-table" style="font-size: 3rem;"></i><p class="mt-3">No hay indicadores disponibles para este subtema.</p></div>';
        return;
    }

    function extraerNumeroIndice(codigo) {
        if (!codigo) return Number.MAX_VALUE;
        var match = codigo.match(/\.(\d+(?:\.\d+)*)$/);
        if (match) return parseFloat(match[1]);
        var matchEnd = codigo.match(/(\d+(?:\.\d+)*)$/);
        return matchEnd ? parseFloat(matchEnd[1]) : Number.MAX_VALUE;
    }

    var ordenados = indicadores.sort(function (a, b) {
        return extraerNumeroIndice(a.codigo_cuadro || '') - extraerNumeroIndice(b.codigo_cuadro || '');
    });

    var html = '<div class="indicadores-lista">';
    ordenados.forEach(function (ind) {
        html += '<a href="javascript:void(0)" onclick="alert(\'ID del Indicador: ' + ind.cuadro_estadistico_id + '\')" class="cuadro-item p-3 mb-3 border rounded text-decoration-none d-block">';
        html += '<div class="row align-items-center"><div class="col-12">';
        html += '<span class="mb-1 d-block text-dark"><span class="fw-bold text-success">' + (ind.codigo_cuadro || 'N/A') + '</span> ' + (ind.cuadro_estadistico_titulo || 'Sin título') + '</span>';
        if (ind.cuadro_estadistico_subtitulo) {
            html += '<small class="text-muted d-block">' + ind.cuadro_estadistico_subtitulo + '</small>';
        }
        html += '</div></div></a>';
    });
    html += '</div>';
    container.innerHTML = html;
}
</script>
@endpush