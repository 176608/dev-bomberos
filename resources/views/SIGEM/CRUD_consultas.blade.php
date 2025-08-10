<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-search"></i> Panel CRUD de Consultas Express</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Administra el sistema de Consultas Express de SIGEM. Gestiona temas, subtemas y contenidos de manera jerárquica.</p>
                <small class="text-muted">
                    Tablas: <code>consulta_express_tema</code>, <code>consulta_express_subtema</code>, <code>consulta_express_contenido</code>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Navegación entre secciones -->
<div class="row mb-3">
    <div class="col-12">
        <nav>
            <div class="nav nav-pills nav-fill" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-temas-tab" data-bs-toggle="tab" data-bs-target="#nav-temas" 
                        type="button" role="tab" aria-controls="nav-temas" aria-selected="true">
                    <i class="bi bi-bookmark-fill"></i> Temas CE <span class="badge bg-primary ms-2">{{ count($ce_temas ?? []) }}</span>
                </button>
                <button class="nav-link" id="nav-subtemas-tab" data-bs-toggle="tab" data-bs-target="#nav-subtemas" 
                        type="button" role="tab" aria-controls="nav-subtemas" aria-selected="false">
                    <i class="bi bi-bookmarks-fill"></i> Subtemas CE <span class="badge bg-success ms-2">{{ count($ce_subtemas ?? []) }}</span>
                </button>
                <button class="nav-link" id="nav-contenidos-tab" data-bs-toggle="tab" data-bs-target="#nav-contenidos" 
                        type="button" role="tab" aria-controls="nav-contenidos" aria-selected="false">
                    <i class="bi bi-file-earmark-text-fill"></i> Contenidos CE <span class="badge bg-warning ms-2">{{ count($ce_contenidos ?? []) }}</span>
                </button>
            </div>
        </nav>
    </div>
</div>

