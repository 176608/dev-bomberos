<div class="card mt-4 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title m-0">Reporte Hidrantes Configurado</h5>
        </div>
        <div id="tablaLoader" class="text-center my-5">
            <div class="spinner-border text-primary" role="status"></div>
            <div>Cargando tabla...</div>
        </div>
        <div class="table-responsive" style="display:none;">
            <table id="hidrantesConfigTable" class="table table-striped table-hover table-bordered w-100">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center align-middle">ID</th>
                        @foreach($columnas as $columna)
                            @if($columna !== 'id' && $columna !== 'acciones')
                                <th class="text-center align-middle">{{ $headerNames[$columna] ?? ucfirst($columna) }}</th>
                            @endif
                        @endforeach
                        <th class="text-center align-middle">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script>
window.hidrantesTableConfig = @json($columnas);
window.hidrantesHeaderNames = @json($headerNames);

$(function() {
    let columnas = window.hidrantesTableConfig || [];
    let dtColumns = [
        { data: 'id', name: 'id', className: 'text-center align-middle' }
    ];
    columnas.forEach(function(col) {
        if(col !== 'id' && col !== 'acciones') {
            dtColumns.push({
                data: col,
                name: col,
                className: 'text-center align-middle'
            });
        }
    });
    dtColumns.push({
        data: 'acciones',
        name: 'acciones',
        orderable: false,
        searchable: false,
        className: 'text-center align-middle'
    });

    $('#hidrantesConfigTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('hidrantes.data') }}",
        columns: dtColumns,
        language: {
            url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
        },
        order: [[0, 'desc']],
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,
        scrollX: true,
        responsive: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 500], [25, 50, 100,  500]],
        drawCallback: function() {
            $('#tablaLoader').hide();
            $('.table-responsive').show();
        },
        createdRow: function(row, data, dataIndex) {
            // Si alguno de los campos contiene "Pendiente", pinta la fila de rojo
            if (
                (data.calle && data.calle.toString().includes('Pendiente')) ||
                (data.y_calle && data.y_calle.toString().includes('Pendiente')) ||
                (data.colonia && data.colonia.toString().includes('Pendiente'))
            ) {
                $(row).css('color', 'red');
            }
        }
    });
});
</script>