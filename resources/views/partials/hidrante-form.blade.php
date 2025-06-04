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
                <div class="modal-body">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Inspección</label>
                            <input type="date" class="form-control" name="fecha_inspeccion" 
                                   value="{{ $hidrante->fecha_inspeccion ? date('Y-m-d', strtotime($hidrante->fecha_inspeccion)) : date('Y-m-d') }}" 
                                   required>
                            <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Estación</label>
                            <select class="form-select" name="numero_estacion">
                                <option value="01" {{ $hidrante->numero_estacion == '01' ? 'selected' : '' }}>01</option>
                                <option value="02" {{ $hidrante->numero_estacion == '02' ? 'selected' : '' }}>02</option>
                                <option value="03" {{ $hidrante->numero_estacion == '03' ? 'selected' : '' }}>03</option>
                                <option value="04" {{ $hidrante->numero_estacion == '04' ? 'selected' : '' }}>04</option>
                                <option value="05" {{ $hidrante->numero_estacion == '05' ? 'selected' : '' }}>05</option>
                                <option value="06" {{ $hidrante->numero_estacion == '06' ? 'selected' : '' }}>06</option>
                                <option value="07" {{ $hidrante->numero_estacion == '07' ? 'selected' : '' }}>07</option>
                                <option value="08" {{ $hidrante->numero_estacion == '08' ? 'selected' : '' }}>08</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Hidrante</label>
                            <input type="number" class="form-control" name="numero_hidrante" value="{{ $hidrante->numero_hidrante }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Principal</label>
                            <select class="form-select" name="id_calle">
                                <option value="">Sin definir, selecciona una...</option>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                        {{ $calle->Nomvial }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control mt-2" value="{{ $hidrante->calle }}" readonly 
                                   placeholder="Nombre actual de la calle">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Secundaria</label>
                            <select class="form-select" name="id_y_calle">
                                <option value="">Sin definir, selecciona una...</option>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                        {{ $calle->Nomvial }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control mt-2" value="{{ $hidrante->y_calle }}" readonly 
                                   placeholder="Nombre actual de la calle secundaria">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Colonia</label>
                            <select class="form-select" name="id_colonia">
                                <option value="">Sin definir, selecciona una...</option>
                                @foreach($colonias as $colonia)
                                    <option value="{{ $colonia->IDKEY }}" 
                                        {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                        {{ $colonia->NOMBRE }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control mt-2" value="{{ $hidrante->colonia }}" readonly 
                                   placeholder="Nombre actual de la colonia">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Hidrante</label>
                            <select class="form-select" name="llave_hidrante">
                                <option value="Pentagono" {{ $hidrante->llave_hidrante == 'Pentagono' ? 'selected' : '' }}>Pentagono</option>
                                <option value="Cuadro" {{ $hidrante->llave_hidrante == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presión de Agua</label>
                            <select class="form-select" name="presion_agua">
                                <option value="Mala" {{ $hidrante->presion_agua == 'Mala' ? 'selected' : '' }}>Mala</option>
                                <option value="Buena" {{ $hidrante->presion_agua == 'Buena' ? 'selected' : '' }}>Buena</option>
                                <option value="Sin agua" {{ $hidrante->presion_agua == 'Sin agua' ? 'selected' : '' }}>Sin agua</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" value="{{ $hidrante->color }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Fosa</label>
                            <select class="form-select" name="llave_fosa">
                                <option value="Cuadro" {{ $hidrante->llave_fosa == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                <option value="Volante" {{ $hidrante->llave_fosa == 'Volante' ? 'selected' : '' }}>Volante</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ubicación Fosa (N MTS.)</label>
                            <input type="text" class="form-control" name="ubicacion_fosa" value="{{ $hidrante->ubicacion_fosa }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Conectado a Tubo</label>
                            <select class="form-select" name="hidrante_conectado_tubo">
                                <option value="4'" {{ $hidrante->hidrante_conectado_tubo == '4\'' ? 'selected' : '' }}>4'</option>
                                <option value="6'" {{ $hidrante->hidrante_conectado_tubo == '6\'' ? 'selected' : '' }}>6'</option>
                                <option value="8'" {{ $hidrante->hidrante_conectado_tubo == '8\'' ? 'selected' : '' }}>8'</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado Hidrante</label>
                            <select class="form-select" name="estado_hidrante">
                                <option value="Servicio" {{ $hidrante->estado_hidrante == 'Servicio' ? 'selected' : '' }}>Servicio</option>
                                <option value="Fuera de servicio" {{ $hidrante->estado_hidrante == 'Fuera de servicio' ? 'selected' : '' }}>Fuera de servicio</option>
                                <option value="Solo Base" {{ $hidrante->estado_hidrante == 'Solo Base' ? 'selected' : '' }}>Solo Base</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" value="{{ $hidrante->marca }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="anio" value="{{ $hidrante->anio }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3">{{ $hidrante->observaciones }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Oficial</label>
                        <input type="text" class="form-control" name="oficial" value="{{ $hidrante->oficial }}">
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
