<div class="card border-success mt-4">
    <div class="card-header bg-success text-white">
        <h6 class="mb-0">
            <i class="bi bi-houses"></i> 
            Gestión de Zonas
        </h6>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6>Listado de Zonas</h6>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createZonaModal">
                <i class="bi bi-plus-circle"></i> Nueva Zona
            </button>
        </div>
        
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

<script>
$(document).ready(function() {
    // Inicializar DataTable para Zonas
    const zonasTable = $('#zonasTable').DataTable({
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
                    zonasTable.ajax.reload();
                    
                    // Mostrar mensaje de éxito
                    const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    $('.card-body').prepend(alert);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response.message || 'Error al crear la zona'));
            }
        });
    });
});
</script>