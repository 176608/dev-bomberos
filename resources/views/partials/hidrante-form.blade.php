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
                            <label class="form-label">Número de Estación</label>
                            <input type="number" class="form-control" name="numero_estacion" value="{{ $hidrante->numero_estacion }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Hidrante</label>
                            <input type="number" class="form-control" name="numero_hidrante" value="{{ $hidrante->numero_hidrante }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Principal</label>
                            <select class="form-select" name="id_calle" required>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                        {{ $calle->Nomvial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Secundaria</label>
                            <select class="form-select" name="id_y_calle" required>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                        {{ $calle->Nomvial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Colonia</label>
                            <select class="form-select" name="id_colonia" required>
                                @foreach($colonias as $colonia)
                                    <option value="{{ $colonia->IDKEY }}" {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                        {{ $colonia->NOMBRE }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Hidrante</label>
                            <select class="form-select" name="llave_hidrante">
                                <option value="Si" {{ $hidrante->llave_hidrante == 'Si' ? 'selected' : '' }}>Si</option>
                                <option value="No" {{ $hidrante->llave_hidrante == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presión de Agua</label>
                            <input type="text" class="form-control" name="presion_agua" value="{{ $hidrante->presion_agua }}">
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
                                <option value="Si" {{ $hidrante->llave_fosa == 'Si' ? 'selected' : '' }}>Si</option>
                                <option value="No" {{ $hidrante->llave_fosa == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ubicación Fosa</label>
                            <input type="text" class="form-control" name="ubicacion_fosa" value="{{ $hidrante->ubicacion_fosa }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Conectado a Tubo</label>
                            <select class="form-select" name="hidrante_conectado_tubo">
                                <option value="Si" {{ $hidrante->hidrante_conectado_tubo == 'Si' ? 'selected' : '' }}>Si</option>
                                <option value="No" {{ $hidrante->hidrante_conectado_tubo == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado Hidrante</label>
                            <select class="form-select" name="estado_hidrante">
                                <option value="Bueno" {{ $hidrante->estado_hidrante == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="Regular" {{ $hidrante->estado_hidrante == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Malo" {{ $hidrante->estado_hidrante == 'Malo' ? 'selected' : '' }}>Malo</option>
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
                        <select class="form-select" name="oficial">
                            <option value="Si" {{ $hidrante->oficial == 'Si' ? 'selected' : '' }}>Si</option>
                            <option value="No" {{ $hidrante->oficial == 'No' ? 'selected' : '' }}>No</option>
                        </select>
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
