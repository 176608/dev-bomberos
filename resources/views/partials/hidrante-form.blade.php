<div class="modal fade modal-edit" id="editarHidranteModal{{ $hidrante->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('hidrantes.update', $hidrante->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Hidrante #{{ $hidrante->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(201, 201, 201, 0.8);">
                    <!-- Primera Sección - Información Básica -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Información Básica
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Inspección:</label>
                                        <input type="date" class="form-control" name="fecha_inspeccion" 
                                               id="edit_fecha_inspeccion"
                                               value="{{ $hidrante->fecha_inspeccion ? date('Y-m-d', strtotime($hidrante->fecha_inspeccion)) : date('Y-m-d') }}" 
                                               required>
                                        <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label">Fecha tentativa:</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input clear-field" type="checkbox" 
                                                       id="clear_fecha_tentativa" data-field="fecha_tentativa" disabled>
                                                <label class="form-check-label">Limpiar</label>
                                            </div>
                                        </div>
                                        
                                        <div id="fecha_tentativa_container">
                                            @php
                                                $invalidDate = !$hidrante->fecha_tentativa || $hidrante->fecha_tentativa->format('Y-m-d') === '0000-00-00';
                                            @endphp
                                            
                                            @if($invalidDate)
                                                <div class="d-grid gap-2 mb-2" id="generarFechaContainer">
                                                    <button type="button" class="btn btn-primary" id="edit_btnGenerarFecha">
                                                        Generar fecha tentativa
                                                    </button>
                                                </div>
                                            @endif
                                            
                                            <input type="date" class="form-control {{ $invalidDate ? 'd-none' : '' }}" 
                                                   name="fecha_tentativa" id="edit_fecha_tentativa"
                                                   value="{{ (!$invalidDate && $hidrante->fecha_tentativa) ? $hidrante->fecha_tentativa->format('Y-m-d') : '' }}">
                                        </div>
                                        <small class="form-text text-muted">Ajustable manualmente</small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Estación:</label>
                                        <select class="form-select" name="numero_estacion">
                                            @foreach(['01', '02', '03', '04', '05', '06', '07', '08'] as $num)
                                                <option value="{{ $num }}" {{ $hidrante->numero_estacion == $num ? 'selected' : '' }}>{{ $num }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Hidrante:</label>
                                        <input type="number" class="form-control" name="numero_hidrante" value="{{ $hidrante->numero_hidrante }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Segunda Sección - Ubicación -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <span>Ubicación</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_ubicacionPendiente">
                                    <label class="form-check-label text-white">Información pendiente de capturar</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label">Calle Principal:</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input clear-field" type="checkbox" 
                                                       id="clear_calle" data-field="calle">
                                                <label class="form-check-label">Limpiar campo</label>
                                            </div>
                                        </div>
                                        <select class="form-select select2-search" name="id_calle" id="edit_id_calle">
                                            <option value="">Buscar nueva calle principal...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="d-flex align-items-center mt-1">
                                            <small class="form-text text-muted flex-grow-1">
                                                Calle actual: <span id="calle_actual">{{ $hidrante->calle ?: 'Sin definir' }}</span>
                                            </small>
                                            @if($hidrante->calle && !in_array($hidrante->calle, ['', 'Sin definir', 'Pendiente']))
                                                <button type="button" class="btn btn-danger btn-sm ms-2 clear-location" data-field="calle">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label">Calle Secundaria(Y Calle):</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input clear-field" type="checkbox" 
                                                       id="clear_y_calle" data-field="y_calle">
                                                <label class="form-check-label">Limpiar campo</label>
                                            </div>
                                        </div>
                                        <select class="form-select select2-search" name="id_y_calle" id="edit_id_y_calle">
                                            <option value="">Buscar nueva calle secundaria...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Calle secundaria actual: <span id="y_calle_actual">{{ $hidrante->y_calle ?: 'Sin definir' }}</span></small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label">Colonia:</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input clear-field" type="checkbox" 
                                                       id="clear_colonia" data-field="colonia">
                                                <label class="form-check-label">Limpiar campo</label>
                                            </div>
                                        </div>
                                        <select class="form-select select2-search" name="id_colonia" id="edit_id_colonia">
                                            <option value="">Buscar nueva colonia...</option>
                                            @foreach($colonias as $colonia)
                                                <option value="{{ $colonia->IDKEY }}" {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                                    {{ $colonia->NOMBRE }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Colonia actual: <span id="colonia_actual">{{ $hidrante->colonia ?: 'Sin definir' }}</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Tercera Sección - Características Técnicas -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Características Técnicas
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Llave Hidrante:</label>
                                        <select class="form-select" name="llave_hidrante">
                                            <option value="Pentagono" {{ $hidrante->llave_hidrante == 'Pentagono' ? 'selected' : '' }}>Pentagono</option>
                                            <option value="Cuadro" {{ $hidrante->llave_hidrante == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Presión de Agua:</label>
                                        <select class="form-select" name="presion_agua">
                                            <option value="Mala" {{ $hidrante->presion_agua == 'Mala' ? 'selected' : '' }}>Mala</option>
                                            <option value="Buena" {{ $hidrante->presion_agua == 'Buena' ? 'selected' : '' }}>Buena</option>
                                            <option value="Sin agua" {{ $hidrante->presion_agua == 'Sin agua' ? 'selected' : '' }}>Sin agua</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Llave Fosa:</label>
                                        <select class="form-select" name="llave_fosa">
                                            <option value="Cuadro" {{ $hidrante->llave_fosa == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                            <option value="Volante" {{ $hidrante->llave_fosa == 'Volante' ? 'selected' : '' }}>Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Conectado a Tubo de:</label>
                                        <select class="form-select" name="hidrante_conectado_tubo">
                                            <option value="4'" {{ $hidrante->hidrante_conectado_tubo == '4\'' ? 'selected' : '' }}>4'</option>
                                            <option value="6'" {{ $hidrante->hidrante_conectado_tubo == '6\'' ? 'selected' : '' }}>6'</option>
                                            <option value="8'" {{ $hidrante->hidrante_conectado_tubo == '8\'' ? 'selected' : '' }}>8'</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label class="form-label">Ubicación Fosa (N MTS.):</label>
                                        <input type="text" class="form-control" name="ubicacion_fosa" value="{{ $hidrante->ubicacion_fosa }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Cuarta Sección - Estado y Características -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white">
                                Estado y Características
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Color:</label>
                                        <select class="form-select" name="color" required>
                                            <option value="Rojo" {{ $hidrante->color == 'Rojo' ? 'selected' : '' }}>Rojo</option>
                                            <option value="Amarillo" {{ $hidrante->color == 'Amarillo' ? 'selected' : '' }}>Amarillo</option>
                                            <option value="Otro"{{ $hidrante->color == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Año:</label>
                                        <input type="number" class="form-control" name="anio" value="{{ $hidrante->anio }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado Hidrante:</label>
                                        <select class="form-select" name="estado_hidrante">
                                            <option value="Servicio" {{ $hidrante->estado_hidrante == 'Servicio' ? 'selected' : '' }}>Servicio</option>
                                            <option value="Fuera de servicio" {{ $hidrante->estado_hidrante == 'Fuera de servicio' ? 'selected' : '' }}>Fuera de servicio</option>
                                            <option value="Solo Base" {{ $hidrante->estado_hidrante == 'Solo Base' ? 'selected' : '' }}>Solo Base</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca*:</label>
                                        <input type="text" class="form-control" name="marca" value="{{ $hidrante->marca }}" required>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Quinta Sección - Información Adicional -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-secondary text-white">
                                Información Adicional
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Observaciones:</label>
                                        <textarea class="form-control" name="observaciones" rows="3">{{ $hidrante->observaciones }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">Oficial:</label>
                                        <input type="text" class="form-control" name="oficial" value="{{ $hidrante->oficial }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
}

.select2-container--bootstrap-5 .select2-search__field:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #0d6efd;
    color: white;
}

.select2-container--bootstrap-5 .select2-results__option {
    padding: 6px 12px;
}

.modal .select2-container {
    z-index: 1056;
}

.select2-container--bootstrap-5.select2-container--open {
    z-index: 1060 !important;
}

.select2-container--bootstrap-5 .select2-dropdown {
    border-color: #dee2e6;
    border-radius: 0.375rem;
}

.select2-container--bootstrap-5 .select2-dropdown--below {
    margin-top: 2px;
}

select2.select2-container {
    width: 100% !important;
}

.modal-body .select2-container {
    display: block;
}

.clear-location {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1;
}

.clear-location i {
    font-size: 0.875rem;
}
</style>

<script>
$(document).ready(function() {
    function initEditSelect2() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#editarHidranteModal{{ $hidrante->id }} .modal-body'),
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            allowClear: true,
            minimumInputLength: 2,
            scrollAfterSelect: false,
            position: function(pos, $el) {
                pos.top += 5;
                return pos;
            }
        }).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').get(0).focus();
            }, 10);
        });
    }

    // Inicializar Select2 cuando se abre el modal
    $('#editarHidranteModal{{ $hidrante->id }}').on('shown.bs.modal', function () {
        initEditSelect2();
        $(window).trigger('resize');
    });

    // Limpiar Select2 cuando se cierra el modal
    $('#editarHidranteModal{{ $hidrante->id }}').on('hidden.bs.modal', function () {
        $('.select2-search').select2('destroy');
    });

    // Manejar limpieza de campos
    $('.clear-field').change(function() {
        const field = $(this).data('field');
        const isChecked = $(this).is(':checked');
        const select = $(`#edit_id_${field}`);
        
        if (isChecked) {
            select.val(null).trigger('change');
            select.prop('disabled', true);
            $(`#${field}_actual`).text('Sin definir');
            
            // Agregar campo oculto para el valor "Sin definir"
            $(`<input type="hidden" name="${field}" value="Sin definir">`).insertAfter(select);
        } else {
            select.prop('disabled', false);
            $(`input[name="${field}"][type="hidden"]`).remove();
        }
    });

    // Manejar fecha de inspección y tentativa
    $('#edit_fecha_inspeccion').change(function() {
        const fechaInspeccion = new Date($(this).val());
        const fechaTentativa = new Date(fechaInspeccion);
        fechaTentativa.setMonth(fechaTentativa.getMonth() + 6);
        
        $('#edit_fecha_tentativa').val(fechaTentativa.toISOString().split('T')[0]);
    });

    // Función para mostrar los botones de plazo
    function showPlazoButtons() {
        const botonesHtml = `
            <div class="btn-group w-100 mb-2" id="edit_opcionesPlazo">
                <button type="button" class="btn btn-outline-primary" data-plazo="corto">Corto plazo</button>
                <button type="button" class="btn btn-outline-primary" data-plazo="largo">Largo plazo</button>
            </div>
        `;
        $('#generarFechaContainer').html(botonesHtml);
    }

    // Función para generar fecha basada en el plazo
    function generateDate(plazo) {
        const fechaHoy = new Date();
        const fechaTentativa = new Date(fechaHoy);
        
        if (plazo === 'corto') {
            fechaTentativa.setMonth(fechaTentativa.getMonth() + 6);
        } else {
            fechaTentativa.setFullYear(fechaTentativa.getFullYear() + 1);
        }

        return fechaTentativa.toISOString().split('T')[0];
    }

    // Manejador para el botón de generar fecha
    $(document).on('click', '#edit_btnGenerarFecha', function(e) {
        e.preventDefault();
        showPlazoButtons();
    });

    // Función para verificar si la fecha es válida
    function isValidDate(date) {
        return date && date !== '0000-00-00';
    }

    // Función para habilitar/deshabilitar y resetear el switch de limpieza
    function toggleCleanSwitch(enable) {
        const switchEl = $('#clear_fecha_tentativa');
        switchEl.prop('disabled', !enable);
        switchEl.prop('checked', false); // Asegura que el switch esté en OFF
    }

    // Verificar estado inicial del switch basado en la fecha precargada
    $(function() {
        const fechaTentativa = $('#edit_fecha_tentativa').val();
        toggleCleanSwitch(isValidDate(fechaTentativa));
    });

    // Actualizar el manejador de botones de plazo
    $(document).on('click', '#edit_opcionesPlazo button', function(e) {
        e.preventDefault();
        const plazo = $(this).data('plazo');
        const fechaFormateada = generateDate(plazo);
        
        // Mostrar el input date con la fecha generada
        const inputDate = $('#edit_fecha_tentativa');
        inputDate.val(fechaFormateada)
                .removeClass('d-none');
        
        // Remover los botones de plazo
        $('#generarFechaContainer').remove();
        
        // Habilitar el switch de limpieza y asegurar que esté en OFF
        toggleCleanSwitch(true);
    });

    // Actualizar el manejador para limpiar fecha
    $('#clear_fecha_tentativa').change(function() {
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            // Ocultar input date y limpiar valor
            $('#edit_fecha_tentativa').addClass('d-none').val('');
            
            // Recrear botón de generar fecha
            const btnHtml = `
                <div class="d-grid gap-2 mb-2" id="generarFechaContainer">
                    <button type="button" class="btn btn-primary" id="edit_btnGenerarFecha">
                        Generar fecha tentativa
                    </button>
                </div>
            `;
            $('#fecha_tentativa_container').prepend(btnHtml);
            
            // Agregar campo oculto con valor 0000-00-00
            $('<input>').attr({
                type: 'hidden',
                name: 'fecha_tentativa',
                value: '0000-00-00'
            }).appendTo('#fecha_tentativa_container');
            
            // Deshabilitar el switch y ponerlo en OFF después de un breve delay
            setTimeout(() => {
                toggleCleanSwitch(false);
            }, 100);
        }
    });

    // Agregar el manejador para el switch de ubicación pendiente
    $('#edit_ubicacionPendiente').change(function() {
        const isChecked = $(this).is(':checked');
        const selects = $('#edit_id_calle, #edit_id_y_calle, #edit_id_colonia');
        const fields = [
            { name: 'calle', id: 'edit_id_calle', span: 'calle_actual' },
            { name: 'y_calle', id: 'edit_id_y_calle', span: 'y_calle_actual' },
            { name: 'colonia', id: 'edit_id_colonia', span: 'colonia_actual' }
        ];
        
        selects.prop('disabled', isChecked);
        
        // Remover campos ocultos previos
        fields.forEach(field => {
            $(`input[name="${field.name}"][type="hidden"]`).remove();
            $(`input[name="${field.id}"][type="hidden"]`).remove();
        });
        
        if (isChecked) {
            // Limpiar selects
            selects.val(null).trigger('change');
            
            // Actualizar textos y agregar campos ocultos
            fields.forEach(field => {
                $(`#${field.span}`).text('Pendiente');
                
                $('<input>').attr({
                    type: 'hidden',
                    name: field.name,
                    value: 'Pendiente'
                }).appendTo(this.form);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: field.id.replace('edit_', ''),
                    value: '0'
                \][]}).appendTo(this.form);
            });
        }
    });

    // Modificar el manejador de submit
    $(this.form).submit(function(e) {
        const fields = ['calle', 'y_calle', 'colonia'];
        const pendienteChecked = $('#edit_ubicacionPendiente').is(':checked');
        
        if (!pendienteChecked) {
            fields.forEach(field => {
                const selectValue = $(`#edit_id_${field}`).val();
                if (!selectValue) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: field,
                        value: 'Sin definir'
                    }).appendTo(this);
                    
                    $('<input>').attr({
                        type: 'hidden',
                        name: `id_${field}`,
                        value: ''
                    }).appendTo(this);
                }
            });
        }
    });

    // Función para manejar el estado de los campos de ubicación
    function handleLocationFields(isPending) {
        const fields = [
            { name: 'calle', id: 'edit_id_calle', span: 'calle_actual' },
            { name: 'y_calle', id: 'edit_id_y_calle', span: 'y_calle_actual' },
            { name: 'colonia', id: 'edit_id_colonia', span: 'colonia_actual' }
        ];
        
        fields.forEach(field => {
            const select = $(`#${field.id}`);
            const span = $(`#${field.span}`);
            
            // Limpiar campos ocultos previos
            $(`input[name="${field.name}"][type="hidden"]`).remove();
            
            if (isPending) {
                // Caso: Información pendiente
                select.val(null).trigger('change').prop('disabled', true);
                span.text('Pendiente');
                $('<input>').attr({
                    type: 'hidden',
                    name: field.name,
                    value: 'Pendiente'
                }).appendTo(select.closest('form'));
                
                // Ocultar botón de limpiar si existe
                select.closest('.mb-3').find('.clear-location').hide();
            } else if (!select.val()) {
                // Caso: Select vacío
                span.text('Sin definir');
                $('<input>').attr({
                    type: 'hidden',
                    name: field.name,
                    value: 'Sin definir'
                }).appendTo(select.closest('form'));
            }
        });
    }

    // Manejador para el switch de información pendiente
    $('#edit_ubicacionPendiente').change(function() {
        const isChecked = $(this).is(':checked');
        handleLocationFields(isChecked);
    });

    // Manejador para los botones de limpiar ubicación
    $('.clear-location').click(function() {
        const field = $(this).data('field');
        const select = $(`#edit_id_${field}`);
        const span = $(`#${field}_actual`);
        
        // Limpiar select y establecer "Sin definir"
        select.val(null).trigger('change');
        span.text('Sin definir');
        
        // Agregar campo oculto
        $(`input[name="${field}"][type="hidden"]`).remove();
        $('<input>').attr({
            type: 'hidden',
            name: field,
            value: 'Sin definir'
        }).appendTo(select.closest('form'));
        
        // Ocultar el botón de limpiar
        $(this).hide();
    });

    // Manejador para cambios en los select
    $('.select2-search').on('change', function() {
        const field = $(this).attr('id').replace('edit_id_', '');
        const value = $(this).val();
        const span = $(`#${field}_actual`);
        const clearBtn = $(this).closest('.mb-3').find('.clear-location');
        
        if (value) {
            // Mostrar botón de limpiar
            clearBtn.show();
            // Remover campo oculto si existe
            $(`input[name="${field}"][type="hidden"]`).remove();
        } else {
            // Establecer "Sin definir" si no hay valor seleccionado
            span.text('Sin definir');
            clearBtn.hide();
            
            if (!$('#edit_ubicacionPendiente').is(':checked')) {
                $('<input>').attr({
                    type: 'hidden',
                    name: field,
                    value: 'Sin definir'
                }).appendTo($(this).closest('form'));
            }
        }
    });

    // Modificar el submit del formulario
    $('form').submit(function() {
        const isPending = $('#edit_ubicacionPendiente').is(':checked');
        if (!isPending) {
            $('.select2-search').each(function() {
                if (!$(this).val()) {
                    const field = $(this).attr('id').replace('edit_id_', '');
                    $('<input>').attr({
                        type: 'hidden',
                        name: field,
                        value: 'Sin definir'
                    }).appendTo(this.form);
                }
            });
        }
    });
});
</script>
