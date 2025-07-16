<div class="card mb-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                {{ $modo === 'tabla' ? 'Filtros Activos de Búsqueda' : 'Resumen de Hidrantes' }}
            </h5>
            <button class="btn btn-sm btn-outline-primary" id="toggleFilters">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="filterContainer">
        @if($modo === 'tabla')
            @if(empty($filtros_act))
                <div class="alert alert-info mb-0">
                    No hay filtros activos. Configure los filtros en el panel de configuración.
                </div>
            @else
                <div class="row">
                    @foreach($filtros_act as $filtro)
                        @php
                            $partes = explode(':', $filtro);
                            $campo = $partes[0];
                            $valor = $partes[1] ?? '0';
                            $nombreCampo = $headerNames[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
                        @endphp
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-header bg-primary text-white py-1">
                                    {{ $nombreCampo }}
                                </div>
                                <div class="card-body py-2">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control filtro-valor" 
                                            data-campo="{{ $campo }}" 
                                            value="{{ $valor !== '0' ? $valor : '' }}" 
                                            placeholder="Filtrar por {{ $nombreCampo }}">
                                        <button class="btn btn-outline-secondary aplicar-filtro" data-campo="{{ $campo }}">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <button class="btn btn-primary btn-sm" id="aplicarTodosFiltros">
                            <i class="fas fa-search me-1"></i> Aplicar Filtros
                        </button>
                        <button class="btn btn-outline-secondary btn-sm ms-2" id="limpiarFiltros">
                            <i class="fas fa-eraser me-1"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>
            @endif
        @else
            <!-- Vista específica para modo resumen -->
            <div class="alert alert-info">
                <p><strong>Información:</strong> Mostrando resumen de hidrantes por estación y estado.</p>
                <p class="mb-0">Utilice los filtros en la vista de tabla para análisis detallados.</p>
            </div>
        @endif
    </div>
</div>

<script>
$(function() {
    // Toggle para expandir/contraer el panel de filtros
    $('#toggleFilters').click(function() {
        const $icon = $(this).find('i');
        const $container = $('#filterContainer');
        
        $container.slideToggle();
        $icon.toggleClass('fa-chevron-down fa-chevron-up');
    });
    
    // Manejo de filtros individuales
    $('.aplicar-filtro').click(function() {
        const campo = $(this).data('campo');
        const valor = $(this).prev('.filtro-valor').val();
        
        // Actualizar el valor del filtro en la lista de filtros activos
        actualizarFiltroActivo(campo, valor);
    });
    
    // Aplicar todos los filtros
    $('#aplicarTodosFiltros').click(function() {
        const filtros = {};
        
        $('.filtro-valor').each(function() {
            const campo = $(this).data('campo');
            const valor = $(this).val();
            if (valor) {
                filtros[campo] = valor;
            }
        });
        
        // Aplicar filtros a la tabla
        aplicarFiltrosATabla(filtros);
    });
    
    // Limpiar todos los filtros
    $('#limpiarFiltros').click(function() {
        $('.filtro-valor').val('');
        aplicarFiltrosATabla({});
    });
    
    function actualizarFiltroActivo(campo, valor) {
        // Obtener la lista actual de filtros activos
        let filtrosActivos = @json($filtros_act) || [];
        
        // Buscar si ya existe un filtro para este campo
        const filtroIndex = filtrosActivos.findIndex(f => f.startsWith(campo + ':'));
        
        // Actualizar o agregar el filtro
        const nuevoFiltro = campo + ':' + (valor || '0');
        
        if (filtroIndex >= 0) {
            filtrosActivos[filtroIndex] = nuevoFiltro;
        } else {
            filtrosActivos.push(nuevoFiltro);
        }
        
        // Guardar los filtros actualizados
        $.ajax({
            url: "{{ route('configuracion.update-filtros') }}",
            method: 'POST',
            data: { filtros_act: filtrosActivos },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Opcional: mostrar notificación
                console.log('Filtro actualizado:', campo, valor);
            },
            error: function(xhr) {
                console.error('Error al actualizar filtro:', xhr);
            }
        });
    }
    
    function aplicarFiltrosATabla(filtros) {
        // Si estamos usando DataTables
        const table = $('#hidrantesConfigTable').DataTable();
        if (table) {
            // Limpiar búsquedas anteriores
            table.search('').columns().search('');
            
            // Recorrer cada filtro y aplicarlo a la tabla
            Object.keys(filtros).forEach(campo => {
                const valor = filtros[campo];
                
                // Encontrar la columna correspondiente en DataTables
                const columnas = window.hidrantesTableConfig || [];
                // Sumar 3 porque las primeras tres columnas son id, acciones y stat
                const columnIndex = columnas.indexOf(campo);
                
                if (columnIndex >= 0) {
                    // Aplicar filtro en la columna correspondiente (+3 por las columnas fijas)
                    table.column(columnIndex + 3).search(valor);
                }
            });
            
            // Redibujar la tabla con los filtros aplicados
            table.draw();
        }
    }
});
</script>