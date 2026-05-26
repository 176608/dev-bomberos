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
                                $temas = \App\Models\SIGEM\ce_tema::orderBy('tema', 'asc')->get();
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

<style>
    .consulta-express-modal-content { animation: fadeInUp 0.4s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    #ce_contenido_container_modal::-webkit-scrollbar { width: 6px; }
    #ce_contenido_container_modal::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
    #ce_contenido_container_modal::-webkit-scrollbar-thumb { background: #198754; border-radius: 3px; }
    #ce_contenido_container_modal::-webkit-scrollbar-thumb:hover { background: #146c43; }
</style>

@push('visor_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const temaSelect = document.getElementById('ce_tema_select_modal');
    const subtemaSelect = document.getElementById('ce_subtema_select_modal');
    const contenidoContainer = document.getElementById('ce_contenido_container_modal');
    const metadataDiv = document.getElementById('ce_metadata_modal');
    const fechaSpan = document.getElementById('ce_fecha_actualizacion_modal');

    if (!temaSelect || !subtemaSelect || !contenidoContainer) return;

    function escapeHtml(text) {
        if (!text) return '';
        var d = document.createElement('div');
        d.textContent = text.toString();
        return d.innerHTML;
    }

    function renderizarTabla(tablaDatos) {
        var html = '<div class="table-responsive"><table class="table table-striped table-bordered table-hover">';
        tablaDatos.forEach(function (fila, filaIndex) {
            html += '<tr>';
            if (Array.isArray(fila)) {
                fila.forEach(function (celda, colIndex) {
                    if (filaIndex === 0) {
                        html += '<th class="table-success text-center fw-bold">' + escapeHtml(celda || '-') + '</th>';
                    } else {
                        html += '<td class="text-center' + (colIndex === 0 ? ' fw-semibold' : '') + '">' + escapeHtml(celda || '-') + '</td>';
                    }
                });
            }
            html += '</tr>';
        });
        html += '</table></div>';
        return html;
    }

    function mostrarContenido(data, actualizado) {
        if (!data || !data.tabla_datos || !Array.isArray(data.tabla_datos) || data.tabla_datos.length === 0) {
            contenidoContainer.innerHTML = '<div class="alert alert-warning text-center">Sin datos de tabla disponibles</div>';
            return;
        }
        var tablaHtml = renderizarTabla(data.tabla_datos);
        var contenido = '<div class="consulta-express-modal-content">';
        contenido += '<div class="text-center mb-4"><h4 class="text-success fw-bold mb-2">' + escapeHtml(data.titulo_tabla || 'Información Estadística') + '</h4></div>';
        contenido += '<div class="table-container">' + tablaHtml + '</div>';
        if (data.pie_tabla) {
            contenido += '<div class="text-center mt-3"><small class="text-muted fw-semibold fst-italic">' + escapeHtml(data.pie_tabla) + '</small></div>';
        }
        contenido += '</div>';
        contenidoContainer.innerHTML = contenido;
        if (metadataDiv && fechaSpan) {
            fechaSpan.textContent = actualizado || '-';
            metadataDiv.style.display = 'block';
        }
    }

    temaSelect.addEventListener('change', function () {
        var temaId = this.value;
        if (temaId) {
            subtemaSelect.disabled = true;
            subtemaSelect.innerHTML = '<option value="">Cargando subtemas...</option>';
            fetch('/sigem/consulta-express/subtemas/' + temaId)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    subtemaSelect.innerHTML = '<option value="">Seleccione un subtema...</option>';
                    if (data.success && data.subtemas && data.subtemas.length > 0) {
                        data.subtemas.forEach(function (s) {
                            var opt = document.createElement('option');
                            opt.value = s.ce_subtema_id;
                            opt.textContent = s.ce_subtema;
                            subtemaSelect.appendChild(opt);
                        });
                        subtemaSelect.disabled = false;
                    } else {
                        subtemaSelect.innerHTML = '<option value="">No hay subtemas disponibles</option>';
                    }
                })
                .catch(function () {
                    subtemaSelect.innerHTML = '<option value="">Error al cargar subtemas</option>';
                });
        } else {
            subtemaSelect.innerHTML = '<option value="">Primero seleccione un tema</option>';
            subtemaSelect.disabled = true;
        }
    });

    subtemaSelect.addEventListener('change', function () {
        var subtemaId = this.value;
        if (subtemaId) {
            contenidoContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Cargando...</p></div>';
            fetch('/sigem/consulta-express/contenido/' + subtemaId)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success && data.contenido) {
                        mostrarContenido(data.contenido, data.actualizado);
                    } else {
                        contenidoContainer.innerHTML = '<div class="alert alert-warning">No se encontró contenido.</div>';
                    }
                })
                .catch(function () {
                    contenidoContainer.innerHTML = '<div class="alert alert-danger">Error al cargar contenido.</div>';
                });
        }
    });
});
</script>
@endpush
