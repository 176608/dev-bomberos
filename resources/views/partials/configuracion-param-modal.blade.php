<!-- Modal para configuración de tabla -->
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
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="fecha_inspeccion" id="col_fecha_inspeccion"
                                    {{ in_array('fecha_inspeccion', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_fecha_inspeccion">Fecha de Inspección</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="fecha_tentativa" id="col_fecha_tentativa"
                                    {{ in_array('fecha_tentativa', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_fecha_tentativa">Fecha Tentativa</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="numero_estacion" id="col_numero_estacion"
                                    {{ in_array('numero_estacion', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_numero_estacion">Número de Estación</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Ubicación</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="calle" id="col_calle"
                                    {{ in_array('calle', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_calle">Calle Principal</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="y_calle" id="col_y_calle"
                                    {{ in_array('y_calle', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_y_calle">Calle Secundaria</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="colonia" id="col_colonia"
                                    {{ in_array('colonia', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_colonia">Colonia</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Características</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="llave_hidrante" id="col_llave_hidrante"
                                    {{ in_array('llave_hidrante', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_llave_hidrante">Llave Hidrante</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="presion_agua" id="col_presion_agua"
                                    {{ in_array('presion_agua', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_presion_agua">Presión de Agua</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="color" id="col_color"
                                    {{ in_array('color', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_color">Color</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="estado_hidrante" id="col_estado_hidrante"
                                    {{ in_array('estado_hidrante', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_estado_hidrante">Estado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Adicional</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="marca" id="col_marca"
                                    {{ in_array('marca', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_marca">Marca</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="anio" id="col_anio"
                                    {{ in_array('anio', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_anio">Año</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="oficial" id="col_oficial"
                                    {{ in_array('oficial', $columnas ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="col_oficial">Oficial</label>
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

    // Cargar configuración al abrir el modal
    $('#configuracionModal').on('show.bs.modal', function () {
        $.get("{{ route('configuracion.get') }}")
            .done(function(response) {
                $('.column-toggle').prop('checked', false);
                if (response.configuracion && Array.isArray(response.configuracion) && response.configuracion.length > 0) {
                    response.configuracion.forEach(function(columnName) {
                        $(`#col_${columnName}`).prop('checked', true);
                    });
                } else {
                    const defaultColumns = ['calle', 'y_calle'];
                    defaultColumns.forEach(function(columnName) {
                        $(`#col_${columnName}`).prop('checked', true);
                    });
                }
                $('#spinnerConfiguracion').addClass('d-none');
            })
            .fail(function(error) {
                console.error('Error al cargar configuración desde partial:', error);
                alert('Error al cargar la configuración desde partial');
                $('#spinnerConfiguracion').addClass('d-none');
            });
    });

    // Guardar configuración
    $('#guardarConfiguracion').click(function() {
        const configuracion = $('.column-toggle:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: "{{ route('configuracion.save') }}",
            method: 'POST',
            data: { configuracion: configuracion },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const modalElement = document.getElementById('configuracionModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                    //location.reload();
                    window.location = window.location.pathname + '?mostrar_tabla=1';
                }
            },
            error: function(xhr) {
                console.error('Error al guardar:', xhr);
                alert('Error al guardar la configuración desde partial');
            }
        });
    });
});
</script>