<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-table me-2"></i>Cuadros Estadísticos V2</h4>
        <a href="{{ route('sgiem.admin.cuadros-v2.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Nuevo Cuadro V2
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(isset($cuadros) && $cuadros->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaCuadrosV2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Subtema</th>
                            <th>Publicado</th>
                            <th>Mapa PDF</th>
                            <th>Gráfica</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuadros as $cuadro)
                        <tr>
                            <td>{{ $cuadro->cuadro_id }}</td>
                            <td>{{ $cuadro->codigo_cuadro }}</td>
                            <td>{{ $cuadro->c_titulo }}</td>
                            <td>{{ $cuadro->subtema->subtema_titulo ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($cuadro->publicado)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($cuadro->tipo_mapa_pdf)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-map"></i></span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($cuadro->permite_grafica)
                                    <span class="badge bg-info"><i class="bi bi-bar-chart"></i></span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('sgiem.admin.cuadros-v2.edit', $cuadro->cuadro_id) }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('sgiem.admin.cuadros-v2.show', $cuadro->cuadro_id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('sgiem.admin.cuadros-v2.destroy', $cuadro->cuadro_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el cuadro {{ $cuadro->codigo_cuadro }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No hay cuadros V2 registrados.</p>
                <a href="{{ route('sgiem.admin.cuadros-v2.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Crear primer cuadro
                </a>
            </div>
        @endif
    </div>
</div>

<script>
$(document).ready(function() {
    @if(isset($cuadros) && $cuadros->count() > 0)
    $('#tablaCuadrosV2').DataTable({
        responsive: true,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        columnDefs: [
            { targets: 0, width: "6%", className: "text-center" },
            { targets: 1, width: "10%", className: "text-center" },
            { targets: 2, width: "25%" },
            { targets: 3, width: "15%" },
            { targets: 4, width: "8%", className: "text-center", orderable: false },
            { targets: 5, width: "8%", className: "text-center", orderable: false },
            { targets: 6, width: "8%", className: "text-center", orderable: false },
            { targets: 7, width: "160px", className: "text-center", orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
    @endif
});
</script>
