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
                                        <label class="form-label">Fecha tentativa:</label>
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
                                    <div class="col-md-6 mb-3 offset-md-2">
                                        <label class="form-label">Número de Estación:</label>
                                        <select class="form-select" name="numero_estacion">
                                            <option value="" {{ empty($hidrante->numero_estacion) ? 'selected disabled' : '' }}>S/D</option>
                                            @foreach(['01', '02', '03', '04', '05', '06', '07', '08'] as $num)
                                                <option value="{{ $num }}" {{ $hidrante->numero_estacion == $num ? 'selected' : '' }}>{{ $num }}</option>
                                            @endforeach
                                        </select>
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
                                        <label class="form-label">Calle Principal:</label>
                                        <select class="form-select select2-search" name="id_calle" id="edit_id_calle">
                                            <option value="">Buscar nueva calle principal...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Calle actual: <span id="calle_actual">{{ $hidrante->calle ?: 'Sin definir' }}</span></small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Secundaria(Y Calle):</label>
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
                                        <label class="form-label">Colonia:</label>
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
                                            <option value="" {{ empty($hidrante->llave_hidrante) ? 'selected disabled' : '' }}>S/D</option>
                                            <option value="Pentagono" {{ $hidrante->llave_hidrante == 'Pentagono' ? 'selected' : '' }}>Pentagono</option>
                                            <option value="Cuadro" {{ $hidrante->llave_hidrante == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Presión de Agua:</label>
                                        <select class="form-select" name="presion_agua">
                                            <option value="" {{ empty($hidrante->presion_agua) ? 'selected disabled' : '' }}>S/D</option>
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
                                            <option value="" {{ empty($hidrante->llave_fosa) ? 'selected disabled' : '' }}>S/D</option>
                                            <option value="Cuadro" {{ $hidrante->llave_fosa == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                            <option value="Volante" {{ $hidrante->llave_fosa == 'Volante' ? 'selected' : '' }}>Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Conectado a Tubo de:</label>
                                        <select class="form-select" name="hidrante_conectado_tubo">
                                            <option value="" {{ empty($hidrante->hidrante_conectado_tubo) ? 'selected disabled' : '' }}>S/D</option>
                                            <option value="4'" {{ $hidrante->hidrante_conectado_tubo == "4'" ? 'selected' : '' }}>4'</option>
                                            <option value="6'" {{ $hidrante->hidrante_conectado_tubo == "6'" ? 'selected' : '' }}>6'</option>
                                            <option value="8'" {{ $hidrante->hidrante_conectado_tubo == "8'" ? 'selected' : '' }}>8'</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label class="form-label">Ubicación Fosa (N MTS.):</label>
                                        <input type="text" class="form-control" name="ubicacion_fosa"
                                               value="{{ $hidrante->ubicacion_fosa ?? '' }}" placeholder="Sin Definir">
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
                                        <select class="form-select" name="color">
                                            <option value="" {{ empty($hidrante->color) ? 'selected disabled' : '' }}>S/D</option>
                                            <option value="Rojo" {{ $hidrante->color == 'Rojo' ? 'selected' : '' }}>Rojo</option>
                                            <option value="Amarillo" {{ $hidrante->color == 'Amarillo' ? 'selected' : '' }}>Amarillo</option>
                                            <option value="Otro" {{ $hidrante->color == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Año:</label>
                                        <input type="number" class="form-control" name="anio"
                                               value="{{ $hidrante->anio ?? '' }}" placeholder="Sin Definir">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado Hidrante:</label>
                                        <select class="form-select" name="estado_hidrante">
                                            <option value="" {{ empty($hidrante->estado_hidrante) ? 'selected disabled' : '' }}>S/D</option>
                                            <option value="Servicio" {{ $hidrante->estado_hidrante == 'Servicio' ? 'selected' : '' }}>Servicio</option>
                                            <option value="Fuera de servicio" {{ $hidrante->estado_hidrante == 'Fuera de servicio' ? 'selected' : '' }}>Fuera de servicio</option>
                                            <option value="Solo Base" {{ $hidrante->estado_hidrante == 'Solo Base' ? 'selected' : '' }}>Solo Base</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca*:</label>
                                        <input type="text" class="form-control" name="marca"
                                               value="{{ $hidrante->marca ?? '' }}" placeholder="Sin Definir">
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
                                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Sin Definir">{{ $hidrante->observaciones ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">Oficial:</label>
                                        <input type="text" class="form-control" name="oficial"
                                               value="{{ $hidrante->oficial ?? '' }}" placeholder="Sin Definir">
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
</style>

<script>
$(document).ready(function() {
    const MODAL_ID = '#editarHidranteModal{{ $hidrante->id }}';
    
    // Configuración centralizada
    const CONFIG = {
        fields: ['calle', 'y_calle', 'colonia'],
        actions: {
            clear: {
                value: 'Sin definir',
                id: '',
                disabled: true
            },
            pending: {
                value: 'Pendiente',
                id: '0',
                disabled: true
            },
            enable: {
                disabled: false
            }
        },
        select2Options: {
            theme: 'bootstrap-5',
            width: '100%',
            language: {
                noResults: () => "No se encontraron resultados"
            }
        }
    };

    // Funciones principales
    function handleLocationField(field, action) {
        const select = $(`#edit_id_${field}`);
        const span = $(`#${field}_actual`);
        const form = select.closest('form');
        
        // Limpiar campos ocultos previos
        $(`input[name="${field}"][type="hidden"], input[name="id_${field}"][type="hidden"]`).remove();

        const config = CONFIG.actions[action];
        if (!config) return;

        select.val(null).trigger('change').prop('disabled', config.disabled);
        
        if (config.value) {
            span.text(config.value);
            
            // Agregar campos ocultos usando un solo jQuery object
            $('<input>', {
                type: 'hidden',
                name: field,
                value: config.value
            }).add($('<input>', {
                type: 'hidden',
                name: `id_${field}`,
                value: config.id
            })).appendTo(form);
        }
    }

    function initSelect2() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(`${MODAL_ID} .modal-body`),
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            placeholder: 'Comienza a escribir para buscar...',
            allowClear: true,
            minimumInputLength: 2, // Requiere mínimo 2 caracteres para buscar
            scrollAfterSelect: false, // Prevenir scroll automático
            position: function(pos, $el) {
                pos.top += 5; // Ajuste fino del posicionamiento
                return pos;
            }
        }).on('select2:open', function() {
            // Asegurar que el dropdown esté visible y enfocado
            setTimeout(function() {
                $('.select2-search__field').get(0).focus();
            }, 10);
        });
    }

    function initDateHandlers() {
        $('#edit_fecha_inspeccion').change(function() {
            const fechaInspeccion = new Date($(this).val());
            const fechaTentativa = new Date(fechaInspeccion);
            fechaTentativa.setMonth(fechaTentativa.getMonth() + 6);
            $('#edit_fecha_tentativa')
                .val(fechaTentativa.toISOString().split('T')[0])
                .removeClass('d-none');
            $('#generarFechaContainer').addClass('d-none');
        });

        // Manejador para el botón de generar fecha
        $('#edit_btnGenerarFecha').click(function() {
            const fechaInspeccion = new Date($('#edit_fecha_inspeccion').val());
            const fechaTentativa = new Date(fechaInspeccion);
            fechaTentativa.setMonth(fechaTentativa.getMonth() + 6);
            
            $('#edit_fecha_tentativa')
                .val(fechaTentativa.toISOString().split('T')[0])
                .removeClass('d-none');
            $(this).closest('#generarFechaContainer').addClass('d-none');
        });
    }

    // Inicialización de eventos
    function initEventHandlers() {
        // Ubicación pendiente
        $('#edit_ubicacionPendiente').change(function() {
            const action = $(this).is(':checked') ? 'pending' : 'enable';
            CONFIG.fields.forEach(field => handleLocationField(field, action));
        });

        // Botones de limpieza
        $('.clear-field').change(function() {
            if ($(this).is(':checked')) {
                handleLocationField($(this).data('field'), 'clear');
                setTimeout(() => $(this).prop('checked', false), 100);
            }
        });

        initDateHandlers();
    }

    // Inicialización del modal
    $(MODAL_ID)
        .on('shown.bs.modal', function() {
            initSelect2();
            $(window).trigger('resize');
        })
        .on('hidden.bs.modal', function() {
            $('.select2-search').select2('destroy');
        });

    initEventHandlers();
});
</script>
