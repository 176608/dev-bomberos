<div class="modal fade modal-create" id="crearHidranteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('hidrantes.store') }}" method="POST" id="formCrearHidrante">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Hidrante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(201, 201, 201, 0.8);">
                    <!-- Campo fecha_inspeccion -->
                    <div class="row">
                        
                        <div class="card text-center p-0">

                            <div class="card-header bg-primary text-white">
                                Información Básica
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Inspección:</label>
                                        <input type="date" class="form-control" name="fecha_inspeccion" 
                                            id="fecha_inspeccion"
                                            value="{{ date('Y-m-d') }}" required>
                                            <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha tentativa de Mantenimiento:</label>
                                        <div class="d-grid gap-2 mb-2">
                                            <button type="button" class="btn btn-primary" id="btnGenerarFecha">
                                                Generar fecha tentativa
                                            </button>
                                        </div>
                                        <div class="btn-group d-none w-100 mb-2" id="opcionesPlazo">
                                            <button type="button" class="btn btn-outline-primary" data-plazo="corto">Corto plazo</button>
                                            <button type="button" class="btn btn-outline-primary" data-plazo="largo">Largo plazo</button>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_tentativa" 
                                            id="fecha_tentativa" value="{{ date('Y-m-d') }}">
                                            <small class="form-text text-muted">Ajustable manualmente</small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Estación:</label>
                                        <select class="form-select" name="numero_estacion" required>
                                            <option value="" selected>Seleccione estación...</option>
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Hidrante:</label>
                                        <input type="number" class="form-control" name="numero_hidrante" placeholder="Ejemplo: 5842" required>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <span>Ubicación</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ubicacionPendiente">
                                    <label class="form-check-label text-white" for="ubicacionPendiente">
                                        Información pendiente de capturar
                                    </label>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Principal:</label>
                                        <select class="form-select select2-search" name="id_calle" id="id_calle">
                                            <option value="">Buscar calle principal...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}">{{ $calle->Nomvial }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Secundaria(Y Calle):</label>
                                        <select class="form-select select2-search" name="id_y_calle" id="id_y_calle">
                                            <option value="">Buscar calle secundaria...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}">{{ $calle->Nomvial }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">Colonia:</label>
                                        <select class="form-select select2-search" name="id_colonia" id="id_colonia">
                                            <option value="">Buscar colonia...</option>
                                            @foreach($colonias as $colonia)
                                                <option value="{{ $colonia->IDKEY }}">{{ $colonia->NOMBRE }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                    </div>

                    <hr class="my-4">

                    
                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-primary text-white">
                                Características Técnicas
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Llave Hidrante:</label>
                                        <select class="form-select" name="llave_hidrante" required>
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="Pentagono">Pentágono</option>
                                            <option value="Cuadro">Cuadro</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Presión de Agua:</label>
                                        <select class="form-select" name="presion_agua" required>
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="Mala">Mala</option>
                                            <option value="Buena">Buena</option>
                                            <option value="Sin agua">Sin agua</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Llave Fosa:</label>
                                        <select class="form-select" name="llave_fosa" required>
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="Cuadro">Cuadro</option>
                                            <option value="Volante">Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Conectado a Tubo de:</label>
                                        <select class="form-select" name="hidrante_conectado_tubo" required>
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="4'">4'</option>
                                            <option value="6'">6'</option>
                                            <option value="8'">8'</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label class="form-label">Ubicación Fosa:</label>
                                        <input type="text" class="form-control" name="ubicacion_fosa" placeholder="(N MTS.) Ejemplo: 5 MTS." required>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-success text-white">
                                Estado y Características
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Color:</label>
                                        <select class="form-select" name="color" required>
                                            <option value="Rojo" selected>Rojo</option>
                                            <option value="Amarillo">Amarillo</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Año:</label>
                                        <input type="number" class="form-control" name="anio" placeholder="Año de inicio del servicio del hidrante" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado Hidrante:</label>
                                        <select class="form-select" name="estado_hidrante" required>
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="Servicio">Servicio</option>
                                            <option value="Fuera de servicio">Fuera de servicio</option>
                                            <option value="Solo Base">Solo Base</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca*:</label>
                                        <input type="text" class="form-control" name="marca" placeholder="Ejemplo: MUELLER" required>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-secondary text-white">
                                Información Adicional
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Observaciones:</label>
                                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Escriba observaciones aquí..."></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">Oficial:</label>
                                        <input type="text" class="form-control" name="oficial" placeholder="Nombre del oficial responsable" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Hidrante</button>
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

/* Ajustes para el posicionamiento del dropdown */
.select2-container--bootstrap-5.select2-container--open {
    z-index: 1060 !important; /* Mayor que el z-index del modal */
}

.select2-container--bootstrap-5 .select2-dropdown {
    border-color: #dee2e6;
    border-radius: 0.375rem;
}

/* Asegurar que el dropdown esté contenido correctamente */
.select2-container--bootstrap-5 .select2-dropdown--below {
    margin-top: 2px;
}

/* Corregir el ancho del contenedor */
.select2.select2-container {
    width: 100% !important;
}

/* Ajustar el padding del contenedor del modal */
.modal-body .select2-container {
    display: block;
}
</style>

<script>
$(document).ready(function() {
    console.log("Hidrante create script loaded");
    
    function initSelect2Modal() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#crearHidranteModal .modal-body'), // Cambio importante aquí
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
            minimumInputLength: 2,
            scrollAfterSelect: false, // Prevenir scroll automático
            position: function(pos, $el) {
                pos.top += 5; // Ajuste fino del posicionamiento
                return pos;
            }
        }).on('select2:open', function() {
            // Asegurar que el dropdown esté visible
            setTimeout(function() {
                $('.select2-search__field').get(0).focus();
            }, 10);
        });
    }

    // Reinicializar Select2 cuando se abre el modal
    $('#crearHidranteModal').on('shown.bs.modal', function () {
        $('#fecha_tentativa').val('');
        initSelect2Modal();
        // Forzar recálculo de posiciones
        $(window).trigger('resize');
    });

    // Limpiar y destruir Select2 cuando se cierra el modal
    $('#crearHidranteModal').on('hidden.bs.modal', function () {
        $('.select2-search').select2('destroy');
    });

    // Manejo de fechas tentativas
    $('#btnGenerarFecha').click(function() {
        $('#opcionesPlazo').removeClass('d-none');
    });

    $('#opcionesPlazo button').click(function() {
        const plazo = $(this).data('plazo');
        const fechaInspeccion = new Date($('#fecha_inspeccion').val());
        
        if (plazo === 'corto') {
            fechaInspeccion.setMonth(fechaInspeccion.getMonth() + 6);
        } else {
            fechaInspeccion.setFullYear(fechaInspeccion.getFullYear() + 1);
        }

        $('#fecha_tentativa').val(fechaInspeccion.toISOString().split('T')[0]);
        $('#opcionesPlazo').addClass('d-none');
    });

    // Manejo de ubicación pendiente
    $('#ubicacionPendiente').change(function() {
        const isChecked = $(this).is(':checked');
        const selects = $('#id_calle, #id_y_calle, #id_colonia');
        
        selects.prop('disabled', isChecked);
        
        if (isChecked) {
            selects.val(null).trigger('change');
            $('<input>').attr({
                type: 'hidden',
                name: 'calle',
                value: 'Pendiente'
            }).appendTo('#formCrearHidrante');
            $('<input>').attr({
                type: 'hidden',
                name: 'y_calle',
                value: 'Pendiente'
            }).appendTo('#formCrearHidrante');
            $('<input>').attr({
                type: 'hidden',
                name: 'colonia',
                value: 'Pendiente'
            }).appendTo('#formCrearHidrante');
        } else {
            $('#formCrearHidrante input[name="calle"]').remove();
            $('#formCrearHidrante input[name="y_calle"]').remove();
            $('#formCrearHidrante input[name="colonia"]').remove();
        }
    });
});
</script>