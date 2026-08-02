@extends('layouts.app')

@section('title', 'Gestor de Dictámenes - IMIP Ciudad Juárez')

@push('styles')
<style>
:root {
    --imip-blue: #2f7064;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    color: #333;
}

.stat-card {
    background: white !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
    text-align: center !important;
    padding: 16px 10px !important;
    height: 100%;
}

.stat-number {
    font-size: 2rem !important;
    font-weight: 700 !important;
    color: var(--imip-blue) !important;
    margin: 6px 0 !important;
}

.stat-label {
    font-size: 0.85rem !important;
    color: #666 !important;
    text-transform: uppercase;
    letter-spacing: 0.4px;
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

<div class="container mt-4">

    <!-- Estadísticas -->
    <div class="row mb-3 g-2" id="statsCards">
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="stat-number" style="color:#6c757d;">{{ $total }}</div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        @foreach($conteoEstatus as $estatus => $count)
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="stat-number" style="color: {{ $badgeColores[$estatus] ?? '#6c757d' }};">{{ $count }}</div>
                <div class="stat-label">{{ $estatus }}</div>
            </div>
        </div>
        @endforeach
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
        <div class="card-body py-2">
            <form id="filtrosForm" method="GET" action="{{ route('gestor-dictamenes.index') }}" class="row g-2">
                <!-- Sección 1: Año arriba + Limpiar abajo -->
                <div class="col-2 d-flex flex-column">
                    <label class="form-label mb-1" for="anioFilter"><strong>Año:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="anioFilter" name="anio">
                        <option value="">Todos</option>
                        @foreach($anios as $a)
                            <option value="{{ $a }}" @selected(request('anio') === (string) $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['estatus', 'anio', 'revisado_por', 'dependencia', 'nombre_puesto']))
                        <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-sm btn-outline-danger w-100 mt-auto" data-limpiar title="Limpiar los filtros activos">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                        </a>
                    @endif
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
                <!-- Sección 3: Nombre/Puesto + Dependencia (una fila cada uno) -->
                <div class="col-5">
                    <label class="form-label mb-1" for="nombrePuestoFilter"><strong>Nombre/Puesto:</strong></label>
                    <select class="form-select form-select-sm filter-select" id="nombrePuestoFilter" name="nombre_puesto">
                        <option value="">Todos</option>
                        @foreach($nombresPuestos as $np)
                            <option value="{{ $np }}" @selected(request('nombre_puesto') === $np)>{{ $np }}</option>
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
    <div class="table-responsive" id="tablaCard">
        <table id="dictamenes-table" class="table table-hover nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Núm. Oficio</th>
                    <th>Dependencia</th>
                    <th>Asunto</th>
                    <th>Estatus</th>
                    <th>Nombre / Puesto</th>
                    <th>Revisado por</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $d)
                <tr
                    data-fecha="{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : '' }}"
                    data-oficio="{{ $d->oficio ?? '' }}"
                    data-nombre-puesto="{{ $d->nombre_puesto ?? '' }}"
                    data-dependencia="{{ $d->dependencia_empres ?? '' }}"
                    data-asunto="{{ $d->asunto ?? '' }}"
                    data-clave-documento="{{ $d->clave_documento ?? '' }}"
                    data-revisado-por="{{ $d->revisado_por ?? '' }}"
                    data-estatus="{{ $d->estatus ?? '' }}"
                    data-observaciones="{{ $d->observaciones ?? '' }}"
                >
                    <td>{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $d->oficio ?? '—' }}</td>
                    <td>{{ $d->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $d->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($d->asunto ?? '', 60) }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $badgeColores[$d->estatus ?? ''] ?? '#6c757d' }}; color: {{ in_array($d->estatus ?? '', ['BORRADOR PARA FIRMA']) ? '#212529' : 'white' }}; font-weight: 500; padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; display: inline-block;">
                            {{ $d->estatus ?? 'S/D' }}
                        </span>
                    </td>
                    <td>{{ $d->nombre_puesto ?? '—' }}</td>
                    <td>{{ $d->revisado_por ?? '—' }}</td>
                    <td>
                        @php
                            $estadoA = $d->estado_archivo ?? 'sin_clave';
                            $encontradosA = $d->archivos_encontrados ?? [];
                            $ligadosA = $d->archivosLigados ?? collect();
                        @endphp
                        @if($estadoA === 'encontrado')
                            <span class="badge bg-primary" title="Encontrado en el servidor: {{ $encontradosA[0] ?? '' }}">
                                <i class="bi bi-check-circle"></i> {{ basename($encontradosA[0] ?? '') }}
                            </span>
                        @elseif($estadoA === 'no_encontrado')
                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                                <button type="button" class="btn btn-sm btn-outline-danger subir-btn"
                                        data-id="{{ $d->id }}" data-clave="{{ $d->clave_documento }}" data-anio="{{ $d->anio }}"
                                        title="No se encontró ningún archivo con la clave {{ $d->clave_documento }} en el servidor. Clic para subirlo.">
                                    <i class="bi bi-x-circle"></i> No encontrado
                                </button>
                            @else
                                <span class="badge bg-danger" title="No se encontró ningún archivo con la clave {{ $d->clave_documento }} en el servidor">
                                    <i class="bi bi-x-circle"></i> No encontrado
                                </span>
                            @endif
                        @elseif($estadoA === 'multiples')
                            <span class="badge bg-warning text-dark" title="{{ implode(PHP_EOL, $encontradosA) }}">
                                <i class="bi bi-exclamation-triangle"></i> {{ count($encontradosA) }} coincidencias
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                        @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']) && ($estadoA === 'encontrado' || $estadoA === 'multiples' || $ligadosA->count() > 0))
                            <button class="btn btn-sm btn-outline-info link-btn ms-1" data-id="{{ $d->id }}" data-clave="{{ $d->clave_documento }}" data-bs-toggle="modal" data-bs-target="#linkModal"
                                    title="Descargar y ligar archivos ({{ $d->clave_documento }} @ {{ $d->anio }})">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        @endif
                    </td>
                    <td>
                        @if(($d->estatus ?? '') === \App\Models\GestorDictamenes\Dictamen::DESHABILITADO)
                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                                <form action="{{ route('gestor-dictamenes.restore', $d->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Restaurar este dictamen deshabilitado" onclick="return confirm('¿Deseas restaurar este dictamen?');">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                    </button>
                                </form>
                            @endif
                        @else
                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']))
                                <button class="btn btn-sm btn-primary edit-btn" title="Editar este dictamen" data-id="{{ $d->id }}" data-route="{{ route('gestor-dictamenes.update', $d->id) }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            @endif

                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                                <form id="delete-form-{{ $d->id }}" action="{{ route('gestor-dictamenes.destroy', $d->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button class="btn btn-sm btn-danger delete-btn" title="Deshabilitar este dictamen"
                                        onclick="if(confirm('¿Estás seguro que deseas deshabilitar este dictamen?')) document.getElementById('delete-form-{{ $d->id }}').submit();">
                                    <i class="bi bi-trash"></i> Deshabilitar
                                </button>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <form method="POST" action="{{ route('gestor-dictamenes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label>Núm. Oficio</label>
                        <input type="text" class="form-control" name="oficio" required placeholder="Ej. DGDU/DCP/APDU/2515/2024 EXP. 50.24">
                    </div>
                    <div class="mb-3">
                        <label>Clave de documento</label>
                        <input type="text" class="form-control" name="clave_documento" placeholder="Ej. PYP024, DIR143">
                    </div>
                    <div class="mb-3">
                        <label>Nombre / Puesto</label>
                        <input type="text" class="form-control" name="nombre_puesto">
                    </div>
                    <div class="mb-3">
                        <label>Dependencia</label>
                        <input type="text" class="form-control" name="dependencia_empres">
                    </div>
                    <div class="mb-3">
                        <label>Asunto</label>
                        <textarea class="form-control" name="asunto" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Revisado por</label>
                        <input type="text" class="form-control" name="revisado_por">
                    </div>
                    <div class="mb-3">
                        <label>Estatus</label>
                        <select class="form-control" name="estatus" required>
                            <option value="">Seleccione un estatus...</option>
                            @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                <option value="{{ $estatus }}">{{ $estatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Observaciones</label>
                        <textarea class="form-control" name="observaciones"></textarea>
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
                        <div class="col-md-6">
                            <label>Fecha</label>
                            <input type="date" class="form-control" id="fecha_edit" name="fecha" required>
                        </div>
                        <div class="col-md-6">
                            <label>Núm. Oficio</label>
                            <input type="text" class="form-control" id="oficio_edit" name="oficio" required>
                        </div>
                        <div class="col-md-4">
                            <label>Clave de documento</label>
                            <input type="text" class="form-control" id="clave_documento_edit" name="clave_documento" placeholder="Ej. PYP024, DIR143">
                        </div>
                        <div class="col-md-4">
                            <label>Estatus</label>
                            <select class="form-control" id="estatus_edit" name="estatus" required>
                                <option value="">Seleccione un estatus...</option>
                                @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                    <option value="{{ $estatus }}">{{ $estatus }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Dependencia</label>
                            <input type="text" class="form-control" id="dependencia_empres_edit" name="dependencia_empres">
                        </div>
                        <div class="col-md-6">
                            <label>Nombre / Puesto</label>
                            <input type="text" class="form-control" id="nombre_puesto_edit" name="nombre_puesto">
                        </div>
                        <div class="col-md-6">
                            <label>Revisado por</label>
                            <input type="text" class="form-control" id="revisado_por_edit" name="revisado_por">
                        </div>
                        <div class="col-md-12">
                            <label>Asunto</label>
                            <textarea class="form-control" id="asunto_edit" name="asunto" required rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="observaciones_edit" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
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
                        <a id="btnDescargarExistente" class="btn btn-sm btn-outline-secondary" href="#" target="_blank" title="Descargar el archivo que ya existe con ese nombre">
                            <i class="bi bi-download"></i> Revisar (descargar) existente
                        </a>
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
                        <a id="btnSubirDescargarExistente" class="btn btn-sm btn-outline-secondary" href="#" target="_blank" title="Descargar el archivo que ya existe con ese nombre">
                            <i class="bi bi-download"></i> Revisar existente
                        </a>
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

@endsection

@section('scripts')
@parent

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    let filtrando = false;

    // Limpieza defensiva del overlay al cargar la página y al restaurarla desde la
    // caché del navegador (bfcache), donde .ready no se vuelve a ejecutar
    $('#uiOverlay').removeClass('visible');
    $(window).on('pageshow', function (e) {
        if (e.persisted) $('#uiOverlay').removeClass('visible');
    });

    // Limpieza única de estado DataTables viejo (estructura de columnas cambió: columna Núm. Oficio unificada)
    if (!sessionStorage.getItem('dictamenes_state_v10')) {
        Object.keys(localStorage).forEach(function(k) {
            if (k.indexOf('DataTables_dictamenes-table') === 0) {
                localStorage.removeItem(k);
            }
        });
        sessionStorage.setItem('dictamenes_state_v10', '1');
    }

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#dictamenes-table')) {
            $('#dictamenes-table').DataTable().destroy();
        }
        $('#dictamenes-table').DataTable({
            "paging": true,
            "lengthMenu": [
                [10, 25, 50, 100, 150, -1],
                ['10', '25', '50', '100', '150', 'Todas']
            ],
            "pageLength": -1,
            "searching": true,
            "info": true,
            "ordering": true,
            "order": [],
            "scrollX": true,
            "autoWidth": false,
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
                    dropdownAutoWidth: false
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
    $(document).on('click', '#filtrosForm a[data-limpiar]', function (e) {
        e.preventDefault();
        const form = document.getElementById('filtrosForm');
        form.reset();
        $('.filter-select').val('').trigger('change.select2');
        aplicarFiltros(form.action, 'GET');
    });

    initDataTable();

    $('.filter-select').select2({
        width: '100%',
        dropdownAutoWidth: false
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
        $('#oficio_edit').val($row.data('oficio') || '');
        $('#nombre_puesto_edit').val($row.data('nombre-puesto') || '');
        $('#dependencia_empres_edit').val($row.data('dependencia') || '');
        $('#asunto_edit').val($row.data('asunto') || '');
        $('#clave_documento_edit').val($row.data('clave-documento') || '');
        $('#revisado_por_edit').val($row.data('revisado-por') || '');
        $('#estatus_edit').val($row.data('estatus') || '');
        $('#observaciones_edit').val($row.data('observaciones') || '');

        $('#editForm').attr('action', route);
        $('#editModal').modal('show');
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
                    '<td><a href="' + urlDescarga(a.ruta) + '" title="Descargar"><i class="bi bi-download me-2 text-primary"></i></a>' + $('<span>').text(a.nombre).html() + '</td>' +
                    '<td class="text-center">' + badge + '</td>' +
                    '<td class="text-center">' + (PUEDE_BORRAR
                        ? '<button class="btn btn-sm btn-outline-danger btn-eliminar-archivo" data-ruta="' + a.ruta + '" title="Eliminar del servidor"><i class="bi bi-trash"></i></button>'
                        : '<a href="' + urlDescarga(a.ruta) + '" class="text-primary" title="Descargar"><i class="bi bi-download"></i></a>') + '</td>' +
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
            },
            error: function(xhr) {
                const res = xhr.responseJSON || {};
                if (res.existe) {
                    $('#conflictoNombre').text(res.ruta || res.nombre);
                    $('#btnDescargarExistente').attr('href', urlDescarga(res.ruta || res.nombre));
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
                setTimeout(function() { window.location.reload(); }, 900);
            },
            error: function(xhr) {
                const res = xhr.responseJSON || {};
                if (res.existe) {
                    $('#subirConflictoNombre').text(res.ruta || res.nombre);
                    $('#btnSubirDescargarExistente').attr('href', urlDescarga(res.ruta || res.nombre));
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
        }).fail(function(xhr) {
            mostrarMsg('danger', (xhr.responseJSON && xhr.responseJSON.mensaje) || 'Error al eliminar el archivo.');
        });
    });
});
</script>
@endsection
