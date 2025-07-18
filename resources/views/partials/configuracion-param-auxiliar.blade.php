<div class="card mb-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 w-100 text-center">
                <i class="fas fa-filter me-2"></i>
                {{ $modo === 'tabla' ? 'Filtros Activos de Búsqueda' : 'Resumen de Hidrantes' }}
            </h5>
            <button class="btn btn-sm btn-outline-primary" id="toggleFilters" title="Expandir/Contraer panel de filtros">
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
                    @foreach($filtros_act as $campo)
                        @php
                            $nombreCampo = $headerNames[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
                            $valorSeleccionado = ''; // Valor por defecto
                        @endphp
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-header bg-primary text-white py-1 text-center">
                                    {{ $nombreCampo }}
                                </div>
                                <div class="card-body py-2">
                                    <div class="input-group input-group-sm">
                                        <select class="form-select filtro-valor" data-campo="{{ $campo }}">
                                            <option value="">Todos</option>
                                            @if(isset($opciones_filtro[$campo]))
                                                @foreach($opciones_filtro[$campo] as $opcion)
                                                    <option value="{{ $opcion }}">
                                                        {{ $opcion ?: 'Vacío' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <button class="btn btn-outline-primary aplicar-filtro" 
                                                data-campo="{{ $campo }}" 
                                                title="Aplicar solo este filtro">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button class="btn btn-primary" id="aplicarTodosFiltros" title="Aplicar todos los filtros seleccionados">
                            <i class="fas fa-search me-1"></i> Aplicar Filtros
                        </button>
                        <button class="btn btn-outline-secondary ms-2" id="limpiarFiltros" title="Limpiar todos los filtros">
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
        
        // Aplicar solo este filtro a la tabla
        const filtros = {};
        if (valor) {
            filtros[campo] = valor;
        }
        aplicarFiltrosATabla(filtros);
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
        
        // Convertir cualquier formato antiguo
        filtrosActivos = filtrosActivos.map(filtro => {
            return typeof filtro === 'string' && filtro.includes(':') ? 
                filtro.split(':')[0] : filtro;
        });
        
        // Buscar si ya existe un filtro para este campo
        const filtroIndex = filtrosActivos.indexOf(campo);
        
        // Guardar la información del valor seleccionado por separado
        const valoresFiltros = {};
        valoresFiltros[campo] = valor;
        
        // Actualizar o agregar el campo a los filtros activos
        if (filtroIndex === -1) {
            filtrosActivos.push(campo);
        }
        
        // Guardar los filtros actualizados y los valores
        $.ajax({
            url: "{{ route('configuracion.update-filtros') }}",
            method: 'POST',
            data: { 
                filtros_act: filtrosActivos,
                valores_filtros: valoresFiltros
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Filtro actualizado:', campo, valor);
            },
            error: function(xhr) {
                console.error('Error al actualizar filtro:', xhr);
            }
        });
    }
    
    function aplicarFiltrosATabla(filtros) {
        // Guardar el estado de los filtros
        localStorage.setItem('hidrantesFilterState', JSON.stringify(filtros));
        
        // Si estamos usando DataTables
        const table = $('#hidrantesConfigTable').DataTable();
        if (table) {
            // Limpiar búsquedas anteriores
            table.search('').columns().search('');
            
            // Separar filtros en visibles y no visibles
            const columnas = window.hidrantesTableConfig || [];
            const filtrosVisibles = {};
            const filtrosNoVisibles = {};
            
            Object.keys(filtros).forEach(campo => {
                const valor = filtros[campo];
                const columnIndex = columnas.indexOf(campo);
                
                if (columnIndex >= 0) {
                    // Es una columna visible, filtramos del lado del cliente
                    filtrosVisibles[campo] = valor;
                } else {
                    // Es una columna no visible, filtraremos del lado del servidor
                    filtrosNoVisibles[campo] = valor;
                }
            });
            
            // Depurar los filtros
            console.log('Filtros visibles:', filtrosVisibles);
            console.log('Filtros no visibles:', filtrosNoVisibles);
            console.log('Columnas configuradas:', columnas);
            
            // Primero limpiar todos los filtros anteriores
            table.columns().search('').draw();
            
            // Aplicar filtros visibles directamente en DataTables
            Object.keys(filtrosVisibles).forEach(campo => {
                const valor = filtrosVisibles[campo];
                const columnIndex = columnas.indexOf(campo);
                if (columnIndex >= 0) {
                    // Determinar el índice real de la columna en la tabla
                    // Las primeras 3 columnas son: id, acciones, stat
                    const realIndex = columnIndex + 3;
                    
                    console.log(`Aplicando filtro a columna ${campo} (índice ${realIndex}): "${valor}"`);
                    
                    // Verificar primero que la columna existe en la tabla
                    if (realIndex < table.columns().nodes().length) {
                        if (valor === '') {
                            // Filtro para valores vacíos
                            table.column(realIndex).search('^$|^N/A$', true, false);
                        } else {
                            // Usar un filtro exacto (sin regex) para evitar problemas
                            table.column(realIndex).search(valor, false, false);
                        }
                    } else {
                        console.error(`La columna con índice ${realIndex} no existe en la tabla`);
                    }
                }
            });
            
            // Si hay filtros para columnas no visibles, recargar la tabla con filtros server-side
            if (Object.keys(filtrosNoVisibles).length > 0) {
                // Guardar los filtros no visibles globalmente para la recarga
                window.filtrosNoVisibles = filtrosNoVisibles;
                
                try {
                    // Guardar los filtros no visibles en la configuración de AJAX
                    const ajaxUrl = new URL(table.ajax.url());
                    ajaxUrl.searchParams.set('filtros_adicionales', JSON.stringify(filtrosNoVisibles));
                    
                    // Actualizar la URL de AJAX y recargar
                    console.log('Recargando tabla con filtros server-side:', filtrosNoVisibles);
                    table.ajax.url(ajaxUrl.toString()).load();
                } catch (error) {
                    console.error('Error al aplicar filtros no visibles:', error);
                    alert('Error al aplicar los filtros. Por favor, inténtelo de nuevo.');
                }
            } else {
                // Si no hay filtros para columnas no visibles, solo redibujamos
                // Y limpiamos cualquier filtro server-side anterior
                window.filtrosNoVisibles = {};
                table.draw();
            }
        } else {
            console.error('La tabla DataTables no está inicializada correctamente');
        }
    }
});
</script>