<div class="tab-content" id="nav-tabContent">
    
    <!-- ========================================== TEMAS CE ========================================== -->
    <div class="tab-pane fade show active" id="nav-temas" role="tabpanel" aria-labelledby="nav-temas-tab">
        <div class="row mb-3">
            <div class="col-md-8">
                <h6><i class="bi bi-bookmark-fill"></i> Temas de Consultas Express</h6>
                <small class="text-muted">Gestiona los temas principales para las consultas express</small>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarTema">
                    <i class="bi bi-plus-circle"></i> Nuevo Tema CE
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if(isset($ce_temas) && count($ce_temas) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Tema</th>
                                    <th>Subtemas</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ce_temas as $tema)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $tema->ce_tema_id }}</span></td>
                                    <td><strong>{{ $tema->tema }}</strong></td>
                                    <td>
                                        @php
                                            $subtemas_count = $tema->subtemas()->count();
                                        @endphp
                                        @if($subtemas_count > 0)
                                            <span class="badge bg-success">{{ $subtemas_count }} subtemas</span>
                                        @else
                                            <span class="text-muted">Sin subtemas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" title="Ver" 
                                                    onclick="verTemaCE({{ $tema->ce_tema_id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" title="Editar" 
                                                    onclick="editarTemaCE({{ $tema->ce_tema_id }}, '{{ addslashes($tema->tema) }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar" 
                                                    onclick="eliminarTemaCI({{ $tema->ce_tema_id }}, '{{ addslashes($tema->tema) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-bookmark text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay temas registrados</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarTema">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Tema CE
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================== SUBTEMAS CE ========================================== -->
    <div class="tab-pane fade" id="nav-subtemas" role="tabpanel" aria-labelledby="nav-subtemas-tab">
        <div class="row mb-3">
            <div class="col-md-8">
                <h6><i class="bi bi-bookmarks-fill"></i> Subtemas de Consultas Express</h6>
                <small class="text-muted">Gestiona los subtemas asociados a cada tema principal</small>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarSubtema">
                    <i class="bi bi-plus-circle"></i> Nuevo Subtema CE
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if(isset($ce_subtemas) && count($ce_subtemas) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Subtema</th>
                                    <th>Tema Padre</th>
                                    <th>Contenidos</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ce_subtemas as $subtema)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $subtema->ce_subtema_id }}</span></td>
                                    <td><strong>{{ $subtema->ce_subtema }}</strong></td>
                                    <td>
                                        @if($subtema->tema)
                                            <span class="badge bg-primary">{{ $subtema->tema->tema }}</span>
                                        @else
                                            <span class="text-danger">Sin tema padre</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $contenidos_count = $subtema->contenidos()->count();
                                        @endphp
                                        @if($contenidos_count > 0)
                                            <span class="badge bg-warning">{{ $contenidos_count }} contenidos</span>
                                        @else
                                            <span class="text-muted">Sin contenidos</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" title="Ver" 
                                                    onclick="verSubtemaCI({{ $subtema->ce_subtema_id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" title="Editar" 
                                                    onclick="editarSubtemaCI({{ $subtema->ce_subtema_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar" 
                                                    onclick="eliminarSubtemaCI({{ $subtema->ce_subtema_id }}, '{{ addslashes($subtema->ce_subtema) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-bookmarks text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay subtemas registrados</h5>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarSubtema">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Subtema CE
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================== CONTENIDOS CE ========================================== -->
    <div class="tab-pane fade" id="nav-contenidos" role="tabpanel" aria-labelledby="nav-contenidos-tab">
        <div class="row mb-3">
            <div class="col-md-8">
                <h6><i class="bi bi-file-earmark-text-fill"></i> Contenidos de Consultas Express</h6>
                <small class="text-muted">Gestiona el contenido HTML de las consultas express</small>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAgregarContenido">
                    <i class="bi bi-plus-circle"></i> Nuevo Contenido CE
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if(isset($ce_contenidos) && count($ce_contenidos) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-warning">
                                <tr>
                                    <th>ID</th>
                                    <th>Contenido (Preview)</th>
                                    <th>Tema</th>
                                    <th>Subtema</th>
                                    <th>Fecha</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ce_contenidos as $contenido)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $contenido->ce_contenido_id }}</span></td>
                                    <td>
                                        <div style="max-width: 300px;">
                                            @if($contenido->ce_contenido)
                                                <small>{!! Str::limit(strip_tags($contenido->ce_contenido), 80) !!}</small>
                                            @else
                                                <span class="text-muted">Sin contenido</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($contenido->subtema && $contenido->subtema->tema)
                                            <span class="badge bg-primary">{{ $contenido->subtema->tema->tema }}</span>
                                        @else
                                            <span class="text-danger">Sin tema</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($contenido->subtema)
                                            <span class="badge bg-success">{{ $contenido->subtema->ce_subtema }}</span>
                                        @else
                                            <span class="text-danger">Sin subtema</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($contenido->created_at)
                                            <small>{{ $contenido->created_at->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" title="Ver" 
                                                    onclick="verContenidoCE({{ $contenido->ce_contenido_id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" title="Editar" 
                                                    onclick="editarContenidoCE({{ $contenido->ce_contenido_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar" 
                                                    onclick="eliminarContenidoCE({{ $contenido->ce_contenido_id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay contenidos registrados</h5>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAgregarContenido">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Contenido CE
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ========================================== MODALES ========================================== -->

<!-- Modal Agregar Tema CE -->
<div class="modal fade" id="modalAgregarTema" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Tema CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarTema" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tema" class="form-label">Nombre del Tema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tema" name="tema" 
                               placeholder="Ej: Demografía" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Tema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agregar Subtema CE -->
<div class="modal fade" id="modalAgregarSubtema" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Subtema CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarSubtema" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ce_tema_id" class="form-label">Tema Padre <span class="text-danger">*</span></label>
                        <select class="form-select" id="ce_tema_id" name="ce_tema_id" required>
                            <option value="">Seleccionar tema...</option>
                            @if(isset($ce_temas))
                                @foreach($ce_temas as $tema)
                                    <option value="{{ $tema->ce_tema_id }}">{{ $tema->tema }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ce_subtema" class="form-label">Nombre del Subtema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ce_subtema" name="ce_subtema" 
                               placeholder="Ej: Población por Edad" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Subtema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agregar Contenido CE -->
<div class="modal fade" id="modalAgregarContenido" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Contenido CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarContenido" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ce_tema_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select" id="ce_tema_select" name="ce_tema_select" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($ce_temas))
                                        @foreach($ce_temas as $tema)
                                            <option value="{{ $tema->ce_tema_id }}">{{ $tema->tema }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ce_subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select" id="ce_subtema_id" name="ce_subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ce_contenido" class="form-label">Contenido HTML <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ce_contenido" name="ce_contenido" rows="15" required 
                                  placeholder="Ingrese el contenido HTML aquí..."></textarea>
                        <small class="form-text text-muted">
                            El contenido se sanitizará automáticamente para prevenir XSS. Tablas HTML están permitidas.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Guardar Contenido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidades -->
<script>
// Datos para filtrado
const ceSubtemasData = @json($ce_subtemas ?? []);

// ============ FUNCIONES DE TEMAS CE ============
function verTemaCI(id) {
    alert('Ver detalles del tema CE ID: ' + id + '\n(Funcionalidad pendiente)');
}

function editarTemaCI(id, nombre) {
    const nuevoNombre = prompt('Editar tema:', nombre);
    if (nuevoNombre && nuevoNombre.trim() !== '') {
        alert('Actualizar tema ID: ' + id + ' a "' + nuevoNombre + '"\n(Funcionalidad pendiente)');
    }
}

function eliminarTemaCI(id, nombre) {
    if (confirm('¿Estás seguro de eliminar el tema "' + nombre + '"?\n\nEsto también eliminará todos los subtemas y contenidos asociados.')) {
        alert('Eliminar tema ID: ' + id + '\n(Funcionalidad pendiente)');
    }
}

// ============ FUNCIONES DE SUBTEMAS CE ============
function verSubtemaCI(id) {
    alert('Ver detalles del subtema CE ID: ' + id + '\n(Funcionalidad pendiente)');
}

function editarSubtemaCI(id) {
    alert('Editar subtema CE ID: ' + id + '\n(Funcionalidad pendiente)');
}

function eliminarSubtemaCI(id, nombre) {
    if (confirm('¿Estás seguro de eliminar el subtema "' + nombre + '"?\n\nEsto también eliminará todos los contenidos asociados.')) {
        alert('Eliminar subtema ID: ' + id + '\n(Funcionalidad pendiente)');
    }
}

// ============ FUNCIONES DE CONTENIDOS CE ============
function verContenidoCI(id) {
    alert('Ver detalles del contenido CE ID: ' + id + '\n(Funcionalidad pendiente)');
}

function editarContenidoCI(id) {
    alert('Editar contenido CE ID: ' + id + '\n(Funcionalidad pendiente)');
}

function eliminarContenidoCI(id) {
    if (confirm('¿Estás seguro de eliminar este contenido?\n\nEsta acción no se puede deshacer.')) {
        alert('Eliminar contenido ID: ' + id + '\n(Funcionalidad pendiente)');
    }
}

// ============ FILTRADO DE SUBTEMAS ============
function filtrarSubtemasCE(temaSelect, subtemaSelect) {
    const temaId = temaSelect.value;
    subtemaSelect.innerHTML = '<option value="">Seleccionar subtema...</option>';
    
    if (temaId) {
        const subtemasDelTema = ceSubtemasData.filter(subtema => subtema.ce_tema_id == temaId);
        
        subtemasDelTema.forEach(subtema => {
            const option = document.createElement('option');
            option.value = subtema.ce_subtema_id;
            option.textContent = subtema.ce_subtema;
            subtemaSelect.appendChild(option);
        });
    }
}

// Event listener para filtrado de subtemas en modal de contenido
document.getElementById('ce_tema_select')?.addEventListener('change', function() {
    filtrarSubtemasCE(this, document.getElementById('ce_subtema_id'));
});
</script>
