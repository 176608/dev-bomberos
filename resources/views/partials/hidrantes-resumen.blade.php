<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Resumen de Hidrantes por Estación y {{ $titulo_resumen }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaResumenHidrantes" class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>ESTACION</th>
                                @foreach($columnas as $titulo => $config)
                                    <th class="{{ $config['clase'] }}">{{ $titulo }}</th>
                                @endforeach
                                <th class="bg-secondary text-white">TOTAL X ESTACION</th>
                                <th class="{{ $ultima_columna['clase'] }}">{{ $ultima_columna['titulo'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estaciones as $est => $row)
                                <tr>
                                    <td>{{ $est }}</td>
                                    @foreach($columnas as $titulo => $config)
                                        <td>{{ $row[$config['key']] }}</td>
                                    @endforeach
                                    <td>{{ $row['TOTAL'] }}</td>
                                    <td>{{ $row[$ultima_columna['key']] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="no-sort">
                                <td><b>TOTALES</b></td>
                                @foreach($columnas as $titulo => $config)
                                    <td><b>{{ $totales[$config['key']] }}</b></td>
                                @endforeach
                                <td><b>{{ $totales['TOTAL'] }}</b></td>
                                <td><b>{{ array_sum(array_map(function($row) use($ultima_columna) {
                                    return $row[$ultima_columna['key']];
                                }, $estaciones)) }}</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTables con botones de exportación
    $('#tablaResumenHidrantes').DataTable({
        paging: false,
        searching: false,
        info: false,
        orderCellsTop: true,
        order: [],
        columnDefs: [
            { className: "text-center align-middle", targets: "_all" },
            { targets: [1], className: 'no-export' }  // No exportar la columna de acciones
        ],
        
        // Agregar configuración de botones para exportación
        dom: "<'row'<'col-sm-12'B>>" +
             "<'row'<'col-sm-12'tr>>",
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy"></i> Copiar',
                titleAttr: 'Copiar al portapapeles',
                className: 'btn btn-sm btn-outline-secondary'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                titleAttr: 'Exportar a CSV',
                className: 'btn btn-sm btn-outline-success'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-sm btn-outline-success',
                filename: function() {
                    const now = new Date();
                    return 'Hidrantes_' + now.getFullYear() + 
                           (now.getMonth() + 1).toString().padStart(2, '0') + 
                           now.getDate().toString().padStart(2, '0');
                },
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-sm btn-outline-danger',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 10;
                    doc.styles.tableHeader.fontSize = 11;
                    doc.pageMargins = [10, 10, 10, 10];
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                titleAttr: 'Imprimir',
                className: 'btn btn-sm btn-outline-info'
            }
        ]
    });
});
</script>