<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 w-100 text-center">
                {{ $modo === 'tabla' ? 'Filtros Activos de Búsqueda' : 'Resumen de Hidrantes' }}
            </h5>
            <button class="btn btn-sm btn-outline-primary" id="toggleFilters" title="Contraer panel">
                <i class="bi bi-arrow-bar-up"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="filterContainer">
        @if($modo === 'tabla')
            <!-- Código existente para filtros en modo tabla -->
            @if(empty($filtros_act))
                <div class="alert alert-info mb-0">
                    No hay filtros activos. Configure los filtros en el panel de configuración.
                </div>
            @else
                <!-- Contenido actual del modo tabla -->
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
                                        @if($campo === 'fecha_inspeccion')
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-calendar3"></i>
                                            </span>
                                        @endif
                                        <select class="form-select filtro-valor" data-campo="{{ $campo }}">
                                            <option value="">Todos</option>
                                            @if(isset($opciones_filtro[$campo]) && $campo === 'fecha_inspeccion')
                                                @foreach($opciones_filtro[$campo] as $opcion)
                                                    @php
                                                        $displayDate = $opcion;
                                                        if (preg_match('/^\d{4}-\d{2}$/', $opcion)) {
                                                            $date = \Carbon\Carbon::createFromFormat('Y-m', $opcion);
                                                            $displayDate = $date->translatedFormat('F Y');
                                                        }
                                                    @endphp
                                                    <option value="{{ $opcion }}">
                                                        {{ ucfirst($displayDate) }}
                                                    </option>
                                                @endforeach
                                            @elseif(isset($opciones_filtro[$campo]))
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
                                            <i class="bi bi-funnel"></i>
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
                            <i class="bi bi-search"></i> Aplicar Filtros
                        </button>
                        <button class="btn btn-outline-secondary ms-2" id="limpiarFiltros" title="Limpiar todos los filtros">
                            <i class="bi bi-eraser"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>
            @endif
        @else
            <!-- Modo resumen: mostrar botones y porcentajes -->
            <div class="row">
                <!-- Columna izquierda: botones de selección -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Tipos de Resumen</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn {{ isset($tipo_resumen) && $tipo_resumen === 0 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                        data-resumen-id="0">
                                    <i class="bi bi-list-check"></i> Estación y Estado
                                </button>
                                <button class="btn {{ isset($tipo_resumen) && $tipo_resumen === 1 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                        data-resumen-id="1">
                                    <i class="bi bi-speedometer"></i> Estación y Presión
                                </button>
                                <button class="btn {{ isset($tipo_resumen) && $tipo_resumen === 2 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                        data-resumen-id="2">
                                    <i class="bi bi-key"></i> Estación y Llaves de Hidrante
                                </button>
                                <button class="btn {{ isset($tipo_resumen) && $tipo_resumen === 3 ? 'btn-primary' : 'btn-outline-primary' }} mb-2 cambiar-resumen" 
                                        data-resumen-id="3">
                                    <i class="bi bi-key-fill"></i> Estación y Llaves de Fosa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Columna derecha: porcentajes (si están disponibles) -->
                <div class="col-md-6">
                    @if(isset($porcentajes) && count($porcentajes) > 0)
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
                    @else
                        <div class="alert alert-info">
                            <p><strong>Información:</strong> Seleccione un tipo de resumen para ver los porcentajes.</p>
                        </div>
                    @endif
                </div>
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
        
        // Cambiar el icono y el título según el estado actual
        if ($icon.hasClass('bi-arrow-bar-up')) {
             // Está contrayendo, cambiar al icono de expandir
            $icon.removeClass('bi-arrow-bar-up').addClass('bi-arrow-bar-down');
            $(this).attr('title', 'Expandir panel');
        } else {
            // Está expandiendo, cambiar al icono de contraer
            $icon.removeClass('bi-arrow-bar-down').addClass('bi-arrow-bar-up');
            $(this).attr('title', 'Contraer panel');
        }
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
        // Limpiar los selectores visuales
        $('.filtro-valor').val('');
        
        // Limpiar localStorage
        localStorage.removeItem('hidrantesFilterState');
        
        // Limpiar filtros globales no visibles
        window.filtrosNoVisibles = {};
        
        // Aplicar filtros vacíos para limpiar todo (con noScroll = true)
        aplicarFiltrosATabla({}, true);
        
        // Usar la función centralizada para mostrar el toast
        if (typeof mostrarToast === 'function') {
            mostrarToast('Filtros limpiados exitosamente');
        }
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
    
    // Reemplazo completo de la función aplicarFiltrosATabla
    function aplicarFiltrosATabla(filtros, noScroll = false) {
        // Guardar el estado de los filtros
        localStorage.setItem('hidrantesFilterState', JSON.stringify(filtros));
        
        // Si estamos usando DataTables
        const table = $('#hidrantesConfigTable').DataTable();
        if (table) {
            // Limpiar búsquedas anteriores
            table.search('').columns().search('');
            
            // Verificar si estamos limpiando todos los filtros
            const limpiandoTodo = Object.keys(filtros).length === 0;
            
            if (limpiandoTodo) {
                // Si estamos limpiando todo, asegurémonos de limpiar también filtros server-side
                window.filtrosNoVisibles = {};
                
                try {
                    // Restaurar URL de AJAX a su estado original sin parámetros adicionales
                    let ajaxUrl = new URL(table.ajax.url());
                    ajaxUrl.searchParams.delete('filtros_adicionales');
                    
                    // Recargar la tabla sin filtros
                    console.log('Recargando tabla sin filtros');
                    table.ajax.url(ajaxUrl.toString()).load(function() {
                        if (noScroll) {
                            // Scroll hacia el panel auxiliar en lugar de la tabla
                            scrollToAuxContainer();
                        }
                    });
                    return; // Terminamos aquí para evitar código adicional
                } catch (error) {
                    console.error('Error al limpiar filtros:', error);
                }
            }
            
            // Separar filtros en visibles y no visibles
            const columnas = window.hidrantesTableConfig || [];
            const filtrosVisibles = {};
            const filtrosNoVisibles = {};
            
            Object.keys(filtros).forEach(campo => {
                const valor = filtros[campo];
                const columnIndex = columnas.indexOf(campo);
                
                // Para campos de fecha y ubicación, mandar al servidor siempre
                if (campo === 'fecha_inspeccion' || 
                    (['calle', 'y_calle', 'colonia'].includes(campo) && valor === 'Con campo pendiente')) {
                    filtrosNoVisibles[campo] = valor;
                    return;
                }
                
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
                    table.ajax.url(ajaxUrl.toString()).load(function() {
                        if (!noScroll) {
                            scrollToTablaHidrantes();
                        }
                    });
                } catch (error) {
                    console.error('Error al aplicar filtros no visibles:', error);
                    alert('Error al aplicar los filtros. Por favor, inténtelo de nuevo.');
                }
            } else {
                // Si no hay filtros para columnas no visibles, solo redibujamos
                // Y limpiamos cualquier filtro server-side anterior si estaba presente antes
                if (window.filtrosNoVisibles && Object.keys(window.filtrosNoVisibles).length > 0) {
                    // Había filtros server-side antes, necesitamos recargar para limpiarlos
                    window.filtrosNoVisibles = {};
                    try {
                        // Restaurar URL de AJAX a su estado original
                        const ajaxUrl = new URL(table.ajax.url());
                        ajaxUrl.searchParams.delete('filtros_adicionales');
                        
                        // Recargar tabla sin filtros server-side
                        table.ajax.url(ajaxUrl.toString()).load();
                    } catch (error) {
                        console.error('Error al limpiar filtros server-side:', error);
                        table.draw(); // Como fallback
                    }
                } else {
                    // No había filtros server-side antes, solo redibujamos
                    table.draw();
                }
            }
        } else {
            console.error('La tabla DataTables no está inicializada correctamente');
        }
    }
    
    // Reemplazar el manejador para los botones de resumen
    $('.cambiar-resumen').click(function() {
        const resumenId = $(this).data('resumen-id');
        
        // Actualizar apariencia de los botones
        $('.cambiar-resumen').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        
        // Mostrar indicador de carga
        $('#resumenHidrantesContainer').html(
            '<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div>' +
            '<div>Cargando resumen...</div></div>'
        );
        
        // Ya no guardamos en la base de datos, solo cargamos el tipo de resumen apropiado
        let url;
        switch(resumenId) {
            case 0:
                url = "{{ route('hidrantes.resumen') }}";
                break;
            case 1:
                url = "{{ route('hidrantes.resumen') }}?tipo=presion";
                break;
            case 2:
                url = "{{ route('hidrantes.resumen') }}?tipo=hidrante";
                break;
            case 3:
                url = "{{ route('hidrantes.resumen') }}?tipo=fosa";
                break;
        }
        
        // Cargar la nueva vista de resumen
        $.get(url, function(html) {
            $('#resumenHidrantesContainer').html(html);
            
            // Actualizar porcentajes en el panel auxiliar según el tipo seleccionado
            actualizarPorcentajes(resumenId);
        });
    });
    
    // Nueva función para actualizar los porcentajes en el panel auxiliar
    function actualizarPorcentajes(resumenId) {
        // Mostrar indicador de carga en los porcentajes
        $('.col-md-6:nth-child(2)').html(
            '<div class="card"><div class="card-body text-center">' +
            '<div class="spinner-border spinner-border-sm text-primary"></div> ' +
            'Actualizando porcentajes...</div></div>'
        );
        
        $.get("{{ route('capturista.panel-auxiliar') }}", { 
            modo: 'resumen',
            tipo: resumenId
        }, function(html) {
            // Parsear el HTML devuelto para extraer la sección de porcentajes
            const $newPanel = $(html);
            const $newPorcentajes = $newPanel.find('.col-md-6:nth-child(2)').html();
            
            // Actualizar la columna de porcentajes con los datos nuevos
            if ($newPorcentajes) {
                $('.col-md-6:nth-child(2)').html($newPorcentajes);
            } else {
                console.error('No se encontraron datos de porcentajes en la respuesta');
            }
        }).fail(function(err) {
            console.error('Error al actualizar porcentajes:', err);
            $('.col-md-6:nth-child(2)').html('<div class="alert alert-danger">Error al cargar porcentajes</div>');
        });
    }
});
</script>