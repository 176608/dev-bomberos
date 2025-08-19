<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Sistema registro de Vías y Zonas')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="bi bi-journal-text"></i> 
                    Sistema de Registro de Vías y Zonas
                </h2>
                <div class="text-muted">
                    <small>
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? Auth::user()->email }}
                        <span class="badge bg-info ms-2">{{ Auth::user()->role }}</span>
                    </small>
                </div>
            </div>

            <!-- Panel principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-map"></i> 
                        Panel de Control - Vías y Zonas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Sección Zonas -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-houses"></i> 
                                        Gestión de Zonas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Administra las Zonas del sistema</p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-success" type="button" id="altaZonaBtn" data-bs-toggle="modal" data-bs-target="#createZonaModal">
                                            <i class="bi bi-plus-circle"></i> Alta Zona
                                        </button>
                                        <button class="btn btn-outline-primary" type="button" id="verZonasBtn">
                                            <i class="bi bi-list-ul"></i> Ver Zonas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Vías -->
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="bi bi-signpost"></i> 
                                        Gestión de Vías
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Administra las Vías del sistema</p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-warning" type="button" id="altaViaBtn" data-bs-toggle="modal" data-bs-target="#createViaModal">
                                            <i class="bi bi-plus-circle"></i> Alta Vía
                                        </button>
                                        <button class="btn btn-outline-primary" type="button" id="verViasBtn">
                                            <i class="bi bi-list-ul"></i> Ver Vías
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenedor dinámico para tablas -->
                    <div class="row mt-4">
                        <div class="col-12" id="tablaContainer">
                            <!-- Las tablas se cargan dinámicamente aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear zona -->
<div class="modal fade" id="createZonaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Zona</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createZonaForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Zona</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="COLONIA">COLONIA</option>
                            <option value="FRACCIONAMIENTO">FRACCIONAMIENTO</option>
                            <option value="UNIDAD HABITACIONAL">UNIDAD HABITACIONAL</option>
                            <option value="BARRIO">BARRIO</option>
                            <option value="PUEBLO">PUEBLO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_colo" class="form-label">ID Colonia (Opcional)</label>
                        <input type="number" class="form-control" id="id_colo" name="id_colo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear Zona</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para crear vía -->
<div class="modal fade" id="createViaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Vía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createViaForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomvial" class="form-label">Nombre de la Vía</label>
                        <input type="text" class="form-control" id="nomvial" name="nomvial" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipovial" class="form-label">Tipo de Vía</label>
                        <select class="form-select" id="tipovial" name="tipovial" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="CALLE">CALLE</option>
                            <option value="AVENIDA">AVENIDA</option>
                            <option value="BOULEVARD">BOULEVARD</option>
                            <option value="CALLEJON">CALLEJON</option>
                            <option value="CERRADA">CERRADA</option>
                            <option value="PRIVADA">PRIVADA</option>
                            <option value="PASAJE">PASAJE</option>
                            <option value="ANDADOR">ANDADOR</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label">Clave (Opcional)</label>
                        <input type="text" class="form-control" id="clave" name="clave">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Crear Vía</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar zona -->
<div class="modal fade" id="editZonaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Zona</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editZonaForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_zona_id" name="zona_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre de la Zona</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="edit_tipo" name="tipo" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="COLONIA">COLONIA</option>
                            <option value="FRACCIONAMIENTO">FRACCIONAMIENTO</option>
                            <option value="UNIDAD HABITACIONAL">UNIDAD HABITACIONAL</option>
                            <option value="BARRIO">BARRIO</option>
                            <option value="PUEBLO">PUEBLO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_id_colo" class="form-label">ID Colonia (Opcional)</label>
                        <input type="number" class="form-control" id="edit_id_colo" name="id_colo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Actualizar Zona</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar vía -->
