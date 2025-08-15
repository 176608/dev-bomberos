<!-- Modal Consulta Express -->
<div class="modal fade" id="consultaExpressModal" tabindex="-1" aria-labelledby="consultaExpressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="consultaExpressModalLabel">
                    <i class="bi bi-search me-2"></i>Consulta Express
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-5">
                            @php
                                use App\Models\SIGEM\ce_tema;
                                $temas = ce_tema::orderBy('tema', 'asc')->get();
                            @endphp
                            
                            <div id="ce_form_modal">
                                <div class="form-group mb-3">
                                    <label for="ce_tema_select_modal" class="form-label fw-semibold">
                                        <i class="bi bi-bookmark-fill me-1"></i>Tema:
                                    </label>
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
                                    <label for="ce_subtema_select_modal" class="form-label fw-semibold">
                                        <i class="bi bi-bookmarks-fill me-1"></i>Subtema:
                                    </label>
                                    <select id="ce_subtema_select_modal" name="ce_subtema_id" class="form-select" disabled>
                                        <option value="">Primero seleccione un tema</option>
                                    </select>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="alert alert-success alert-sm">
                                        <i class="bi bi-journal-text me-2"></i>
                                        <strong>Consulta Express</strong><br>
                                        <small>Sistema de consulta rápida de información estadística municipal organizada por temas y subtemas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <div id="ce_contenido_container_modal" class="border rounded p-3 bg-light" style="min-height: 300px; max-height: 500px; overflow-y: auto;">
                                <div class="text-center text-muted py-5" id="ce_estado_inicial">
                                    <i class="bi bi-table" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="mt-3 text-muted">Consulta Express</h5>
                                    <p class="mb-0">Seleccione un tema y subtema para ver la información estadística</p>
                                </div>
                            </div>
                            
                            <div id="ce_metadata_modal" class="text-end text-muted small mt-2" style="display: none;">
                                <i class="bi bi-clock me-1"></i>Última actualización: <span id="ce_fecha_actualizacion_modal">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Sistema de Información Geográfica y Estadística Municipal
                    </small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>