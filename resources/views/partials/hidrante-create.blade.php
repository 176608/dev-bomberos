
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>


<div class="modal fade" id="crearHidranteModal" tabindex="-1">
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
                                            value="{{ date('Y-m-d') }}" required>
                                            <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha tentativa de Mantenimiento*:</label>
                                        <input type="date" class="form-control" name="NOTFECHA" 
                                            value="{{ date('Y-m-d') }}" >
                                            <small class="form-text text-muted">Campo de Muestra*</small>
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

                            <div class="card-header bg-success text-white">
                                Ubicación
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Principal:</label>
                                        <select class="selectpicker form-select" 
                                                name="id_calle" 
                                                id="id_calle" 
                                                data-live-search="true" 
                                                data-size="30"
                                                title="Buscar calle principal..."
                                                data-live-search-placeholder="Escribe para buscar...">
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" data-tokens="{{ $calle->Nomvial }}">
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Secundaria(Y Calle):</label>
                                        <select class="selectpicker form-select" 
                                                name="id_y_calle" 
                                                id="id_y_calle" 
                                                data-live-search="true" 
                                                data-size="30"
                                                title="Buscar calle secundaria..."
                                                data-live-search-placeholder="Escribe para buscar...">
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" data-tokens="{{ $calle->Nomvial }}">
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">Colonia:</label>
                                        <select class="selectpicker form-select" 
                                                name="id_colonia" 
                                                id="id_colonia" 
                                                data-live-search="true" 
                                                data-size="30"
                                                title="Buscar colonia..."
                                                data-live-search-placeholder="Escribe para buscar...">
                                            @foreach($colonias as $colonia)
                                                <option value="{{ $colonia->IDKEY }}" data-tokens="{{ $colonia->NOMBRE }}">
                                                    {{ $colonia->NOMBRE }}
                                                </option>
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
                                        <select class="form-select" name="llave_hidrante">
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
                                        <select class="form-select" name="llave_fosa">
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="Cuadro">Cuadro</option>
                                            <option value="Volante">Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Conectado a Tubo de:</label>
                                        <select class="form-select" name="hidrante_conectado_tubo">
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
                                        <input type="text" class="form-control" name="ubicacion_fosa" placeholder="(N MTS.) Ejemplo: 5 MTS.">
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
                                        <select class="form-select" name="estado_hidrante">
                                            <option value="" selected>Sin definir, selecciona una...</option>
                                            <option value="Servicio">Servicio</option>
                                            <option value="Fuera de servicio">Fuera de servicio</option>
                                            <option value="Solo Base">Solo Base</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca*:</label>
                                        <input type="text" class="form-control" name="marca" placeholder="Ejemplo: MUELLER">
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
.bootstrap-select .dropdown-menu {
    max-width: 100%;
    max-height: 300px;
}

.bootstrap-select .dropdown-toggle {
    white-space: normal;
    word-wrap: break-word;
}

.bootstrap-select .filter-option {
    white-space: normal;
    word-wrap: break-word;
}

.bootstrap-select .bs-searchbox {
    padding: 8px;
}

.bootstrap-select .bs-searchbox input {
    border-radius: 4px;
}

.bootstrap-select .dropdown-item {
    white-space: normal;
    word-wrap: break-word;
    padding: 6px 12px;
}

.bootstrap-select .dropdown-item.active,
.bootstrap-select .dropdown-item:active {
    background-color: #007bff;
    color: white;
}

.bootstrap-select .no-results {
    padding: 8px;
    background: #f8f9fa;
    margin: 0;
    text-align: center;
    border-radius: 4px;
}
</style>

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar Bootstrap Select
    $('.selectpicker').selectpicker({
        noneResultsText: 'No se encontraron resultados para {0}',
        liveSearch: true,
        styleBase: 'form-control',
        style: '',
        size: 7
    });

    // Estilizado adicional para el campo de búsqueda
    $('.bootstrap-select .bs-searchbox input').addClass('form-control-sm');

    // Evento cuando se selecciona un valor
    $('#id_calle, #id_y_calle, #id_colonia').on('changed.bs.select', function() {
        $(this).trigger('change'); // Trigger para validación de formulario si es necesario
    });

    // Mejorar la experiencia de búsqueda
    $('.selectpicker').on('shown.bs.select', function() {
        $(this).parent().find('.bs-searchbox input').focus();
    });

    // Limpiar búsqueda al cerrar
    $('.selectpicker').on('hidden.bs.select', function() {
        $(this).parent().find('.bs-searchbox input').val('');
        $(this).selectpicker('refresh');
    });
});
</script>
@endsection