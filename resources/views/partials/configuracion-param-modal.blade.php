<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="modal fade modal-params" id="configuracionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configuración de Tabla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Configuración de columnas y filtros</h6>
                                    <p class="mb-0">Seleccione las columnas que desea ver en la tabla y active los filtros que necesite.</p>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm d-none" id="limpiarFiltrosSeleccionados">
                                    <i class="bi bi-eraser me-1"></i> Limpiar filtros seleccionados
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="formConfiguracion">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Básica</h6>
                            <div class="mb-3">
                                <label class="form-label">Fecha de Inspección</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_fecha_inspeccion" data-column="fecha_inspeccion"
                                            {{ in_array('fecha_inspeccion', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_fecha_inspeccion">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_fecha_inspeccion" data-column="fecha_inspeccion"
                                            {{ in_array('fecha_inspeccion', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_fecha_inspeccion">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Número de Estación</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_numero_estacion" data-column="numero_estacion"
                                            {{ in_array('numero_estacion', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_numero_estacion">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_numero_estacion" data-column="numero_estacion"
                                            {{ in_array('numero_estacion', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_numero_estacion">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Ubicación</h6>
                            <div class="mb-3">
                                <label class="form-label">Calle</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_calle" data-column="calle"
                                            {{ in_array('calle', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_calle">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_calle" data-column="calle"
                                            {{ in_array('calle', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_calle">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Y Calle</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_y_calle" data-column="y_calle"
                                            {{ in_array('y_calle', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_y_calle">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_y_calle" data-column="y_calle"
                                            {{ in_array('y_calle', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_y_calle">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Colonia</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_colonia" data-column="colonia"
                                            {{ in_array('colonia', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_colonia">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_colonia" data-column="colonia"
                                            {{ in_array('colonia', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_colonia">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Características</h6>
                            <div class="mb-3">
                                <label class="form-label">Llave Hidrante</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_llave_hidrante" data-column="llave_hidrante"
                                            {{ in_array('llave_hidrante', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_llave_hidrante">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_llave_hidrante" data-column="llave_hidrante"
                                            {{ in_array('llave_hidrante', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_llave_hidrante">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Llave Fosa</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_llave_fosa" data-column="llave_fosa"
                                            {{ in_array('llave_fosa', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_llave_fosa">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_llave_fosa" data-column="llave_fosa"
                                            {{ in_array('llave_fosa', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_llave_fosa">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Presión de Agua</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_presion_agua" data-column="presion_agua"
                                            {{ in_array('presion_agua', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_presion_agua">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_presion_agua" data-column="presion_agua"
                                            {{ in_array('presion_agua', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_presion_agua">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_estado_hidrante" data-column="estado_hidrante"
                                            {{ in_array('estado_hidrante', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_estado_hidrante">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_estado_hidrante" data-column="estado_hidrante"
                                            {{ in_array('estado_hidrante', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_estado_hidrante">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Adicional</h6>
                            <div class="mb-3">
                                <label class="form-label">Marca</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_marca" data-column="marca"
                                            {{ in_array('marca', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_marca">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_marca" data-column="marca"
                                            {{ in_array('marca', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_marca">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Año</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_anio" data-column="anio"
                                            {{ in_array('anio', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_anio">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_anio" data-column="anio"
                                            {{ in_array('anio', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_anio">
                                            Activar filtro
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Oficial</label>
                                <div class="d-flex">
                                    <div class="form-check form-switch me-4">
                                        <input class="form-check-input column-toggle-view" type="checkbox" 
                                            id="toggleColumn_oficial" data-column="oficial"
                                            {{ in_array('oficial', $columnas ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleColumn_oficial">
                                            Ver en tabla
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-toggle-filter" type="checkbox" 
                                            id="toggleFilter_oficial" data-column="oficial"
                                            {{ in_array('oficial', $filtros_act ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="toggleFilter_oficial">
                                            Activar filtro
                                        </label>
                                    </div>
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
    let columnasVisibles = @json($columnas ?? []);
    let filtrosActivos = @json($filtros_act ?? []);
    
    filtrosActivos = filtrosActivos.map(filtro => {
        if (filtro.includes(':')) {
            return filtro.split(':')[0];
        }
        return filtro;
    });

    console.log("Columnas cargadas:", columnasVisibles);
    console.log("Filtros cargados:", filtrosActivos);
    
    // Verificar inicialmente si debemos mostrar el botón de limpiar filtros
    actualizarBotonLimpiarFiltros();

    // Manejar cambios en visibilidad de columnas
    $('.column-toggle-view').change(function() {
        const column = $(this).data('column');
        const isChecked = $(this).prop('checked');
        
        if (isChecked) {
            if (!columnasVisibles.includes(column)) {
                columnasVisibles.push(column);
            }
        } else {
            // No permitir desactivar si es la única columna visible
            if (columnasVisibles.length <= 1 && columnasVisibles.includes(column)) {
                alert('Debe haber al menos una columna visible en la tabla.');
                $(this).prop('checked', true);
                return;
            }
            
            columnasVisibles = columnasVisibles.filter(col => col !== column);
        }
    });
    
    // Manejar cambios en activación de filtros
    $('.column-toggle-filter').change(function() {
        const column = $(this).data('column');
        const isChecked = $(this).prop('checked');
        
        if (isChecked) {
            if (!filtrosActivos.includes(column)) {
                filtrosActivos.push(column);
            }
        } else {
            filtrosActivos = filtrosActivos.filter(f => f !== column);
        }
        
        // Actualizar el botón de limpiar filtros
        actualizarBotonLimpiarFiltros();
    });
    
    // Manejar clic en el botón de limpiar filtros
    $('#limpiarFiltrosSeleccionados').click(function() {
        // Desactivar todos los switches de filtros
        $('.column-toggle-filter').each(function() {
            $(this).prop('checked', false);
        });
        
        // Limpiar array de filtros activos
        filtrosActivos = [];
        
        // Ocultar el botón
        $(this).addClass('d-none');
    });
    
    // Función para actualizar la visibilidad del botón de limpiar filtros
    function actualizarBotonLimpiarFiltros() {
        const $btn = $('#limpiarFiltrosSeleccionados');
        if (filtrosActivos.length >= 2) {
            $btn.removeClass('d-none');
        } else {
            $btn.addClass('d-none');
        }
    }

    // Guardar configuración
    $('#guardarConfiguracion').click(function() {
        $.ajax({
            url: "{{ route('configuracion.save') }}",
            method: 'POST',
            data: { 
                configuracion: columnasVisibles,
                filtros_act: filtrosActivos  // Ahora solo enviamos los nombres de los campos
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