<div class="modal fade" id="editViaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Vía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editViaForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_via_id" name="via_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nomvial" class="form-label">Nombre de la Vía</label>
                        <input type="text" class="form-control" id="edit_nomvial" name="nomvial" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipovial" class="form-label">Tipo de Vía</label>
                        <select class="form-select" id="edit_tipovial" name="tipovial" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="CALLE">CALLE</option>
                            <option value="AVENIDA">AVENIDA</option>
                            <option value="BOULEVARD">BOULEVARD</option>
                            <option value="CALLEJON">CALLEJON</option>
                            <option value="CERRADA">CERRADA</option>
                            <option value="PRIVADA">PRIVADA</option>
                            <option value="PASAJE">PASAJE</option>
                            <option value="ANDADOR">ANDADOR</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_clave" class="form-label">Clave (Opcional)</label>
                        <input type="text" class="form-control" id="edit_clave" name="clave">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar Vía</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentTable = null;
    let zonasTable = null;
    let viasTable = null;

    // Función para resetear estados de botones
    function resetButtonStates() {
        $('#verZonasBtn, #verViasBtn').removeClass('btn-primary btn-success btn-warning')
            .addClass('btn-outline-primary');
    }

    // Función para cargar tabla de zonas
    function loadZonasTable() {
        const zonasHtml = `
            <div class="card border-success mt-4" id="zonasTableCard">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-houses"></i> 
                        Gestión de Zonas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="zonasTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>ID Colonia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables se encarga del contenido -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        $('#tablaContainer').html(zonasHtml);
        
        // Inicializar DataTable para Zonas
        zonasTable = $('#zonasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("registrador.zonas.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'IDKEY', name: 'IDKEY' },
                { data: 'NOMBRE', name: 'NOMBRE' },
                { data: 'TIPO', name: 'TIPO' },
                { data: 'ID_COLO', name: 'ID_COLO' },
                { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            responsive: true,
            order: [[0, 'desc']]
        });

        currentTable = 'zonas';
    }

    // Función para cargar tabla de vías
    function loadViasTable() {
        const viasHtml = `
            <div class="card border-warning mt-4" id="viasTableCard">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="bi bi-signpost"></i> 
                        Gestión de Vías
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="viasTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Clave</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables se encarga del contenido -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        $('#tablaContainer').html(viasHtml);
        
        // Inicializar DataTable para Vías
        viasTable = $('#viasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("registrador.vias.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'IDKEY', name: 'IDKEY' },
                { data: 'Nomvial', name: 'Nomvial' },
                { data: 'Tipovial', name: 'Tipovial' },
                { data: 'CLAVE', name: 'CLAVE' },
                { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            responsive: true,
            order: [[0, 'desc']]
        });

        currentTable = 'vias';
    }

    // Event listeners para botones Ver
    $('#verZonasBtn').on('click', function() {
        if (currentTable === 'zonas') {
            // Si ya está visible, ocultarla
            $('#tablaContainer').empty();
            currentTable = null;
            resetButtonStates();
        } else {
            // Mostrar tabla de zonas
            resetButtonStates();
            $(this).removeClass('btn-outline-primary').addClass('btn-success');
            loadZonasTable();
        }
    });

    $('#verViasBtn').on('click', function() {
        if (currentTable === 'vias') {
            // Si ya está visible, ocultarla
            $('#tablaContainer').empty();
            currentTable = null;
            resetButtonStates();
        } else {
            // Mostrar tabla de vías
            resetButtonStates();
            $(this).removeClass('btn-outline-primary').addClass('btn-warning');
            loadViasTable();
        }
    });

    // Manejar envío del formulario de crear zona
    $('#createZonaForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("registrador.zonas.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#createZonaModal').modal('hide');
                    $('#createZonaForm')[0].reset();
                    
                    // Mostrar mensaje de éxito
                    const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    $('.card-body').first().prepend(alert);
                    
                    // Mostrar tabla de zonas automáticamente
                    resetButtonStates();
                    $('#verZonasBtn').removeClass('btn-outline-primary').addClass('btn-success');
                    loadZonasTable();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response.message || 'Error al crear la zona'));
            }
        });
    });

    // Manejar envío del formulario de crear vía
    $('#createViaForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("registrador.vias.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#createViaModal').modal('hide');
                    $('#createViaForm')[0].reset();
                    
                    // Mostrar mensaje de éxito
                    const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    $('.card-body').first().prepend(alert);
                    
                    // Mostrar tabla de vías automáticamente
                    resetButtonStates();
                    $('#verViasBtn').removeClass('btn-outline-primary').addClass('btn-warning');
                    loadViasTable();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response.message || 'Error al crear la vía'));
            }
        });
    });

    // Función para llenar formulario de edición de zona
    window.fillEditZonaForm = function(data) {
        $('#edit_zona_id').val(data.IDKEY);
        $('#edit_nombre').val(data.NOMBRE);
        $('#edit_tipo').val(data.TIPO);
        $('#edit_id_colo').val(data.ID_COLO);
    }

    // Función para llenar formulario de edición de vía
    window.fillEditViaForm = function(data) {
        $('#edit_via_id').val(data.IDKEY);
        $('#edit_nomvial').val(data.Nomvial);
        $('#edit_tipovial').val(data.Tipovial);
        $('#edit_clave').val(data.CLAVE);
    }

    // Manejar envío del formulario de editar zona
    $('#editZonaForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("registrador.zonas.update", ":id") }}'.replace(':id', $('#edit_zona_id').val()),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editZonaModal').modal('hide');
                    $('#editZonaForm')[0].reset();
                    
                    // Mostrar mensaje de éxito
                    const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    $('.card-body').first().prepend(alert);
                    
                    // Actualizar tabla de zonas automáticamente
                    if (currentTable === 'zonas') {
                        loadZonasTable();
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response.message || 'Error al actualizar la zona'));
            }
        });
    });

    // Manejar envío del formulario de editar vía
    $('#editViaForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("registrador.vias.update", ":id") }}'.replace(':id', $('#edit_via_id').val()),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editViaModal').modal('hide');
                    $('#editViaForm')[0].reset();
                    
                    // Mostrar mensaje de éxito
                    const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    $('.card-body').first().prepend(alert);
                    
                    // Actualizar tabla de vías automáticamente
                    if (currentTable === 'vias') {
                        loadViasTable();
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response.message || 'Error al actualizar la vía'));
            }
        });
    });

    console.log('Panel Registrador cargado correctamente');
});
</script>
@endsection