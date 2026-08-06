@extends('layouts.app')

@section('title', 'Gestor de Dictámenes - IMIP Ciudad Juárez')

@push('styles')
<style>
:root {
    --imip-blue: #2f7064;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.chart-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.chart-container-sm {
    width: 100%;
    max-width: 1000px;
    height: 300px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.chart-container-sm canvas {
    width: 100% !important;
    height: 100% !important;
}

.docx-preview {
    max-height: 70vh;
    overflow-y: auto;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 20px;
}

.docx-preview h1, .docx-preview h2, .docx-preview h3 {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.docx-preview img {
    max-width: 100%;
    height: auto;
}

.estado-badge {
    border: 0;
    cursor: pointer;
    font-weight: 500;
    padding: 4px 8px;
    font-size: 0.75rem;
    border-radius: 4px;
    display: inline-block;
    text-align: left;
}

#dictamenes-table tbody tr {
    user-select: none;
}

table {
    font-size: 0.85rem;
}

th {
    font-weight: 600;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

tr:hover td {
    background-color: #f8fafd;
}

/* Overlay de carga global */
.ui-overlay {
    position: fixed;
    inset: 0;
    z-index: 2000;
    background: rgba(15, 15, 20, 0.55);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.ui-overlay.visible {
    opacity: 1;
    pointer-events: all;
}

/* Placeholder mientras se dibuja la gráfica */
.chart-loading {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.65);
    transition: opacity 0.25s ease;
}

.chart-loading.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Transición suave al reemplazar stats/tabla tras un filtro */
.fade-swap {
    opacity: 0.45;
    transition: opacity 0.15s ease;
}
</style>
@endpush

@section('content')

@php
    $badgeColores = [
        'ENVIADO' => '#28a745',
        'BORRADOR PARA FIRMA' => '#ffc107',
        'EN REVISION' => '#007bff',
        'INFORMATIVO' => '#8a2be2',
        'S/D' => '#6c757d',
        'DESHABILITADO' => '#dc3545',
    ];
@endphp

<div class="container-fluid pt-4 bg-fonde" style="min-height: 100vh;">
  <div class="container pb-5">

    <!-- Estadísticas -->
    <div class="row mb-4" id="statsCards">
        <div class="col-md-6">
            <div class="row h-100 g-3">
                <div class="col-6 h-100">
                    <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; padding: 20px 10px; height: 100%;">
                        <div style="font-size: 2.8rem; font-weight: 700; color: #2f7064; margin: 10px 0;">{{ $total }}</div>
                        <div style="font-size: 0.9rem; color: #666;">Total de dictámenes</div>
                    </div>
                </div>
                <div class="col-6 h-100">
                    <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; padding: 20px 10px; height: 100%;">
                        <div style="font-size: 2.8rem; font-weight: 700; color: #28a745; margin: 10px 0;">{{ $enviados }}</div>
                        <div style="font-size: 0.9rem; color: #666;">Dictámenes Enviados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row g-3">
                @foreach($conteoEstatus as $estatus => $count)
                @if($estatus !== 'ENVIADO')
                <div class="col-6">
                    <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; padding: 18px 10px; height: 100%;">
                        <div style="font-size: 1.6rem; font-weight: 700; color: {{ $badgeColores[$estatus] ?? '#6c757d' }}; margin: 6px 0;">{{ $count }}</div>
                        <div style="font-size: 0.7rem; color: #666; text-transform: uppercase; letter-spacing: 0.3px;">{{ $estatus }}</div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>


    <!-- Gráfica -->
    <div class="card mb-3" id="graficaCard" style="border-left: 4px solid #2f7064;">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Dictámenes por mes y estatus</h5>
                <span class="text-muted small">Toca un estatus para mostrarlo/ocultarlo (todos activados)</span>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3" id="chartToggles"></div>
            <div style="height: 280px; position: relative;">
                <canvas id="chartMeses"></canvas>
                <div class="chart-loading" id="chartLoading">
                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                    <span class="ms-2 text-muted">Generando gráfica…</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros (GET, se guardan en la URL; se aplican al seleccionar) -->
    <div class="card mb-3" id="filtrosCard" style="border-left: 4px solid #2f7064;">
        <div class="card-header py-2 position-relative">
            <h6 class="text-center mb-0">Filtros para dictámenes</h6>
            @if(request()->hasAny(['estatus', 'anio', 'mes', 'revisado_por', 'dependencia', 'tipo_dictamen']))
                <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-sm btn-primary position-absolute top-50 end-0 translate-middle-y me-2" data-limpiar title="Limpiar los filtros activos">
                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar selección
                </a>
            @endif
        </div>
        <div class="card-body py-2">
            <form id="filtrosForm" method="GET" action="{{ route('gestor-dictamenes.index') }}" class="row g-2">
                <!-- Sección 1: Año arriba + Mes abajo -->
                <div class="col-2 d-flex flex-column">
                    <label class="form-label mb-1" for="anioFilter"><strong>Año:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="anioFilter" name="anio">
                        <option value="">Todos</option>
                        @foreach($anios as $a)
                            <option value="{{ $a }}" @selected(request('anio') === (string) $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                    <label class="form-label mb-1 mt-2" for="mesFilter"><strong>Mes:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="mesFilter" name="mes">
                        <option value="">Todos</option>
                        @foreach(\App\Models\GestorDictamenes\Dictamen::MESES as $mesNum => $mesNombre)
                            <option value="{{ $mesNum }}" @selected(request('mes') === (string) $mesNum)>{{ $mesNombre }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Sección 2: Estatus + Revisado por (una fila cada uno) -->
                <div class="col-5">
                    <label class="form-label mb-1" for="estatusFilter"><strong>Estatus:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="estatusFilter" name="estatus">
                        <option value="">Todos</option>
                        @foreach(\App\Models\GestorDictamenes\Dictamen::FILTERABLE_STATUSES as $estatus)
                            <option value="{{ $estatus }}" @selected(request('estatus') === $estatus)>{{ $estatus }}</option>
                        @endforeach
                    </select>
                    <label class="form-label mb-1 mt-2" for="revisadoFilter"><strong>Revisado por:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="revisadoFilter" name="revisado_por">
                        <option value="">Todos</option>
                        @foreach($revisadosPor as $r)
                            <option value="{{ $r }}" @selected(request('revisado_por') === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Sección 3: Tipo de Dictamen + Dependencia (una fila cada uno) -->
                <div class="col-5">
                    <label class="form-label mb-1" for="tipoDictamenFilter"><strong>Tipo de Dictamen:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="tipoDictamenFilter" name="tipo_dictamen">
                        <option value="">Todos</option>
                        @foreach($tiposDictamen as $td)
                            <option value="{{ $td }}" @selected(request('tipo_dictamen') === $td)>{{ $td }}</option>
                        @endforeach
                    </select>
                    <label class="form-label mb-1 mt-2" for="dependenciaFilter"><strong>Dependencia:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="dependenciaFilter" name="dependencia">
                        <option value="">Todas</option>
                        @foreach($dependencias as $dep)
                            <option value="{{ $dep }}" @selected(request('dependencia') === $dep)>{{ $dep }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Botones superiores -->
    <div class="mb-3">
        @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" title="Crear un nuevo dictamen">
                <i class="bi bi-plus-circle"></i> Agregar nuevo dictamen
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#archivosModal" title="Gestionar los archivos subidos al servidor (subir, reemplazar, eliminar y descargar)">
                <i class="bi bi-folder2-open"></i> Gestionar Archivos de Dictámenes
            </button>
        @endif
        @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']))
            <button class="btn btn-outline-secondary" disabled title="Historial de cambios (próximamente)">
                <i class="bi bi-clock-history"></i> Ver últimos cambios
            </button>
            <a href="{{ route('visor-dictamenes.public') }}" target="_blank" class="btn btn-outline-success" title="Abrir el visor público de dictámenes">
                <i class="bi bi-eye"></i> Ver Visor de Dictámenes
            </a>
        @endif
    </div>

    <!-- Tabla -->
    <div id="tablaCard">
        <table id="dictamenes-table" class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Oficio Recibido</th>
                    <th>Asunto</th>
                    <th>Estatus</th>
                    <th>Número Oficio</th>
                    <th class="d-none">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $d)
                <tr
                    data-fecha="{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : '' }}"
                    data-oficio-recibido="{{ $d->oficio_recibido ?? '' }}"
                    data-tipo-dictamen="{{ $d->tipo_dictamen ?? '' }}"
                    data-nombre-puesto="{{ $d->nombre_puesto ?? '' }}"
                    data-dependencia="{{ $d->dependencia_empres ?? '' }}"
                    data-asunto="{{ $d->asunto ?? '' }}"
                    data-numero-oficio="{{ $d->numero_oficio ?? '' }}"
                    data-revisado-por="{{ $d->revisado_por ?? '' }}"
                    data-estatus="{{ $d->estatus ?? '' }}"
                    data-observaciones="{{ $d->observaciones ?? '' }}"
                >
                    <td data-order="{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : '0000-00-00' }}">{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $d->oficio_recibido ?? '—' }}</td>
                    <td data-search="{{ trim(($d->asunto ?? '') . ' ' . ($d->observaciones ?? '') . ' ' . ($d->tipo_dictamen ?? '') . ' ' . ($d->dependencia_empres ?? '') . ' ' . ($d->nombre_puesto ?? '') . ' ' . ($d->revisado_por ?? '') . ' ' . ($d->numero_oficio ?? '') . ' ' . ($d->oficio_recibido ?? '')) }}" title="{{ $d->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($d->asunto ?? '', 60) }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $badgeColores[$d->estatus ?? ''] ?? '#6c757d' }}; color: {{ in_array($d->estatus ?? '', ['BORRADOR PARA FIRMA']) ? '#212529' : 'white' }}; font-weight: 500; padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; display: inline-block;">
                            {{ $d->estatus ?? 'S/D' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $estadoA = $d->estado_archivo ?? 'sin_clave';
                            $encontradosA = $d->archivos_encontrados ?? [];
                            $ligadosA = $d->archivosLigados ?? collect();
                            $numFolio = trim((string) ($d->numero_oficio ?? ''));
                            $numDefinido = $numFolio !== '' && !in_array(strtoupper($numFolio), ['S/N', 'S/D'], true);
                            $nCoinc = count($encontradosA);
                            $esAdmin = auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']);
                            $esEditor = auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']);
                        @endphp
                        @if(!$numDefinido)
                            {{-- Caso D: sin definir --}}
                            @if($esEditor)
                                <button type="button" class="badge bg-secondary estado-badge badge-sin-definir" data-id="{{ $d->id }}"
                                        title="Sin definir. Clic para editar el dictamen.">Sin definir</button>
                            @else
                                <span class="badge bg-secondary" title="Sin definir">Sin definir</span>
                            @endif
                        @elseif($nCoinc > 1)
                            {{-- Caso A: más de una coincidencia --}}
                            <button type="button" class="badge bg-warning text-dark estado-badge link-btn"
                                    data-id="{{ $d->id }}" data-clave="{{ $d->numero_oficio }}"
                                    data-bs-toggle="modal" data-bs-target="#linkModal"
                                    title="{{ $numFolio }}: {{ $nCoinc }} coincidencias en el servidor. Clic para visualizar, descargar o ligar archivos.">
                                {{ $numFolio }} · {{ $nCoinc }} coincidencias
                            </button>
                        @elseif($nCoinc === 1)
                            {{-- Caso B: una coincidencia --}}
                            <button type="button" class="badge bg-success estado-badge link-btn"
                                    data-id="{{ $d->id }}" data-clave="{{ $d->numero_oficio }}"
                                    data-bs-toggle="modal" data-bs-target="#linkModal"
                                    title="{{ $numFolio }}: 1 coincidencia en el servidor. Clic para visualizar, descargar o ligar archivos.">
                                {{ $numFolio }} · coincidencia
                            </button>
                        @else
                            {{-- Caso C: definido pero no encontrado --}}
                            @if($esAdmin)
                                <button type="button" class="badge bg-danger estado-badge subir-btn"
                                        data-id="{{ $d->id }}" data-clave="{{ $d->numero_oficio }}" data-anio="{{ $d->anio }}"
                                        title="{{ $numFolio }}: no se encontró en el servidor. Clic para subir el archivo.">
                                    {{ $numFolio }} · No encontrado
                                </button>
                            @else
                                <span class="badge bg-danger" title="{{ $numFolio }}: no se encontró en el servidor.">
                                    {{ $numFolio }} · No encontrado
                                </span>
                            @endif
                        @endif
                        @if($ligadosA->count() > 0 && $numDefinido && $estadoA !== 'encontrado' && $estadoA !== 'multiples')
                            <span class="badge bg-info text-dark mt-1" title="Tiene {{ $ligadosA->count() }} archivo(s) ligado(s)">
                                <i class="bi bi-link-45deg"></i> {{ $ligadosA->count() }} ligado(s)
                            </span>
                        @endif
                    </td>
                    <td class="d-none">
                        @if(($d->estatus ?? '') === \App\Models\GestorDictamenes\Dictamen::DESHABILITADO)
                        @else
                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']))
                                <button class="btn btn-sm btn-primary edit-btn d-none" title="Editar este dictamen" data-id="{{ $d->id }}" data-route="{{ route('gestor-dictamenes.update', $d->id) }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endif

                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                                <form id="delete-form-{{ $d->id }}" action="{{ route('gestor-dictamenes.destroy', $d->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <form method="POST" action="{{ route('gestor-dictamenes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label>Recibido en fecha <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                        <div class="col-md-5">
                            <label>Tipo de dictamen</label>
                            <select class="form-select select2-tags" id="tipo_dictamen_create" name="tipo_dictamen" title="Seleccione un tipo existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($tiposDictamen as $td)
                                    <option value="{{ $td }}">{{ $td }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Oficio Recibido <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <input type="text" class="form-control" name="oficio_recibido" required placeholder="Ej. DGDU/DCP/APDU/2515/2024 EXP. 50.24">
                        </div>
                        <div class="col-md-6">
                            <label>Dependencia</label>
                            <select class="form-select select2-tags" name="dependencia_empres" title="Seleccione una dependencia existente o escriba una nueva">
                                <option value=""></option>
                                @foreach($dependencias as $dep)
                                    <option value="{{ $dep }}">{{ $dep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Nombre / Puesto</label>
                            <select class="form-select select2-tags" name="nombre_puesto" title="Seleccione un nombre/puesto existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($nombresPuestos as $np)
                                    <option value="{{ $np }}">{{ $np }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Estatus <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <select class="form-select" name="estatus" required>
                                <option value="">Seleccione un estatus...</option>
                                @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                    <option value="{{ $estatus }}">{{ $estatus }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Número de oficio</label>
                            <input type="text" class="form-control" name="numero_oficio" placeholder="Ej. PYP024, DIR143">
                        </div>
                        <div class="col-md-4">
                            <label>Revisado por</label>
                            <select class="form-select select2-tags" name="revisado_por" title="Seleccione un nombre existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($revisadosPor as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>Asunto <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <textarea class="form-control" name="asunto" required rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cerrar sin guardar">Cancelar</button>
                    <button type="submit" class="btn btn-primary" title="Guardar el nuevo dictamen">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar (extendido horizontalmente) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <form id="editForm" method="POST" onsubmit="return confirm('¿Seguro que deseas editar este dictamen?');">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label>Recibido en fecha <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <input type="date" class="form-control" id="fecha_edit" name="fecha" required>
                        </div>
                        <div class="col-md-5">
                            <label>Tipo de dictamen</label>
                            <select class="form-select select2-tags" id="tipo_dictamen_edit" name="tipo_dictamen" title="Seleccione un tipo existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($tiposDictamen as $td)
                                    <option value="{{ $td }}">{{ $td }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Oficio Recibido <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <input type="text" class="form-control" id="oficio_recibido_edit" name="oficio_recibido" required>
                        </div>
                        <div class="col-md-6">
                            <label>Dependencia</label>
                            <select class="form-select select2-tags" id="dependencia_empres_edit" name="dependencia_empres" title="Seleccione una dependencia existente o escriba una nueva">
                                <option value=""></option>
                                @foreach($dependencias as $dep)
                                    <option value="{{ $dep }}">{{ $dep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Nombre / Puesto</label>
                            <select class="form-select select2-tags" id="nombre_puesto_edit" name="nombre_puesto" title="Seleccione un nombre/puesto existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($nombresPuestos as $np)
                                    <option value="{{ $np }}">{{ $np }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Estatus <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <select class="form-select" id="estatus_edit" name="estatus" required>
                                <option value="">Seleccione un estatus...</option>
                                @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                    <option value="{{ $estatus }}">{{ $estatus }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Número de oficio</label>
                            <input type="text" class="form-control" id="numero_oficio_edit" name="numero_oficio" placeholder="Ej. PYP024, DIR143">
                        </div>
                        <div class="col-md-4">
                            <label>Revisado por</label>
                            <select class="form-select select2-tags" id="revisado_por_edit" name="revisado_por" title="Seleccione un nombre existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($revisadosPor as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>Asunto <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <textarea class="form-control" id="asunto_edit" name="asunto" required rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="observaciones_edit" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                        <button type="button" class="btn btn-danger" id="btnDeshabilitarModal" title="Deshabilitar este dictamen">
                            <i class="bi bi-trash"></i> Deshabilitar
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cerrar sin guardar">Cancelar</button>
                    <button type="submit" class="btn btn-primary" title="Guardar los cambios del dictamen">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Gestionar Archivos de Dictámenes -->
<div class="modal fade" id="archivosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-folder2-open"></i> Gestionar Archivos de Dictámenes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="archivosSearch" class="form-control" placeholder="Buscar por nombre...">
                    <select id="archivosFiltroAnio" class="form-select form-select-sm text-nowrap" style="width: 110px;">
                        <option value="">Todos</option>
                        @foreach($aniosDisponibles as $a)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endforeach
                    </select>
                    <input type="file" id="archivoInput" accept=".doc,.docx" hidden>
                    <button class="btn btn-success text-nowrap" id="btnSubirArchivo" title="Subir un archivo al servidor (se ubicará en la carpeta del año seleccionado)">
                        <i class="bi bi-upload"></i> Subir nuevo archivo
                    </button>
                </div>

                <div id="archivosMsg"></div>

                <div id="archivoConflicto" class="alert alert-warning d-none">
                    <strong><i class="bi bi-exclamation-triangle"></i> Ya existe un archivo con ese nombre:</strong>
                    <span id="conflictoNombre" class="fw-bold"></span>
                    <p class="mb-0 mt-1">¿Deseas revisar el archivo con el mismo nombre (descargarlo) o reemplazar el archivo existente?</p>
                    <div class="mt-2 d-flex gap-2 flex-wrap">
                        <button type="button" id="btnDescargarExistente" class="btn btn-sm btn-outline-secondary" title="Visualizar el archivo que ya existe con ese nombre">
                            <i class="bi bi-eye"></i> Revisar existente
                        </button>
                        <button id="btnReemplazarArchivo" class="btn btn-sm btn-warning" title="Sobrescribir el archivo existente con el nuevo">
                            <i class="bi bi-arrow-repeat"></i> Reemplazar archivo
                        </button>
                        <button id="btnCancelarConflicto" class="btn btn-sm btn-link" title="Cancelar la subida y dejar el archivo existente">Dejar como está</button>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Carpeta</th>
                                <th>Archivo</th>
                                <th class="text-center" style="width: 120px;">Ligado a</th>
                                <th class="text-center" style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="archivosLista">
                            <tr><td colspan="4" class="text-center text-muted">Cargando archivos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir archivo faltante (badge "No encontrado") -->
<div class="modal fade" id="subirArchivoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Subir archivo del dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    Clave: <strong id="subirClave"></strong><br>
                    Se guardará en la carpeta: <strong id="subirCarpeta"></strong>
                </p>
                <div id="subirMsg"></div>
                <input type="file" id="subirArchivoInput" accept=".doc,.docx" hidden>
                <button class="btn btn-success w-100" id="btnElegirSubir" title="Elegir el archivo .doc/.docx a subir">
                    <i class="bi bi-upload"></i> Seleccionar archivo (.doc / .docx)
                </button>
                <div id="subirConflicto" class="alert alert-warning d-none mt-3">
                    <strong><i class="bi bi-exclamation-triangle"></i> Ya existe un archivo con ese nombre:</strong>
                    <span id="subirConflictoNombre" class="fw-bold"></span>
                    <p class="mb-0 mt-1">¿Revisar (descargar) el existente o reemplazarlo?</p>
                    <div class="mt-2 d-flex gap-2 flex-wrap">
                        <button type="button" id="btnSubirDescargarExistente" class="btn btn-sm btn-outline-secondary" title="Visualizar el archivo que ya existe con ese nombre">
                            <i class="bi bi-eye"></i> Revisar existente
                        </button>
                        <button id="btnSubirReemplazar" class="btn btn-sm btn-warning" title="Sobrescribir el archivo existente con el nuevo">
                            <i class="bi bi-arrow-repeat"></i> Reemplazar archivo
                        </button>
                        <button id="btnSubirCancelarConflicto" class="btn btn-sm btn-link" title="Cancelar la subida y dejar el archivo existente">Dejar como está</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ligar Archivos a un Dictamen -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-link-45deg"></i> Ligar archivos
                    <span id="linkClave" class="badge bg-primary ms-1"></span>
                    <span id="linkAnio" class="badge bg-secondary ms-1"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="linkMsg"></div>

                <h6 class="text-muted">Coincidencias detectadas en el servidor (por clave):</h6>
                <div id="linkCoincidencias" class="mb-3"></div>

                <h6 class="text-muted">Archivos ligados a este dictamen:</h6>
                <div id="linkLigados" class="mb-3"></div>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- Modal Visualizar Archivo (DOCX/DOC) - pantalla completa -->
<div class="modal fade" id="verArchivoModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-text"></i> Visualizar archivo
                    <span id="verArchivoNombre" class="badge bg-secondary ms-1"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="verArchivoLoading" class="text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted">Convirtiendo documento…</p>
                </div>
                <div id="verArchivoContenido" class="docx-preview"></div>
                <div id="verArchivoError" class="d-none text-center py-4"></div>
            </div>
            <div class="modal-footer">
                <a id="verArchivoDescargar" class="btn btn-outline-primary" href="#" title="Descargar este archivo">
                    <i class="bi bi-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cerrar">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.8.0/mammoth.browser.min.js"></script>

<script>
$(document).ready(function() {
    let filtrando = false;

    // Limpieza defensiva del overlay al cargar la página y al restaurarla desde la
    // caché del navegador (bfcache), donde .ready no se vuelve a ejecutar
    $('#uiOverlay').removeClass('visible');
    $(window).on('pageshow', function (e) {
        if (e.persisted) $('#uiOverlay').removeClass('visible');
    });

    // Limpieza única de estado DataTables viejo (estructura de columnas cambió: columna Núm. Oficio unificada, sin scroll lateral, 50 por página)
    if (!sessionStorage.getItem('dictamenes_state_v12')) {
        Object.keys(localStorage).forEach(function(k) {
            if (k.indexOf('DataTables_dictamenes-table') === 0) {
                localStorage.removeItem(k);
            }
        });
        sessionStorage.setItem('dictamenes_state_v12', '1');
    }

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#dictamenes-table')) {
            $('#dictamenes-table').DataTable().destroy();
        }
        $('#dictamenes-table').DataTable({
            "paging": true,
            "lengthMenu": [
                [10, 25, 50, 100, 150, -1],
                ['10', '25', '50', '100', '150', 'Todos los registros']
            ],
            "pageLength": 50,
            "searching": true,
            "info": true,
            "ordering": true,
            "order": [],
            "stateSave": true,
            "stateDuration": 60 * 60 * 24 * 30,
            "language": {
                "search": "Buscar:",
                "paginate": { "previous": "‹", "next": "›" },
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros (_MAX_ en total)",
                "emptyTable": "No hay dictámenes",
                "zeroRecords": "No se encontró nada"
            }
        });
        $('#dictamenes-table_length').addClass('mb-3');
    }

    // Petición parcial: reemplaza stats + tabla y recalcula la gráfica, sin recargar la página
    function aplicarFiltros(url, metodo, body) {
        if (filtrando) return;
        filtrando = true;
        $('#uiOverlay').addClass('visible');
        $('#statsCards, #tablaCard').addClass('fade-swap');

        const opts = {
            method: metodo || 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        };
        if (body) {
            opts.body = body;
        }

        // Cierra el overlay y restaura el estado. Se llama tanto en éxito como en error;
        // no se usa Promise.finally para no dejar el spinner pegado en navegadores que no lo soportan.
        function ocultarOverlay() {
            filtrando = false;
            $('#uiOverlay').removeClass('visible');
            $('#statsCards, #tablaCard').removeClass('fade-swap');
        }

        fetch(url, opts)
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.text();
            })
            .then(function (html) {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                $('#statsCards').html(doc.getElementById('statsCards').innerHTML);
                $('#tablaCard').html(doc.getElementById('tablaCard').innerHTML);
                $('#filtrosCard').html(doc.getElementById('filtrosCard').innerHTML);

                $('.filter-select').select2({
                    width: '100%',
                    dropdownAutoWidth: false,
                    theme: 'bootstrap-5'
                });

                initDataTable();
                if (window.aplicarGrafica) {
                    window.aplicarGrafica();
                }

                if (history && metodo !== 'POST') {
                    history.pushState(null, '', url);
                }
                ocultarOverlay();
            })
            .catch(function () {
                ocultarOverlay();
                window.location.href = url;
            });
    }

    // Filtros: se aplican al cambiar la selección (GET con query string en la URL)
    $(document).on('change', '.filter-select', function () {
        if (!$(this).closest('#filtrosForm').length) return;
        const form = document.getElementById('filtrosForm');
        aplicarFiltros(form.action + '?' + $(form).serialize(), 'GET');
    });

    // Limpiar filtros sin recarga
    $(document).on('click', '#filtrosCard a[data-limpiar]', function (e) {
        e.preventDefault();
        const form = document.getElementById('filtrosForm');
        form.reset();
        $('.filter-select').val('').trigger('change.select2');
        aplicarFiltros(form.action, 'GET');
    });

    // Recarga la tabla manteniendo los filtros/búsqueda actuales del formulario de filtros
    function recargarTablaConFiltros() {
        const form = document.getElementById('filtrosForm');
        if (form) {
            aplicarFiltros(form.action + '?' + $(form).serialize(), 'GET');
        } else {
            window.location.reload();
        }
    }

    initDataTable();

    $('.filter-select').select2({
        width: '100%',
        dropdownAutoWidth: false,
        theme: 'bootstrap-5'
    });

    // Select2 de los modales (crear/editar): se puede elegir un valor existente o escribir uno nuevo (tags).
    // Se inicializan con dropdownParent apuntando al modal y se re-inicializan al ABRIR el modal:
    // al cargar la página el modal está display:none y select2 no calcula bien anchos ni posiciona el dropdown.
    function initSelect2Tags($scope) {
        $scope.find('.select2-tags').each(function () {
            const $sel = $(this);
            if ($sel.data('select2')) {
                $sel.select2('destroy');
            }
            $sel.select2({
                tags: true,
                width: '100%',
                dropdownAutoWidth: false,
                placeholder: 'Seleccione o escriba...',
                allowClear: true,
                theme: 'bootstrap-5',
                dropdownParent: $sel.closest('.modal')
            });
        });
    }

    initSelect2Tags($('body'));

    // Autorelleno de "Oficio Recibido" según el tipo de dictamen (mapa definido en el modelo)
    const AUTOFILL_OFICIO_RECIBIDO = @json(\App\Models\GestorDictamenes\Dictamen::AUTOFILL_OFICIO_RECIBIDO);

    function aplicarAutofillOficio($tipoSelect, $input) {
        const tipo = String($tipoSelect.val() || '').toUpperCase().trim();
        const prefijo = AUTOFILL_OFICIO_RECIBIDO[tipo];
        if (!prefijo) return;
        const actual = String($input.val() || '');
        const conPrefijoAnterior = Object.values(AUTOFILL_OFICIO_RECIBIDO).some(function (p) {
            return actual.startsWith(p);
        });
        if (!actual.trim() || conPrefijoAnterior) {
            $input.val(prefijo);
        }
    }

    $(document).on('change', '#tipo_dictamen_create, #tipo_dictamen_edit', function () {
        const $form = $(this).closest('form');
        aplicarAutofillOficio($(this), $form.find('input[name="oficio_recibido"]'));
    });

    // Reset + re-inicialización de select2 al abrir el modal de creación
    $('#createModal').on('shown.bs.modal', function () {
        initSelect2Tags($(this));
        this.querySelector('form').reset();
        $(this).find('.select2-tags').val('').trigger('change');
    });

    // Re-inicialización de select2 al abrir el modal de edición (los valores ya cargados en el select se conservan)
    $('#editModal').on('shown.bs.modal', function () {
        initSelect2Tags($(this));
    });

    // EDITAR - Cargar datos desde atributos de la fila (SIN AJAX)
    $(document).on('click', '.edit-btn', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');
        const route = $(this).data('route');

        if (!id) {
            alert('Error: No se encontró el ID del dictamen');
            return;
        }

        $('#fecha_edit').val($row.data('fecha') || '');
        $('#oficio_recibido_edit').val($row.data('oficio-recibido') || '');
        $('#tipo_dictamen_edit').val($row.data('tipo-dictamen') || '').trigger('change');
        $('#nombre_puesto_edit').val($row.data('nombre-puesto') || '').trigger('change');
        $('#dependencia_empres_edit').val($row.data('dependencia') || '').trigger('change');
        $('#asunto_edit').val($row.data('asunto') || '');
        $('#numero_oficio_edit').val($row.data('numero-oficio') || '');
        $('#revisado_por_edit').val($row.data('revisado-por') || '').trigger('change');
        $('#estatus_edit').val($row.data('estatus') || '');
        $('#observaciones_edit').val($row.data('observaciones') || '');

        $('#editForm').attr('action', route);
        editDictamenId = id;
        $('#editModal').modal('show');
    });

    // Doble click en una fila: abre el modal de edición (sin interferir con botones/enlaces de la fila)
    $(document).on('dblclick', '#dictamenes-table tbody tr', function(e) {
        if ($(e.target).closest('button, a, input, select, textarea').length) return;
        const $editBtn = $(this).find('.edit-btn');
        if ($editBtn.length) {
            $editBtn.trigger('click');
        }
    });

    // Deshabilitar desde el modal de edición
    $(document).on('click', '#btnDeshabilitarModal', function() {
        if (!editDictamenId) return;
        if (confirm('¿Estás seguro que deseas deshabilitar este dictamen?')) {
            const $form = $('#delete-form-' + editDictamenId);
            if ($form.length) {
                $form.submit();
            }
        }
    });

    // Gráfica de Chart.js (client-side, reactiva a los filtros select; todos los dictámenes)
    const DATOS_GRAFICA = @json($datosGrafica ?? []);
    const ORDEN_ESTATUS = ['ENVIADO', 'BORRADOR PARA FIRMA', 'EN REVISION', 'INFORMATIVO', 'S/D', 'DESHABILITADO'];
    const COLORES_ESTATUS = {
        'ENVIADO': '#28a745',
        'BORRADOR PARA FIRMA': '#ffc107',
        'EN REVISION': '#007bff',
        'INFORMATIVO': '#8a2be2',
        'S/D': '#6c757d',
        'DESHABILITADO': '#dc3545'
    };
    const MESES_ESP = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    const ctx = document.getElementById('chartMeses');
    if (ctx) {
        const chart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: ORDEN_ESTATUS.map(function (s) {
                    return {
                        label: s,
                        data: [],
                        backgroundColor: COLORES_ESTATUS[s] + 'cc',
                        borderColor: COLORES_ESTATUS[s],
                        borderWidth: 1,
                        stack: 'estatus'
                    };
                })
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            footer: function (items) {
                                let total = 0;
                                items.forEach(function (i) { total += i.parsed.y || 0; });
                                return 'Total: ' + total;
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Toggles de estatus (todos activados por defecto)
        const togglesContainer = document.getElementById('chartToggles');
        ORDEN_ESTATUS.forEach(function (s, i) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm chart-toggle-btn';
            btn.title = 'Mostrar / ocultar la serie ' + s;
            btn.style.backgroundColor = COLORES_ESTATUS[s] + 'cc';
            btn.style.borderColor = COLORES_ESTATUS[s];
            btn.style.color = '#fff';
            btn.dataset.serie = i;
            btn.textContent = s;
            btn.addEventListener('click', function () {
                const meta = chart.getDatasetMeta(i);
                meta.hidden = !meta.hidden;
                btn.style.opacity = meta.hidden ? '0.35' : '1';
                chart.update();
            });
            togglesContainer.appendChild(btn);
        });

        // Recalcula la gráfica según los valores actuales de los filtros select
        function aplicarGrafica() {
            const flt = {
                anio: ($('#anioFilter').val() || ''),
                estatus: ($('#estatusFilter').val() || ''),
                revisado: ($('#revisadoFilter').val() || ''),
                dependencia: ($('#dependenciaFilter').val() || ''),
                nombrePuesto: ($('#nombrePuestoFilter').val() || '')
            };

            const datos = DATOS_GRAFICA.filter(function (d) {
                return d.f && (!flt.anio || d.f.substring(0, 4) === flt.anio) &&
                    (!flt.estatus || d.e === flt.estatus) &&
                    (!flt.revisado || d.r === flt.revisado) &&
                    (!flt.dependencia || d.d === flt.dependencia) &&
                    (!flt.nombrePuesto || d.n === flt.nombrePuesto);
            });

            const buckets = {};
            const llaves = [];
            datos.forEach(function (d) {
                const k = d.f.substring(0, 7);
                if (!(k in buckets)) {
                    buckets[k] = {};
                    llaves.push(k);
                }
                buckets[k][d.e] = (buckets[k][d.e] || 0) + 1;
            });
            llaves.sort();

            chart.data.labels = llaves.map(function (k) {
                const partes = k.split('-');
                return MESES_ESP[parseInt(partes[1], 10) - 1] + ' ' + partes[0];
            });
            ORDEN_ESTATUS.forEach(function (s, i) {
                chart.data.datasets[i].data = llaves.map(function (k) {
                    return buckets[k][s] || 0;
                });
            });
            chart.update();
            ocultarCargaGrafica();
        }

        aplicarGrafica();
        window.aplicarGrafica = aplicarGrafica;
    } else {
        ocultarCargaGrafica();
    }

    function ocultarCargaGrafica() {
        const loadingEl = document.getElementById('chartLoading');
        if (loadingEl) {
            loadingEl.style.display = 'none';
            loadingEl.classList.add('d-none');
        }
    }

    // ============ Gestor de Archivos ============
    let editDictamenId = null;
    const URL_ARCHIVOS = '{{ route('gestor-dictamenes.archivos') }}';
    const URL_SUBIR = '{{ route('gestor-dictamenes.archivo-subir') }}';
    const URL_DESCARGAR = '{{ route('gestor-dictamenes.archivo-descargar') }}';
    const URL_VINCULAR = '{{ route('gestor-dictamenes.archivo-vincular') }}';
    const URL_DESVINCULAR = '{{ route('gestor-dictamenes.archivo-desvincular') }}';
    const URL_ELIMINAR = '{{ route('gestor-dictamenes.archivo-eliminar') }}';
    const PUEDE_BORRAR = {{ auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']) ? 'true' : 'false' }};

    let archivoPendiente = null;

    function urlDescarga(ruta) {
        return URL_DESCARGAR + '?archivo=' + encodeURIComponent(ruta);
    }

    // ============ Visualizar archivo (DOCX/DOC) en modal ============
    function verArchivo(ruta) {
        const $nombre = $('#verArchivoNombre');
        const $loading = $('#verArchivoLoading');
        const $contenido = $('#verArchivoContenido');
        const $error = $('#verArchivoError');
        const $descargar = $('#verArchivoDescargar');

        $nombre.text(ruta || '');
        $descargar.attr('href', urlDescarga(ruta || ''));
        $contenido.empty();
        $error.addClass('d-none');
        $loading.removeClass('d-none');
        $('#verArchivoModal').modal('show');

        if (!ruta) {
            mostrarVerError('Ruta de archivo inválida.');
            return;
        }

        const ext = (ruta.split('.').pop() || '').toLowerCase();

        if (ext !== 'docx' && ext !== 'doc') {
            mostrarVerError('Este tipo de archivo no se puede previsualizar. Usa el botón Descargar.');
            return;
        }

        fetch(urlDescarga(ruta))
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.arrayBuffer();
            })
            .then(function (buffer) {
                if (ext === 'docx' && typeof mammoth !== 'undefined') {
                    return mammoth.convertToHtml({ arrayBuffer: buffer });
                }
                throw new Error('DOC_LEGACY');
            })
            .then(function (result) {
                $loading.addClass('d-none');
                $contenido.html(result.value);
            })
            .catch(function (err) {
                if (err.message === 'DOC_LEGACY' || !err.message || err.message.indexOf('HTTP') === 0) {
                    mostrarVerError(
                        ext === 'doc'
                            ? 'Los archivos .doc antiguos no se pueden previsualizar. Usa el botón Descargar.'
                            : 'No se pudo cargar el archivo desde el servidor. Usa el botón Descargar.'
                    );
                } else {
                    mostrarVerError('No se pudo convertir el documento. Usa el botón Descargar.');
                }
            });
    }

    function mostrarVerError(texto) {
        $('#verArchivoLoading').addClass('d-none');
        $('#verArchivoContenido').empty();
        $('#verArchivoError').html('<p class="text-muted mb-0">' + texto + '</p>').removeClass('d-none');
    }

    $(document).on('click', '.ver-btn', function() {
        verArchivo($(this).data('ruta') || '');
    });

    $(document).on('click', '#btnDescargarExistente, #btnSubirDescargarExistente', function() {
        verArchivo($(this).data('ruta') || '');
    });

    // Caso D: badge "Sin definir" abre el modal de edición (admin y editor)
    $(document).on('click', '.badge-sin-definir', function() {
        const $editBtn = $(this).closest('tr').find('.edit-btn');
        if ($editBtn.length) {
            $editBtn.trigger('click');
        }
    });

    function mostrarMsg(tipo, texto) {
        const $msg = $('#archivosMsg');
        $msg.html('<div class="alert alert-' + tipo + ' py-2 mb-3">' + texto + '</div>');
        setTimeout(() => $msg.empty(), 6000);
    }

    function cargarArchivos() {
        $('#archivosLista').html('<tr><td colspan="4" class="text-center text-muted">Cargando archivos...</td></tr>');
        $.get(URL_ARCHIVOS, function(res) {
            window.__archivos = res.archivos || [];
            renderArchivos();
        }).fail(function() {
            $('#archivosLista').html('<tr><td colspan="4" class="text-center text-danger">No se pudo cargar la lista de archivos.</td></tr>');
        });
    }

    function renderArchivos() {
        const q = ($('#archivosSearch').val() || '').toLowerCase();
        const anioFiltro = $('#archivosFiltroAnio').val();
        const $tbody = $('#archivosLista').empty();
        const lista = (window.__archivos || []).filter(a =>
            a.nombre.toLowerCase().includes(q) &&
            (anioFiltro === '' || (a.anio !== null && String(a.anio) === anioFiltro))
        );

        if (!lista.length) {
            $tbody.html('<tr><td colspan="4" class="text-center text-muted">No se encontraron archivos.</td></tr>');
            return;
        }

        lista.forEach(a => {
            const badge = a.ligado > 0
                ? '<span class="badge bg-success">' + a.ligado + '</span>'
                : '<span class="badge bg-secondary">0</span>';
            const anio = a.anio
                ? '<span class="badge bg-dark">' + a.anio + '</span>'
                : '<span class="badge bg-secondary">raíz</span>';
            $tbody.append(
                '<tr>' +
                    '<td>' + anio + '</td>' +
                    '<td><button type="button" class="btn btn-link btn-sm p-0 ver-btn me-1" data-ruta="' + a.ruta + '" title="Visualizar contenido"><i class="bi bi-eye text-primary"></i></button>' + $('<span>').text(a.nombre).html() + '</td>' +
                    '<td class="text-center">' + badge + '</td>' +
                    '<td class="text-center">' + (PUEDE_BORRAR
                        ? '<button class="btn btn-sm btn-outline-danger btn-eliminar-archivo" data-ruta="' + a.ruta + '" title="Eliminar del servidor"><i class="bi bi-trash"></i></button>'
                        : '<button type="button" class="btn btn-link btn-sm p-0 ver-btn" data-ruta="' + a.ruta + '" title="Visualizar contenido"><i class="bi bi-eye text-primary"></i></button>') + '</td>' +
                '</tr>'
            );
        });
    }

    $('#archivosModal').on('show.bs.modal', function() {
        $('#archivoConflicto').addClass('d-none');
        $('#archivosSearch').val('');
        $('#archivosFiltroAnio').val('');
        $('#archivoInput').val('');
        archivoPendiente = null;
        cargarArchivos();
    });

    $('#archivosSearch').on('input', renderArchivos);
    $('#archivosFiltroAnio').on('change', renderArchivos);

    $('#btnSubirArchivo').on('click', function() {
        $('#archivoInput').trigger('click');
    });

    $('#archivoInput').on('change', function() {
        if (!this.files.length) return;
        archivoPendiente = this.files[0];
        subirArchivo(false);
    });

    function subirArchivo(reemplazar) {
        if (!archivoPendiente) return;

        const anioSubida = $('#archivosFiltroAnio').val() || String(new Date().getFullYear());
        const fd = new FormData();
        fd.append('archivo', archivoPendiente);
        fd.append('anio', anioSubida);
        if (reemplazar) fd.append('reemplazar', '1');

        $.ajax({
            url: URL_SUBIR,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#archivoConflicto').addClass('d-none');
                mostrarMsg('success', res.mensaje);
                $('#archivoInput').val('');
                archivoPendiente = null;
                cargarArchivos();
                recargarTablaConFiltros();
            },
            error: function(xhr) {
                const res = xhr.responseJSON || {};
                if (res.existe) {
                    $('#conflictoNombre').text(res.ruta || res.nombre);
                    $('#btnDescargarExistente').data('ruta', res.ruta || res.nombre);
                    $('#archivoConflicto').removeClass('d-none');
                } else {
                    mostrarMsg('danger', res.mensaje || 'Error al subir el archivo.');
                    $('#archivoInput').val('');
                    archivoPendiente = null;
                }
            }
        });
    }

    $('#btnReemplazarArchivo').on('click', function() {
        subirArchivo(true);
    });

    $('#btnCancelarConflicto').on('click', function() {
        $('#archivoConflicto').addClass('d-none');
        $('#archivoInput').val('');
        archivoPendiente = null;
    });

    // ============ Ligar / Desligar archivos a un dictamen ============
    const DATOS_ARCHIVOS_DICTAMEN = @json($dictamenes->mapWithKeys(function ($d) {
        return [$d->id => [
            'encontrados' => $d->archivos_encontrados ?? [],
            'ligados' => $d->archivosLigados->map(fn ($a) => ($a->anio ? $a->anio . '/' . $a->nombre_archivo : $a->nombre_archivo))->all(),
        ]];
    })->all());

    let linkDictamenId = null;

    function mostrarLinkMsg(tipo, texto) {
        const $msg = $('#linkMsg');
        $msg.html('<div class="alert alert-' + tipo + ' py-2 mb-3">' + texto + '</div>');
        setTimeout(() => $msg.empty(), 6000);
    }

    function renderLink() {
        const data = DATOS_ARCHIVOS_DICTAMEN[linkDictamenId] || { encontrados: [], ligados: [] };
        const $c = $('#linkCoincidencias').empty();
        const $l = $('#linkLigados').empty();

        if (!data.encontrados.length) {
            $c.html('<p class="text-muted small mb-0">Sin coincidencias en el servidor para esta clave/año.</p>');
        }
        data.encontrados.forEach(ruta => {
            const ligado = data.ligados.includes(ruta);
            $c.append(
                '<div class="d-flex justify-content-between align-items-center border-bottom py-1">' +
                    '<span><i class="bi bi-file-earmark-text me-2"></i>' + $('<span>').text(ruta).html() +
                    ' <button type="button" class="btn btn-link btn-sm p-0 ver-btn ms-1" data-ruta="' + ruta + '" title="Visualizar este archivo"><i class="bi bi-eye text-primary"></i></button>' +
                    ' <a href="' + urlDescarga(ruta) + '" title="Descargar este archivo"><i class="bi bi-download ms-1 text-primary"></i></a></span>' +
                    (ligado
                        ? '<span class="badge bg-success">Ligado</span>'
                        : '<button class="btn btn-sm btn-outline-success btn-link-archivo" data-ruta="' + ruta + '" title="Ligar este archivo al dictamen"><i class="bi bi-link-45deg"></i> Ligar</button>') +
                '</div>'
            );
        });

        if (!data.ligados.length) {
            $l.html('<p class="text-muted small mb-0">Este dictamen no tiene archivos ligados.</p>');
        }
        data.ligados.forEach(ruta => {
            $l.append(
                '<div class="d-flex justify-content-between align-items-center border-bottom py-1">' +
                    '<span><i class="bi bi-file-earmark-lock me-2"></i>' + $('<span>').text(ruta).html() +
                    ' <button type="button" class="btn btn-link btn-sm p-0 ver-btn ms-1" data-ruta="' + ruta + '" title="Visualizar este archivo"><i class="bi bi-eye text-primary"></i></button>' +
                    ' <a href="' + urlDescarga(ruta) + '" title="Descargar este archivo"><i class="bi bi-download ms-1 text-primary"></i></a></span>' +
                    '<button class="btn btn-sm btn-outline-danger btn-desligar-archivo" data-ruta="' + ruta + '" title="Quitar el vínculo de este archivo al dictamen"><i class="bi bi-unlink"></i> Desligar</button>' +
                '</div>'
            );
        });
    }

    $('#linkModal').on('show.bs.modal', function(e) {
        const btn = e.relatedTarget || $(this).data('triggerBtn');
        if (!btn) {
            $('#linkCoincidencias').html('<p class="text-muted small mb-0">Selecciona un dictamen para ligar archivos.</p>');
            $('#linkLigados').html('<p class="text-muted small mb-0">Este dictamen no tiene archivos ligados.</p>');
            return;
        }
        linkDictamenId = $(btn).data('id');
        $('#linkClave').text($(btn).data('clave') || '');
        renderLink();
    });

    $(document).on('click', '.link-btn', function() {
        $('#linkModal').data('triggerBtn', $(this));
    });

    $('#linkModal').on('click', '.btn-link-archivo', function() {
        const ruta = $(this).data('ruta');
        $.post(URL_VINCULAR, { dictamen_id: linkDictamenId, ruta: ruta }, function(res) {
            mostrarLinkMsg('success', res.mensaje);
            DATOS_ARCHIVOS_DICTAMEN[linkDictamenId].ligados.push(ruta);
            renderLink();
            recargarTablaConFiltros();
        }).fail(function(xhr) {
            mostrarLinkMsg('danger', (xhr.responseJSON && xhr.responseJSON.mensaje) || 'Error al ligar.');
        });
    });

    $('#linkModal').on('click', '.btn-desligar-archivo', function() {
        const ruta = $(this).data('ruta');
        if (!confirm('¿Desligar este archivo del dictamen?')) return;
        $.post(URL_DESVINCULAR, { dictamen_id: linkDictamenId, ruta: ruta }, function(res) {
            mostrarLinkMsg('success', res.mensaje);
            DATOS_ARCHIVOS_DICTAMEN[linkDictamenId].ligados = DATOS_ARCHIVOS_DICTAMEN[linkDictamenId].ligados.filter(r => r !== ruta);
            renderLink();
            recargarTablaConFiltros();
        }).fail(function(xhr) {
            mostrarLinkMsg('danger', (xhr.responseJSON && xhr.responseJSON.mensaje) || 'Error al desligar.');
        });
    });

    // ============ Subir archivo faltante desde el badge "No encontrado" ============
    let subirPendiente = null;
    let subirAnio = '';

    function mostrarSubirMsg(tipo, texto) {
        $('#subirMsg').html('<div class="alert alert-' + tipo + ' py-2 mb-2">' + texto + '</div>');
    }

    $(document).on('click', '.subir-btn', function() {
        const btn = $(this);
        subirAnio = String(btn.data('anio') || new Date().getFullYear());
        $('#subirClave').text(btn.data('clave') || '—');
        $('#subirCarpeta').text(subirAnio + '/');
        $('#subirMsg').empty();
        $('#subirConflicto').addClass('d-none');
        $('#subirArchivoInput').val('');
        subirPendiente = null;
        $('#subirArchivoModal').modal('show');
    });

    $('#btnElegirSubir').on('click', function() {
        $('#subirArchivoInput').trigger('click');
    });

    $('#subirArchivoInput').on('change', function() {
        if (!this.files.length) return;
        subirPendiente = this.files[0];
        subirSubida(false);
    });

    function subirSubida(reemplazar) {
        if (!subirPendiente) return;

        const fd = new FormData();
        fd.append('archivo', subirPendiente);
        fd.append('anio', subirAnio);
        if (reemplazar) fd.append('reemplazar', '1');

        $.ajax({
            url: URL_SUBIR,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                mostrarSubirMsg('success', res.mensaje);
                $('#subirArchivoInput').val('');
                subirPendiente = null;
                recargarTablaConFiltros();
            },
            error: function(xhr) {
                const res = xhr.responseJSON || {};
                if (res.existe) {
                    $('#subirConflictoNombre').text(res.ruta || res.nombre);
                    $('#btnSubirDescargarExistente').data('ruta', res.ruta || res.nombre);
                    $('#subirConflicto').removeClass('d-none');
                } else {
                    mostrarSubirMsg('danger', res.mensaje || 'Error al subir el archivo.');
                    $('#subirArchivoInput').val('');
                    subirPendiente = null;
                }
            }
        });
    }

    $('#btnSubirReemplazar').on('click', function() {
        subirSubida(true);
    });

    $('#btnSubirCancelarConflicto').on('click', function() {
        $('#subirConflicto').addClass('d-none');
        $('#subirArchivoInput').val('');
        subirPendiente = null;
    });

    // Eliminar archivo del servidor (modal Gestionar Archivos)
    $(document).on('click', '.btn-eliminar-archivo', function() {
        const ruta = $(this).data('ruta');
        if (!confirm('¿Eliminar el archivo "' + ruta + '" del servidor? Se desligará de los dictámenes que lo referencien.')) return;

        $.post(URL_ELIMINAR, { ruta: ruta }, function(res) {
            mostrarMsg('success', res.mensaje);
            cargarArchivos();
            recargarTablaConFiltros();
        }).fail(function(xhr) {
            mostrarMsg('danger', (xhr.responseJSON && xhr.responseJSON.mensaje) || 'Error al eliminar el archivo.');
        });
    });
});
</script>
@endsection
