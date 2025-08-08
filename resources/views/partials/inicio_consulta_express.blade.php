<!-- Modal Consulta Express -->
<div class="modal fade" id="consultaExpressModal" tabindex="-1" aria-labelledby="consultaExpressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Header del Modal -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="consultaExpressModalLabel">
                    Consulta Express
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Columna de imagen - Ahora 2 columnas -->
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow-sm" style="max-height: 220px;">
                        </div>
                        
                        <!-- Columna de selectores - Ahora 4 columnas -->
                        <div class="col-md-4">
                            @php
                                use App\Models\SIGEM\ce_tema;
                                $temas = ce_tema::orderBy('tema', 'asc')->get();
                            @endphp
                            
                            <!-- Formulario para selección en modal -->
                            <div id="ce_form_modal">
                                <div class="form-group mb-3">
                                    <label for="ce_tema_select_modal" class="form-label">Tema:</label>
                                    <select id="ce_tema_select_modal" name="ce_tema_id" class="form-select">
                                        <option value="">Seleccione un tema...</option>
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->ce_tema_id }}">
                                                {{ $tema->tema }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="ce_subtema_select_modal" class="form-label">Subtema:</label>
                                    <select id="ce_subtema_select_modal" name="ce_subtema_id" class="form-select" disabled>
                                        <option value="">Primero seleccione un tema</option>
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                        
                        <!-- Columna de contenido - Se mantiene en 6 columnas -->
                        <div class="col-md-6">
                            <div id="ce_contenido_container_modal" class="border rounded p-3" style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-info-circle fs-2"></i>
                                    <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                                </div>
                            </div>
                            
                            <div id="ce_metadata_modal" class="text-end text-muted small mt-2" style="display: none;">
                                Última actualización: <span id="ce_fecha_actualizacion_modal">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
