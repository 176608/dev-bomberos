<div class="modal fade modal-params" id="configuracionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configuración de Tabla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfiguracion">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Básica</h6>
                            <div class="mb-3">
                                <label class="d-block mb-2">Fecha de Inspección</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('fecha_inspeccion', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="fecha_inspeccion">
                                        {{ in_array('fecha_inspeccion', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('fecha_inspeccion:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="fecha_inspeccion">
                                        {{ in_array('fecha_inspeccion:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Número de Estación</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('numero_estacion', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="numero_estacion">
                                        {{ in_array('numero_estacion', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('numero_estacion:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="numero_estacion">
                                        {{ in_array('numero_estacion:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Ubicación</h6>
                            <div class="mb-3">
                                <label class="d-block mb-2">Calle</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('calle', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="calle">
                                        {{ in_array('calle', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('calle:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="calle">
                                        {{ in_array('calle:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Y Calle</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('y_calle', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="y_calle">
                                        {{ in_array('y_calle', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('y_calle:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="y_calle">
                                        {{ in_array('y_calle:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Colonia</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('colonia', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="colonia">
                                        {{ in_array('colonia', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('colonia:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="colonia">
                                        {{ in_array('colonia:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Características</h6>
                            <div class="mb-3">
                                <label class="d-block mb-2">Llave Hidrante</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('llave_hidrante', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="llave_hidrante">
                                        {{ in_array('llave_hidrante', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('llave_hidrante:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="llave_hidrante">
                                        {{ in_array('llave_hidrante:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Llave Fosa</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('llave_fosa', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="llave_fosa">
                                        {{ in_array('llave_fosa', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('llave_fosa:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="llave_fosa">
                                        {{ in_array('llave_fosa:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Presión de Agua</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('presion_agua', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="presion_agua">
                                        {{ in_array('presion_agua', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('presion_agua:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="presion_agua">
                                        {{ in_array('presion_agua:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Estado</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('estado_hidrante', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="estado_hidrante">
                                        {{ in_array('estado_hidrante', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('estado_hidrante:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="estado_hidrante">
                                        {{ in_array('estado_hidrante:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Adicional</h6>
                            <div class="mb-3">
                                <label class="d-block mb-2">Marca</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('marca', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="marca">
                                        {{ in_array('marca', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('marca:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="marca">
                                        {{ in_array('marca:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Año</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('anio', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="anio">
                                        {{ in_array('anio', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('anio:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="anio">
                                        {{ in_array('anio:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block mb-2">Oficial</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm column-toggle-view {{ in_array('oficial', $columnas ?? []) ? 'btn-primary' : 'btn-outline-primary' }}" 
                                        data-column="oficial">
                                        {{ in_array('oficial', $columnas ?? []) ? 'Remover de la tabla' : 'Ver en la tabla' }}
                                    </button>
                                    <button type="button" class="btn btn-sm column-toggle-filter {{ in_array('oficial:0', $filtros_act ?? []) ? 'btn-success' : 'btn-outline-success' }}"
                                        data-column="oficial">
                                        {{ in_array('oficial:0', $filtros_act ?? []) ? 'Desactivar filtro' : 'Activar filtro' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarConfiguracion">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $('#configuracionModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').removeAttr('style');
    });

    // Variables para almacenar el estado actual
    let columnasVisibles = [];
    let filtrosActivos = [];

    // Cargar configuración al abrir el modal
    $('#configuracionModal').on('show.bs.modal', function () {
        $.get("{{ route('configuracion.get') }}")
            .done(function(response) {
                // Limpiar estados anteriores
                columnasVisibles = response.configuracion || [];
                filtrosActivos = response.filtros_act || [];
                
                // Actualizar UI para botones de visualización
                $('.column-toggle-view').each(function() {
                    const column = $(this).data('column');
                    const isVisible = columnasVisibles.includes(column);
                    
                    $(this)
                        .toggleClass('btn-primary', isVisible)
                        .toggleClass('btn-outline-primary', !isVisible)
                        .text(isVisible ? 'Remover de la tabla' : 'Ver en la tabla');
                });
                
                // Actualizar UI para botones de filtro
                $('.column-toggle-filter').each(function() {
                    const column = $(this).data('column');
                    const filterKey = column + ':0';
                    const isActive = filtrosActivos.includes(filterKey);
                    
                    $(this)
                        .toggleClass('btn-success', isActive)
                        .toggleClass('btn-outline-success', !isActive)
                        .text(isActive ? 'Desactivar filtro' : 'Activar filtro');
                });
                
                $('#spinnerConfiguracion').addClass('d-none');
            })
            .fail(function(error) {
                console.error('Error al cargar configuración:', error);
                alert('Error al cargar la configuración');
                $('#spinnerConfiguracion').addClass('d-none');
            });
    });

    // Manejar clic en botones de visualización de columnas
    $(document).on('click', '.column-toggle-view', function() {
        const column = $(this).data('column');
        const isCurrentlyVisible = columnasVisibles.includes(column);
        
        if (isCurrentlyVisible) {
            // No permitir desactivar si es la única columna visible
            if (columnasVisibles.length <= 1) {
                alert('Debe haber al menos una columna visible en la tabla.');
                return;
            }
            
            // Remover de las columnas visibles
            columnasVisibles = columnasVisibles.filter(col => col !== column);
            $(this).removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).text('Ver en la tabla');
        } else {
            // Agregar a las columnas visibles
            columnasVisibles.push(column);
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $(this).text('Remover de la tabla');
        }
    });
    
    // Manejar clic en botones de filtros
    $(document).on('click', '.column-toggle-filter', function() {
        const column = $(this).data('column');
        const filterKey = column + ':0';
        const isCurrentlyActive = filtrosActivos.includes(filterKey);
        
        if (isCurrentlyActive) {
            // Desactivar filtro
            filtrosActivos = filtrosActivos.filter(filter => filter !== filterKey);
            $(this).removeClass('btn-success').addClass('btn-outline-success');
            $(this).text('Activar filtro');
        } else {
            // Activar filtro
            filtrosActivos.push(filterKey);
            $(this).removeClass('btn-outline-success').addClass('btn-success');
            $(this).text('Desactivar filtro');
        }
    });

    // Guardar configuración
    $('#guardarConfiguracion').click(function() {
        $.ajax({
            url: "{{ route('configuracion.save') }}",
            method: 'POST',
            data: { 
                configuracion: columnasVisibles,
                filtros_act: filtrosActivos 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const modalElement = document.getElementById('configuracionModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                    window.location = window.location.pathname + '?mostrar_tabla=1';
                }
            },
            error: function(xhr) {
                console.error('Error al guardar:', xhr);
                alert('Error al guardar la configuración');
            }
        });
    });
});
</script>