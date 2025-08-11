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

<!-- Mensajes de éxito, error y validación -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> 
        <strong>Errores de validación:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Modal Agregar Tema CE -->
<div class="modal fade" id="modalAgregarTema" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Tema CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarTema" method="POST" action="{{ route('sigem.admin.consultas.tema.crear') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tema" class="form-label">Nombre del Tema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tema') is-invalid @enderror" 
                               id="tema" name="tema" 
                               placeholder="Ej: Demografía" 
                               value="{{ old('tema') }}" required>
                        @error('tema')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> El nombre del tema debe ser único en el sistema.
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

<!-- Modal Editar Tema CE -->
<div class="modal fade" id="modalEditarTema" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Tema CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarTema" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_tema_id" name="ce_tema_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tema" class="form-label">Nombre del Tema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_tema" name="tema" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Tema
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
            <form id="formAgregarSubtema" method="POST" action="{{ route('sigem.admin.consultas.subtema.crear') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ce_tema_id" class="form-label">Tema Padre <span class="text-danger">*</span></label>
                        <select class="form-select @error('ce_tema_id') is-invalid @enderror" 
                                id="ce_tema_id" name="ce_tema_id" required>
                            <option value="">Seleccionar tema...</option>
                            @if(isset($ce_temas))
                                @foreach($ce_temas as $tema)
                                    <option value="{{ $tema->ce_tema_id }}" 
                                            {{ old('ce_tema_id') == $tema->ce_tema_id ? 'selected' : '' }}>
                                        {{ $tema->tema }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('ce_tema_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="ce_subtema" class="form-label">Nombre del Subtema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ce_subtema') is-invalid @enderror" 
                               id="ce_subtema" name="ce_subtema" 
                               placeholder="Ej: Población por Edad" 
                               value="{{ old('ce_subtema') }}" required>
                        @error('ce_subtema')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

<!-- Modal Editar Subtema CE -->
<div class="modal fade" id="modalEditarSubtema" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Subtema CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarSubtema" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_subtema_id" name="ce_subtema_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_ce_tema_id" class="form-label">Tema Padre <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_ce_tema_id" name="ce_tema_id" required>
                            <option value="">Seleccionar tema...</option>
                            @if(isset($ce_temas))
                                @foreach($ce_temas as $tema)
                                    <option value="{{ $tema->ce_tema_id }}">{{ $tema->tema }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ce_subtema" class="form-label">Nombre del Subtema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_ce_subtema" name="ce_subtema" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Subtema
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
            <form id="formAgregarContenido" method="POST" action="{{ route('sigem.admin.consultas.contenido.crear') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ce_tema_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select @error('ce_tema_select') is-invalid @enderror" 
                                        id="ce_tema_select" name="ce_tema_select" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($ce_temas))
                                        @foreach($ce_temas as $tema)
                                            <option value="{{ $tema->ce_tema_id }}" 
                                                    {{ old('ce_tema_select') == $tema->ce_tema_id ? 'selected' : '' }}>
                                                {{ $tema->tema }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('ce_tema_select')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ce_subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select @error('ce_subtema_id') is-invalid @enderror" 
                                        id="ce_subtema_id" name="ce_subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                                @error('ce_subtema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ce_contenido" class="form-label">Contenido HTML <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('ce_contenido') is-invalid @enderror" 
                                  id="ce_contenido" name="ce_contenido" rows="15" required 
                                  placeholder="Ingrese el contenido HTML aquí...">{{ old('ce_contenido') }}</textarea>
                        <small class="form-text text-muted">
                            El contenido se sanitizará automáticamente para prevenir XSS. Tablas HTML están permitidas.
                        </small>
                        @error('ce_contenido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

<!-- Modal Editar Contenido CE -->
<div class="modal fade" id="modalEditarContenido" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Contenido CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarContenido" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_contenido_id" name="ce_contenido_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_ce_tema_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_ce_tema_select" name="ce_tema_select" required>
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
                                <label for="edit_ce_subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_ce_subtema_id" name="ce_subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ce_contenido" class="form-label">Contenido HTML <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_ce_contenido" name="ce_contenido" rows="15" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Contenido
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

// Rutas para JavaScript
const routesConsultas = {
    temaUpdate: '{{ route("sigem.admin.consultas.tema.actualizar", ":id") }}',
    temaDelete: '{{ route("sigem.admin.consultas.tema.eliminar", ":id") }}',
    subtemaUpdate: '{{ route("sigem.admin.consultas.subtema.actualizar", ":id") }}',
    subtemaDelete: '{{ route("sigem.admin.consultas.subtema.eliminar", ":id") }}',
    contenidoUpdate: '{{ route("sigem.admin.consultas.contenido.actualizar", ":id") }}',
    contenidoDelete: '{{ route("sigem.admin.consultas.contenido.eliminar", ":id") }}',
    subtemasAjax: '{{ url("/sigem/admin/consultas/subtemas-ce") }}'
};

// ============ FUNCIONES DE TEMAS CE ============
function verTemaCE(id) {
    alert('Ver detalles del tema CE ID: ' + id + '\n(Funcionalidad de vista pendiente)');
}

function editarTemaCE(id, nombre) {
    // Llenar el modal de edición
    document.getElementById('edit_tema_id').value = id;
    document.getElementById('edit_tema').value = nombre;
    
    // Actualizar la acción del formulario
    const form = document.getElementById('formEditarTema');
    form.action = routesConsultas.temaUpdate.replace(':id', id);
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalEditarTema')).show();
}

function eliminarTemaCE(id, nombre) {
    if (confirm('¿Estás seguro de eliminar el tema CE "' + nombre + '"?\n\n⚠️ ADVERTENCIA: Esto también eliminará todos los subtemas y contenidos asociados.\n\nEsta acción no se puede deshacer.')) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesConsultas.temaDelete.replace(':id', id);
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Method spoofing para DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
    }
}

// ============ FUNCIONES DE SUBTEMAS CE ============
function verSubtemaCE(id) {
    alert('Ver detalles del subtema CE ID: ' + id + '\n(Funcionalidad de vista pendiente)');
}

function editarSubtemaCE(id) {
    // Buscar los datos del subtema en la tabla
    const fila = event.target.closest('tr');
    const subtema_nombre = fila.cells[1].querySelector('strong').textContent;
    const tema_badge = fila.cells[2].querySelector('.badge');
    
    // Llenar el modal de edición
    document.getElementById('edit_subtema_id').value = id;
    document.getElementById('edit_ce_subtema').value = subtema_nombre;
    
    // Seleccionar el tema en el select
    const temaSelect = document.getElementById('edit_ce_tema_id');
    if (tema_badge) {
        const temaTexto = tema_badge.textContent;
        for (let option of temaSelect.options) {
            if (option.text === temaTexto) {
                option.selected = true;
                break;
            }
        }
    }
    
    // Actualizar la acción del formulario
    const form = document.getElementById('formEditarSubtema');
    form.action = routesConsultas.subtemaUpdate.replace(':id', id);
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalEditarSubtema')).show();
}

function eliminarSubtemaCE(id, nombre) {
    if (confirm('¿Estás seguro de eliminar el subtema CE "' + nombre + '"?\n\n⚠️ ADVERTENCIA: Esto también eliminará todos los contenidos asociados.\n\nEsta acción no se puede deshacer.')) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesConsultas.subtemaDelete.replace(':id', id);
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Method spoofing para DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
    }
}

// ============ FUNCIONES DE CONTENIDOS CE ============
function verContenidoCE(id) {
    // Buscar el contenido en la tabla y mostrar en modal
    const fila = event.target.closest('tr');
    const contenidoPreview = fila.cells[1].querySelector('small') ? 
        fila.cells[1].querySelector('small').textContent : 
        'Sin contenido disponible';
    
    alert('Contenido CE ID: ' + id + '\n\nPreview:\n' + contenidoPreview + '\n\n(Modal de vista completa pendiente)');
}

function editarContenidoCE(id) {
    // Implementar edición de contenido
    alert('Editar contenido CE ID: ' + id + '\n(Funcionalidad pendiente - requiere cargar contenido HTML completo)');
}

function eliminarContenidoCE(id) {
    if (confirm('¿Estás seguro de eliminar este contenido CE?\n\nEsta acción no se puede deshacer.')) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesConsultas.contenidoDelete.replace(':id', id);
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Method spoofing para DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
    }
}

// ============ FILTRADO DE SUBTEMAS CE ============
function filtrarSubtemasCE(temaSelect, subtemaSelect) {
    const temaId = temaSelect.value;
    subtemaSelect.innerHTML = '<option value="">Seleccionar subtema...</option>';
    
    if (temaId) {
        // Usar datos locales si están disponibles
        const subtemasDelTema = ceSubtemasData.filter(subtema => subtema.ce_tema_id == temaId);
        
        subtemasDelTema.forEach(subtema => {
            const option = document.createElement('option');
            option.value = subtema.ce_subtema_id;
            option.textContent = subtema.ce_subtema;
            subtemaSelect.appendChild(option);
        });
        
        // Alternativamente, usar AJAX si se necesita datos frescos
        /*
        fetch(`${routesConsultas.subtemasAjax}/${temaId}`)
            .then(response => response.json())
            .then(data => {
                data.subtemas.forEach(subtema => {
                    const option = document.createElement('option');
                    option.value = subtema.ce_subtema_id;
                    option.textContent = subtema.ce_subtema;
                    subtemaSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
        */
    }
}

// Event listeners para filtrado de subtemas
document.getElementById('ce_tema_select')?.addEventListener('change', function() {
    filtrarSubtemasCE(this, document.getElementById('ce_subtema_id'));
});

document.getElementById('edit_ce_tema_select')?.addEventListener('change', function() {
    filtrarSubtemasCE(this, document.getElementById('edit_ce_subtema_id'));
});

// Limpiar modales al cerrar
document.getElementById('modalAgregarTema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalEditarTema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalAgregarSubtema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalEditarSubtema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalAgregarContenido')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('ce_subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
});

document.getElementById('modalEditarContenido')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('edit_ce_subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
});
</script>
