<div class="modal fade" id="crearHidranteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('hidrantes.store') }}" method="POST" id="formCrearHidrante">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Hidrante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(161, 224, 152, 0.8);">
                    <!-- Campo fecha_inspeccion -->
                    <div class="row">
                        
                        <div class="card text-center m-2">

                            <div class="card-header">
                                Primera Seccion
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Inspección(DD-MM-YYYY)</label>
                                        <input type="date" class="form-control" name="fecha_inspeccion" 
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha tentativa de Mantenimiento(DD-MM-YYYY)</label>
                                        <input type="date" class="form-control" name="NOTFECHA" 
                                            value="{{ date('Y-m-d') }}" >
                                    </div>
                                </div>

                                <hr class="my-4">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Estación</label>
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
                                        <label class="form-label">Número de Hidrante</label>
                                        <input type="number" class="form-control" name="numero_hidrante" placeholder="Ejemplo: 5842" required>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Principal</label>
                            <select class="form-select" name="id_calle">
                                <option value="">Sin definir, selecciona una...</option>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}">{{ $calle->Nomvial }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Secundaria</label>
                            <select class="form-select" name="id_y_calle">
                                <option value="">Sin definir, selecciona una...</option>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}">{{ $calle->Nomvial }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Colonia</label>
                            <select class="form-select" name="id_colonia">
                                <option value="">Sin definir, selecciona una...</option>
                                @foreach($colonias as $colonia)
                                    <option value="{{ $colonia->IDKEY }}">{{ $colonia->NOMBRE }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Hidrante</label>
                            <select class="form-select" name="llave_hidrante">
                                <option value="" selected>Sin definir, selecciona una...</option>
                                <option value="Pentagono">Pentágono</option>
                                <option value="Cuadro">Cuadro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presión de Agua</label>
                            <select class="form-select" name="presion_agua" required>
                                <option value="" selected>Sin definir, selecciona una...</option>
                                <option value="Mala">Mala</option>
                                <option value="Buena">Buena</option>
                                <option value="Sin agua">Sin agua</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" placeholder="Ejemplo: Rojo">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Fosa</label>
                            <select class="form-select" name="llave_fosa">
                                <option value="" selected>Sin definir, selecciona una...</option>
                                <option value="Cuadro">Cuadro</option>
                                <option value="Volante">Volante</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ubicación Fosa</label>
                            <input type="text" class="form-control" name="ubicacion_fosa" placeholder="(N MTS.) Ejemplo: 5 MTS.">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Conectado a Tubo</label>
                            <select class="form-select" name="hidrante_conectado_tubo">
                                <option value="" selected>Sin definir, selecciona una...</option>
                                <option value="4'">4'</option>
                                <option value="6'">6'</option>
                                <option value="8'">8'</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado Hidrante</label>
                            <select class="form-select" name="estado_hidrante">
                                <option value="" selected>Sin definir, selecciona una...</option>
                                <option value="Servicio">Servicio</option>
                                <option value="Fuera de servicio">Fuera de servicio</option>
                                <option value="Solo Base">Solo Base</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca*</label>
                            <input type="text" class="form-control" name="marca" placeholder="Ejemplo: MUELLER">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="anio" placeholder="Año de inicio del servicio del hidrante" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Escriba observaciones aquí..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Oficial</label>
                        <input type="text" class="form-control" name="oficial" placeholder="Nombre del oficial responsable" required>
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