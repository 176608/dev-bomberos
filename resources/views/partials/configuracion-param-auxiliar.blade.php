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
            @if(empty($filtros_act))
                <div class="alert alert-info mb-0">
                    No hay filtros activos. Configure los filtros en el panel de configuración.
                </div>
            @else
                <div class="row">
                    @foreach($filtros_act as $campo)
                        @php
                            $nombreCampo = $headerNames[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
                            $valorSeleccionado = ''; 
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
            
            <div class="row">
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
                
                <div class="col-md-6">
                    @if(isset($porcentajes) && count($porcentajes) > 0)
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Porcentajes</h6>
                            </div>
                            <div class="card-body bg-light">
                                @foreach($porcentajes as $categoria => $porcentaje)
                                    <div class="mb-2 fw-bold">
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
    $('#toggleFilters').click(function() {
        const $icon = $(this).find('i');
        const $container = $('#filterContainer');
        
        $container.slideToggle();
        
        if ($icon.hasClass('bi-arrow-bar-up')) {
            $icon.removeClass('bi-arrow-bar-up').addClass('bi-arrow-bar-down');
            $(this).attr('title', 'Expandir panel');
        } else {
            $icon.removeClass('bi-arrow-bar-down').addClass('bi-arrow-bar-up');
            $(this).attr('title', 'Contraer panel');
        }
    });
    
    $('.aplicar-filtro').click(function() {
        const campo = $(this).data('campo');
        const valor = $(this).prev('.filtro-valor').val();
        
        actualizarFiltroActivo(campo, valor);
        
        const filtros = {};
        if (valor) {
            filtros[campo] = valor;
        }
        aplicarFiltrosATabla(filtros);
    });
    
    $('#aplicarTodosFiltros').click(function() {
        const filtros = {};
        
        $('.filtro-valor').each(function() {
            const campo = $(this).data('campo');
            const valor = $(this).val();
            if (valor) {
                filtros[campo] = valor;
            }
        });
        
        aplicarFiltrosATabla(filtros);
    });
    
    $('#limpiarFiltros').click(function() {
        $('.filtro-valor').val('');
        
        localStorage.removeItem('hidrantesFilterState');
        
        window.filtrosNoVisibles = {};
        
        aplicarFiltrosATabla({}, true);
        
        if (typeof mostrarToast === 'function') {
            mostrarToast('Filtros limpiados exitosamente');
        }
    });
    
    function actualizarFiltroActivo(campo, valor) {
        let filtrosActivos = @json($filtros_act) || [];
        
        filtrosActivos = filtrosActivos.map(filtro => {
            return typeof filtro === 'string' && filtro.includes(':') ? 
                filtro.split(':')[0] : filtro;
        });
        
        const filtroIndex = filtrosActivos.indexOf(campo);
        
        const valoresFiltros = {};
        valoresFiltros[campo] = valor;
        
        if (filtroIndex === -1) {
            filtrosActivos.push(campo);
        }
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
            },
            error: function(xhr) {
                console.error('Error al actualizar filtro:', xhr);
            }
        });
    }
    
    function aplicarFiltrosATabla(filtros, noScroll = false) {
        localStorage.setItem('hidrantesFilterState', JSON.stringify(filtros));
        
        const table = $('#hidrantesConfigTable').DataTable();
        if (table) {
            table.search('').columns().search('');
            
            const limpiandoTodo = Object.keys(filtros).length === 0;
            
            if (limpiandoTodo) {
                window.filtrosNoVisibles = {};
                
                try {
                    let ajaxUrl = new URL(table.ajax.url());
                    ajaxUrl.searchParams.delete('filtros_adicionales');
                    
                    table.ajax.url(ajaxUrl.toString()).load(function() {
                        if (noScroll) {
                            scrollToAuxContainer();
                        }
                    });
                    return; 
                } catch (error) {
                    console.error('Error al limpiar filtros:', error);
                }
            }
            
            const columnas = window.hidrantesTableConfig || [];
            const filtrosVisibles = {};
            const filtrosNoVisibles = {};
            
            Object.keys(filtros).forEach(campo => {
                const valor = filtros[campo];
                const columnIndex = columnas.indexOf(campo);
                
                if (campo === 'fecha_inspeccion' || 
                    (['calle', 'y_calle', 'colonia'].includes(campo) && valor === 'Con campo pendiente')) {
                    filtrosNoVisibles[campo] = valor;
                    return;
                }
                
                if (columnIndex >= 0) {
                    filtrosVisibles[campo] = valor;
                } else {
                    filtrosNoVisibles[campo] = valor;
                }
            });
            
          
            table.columns().search('').draw();
            
            Object.keys(filtrosVisibles).forEach(campo => {
                const valor = filtrosVisibles[campo];
                const columnIndex = columnas.indexOf(campo);
                if (columnIndex >= 0) {
                   
                    const realIndex = columnIndex + 3;
                    
                    console.log(`Aplicando filtro a columna ${campo} (índice ${realIndex}): "${valor}"`);
                    
                    if (realIndex < table.columns().nodes().length) {
                        if (valor === '') {
                            table.column(realIndex).search('^$|^N/A$', true, false);
                        } else {
                            table.column(realIndex).search(valor, false, false);
                        }
                    } else {
                        console.error(`La columna con índice ${realIndex} no existe en la tabla`);
                    }
                }
            });
            
            if (Object.keys(filtrosNoVisibles).length > 0) {
                window.filtrosNoVisibles = filtrosNoVisibles;
                
                try {
                    const ajaxUrl = new URL(table.ajax.url());
                    ajaxUrl.searchParams.set('filtros_adicionales', JSON.stringify(filtrosNoVisibles));
                    
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
             
                if (window.filtrosNoVisibles && Object.keys(window.filtrosNoVisibles).length > 0) {
                    window.filtrosNoVisibles = {};
                    try {
                        const ajaxUrl = new URL(table.ajax.url());
                        ajaxUrl.searchParams.delete('filtros_adicionales');
                        
                        table.ajax.url(ajaxUrl.toString()).load();
                    } catch (error) {
                        console.error('Error al limpiar filtros server-side:', error);
                        table.draw(); 
                    }
                } else {
                    table.draw();
                }
            }
        } else {
            console.error('La tabla DataTables no está inicializada correctamente');
        }
    }
    
    $('.cambiar-resumen').click(function() {
        const resumenId = $(this).data('resumen-id');
        
        $('.cambiar-resumen').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        
        $('#resumenHidrantesContainer').html(
            '<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div>' +
            '<div>Cargando resumen...</div></div>'
        );
        
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
        
        $.get(url, function(html) {
            $('#resumenHidrantesContainer').html(html);
            
            actualizarPorcentajes(resumenId);
        });
    });
    
    function actualizarPorcentajes(resumenId) {
        $('.col-md-6:nth-child(2)').html(
            '<div class="card"><div class="card-body text-center">' +
            '<div class="spinner-border spinner-border-sm text-primary"></div> ' +
            'Actualizando porcentajes...</div></div>'
        );
        
        $.get("{{ route('capturista.panel-auxiliar') }}", { 
            modo: 'resumen',
            tipo: resumenId
        }, function(html) {
            const $newPanel = $(html);
            const $newPorcentajes = $newPanel.find('.col-md-6:nth-child(2)').html();
            
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