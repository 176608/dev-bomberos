<div class="modal fade modal-edit" id="editarHidranteModal{{ $hidrante->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl"> <!-- Cambio de modal-lg a modal-xl -->
        <div class="modal-content">
            <form action="{{ route('hidrantes.update', $hidrante->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header d-flex justify-content-center">
                    <h5 class="modal-title text-center">Editar Hidrante #{{ $hidrante->id }}</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(255, 185, 185, 0.51);">
                    <!-- Primera Sección - Información Básica -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Información Básica
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Inspección</label>
                                        <input type="date" class="form-control" name="fecha_inspeccion" 
                                               id="edit_fecha_inspeccion"
                                               value="{{ $hidrante->fecha_inspeccion ? date('Y-m-d', strtotime($hidrante->fecha_inspeccion)) : date('Y-m-d') }}" 
                                               required>
                                        <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Estación
                                            <span id="edit_iconoExclamacionNumero_estacion{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="numero_estacion">
                                            @foreach(['S/I', '01', '02', '03', '04', '05', '06', '07', '08', '09'] as $num)
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
                            <div class="card-header bg-success text-white d-flex justify-content-center align-items-center">
                                <span class="text-center w-100">Ubicación</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Calle -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="edit_iconoExclamacionCalle{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                            </span>
                                            Entre <span id="edit_calle_tipo_display" class="d-none fw-bold text-primary"></span>:
                                        </label>
                                        <div class="input-group mb-2">
                                            <select class="form-select select2-search" name="id_calle" id="edit_id_calle">
                                                <option value="">Buscar nueva calle ...</option>
                                                @foreach($calles as $calle)
                                                    <option value="{{ $calle->IDKEY }}" 
                                                            data-tipo="{{ $calle->Tipovial }}"
                                                            {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                        {{ $calle->Nomvial }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="text" class="form-control manual-input" name="calle" id="edit_calle_manual" 
                                               placeholder="O escribe manualmente si no aparece en la lista"
                                               value="{{ $hidrante->id_calle == '0' ? $hidrante->calle : '' }}">
                                        <small class="form-text text-muted">
                                            @if($hidrante->id_calle && $hidrante->id_calle != '0' && $hidrante->callePrincipal)
                                                <div id="edit_calle_selected_container">
                                                    Tipo y nombre: <span id="edit_calle_selected_tipo" class="fw-bold tipo-info">
                                                        {{ $hidrante->callePrincipal->Tipovial . ' ' . $hidrante->callePrincipal->Nomvial }}
                                                    </span>
                                                </div>
                                            @else
                                                <div id="edit_calle_actual_container">
                                                    Información guardada: <span id="edit_calle_actual">{{ $hidrante->calle ?: 'N/A' }}</span>
                                                </div>
                                            @endif
                                        </small>
                                    </div>
                                    <!-- Y Calle -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Y <span id="edit_y_calle_tipo_display" class="d-none fw-bold text-primary"></span>:
                                        </label>
                                        <div class="input-group mb-2">
                                            <select class="form-select select2-search" name="id_y_calle" id="edit_id_y_calle">
                                                <option value="">Buscar nueva calle ...</option>
                                                @foreach($calles as $calle)
                                                    <option value="{{ $calle->IDKEY }}" 
                                                            data-tipo="{{ $calle->Tipovial }}"
                                                            {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                        {{ $calle->Nomvial }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="text" class="form-control manual-input" name="y_calle" id="edit_y_calle_manual" 
                                               placeholder="O escribe manualmente si no aparece en la lista"
                                               value="{{ $hidrante->id_y_calle == '0' ? $hidrante->y_calle : '' }}">
                                        <small class="form-text text-muted">
                                            @if($hidrante->id_y_calle && $hidrante->id_y_calle != '0' && $hidrante->calleSecundaria)
                                                <div id="edit_y_calle_selected_container">
                                                    Tipo y nombre: <span id="edit_y_calle_selected_tipo" class="fw-bold tipo-info">
                                                        {{ $hidrante->calleSecundaria->Tipovial . ' ' . $hidrante->calleSecundaria->Nomvial }}
                                                    </span>
                                                </div>
                                            @else
                                                <div id="edit_y_calle_actual_container">
                                                    y Calle guardada: <span id="edit_y_calle_actual">{{ $hidrante->y_calle ?: 'N/A' }}</span>
                                                </div>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <!-- Colonia -->
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">
                                            En <span id="edit_colonia_tipo_display" class="d-none fw-bold text-primary"></span>:
                                        </label>
                                        <div class="input-group mb-2">
                                            <select class="form-select select2-search" name="id_colonia" id="edit_id_colonia">
                                                <option value="">Buscar nueva colonia...</option>
                                                @foreach($colonias as $colonia)
                                                    <option value="{{ $colonia->IDKEY }}" 
                                                            data-tipo="{{ $colonia->TIPO }}"
                                                            {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                                        {{ $colonia->NOMBRE }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="text" class="form-control manual-input" name="colonia" id="edit_colonia_manual" 
                                               placeholder="O escribe manualmente si no aparece en la lista"
                                               value="{{ $hidrante->id_colonia == '0' ? $hidrante->colonia : '' }}">
                                        <small class="form-text text-muted">
                                            @if($hidrante->id_colonia && $hidrante->id_colonia != '0' && $hidrante->coloniaLocacion)
                                                <div id="edit_colonia_selected_container">
                                                    Tipo y nombre: <span id="edit_colonia_selected_tipo" class="fw-bold tipo-info">
                                                        {{ $hidrante->coloniaLocacion->TIPO . ' ' . $hidrante->coloniaLocacion->NOMBRE }}
                                                    </span>
                                                </div>
                                            @else
                                                <div id="edit_colonia_actual_container">
                                                    Información guardada: <span id="edit_colonia_actual">{{ $hidrante->colonia ?: 'N/A' }}</span>
                                                </div>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Tercera Sección - Estado y Características -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Estado y Características
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3 offset-md-3">
                                        <label class="form-label">
                                            Estado Hidrante
                                            <span id="edit_iconoExclamacionEstado_Hidrante{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="estado_hidrante">
                                            <option value="S/I" {{ $hidrante->estado_hidrante == 'S/I' ? 'selected' : '' }}> Pendiente</option>
                                            <option value="EN SERVICIO" {{ $hidrante->estado_hidrante == 'EN SERVICIO' ? 'selected' : '' }}>En servicio</option>
                                            <option value="FUERA DE SERVICIO" {{ $hidrante->estado_hidrante == 'FUERA DE SERVICIO' ? 'selected' : '' }}>Fuera de servicio</option>
                                            <option value="SOLO BASE" {{ $hidrante->estado_hidrante == 'SOLO BASE' ? 'selected' : '' }}>Solo Base</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"> <span id="edit_iconoExclamacionMarca{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-danger mx-1"></i></span> Marca: </span>
                                            <input type="text" class="form-control" name="marca"
                                                   value="{{ $hidrante->marca ?? '' }}" placeholder="N/A">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"> <span id="edit_iconoExclamacionAnio{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-danger mx-2"></i></span> Año:</span>
                                            <input type="text" class="form-control" name="anio"
                                                   value="{{ $hidrante->anio ?? '' }}" placeholder="N/A">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Cuarta Sección - Características Técnicas -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white">
                                Características Técnicas
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><span id="edit_iconoExclamacionLlave_Hidrante{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-warning mx-2"></i></span>Llave Hidrante:</span>
                                            <select class="form-select" name="llave_hidrante">
                                                <option value="S/I" {{ $hidrante->llave_hidrante == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                                <option value="PENTAGONO" {{ $hidrante->llave_hidrante == 'PENTAGONO' ? 'selected' : '' }}>Pentagono</option>
                                                <option value="CUADRO" {{ $hidrante->llave_hidrante == 'CUADRO' ? 'selected' : '' }}>Cuadro</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><span id="edit_iconoExclamacionPresion_agua{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-warning mx-2"></i></span>Presión de Agua:</span>
                                            <select class="form-select" name="presion_agua">
                                                <option value="S/I" {{ $hidrante->presion_agua == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                                <option value="NULA" {{ $hidrante->presion_agua == 'NULA' ? 'selected' : '' }}>Nula</option>
                                                <option value="BAJA" {{ $hidrante->presion_agua == 'BAJA' ? 'selected' : '' }}>Baja</option>
                                                <option value="REGULAR" {{ $hidrante->presion_agua == 'REGULAR' ? 'selected' : '' }}>Regular</option>
                                                <option value="ALTA" {{ $hidrante->presion_agua == 'ALTA' ? 'selected' : '' }}>Alta</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><span id="edit_iconoExclamacionLlave_Fosa{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-warning mx-2"></i></span>Llave Fosa:</span>
                                            <select class="form-select" name="llave_fosa">
                                                <option value="S/I" {{ $hidrante->llave_fosa == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                                <option value="CUADRO" {{ $hidrante->llave_fosa == 'CUADRO' ? 'selected' : '' }}>Cuadro</option>
                                                <option value="VOLANTE" {{ $hidrante->llave_fosa == 'VOLANTE' ? 'selected' : '' }}>Volante</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><span id="edit_iconoExclamacionHidrante_conectado_tubo{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-warning mx-2"></i></span>Conectado a Tubo de:</span>
                                            <select class="form-select" name="hidrante_conectado_tubo">
                                                <option value="S/I" {{ $hidrante->hidrante_conectado_tubo == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                                <option value="4'" {{ $hidrante->hidrante_conectado_tubo == "4'" ? 'selected' : '' }}>4 pulgadas</option>
                                                <option value="6'" {{ $hidrante->hidrante_conectado_tubo == "6'" ? 'selected' : '' }}>6 pulgadas</option>
                                                <option value="8'" {{ $hidrante->hidrante_conectado_tubo == "8'" ? 'selected' : '' }}>8 pulgadas</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><span id="edit_iconoExclamacionUbicacion_fosa{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-danger mx-2"></i></span>Ubicación Fosa:</span>
                                            <input type="text" class="form-control" name="ubicacion_fosa" required
                                                   value="{{ $hidrante->ubicacion_fosa ?? '' }}" placeholder="N/A">
                                        </div>
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
                                        <label class="form-label">Observaciones</label>
                                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Sin observaciones">{{ $hidrante->observaciones ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><span id="edit_iconoExclamacionOficial{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-danger mx-2"></i></span>Oficial:</span>
                                            <input type="text" class="form-control" name="oficial"
                                                   value="{{ $hidrante->oficial ?? '' }}" placeholder="N/A">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <span class="d-inline-block" tabindex="0" id="edit_popoverGuardarHidrante{{ $hidrante->id }}"
                        data-bs-toggle="popover"
                        data-bs-trigger="hover focus"
                        data-bs-placement="top"
                        title="¡Atención!"
                        data-bs-content="Debe seleccionar una calle (o marcar como pendiente) y definir el Estado del Hidrante.">
                        <button type="submit" class="btn btn-danger me-2" id="edit_btnGuardarHidrante{{ $hidrante->id }}" disabled>
                            Guardar Cambios
                        </button>
                    </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

/* Personaliza el fondo y texto del título del popover */
.popover-header {
    background-color: #dc3545 !important; /* Rojo Bootstrap */
    color: #fff !important;               /* Letras blancas */
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #fff;
}

/* Personaliza el contenido del popover */
.popover-body {
    color: #212529 !important;            /* Texto oscuro */
    font-size: 1rem;
    text-align: center;
}

/* Opcional: cambia el borde del popover */
.popover {
    border: 2px solid #dc3545;
}

/* Añadir estos estilos al bloque <style> existente */
.fade-transition {
    transition: opacity 0.3s ease-in-out;
}

.fade-out {
    opacity: 0;
}

.fade-in {
    opacity: 1;
}

/* Estilo para destacar el campo manual */
input[id^="manual_"] {
    border-left: 3px solid #28a745;
    padding-left: 10px;
}

/* Badge para indicar entrada manual */
.manual-badge {
    font-size: 0.7rem;
    padding: 2px 5px;
    margin-left: 5px;
    background-color: #28a745;
    color: white;
    border-radius: 3px;
    display: inline-block;
    vertical-align: middle;
}

/* Añadir estos estilos al final del bloque <style> en ambos archivos */
.input-disabled {
    background-color: #e9ecef !important;
    opacity: 0.7;
    pointer-events: none;
}

.manual-input {
    border-left: 3px solid #28a745;
}

.manual-input:focus {
    border-left-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>

<script>
$(document).ready(function() {
    // --- CONFIGURACIÓN CENTRAL ---
    const CONFIG = {
        hidranteId: "{{ $hidrante->id }}",
        modalId: '#editarHidranteModal{{ $hidrante->id }}',
        fieldsWithIcons: [
            { name: 'numero_estacion', icon: 'edit_iconoExclamacionNumero_estacion', tipo: 'select' },
            { name: 'llave_hidrante', icon: 'edit_iconoExclamacionLlave_Hidrante', tipo: 'select' },
            { name: 'presion_agua', icon: 'edit_iconoExclamacionPresion_agua', tipo: 'select' },
            { name: 'llave_fosa', icon: 'edit_iconoExclamacionLlave_Fosa', tipo: 'select' },
            { name: 'hidrante_conectado_tubo', icon: 'edit_iconoExclamacionHidrante_conectado_tubo', tipo: 'select' },
            { name: 'estado_hidrante', icon: 'edit_iconoExclamacionEstado_Hidrante', tipo: 'select' },
            { name: 'marca', icon: 'edit_iconoExclamacionMarca', tipo: 'input' },
            { name: 'anio', icon: 'edit_iconoExclamacionAnio', tipo: 'input' },
            { name: 'oficial', icon: 'edit_iconoExclamacionOficial', tipo: 'input' },
            { name: 'ubicacion_fosa', icon: 'edit_iconoExclamacionUbicacion_fosa', tipo: 'input' },
            { name: 'id_calle', icon: 'edit_iconoExclamacionCalle', tipo: 'select' }
        ]
    };

    // --- FUNCIÓN PARA ICONOS ---
    function toggleExclamationIcon(iconId, value) {
        const icon = $(`${iconId}${CONFIG.hidranteId}`);
        if (value === 'S/I' || value === '0' || value === '' || value === null) {
            icon.removeClass('d-none');
        } else {
            icon.addClass('d-none');
        }
    }

    // --- FUNCIÓN PARA MANEJAR SELECT2 + INPUT MANUAL EN EDICIÓN ---
    function setupLocationFieldEdit(selectId, manualId, tipoDisplayId, fieldType) {
        const $select = $(selectId);
        const $manual = $(manualId);
        const $tipoDisplay = $(tipoDisplayId);
        
        // Al seleccionar en Select2
        $select.on('select2:select', function(e) {
            const data = e.params.data;
            const selectedId = data.id;
            const selectedText = data.text;
            const tipo = $(this).find('option:selected').data('tipo');
            
            if (selectedId && selectedId !== '0') {
                // Deshabilitar input manual y limpiar su contenido
                $manual.prop('disabled', true).val('').addClass('input-disabled');
                
                // Mostrar tipo en el campo pequeño
                $tipoDisplay.val(tipo || '');
                
                // Mostrar información en el contenedor
                const containerId = selectId.replace('#edit_id_', '#edit_') + '_selected_container';
                const tipoId = selectId.replace('#edit_id_', '#edit_') + '_selected_tipo';
                const actualContainer = selectId.replace('#edit_id_', '#edit_') + '_actual_container';
                
                $(containerId).removeClass('d-none');
                $(actualContainer).addClass('d-none');
                $(tipoId).text(tipo + ' ' + selectedText);
            }
        });
        
        // Al limpiar Select2
        $select.on('select2:clear', function() {
            // Habilitar input manual
            $manual.prop('disabled', false).removeClass('input-disabled');
            
            // Limpiar tipo display
            $tipoDisplay.val('');
            
            // Mostrar contenedor actual, ocultar contenedor de selección
            const containerId = selectId.replace('#edit_id_', '#edit_') + '_selected_container';
            const actualContainer = selectId.replace('#edit_id_', '#edit_') + '_actual_container';
            
            $(containerId).addClass('d-none');
            $(actualContainer).removeClass('d-none');
        });
        
        // Al escribir en input manual
        $manual.on('input', function() {
            updateSaveButtonState();
        });
        
        // Inicializar estado según valor actual
        if ($select.val() && $select.val() !== '0') {
            // Hay selección válida - deshabilitar manual
            $manual.prop('disabled', true).addClass('input-disabled');
        } else {
            // No hay selección válida - habilitar manual
            $manual.prop('disabled', false).removeClass('input-disabled');
        }
    }

    // --- SELECT2 ---
    function initSelect2() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(`${CONFIG.modalId} .modal-body`),
            language: {
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando..."
            },
            placeholder: 'Comienza a escribir para buscar...',
            allowClear: true,
            minimumInputLength: 2
        });
    }

    // --- ICONOS DE ADVERTENCIA ---
    function setupIcons() {
        const estado = $('select[name="estado_hidrante"]').val();
        const esSoloBase = estado === 'SOLO BASE';

        CONFIG.fieldsWithIcons.forEach(({ name, icon, tipo }) => {
            const iconSelector = `#${icon}${CONFIG.hidranteId}`;
            if (esSoloBase) {
                $(iconSelector).addClass('d-none');
            } else {
                if (tipo === 'select') {
                    $(`select[name="${name}"]`).on('change', function() {
                        toggleExclamationIcon(`#${icon}`, $(this).val());
                    });
                    toggleExclamationIcon(`#${icon}`, $(`select[name="${name}"]`).val());
                } else if (tipo === 'input') {
                    $(`input[name="${name}"]`).on('input', function() {
                        toggleExclamationIcon(`#${icon}`, $(this).val());
                    });
                    toggleExclamationIcon(`#${icon}`, $(`input[name="${name}"]`).val());
                }
            }
        });
    }

    // --- BOTÓN GUARDAR Y POPOVER ---
    function updateSaveButtonState() {
        // Verifica si el campo calle está cubierto
        let calleOk = false;
        const calleSelectVal = $('#edit_id_calle').val();
        const calleManualVal = $('#edit_calle_manual').val().trim();
        
        if ((calleSelectVal && calleSelectVal !== '') || calleManualVal) {
            calleOk = true;
        }

        // Verifica si el estado de hidrante está definido (no es 'S/I')
        let estadoOk = false;
        const estadoVal = $(`select[name="estado_hidrante"]`).val();
        if (estadoVal && estadoVal !== 'S/I') {
            estadoOk = true;
        }

        if (calleOk && estadoOk) {
            $('#edit_btnGuardarHidrante' + CONFIG.hidranteId).prop('disabled', false);
            $('#edit_popoverGuardarHidrante' + CONFIG.hidranteId).removeAttr('data-bs-toggle data-bs-trigger data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId))) {
                bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId)).dispose();
            }
        } else {
            $('#edit_btnGuardarHidrante' + CONFIG.hidranteId).prop('disabled', true);
            let mensaje = 'Debe seleccionar una calle (o escribir manualmente) y definir el Estado del Hidrante.';
            $('#edit_popoverGuardarHidrante' + CONFIG.hidranteId)
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', mensaje);
            if (typeof edit_initPopover === 'function') edit_initPopover();
        }
    }

    // --- POPOVER ---
    function edit_initPopover() {
        const popoverTrigger = document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId);
        if (popoverTrigger) {
            if (bootstrap.Popover.getInstance(popoverTrigger)) {
                bootstrap.Popover.getInstance(popoverTrigger).dispose();
            }
            new bootstrap.Popover(popoverTrigger);
        }
    }

    // --- SOLO BASE: LÓGICA DE BLOQUEO Y LIMPIEZA ---
    function handleSoloBaseState(isSoloBase) {
        const fields = [
            'marca', 'anio',
            'llave_hidrante', 'presion_agua', 'llave_fosa',
            'hidrante_conectado_tubo', 'ubicacion_fosa'
        ];
        const iconos = [
            '#edit_iconoExclamacionMarca',
            '#edit_iconoExclamacionAnio',
            '#edit_iconoExclamacionLlave_Hidrante',
            '#edit_iconoExclamacionPresion_agua',
            '#edit_iconoExclamacionLlave_Fosa',
            '#edit_iconoExclamacionHidrante_conectado_tubo',
            '#edit_iconoExclamacionUbicacion_fosa'
        ];
        fields.forEach(function(name) {
            const $input = $(`[name="${name}"]`);
            if (isSoloBase) {
                if ($input.is('select')) {
                    $input.val('S/I').trigger('change');
                } else {
                    if (name === 'marca' || name === 'ubicacion_fosa') $input.val('S/I');
                    if (name === 'anio') $input.val('S/I');
                }
            }
        });
        iconos.forEach(function(sel) {
            if (isSoloBase) {
                $(`${sel}${CONFIG.hidranteId}`).addClass('d-none');
            } else {
                const field = sel.replace('#edit_iconoExclamacion', '').replace(CONFIG.hidranteId, '').toLowerCase();
                const $input = $(`[name="${field}"]`);
                if ($input.length) {
                    if ($input.is('select')) {
                        toggleExclamationIcon(sel, $input.val());
                    } else {
                        toggleExclamationIcon(sel, $input.val());
                    }
                }
            }
        });
    }

    // --- EVENTOS ---
    function initEventHandlers() {
        // Iconos de advertencia
        setupIcons();

        // Select2
        initSelect2();

        // Configurar campos de ubicación
        setupLocationFieldEdit('#edit_id_calle', '#edit_calle_manual', '#edit_calle_tipo_display', 'calle');
        setupLocationFieldEdit('#edit_id_y_calle', '#edit_y_calle_manual', '#edit_y_calle_tipo_display', 'calle');
        setupLocationFieldEdit('#edit_id_colonia', '#edit_colonia_manual', '#edit_colonia_tipo_display', 'colonia');

        // Guardar: validación
        $('form').on('submit', function(e) {
            // Validar calle (obligatoria)
            const calleSelectVal = $('#edit_id_calle').val();
            const calleManualVal = $('#edit_calle_manual').val().trim();
            
            if (!calleSelectVal && !calleManualVal) {
                e.preventDefault();
                alert('El campo Calle es obligatorio. Selecciona una opción o escribe manualmente.');
                return false;
            }
            
            // NO PROCESAR NADA - Solo enviar tal como está
            // El servidor decidirá qué usar basándose en qué campo tiene contenido
        });

        // Actualizar botón guardar cuando cambian campos clave
        $('#edit_id_calle, #edit_calle_manual').on('change input', updateSaveButtonState);
    }

    // --- EVENTO DE CAMBIO EN ESTADO HIDRANTE ---
    $(`select[name="estado_hidrante"]`).on('change', function() {
        const isSoloBase = $(this).val() === 'SOLO BASE';
        handleSoloBaseState(isSoloBase);
    });

    // --- Al abrir el modal, aplicar si ya está en SOLO BASE ---
    $(CONFIG.modalId).on('shown.bs.modal', function() {
        const isSoloBase = $(`select[name="estado_hidrante"]`).val() === 'SOLO BASE';
        handleSoloBaseState(isSoloBase);
    });

    // --- INICIALIZACIÓN DEL MODAL ---
    $(CONFIG.modalId)
        .on('shown.bs.modal', function() {
            initEventHandlers();
            updateSaveButtonState();
            setTimeout(edit_initPopover, 200);
        })
        .on('hidden.bs.modal', function() {
            $('.select2-search').select2('destroy');
            recargarSoloTabla();
        });

    // Inicialización directa si el modal ya está abierto
    if ($(CONFIG.modalId).is(':visible')) {
        initEventHandlers();
        updateSaveButtonState();
    }
});
</script>
