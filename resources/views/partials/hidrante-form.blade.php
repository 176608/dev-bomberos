<div class="modal fade" id="editarHidranteModal{{ $hidrante->id }}" tabindex="-1">
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
                                               value="{{ $hidrante->fecha_inspeccion ? date('Y-m-d', strtotime($hidrante->fecha_inspeccion)) : date('Y-m-d') }}" 
                                               required>
                                        <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Mantenimiento*:</label>
                                        <input type="date" class="form-control" name="xxx" 
                                               value="{{ $hidrante->fecha_inspeccion ? date('Y-m-d', strtotime($hidrante->fecha_inspeccion)) : date('Y-m-d') }}" 
                                               readonly>
                                        <small class="form-text text-muted">Campo Muestra*</small>
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
                                        <input type="number" class="form-control" name="numero_hidrante" value="{{ $hidrante->numero_hidrante }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Segunda Sección - Ubicación -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white">
                                Ubicación
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Principal:</label>
                                        <select class="form-select" name="id_calle">
                                            <option value="">Sin definir, selecciona una...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mt-2" value="{{ $hidrante->calle }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calle Secundaria(Y Calle):</label>
                                        <select class="form-select" name="id_y_calle">
                                            <option value="">Sin definir, selecciona una...</option>
                                            @foreach($calles as $calle)
                                                <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                    {{ $calle->Nomvial }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mt-2" value="{{ $hidrante->y_calle }}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">Colonia:</label>
                                        <select class="form-select" name="id_colonia">
                                            <option value="">Sin definir, selecciona una...</option>
                                            @foreach($colonias as $colonia)
                                                <option value="{{ $colonia->IDKEY }}" 
                                                    {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                                    {{ $colonia->NOMBRE }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mt-2" value="{{ $hidrante->colonia }}" readonly>
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
                                        <input type="text" class="form-control" name="ubicacion_fosa" value="{{ $hidrante->ubicacion_fosa }}">
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
                                        <input type="number" class="form-control" name="anio" value="{{ $hidrante->anio }}">
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
                                        <input type="text" class="form-control" name="marca" value="{{ $hidrante->marca }}">
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
                                        <input type="text" class="form-control" name="oficial" value="{{ $hidrante->oficial }}">
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
