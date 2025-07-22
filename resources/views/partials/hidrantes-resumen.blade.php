<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Resumen de Hidrantes por Estación y {{ $titulo_resumen }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Columna izquierda: botones de selección de tipo -->
                    <div class="col-md-3">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Tipos de Resumen</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn {{ $tipo_resumen === 0 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                            data-resumen-id="0">
                                        <i class="bi bi-list-check"></i> Estación y Estado
                                    </button>
                                    <button class="btn {{ $tipo_resumen === 1 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                            data-resumen-id="1">
                                        <i class="bi bi-speedometer"></i> Estación y Presión
                                    </button>
                                    <button class="btn {{ $tipo_resumen === 2 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                            data-resumen-id="2">
                                        <i class="bi bi-key"></i> Estación y Llaves de Hidrante
                                    </button>
                                    <button class="btn {{ $tipo_resumen === 3 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                            data-resumen-id="3">
                                        <i class="bi bi-key-fill"></i> Estación y Llaves de Fosa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha: tabla y gráficos -->
                    <div class="col-md-9">
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

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Porcentajes</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($porcentajes as $categoria => $porcentaje)
                                            <div class="mb-2">
                                                @php
                                                    $color = 'primary';
                                                    if (isset($columnas[$categoria])) {
                                                        $clase = $columnas[$categoria]['clase'];
                                                        if (strpos($clase, 'bg-success') !== false) $color = 'success';
                                                        elseif (strpos($clase, 'bg-danger') !== false) $color = 'danger';
                                                        elseif (strpos($clase, 'bg-warning') !== false) $color = 'warning';
                                                        elseif (strpos($clase, 'bg-info') !== false) $color = 'info';
                                                        elseif (strpos($clase, 'bg-secondary') !== false) $color = 'secondary';
                                                        elseif (strpos($clase, 'bg-primary') !== false) $color = 'primary';
                                                    }
                                                @endphp
                                                <span class="text-{{ $color }}">{{ $porcentaje }}% {{ $categoria }}</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-{{ $color }}" 
                                                        role="progressbar" 
                                                        style="width: {{ $porcentaje }}%" 
                                                        aria-valuenow="{{ $porcentaje }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTables
    $('#tablaResumenHidrantes').DataTable({
        paging: false,
        searching: false,
        info: false,
        orderCellsTop: true,
        order: [],
        columnDefs: [
            { className: "text-center align-middle", targets: "_all" }
        ]
    });
    
    // Manejar el cambio entre tipos de resumen
    $('.cambiar-resumen').click(function() {
        const resumenId = $(this).data('resumen-id');
        
        // Mostrar indicador de carga
        $('#resumenHidrantesContainer').html(
            '<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div>' +
            '<div>Cargando resumen...</div></div>'
        );
        
        // Guardar la selección del usuario en la base de datos
        $.ajax({
            url: "{{ route('capturista.actualizar-resumen') }}",
            method: 'POST',
            data: { resumen_id: resumenId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Cargar la nueva vista de resumen
                    $.get("{{ route('hidrantes.resumen') }}", function(html) {
                        $('#resumenHidrantesContainer').html(html);
                    });
                } else {
                    mostrarToast('Error al cambiar el tipo de resumen', 'error');
                }
            },
            error: function() {
                mostrarToast('Error al guardar la preferencia', 'error');
            }
        });
    });
});
</script>