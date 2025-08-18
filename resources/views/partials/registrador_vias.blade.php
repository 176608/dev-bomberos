<div class="card border-warning mt-4">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0">
            <i class="bi bi-signpost"></i> 
            Gestión de Vías
        </h6>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6>Listado de Vías</h6>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#createViaModal">
                <i class="bi bi-plus-circle"></i> Nueva Vía
            </button>
        </div>
        
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

<script>
$(document).ready(function() {
    // Inicializar DataTable para Vías
    const viasTable = $('#viasTable').DataTable({
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
                    viasTable.ajax.reload();
                    
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
                alert('Error: ' + (response.message || 'Error al crear la vía'));
            }
        });
    });
});
</script>