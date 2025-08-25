<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="mb-0">Resumen de Hidrantes por Estación y {{ $titulo_resumen }}</h5>
                    </div>
                    <div class="col-6">
                        <div id="exportButtonsContainerResumen" class="d-flex justify-content-end">
                        </div>
                    </div>
                </div>
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
    let table = $('#tablaResumenHidrantes').DataTable({
        paging: false,
        searching: false,
        info: false,
        orderCellsTop: true,
        order: [],
        columnDefs: [
            { className: "text-center align-middle", targets: "_all" },
            { targets: [1], className: 'no-export' }  
        ],
        
        dom: "<'row d-none'<'col-sm-12'B>>" +
             "<'row'<'col-sm-12'tr>>",
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'excelHtml5',
                filename: function() {
                    const now = new Date();
                    return 'Hidrantes_Resumen_' + now.getFullYear() + 
                           (now.getMonth() + 1).toString().padStart(2, '0') + 
                           now.getDate().toString().padStart(2, '0');
                },
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 10;
                    doc.styles.tableHeader.fontSize = 11;
                    doc.pageMargins = [10, 10, 10, 10];
                },
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            }
        ],
        drawCallback: function() {
            setTimeout(moverBotonesAlHeader, 100);
        }
    });
    
    function moverBotonesAlHeader() {
        const contenedorBotones = document.getElementById('exportButtonsContainerResumen');
        
        if (contenedorBotones && table) {
            contenedorBotones.innerHTML = '';
            
            const botonesConfig = [
                {
                    text: '<i class="bi bi-clipboard"></i>',
                    className: 'btn btn-sm btn-outline-light',
                    action: 'copy',
                    title: 'Copiar al portapapeles'
                },
                {
                    text: '<i class="bi bi-filetype-csv"></i>',
                    className: 'btn btn-sm btn-outline-light',
                    action: 'csv',
                    title: 'Exportar a CSV'
                },
                {
                    text: '<i class="bi bi-file-earmark-excel"></i>',
                    className: 'btn btn-sm btn-outline-light',
                    action: 'excel',
                    title: 'Exportar a Excel'
                },
                {
                    text: '<i class="bi bi-file-earmark-pdf"></i>',
                    className: 'btn btn-sm btn-outline-light',
                    action: 'pdf',
                    title: 'Exportar a PDF'
                },
                {
                    text: '<i class="bi bi-printer"></i>',
                    className: 'btn btn-sm btn-outline-light',
                    action: 'print',
                    title: 'Imprimir'
                }
            ];
            
            botonesConfig.forEach(function(config, index) {
                const btnElement = document.createElement('button');
                btnElement.className = config.className;
                btnElement.innerHTML = config.text;
                btnElement.title = config.title;
                btnElement.style.marginLeft = '3px';
                
                btnElement.addEventListener('click', function() {
                    try {
                        table.button(index).trigger();
                    } catch (e) {
                        console.error('Error al activar botón:', e);
                    }
                });
                
                contenedorBotones.appendChild(btnElement);
            });
        }
    }
});
</script>