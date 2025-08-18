<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-search"></i> Panel CRUD de Consultas Express</h5>
            </div>
            <div class="card-body">
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
            </div>
        </div>
    </div>
</div>

<!-- Navegación entre secciones -->
<div class="row mb-3">
    <div class="col-12">
        <nav>
            <div class="nav nav-pills nav-fill" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-contenidos-tab" data-bs-toggle="tab" data-bs-target="#nav-contenidos" 
                        type="button" role="tab" aria-controls="nav-contenidos" aria-selected="true">
                    <i class="bi bi-file-earmark-text-fill"></i> Contenidos CE <span class="badge bg-warning ms-2">{{ count($ce_contenidos ?? []) }}</span>
                </button>
                <button class="nav-link" id="nav-subtemas-tab" data-bs-toggle="tab" data-bs-target="#nav-subtemas" 
                        type="button" role="tab" aria-controls="nav-subtemas" aria-selected="false">
                    <i class="bi bi-bookmarks-fill"></i> Subtemas CE <span class="badge bg-success ms-2">{{ count($ce_subtemas ?? []) }}</span>
                </button>
                <button class="nav-link" id="nav-temas-tab" data-bs-toggle="tab" data-bs-target="#nav-temas" 
                        type="button" role="tab" aria-controls="nav-temas" aria-selected="false">
                    <i class="bi bi-bookmark-fill"></i> Temas CE <span class="badge bg-primary ms-2">{{ count($ce_temas ?? []) }}</span>
                </button>
            </div>
        </nav>
    </div>
</div>

<div class="tab-content" id="nav-tabContent">
    
    <!-- ========================================== CONTENIDOS CE ========================================== -->
    <div class="tab-pane fade show active" id="nav-contenidos" role="tabpanel" aria-labelledby="nav-contenidos-tab">
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
                        <table id="tablaContenidosCE" class="table table-striped table-hover table-sm">
                            <thead class="table-warning">
                                <tr>
                                    <th>ID</th>
                                    <th>Tabla</th>
                                    <th>Dimensiones</th>
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
                                        <div style="max-width: 200px;">
                                            <strong>{{ $contenido->titulo_tabla ?: 'Sin título' }}</strong>
                                            @if($contenido->pie_tabla)
                                                <br><small class="text-muted">{{ Str::limit($contenido->pie_tabla, 60) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-order="{{ $contenido->tabla_filas * $contenido->tabla_columnas }}">
                                        <span class="badge bg-info">
                                            {{ $contenido->tabla_filas }}×{{ $contenido->tabla_columnas }}
                                        </span>
                                        <br><small class="text-muted">{{ $contenido->tabla_filas * $contenido->tabla_columnas }} celdas</small>
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
                                    <td data-order="{{ $contenido->created_at ? $contenido->created_at->timestamp : 0 }}">
                                        @if($contenido->created_at)
                                            <small>{{ $contenido->created_at->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" title="Ver Tabla" 
                                                    onclick="verTablaContenidoCE({{ $contenido->ce_contenido_id }})">
                                                <i class="bi bi-table"></i>
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
                        <table id="tablaSubtemasCE" class="table table-striped table-hover">
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
                                    <td data-order="{{ $subtema->contenidos()->count() }}">
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
                                            <button type="button" class="btn btn-outline-warning" title="Editar" 
                                                    onclick="editarSubtemaCE({{ $subtema->ce_subtema_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar" 
                                                    onclick="eliminarSubtemaCE({{ $subtema->ce_subtema_id }}, '{{ addslashes($subtema->ce_subtema) }}')">
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

    <!-- ========================================== TEMAS CE ========================================== -->
    <div class="tab-pane fade" id="nav-temas" role="tabpanel" aria-labelledby="nav-temas-tab">
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
                        <table id="tablaTemasCE" class="table table-striped table-hover">
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
                                    <td data-order="{{ $tema->subtemas()->count() }}">
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
                                            <button type="button" class="btn btn-outline-warning" title="Editar" 
                                                    onclick="editarTemaCE({{ $tema->ce_tema_id }}, '{{ addslashes($tema->tema) }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar" 
                                                    onclick="eliminarTemaCE({{ $tema->ce_tema_id }}, '{{ addslashes($tema->tema) }}')">
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
                <h5 class="modal-title"><i class="bi bi-table"></i> Nueva Tabla de Consulta Express</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarContenido" method="POST" action="{{ route('sigem.admin.consultas.contenido.crear') }}">
                @csrf
                <div class="modal-body">
                    <!-- Información básica -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ce_tema_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select @error('ce_tema_select') is-invalid @enderror" 
                                        id="ce_tema_select" name="ce_tema_select" required>
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
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titulo_tabla" class="form-label">Título de la Tabla <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('titulo_tabla') is-invalid @enderror" 
                                       id="titulo_tabla" name="titulo_tabla" 
                                       placeholder="Ej: Población por Edad y Sexo 2024" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="pie_tabla" class="form-label">Pie de Tabla</label>
                                <input type="text" class="form-control" id="pie_tabla" name="pie_tabla" 
                                       placeholder="Fuente: INEGI 2024">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dimensiones de la tabla -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-grid-3x3"></i> Dimensiones de la Tabla</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="tabla_filas" class="form-label">Filas <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="tabla_filas" name="tabla_filas" 
                                               min="1" max="50" value="3" required>
                                        <small class="text-muted">Máximo 50 filas</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="tabla_columnas" class="form-label">Columnas <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="tabla_columnas" name="tabla_columnas" 
                                               min="1" max="20" value="3" required>
                                        <small class="text-muted">Máximo 20 columnas</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Vista Previa</label>
                                        <div class="alert alert-info">
                                            <span id="preview-dimensiones">Tabla de 3x3 (9 celdas total)</span>
                                            <br><small>Ajusta las dimensiones y presiona "Generar Tabla" para crear los campos de entrada.</small>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="generarTabla()">
                                            <i class="bi bi-arrow-clockwise"></i> Generar Tabla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenedor para la tabla editable -->
                    <div id="tabla-editor" class="card" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-pencil-square"></i> Editor de Tabla</h6>
                        </div>
                        <div class="card-body">
                            <div id="tabla-inputs"></div>
                            <small class="text-muted">
                                <strong>Tip:</strong> La primera fila se detectará automáticamente como encabezados si contiene principalmente texto.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning" id="btn-guardar-tabla" disabled>
                        <i class="bi bi-save"></i> Guardar Tabla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Tabla Completa -->
<div class="modal fade" id="modalVerTabla" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-table"></i> Vista de Tabla CE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="tabla-vista-completa">
                    <!-- Aquí se cargará la tabla completa -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Contenido CE -->
<div class="modal fade" id="modalEditarContenido" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Tabla de Consulta Express</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarContenido" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_contenido_id" name="ce_contenido_id">
                <div class="modal-body">
                    <!-- Información básica -->
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
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_titulo_tabla" class="form-label">Título de la Tabla <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_titulo_tabla" name="titulo_tabla" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_pie_tabla" class="form-label">Pie de Tabla</label>
                                <input type="text" class="form-control" id="edit_pie_tabla" name="pie_tabla">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dimensiones de la tabla -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-grid-3x3"></i> Dimensiones de la Tabla</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edit_tabla_filas" class="form-label">Filas <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="edit_tabla_filas" name="tabla_filas" 
                                               min="1" max="50" required>
                                        <small class="text-muted">Máximo 50 filas</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edit_tabla_columnas" class="form-label">Columnas <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="edit_tabla_columnas" name="tabla_columnas" 
                                               min="1" max="20" required>
                                        <small class="text-muted">Máximo 20 columnas</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Vista Previa</label>
                                        <div class="alert alert-info">
                                            <span id="edit_preview-dimensiones">Tabla cargada</span>
                                            <br><small>Modifica las dimensiones y presiona "Regenerar Tabla" si necesitas cambiar el tamaño.</small>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="regenerarTablaEdicion()">
                                            <i class="bi bi-arrow-clockwise"></i> Regenerar Tabla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenedor para la tabla editable -->
                    <div id="edit_tabla-editor" class="card" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-pencil-square"></i> Editor de Tabla</h6>
                        </div>
                        <div class="card-body">
                            <div id="edit_tabla-inputs"></div>
                            <small class="text-muted">
                                <strong>Tip:</strong> La primera fila se detectará automáticamente como encabezados si contiene principalmente texto.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning" id="edit_btn-guardar-tabla" disabled>
                        <i class="bi bi-save"></i> Actualizar Tabla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript actualizado -->
<script>
// Inicializar DataTables
$(document).ready(function() {
    // Configuración común de idioma para todas las tablas
    const dataTablesLanguage = {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    };

    // DataTable para Temas CE
    @if(isset($ce_temas) && count($ce_temas) > 0)
    $('#tablaTemasCE').DataTable({
        responsive: true,
        language: dataTablesLanguage,
        columnDefs: [
            {
                targets: 0, // ID
                width: "10%",
                className: "text-center"
            },
            {
                targets: 1, // Tema
                width: "50%"
            },
            {
                targets: 2, // Subtemas
                width: "20%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 3, // Acciones
                width: "20%",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[1, 'asc']], // Ordenar por tema por defecto
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Ya no necesitamos reinicializar tooltips de Bootstrap
        }
    });
    @endif

    // DataTable para Subtemas CE
    @if(isset($ce_subtemas) && count($ce_subtemas) > 0)
    $('#tablaSubtemasCE').DataTable({
        responsive: true,
        language: dataTablesLanguage,
        columnDefs: [
            {
                targets: 0, // ID
                width: "8%",
                className: "text-center"
            },
            {
                targets: 1, // Subtema
                width: "35%"
            },
            {
                targets: 2, // Tema Padre
                width: "25%"
            },
            {
                targets: 3, // Contenidos
                width: "15%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 4, // Acciones
                width: "17%",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'asc'], [1, 'asc']], // Ordenar por tema padre y luego por subtema
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Ya no necesitamos reinicializar tooltips de Bootstrap
        }
    });
    @endif

    // DataTable para Contenidos CE
    @if(isset($ce_contenidos) && count($ce_contenidos) > 0)
    $('#tablaContenidosCE').DataTable({
        responsive: true,
        language: dataTablesLanguage,
        columnDefs: [
            {
                targets: 0, // ID
                width: "6%",
                className: "text-center"
            },
            {
                targets: 1, // Tabla
                width: "25%"
            },
            {
                targets: 2, // Dimensiones
                width: "12%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 3, // Tema
                width: "15%"
            },
            {
                targets: 4, // Subtema
                width: "15%"
            },
            {
                targets: 5, // Fecha
                width: "12%",
                className: "text-center",
                type: "date"
            },
            {
                targets: 6, // Acciones
                width: "15%",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[5, 'desc']], // Ordenar por fecha más reciente por defecto
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Ya no necesitamos reinicializar tooltips de Bootstrap
        }
    });
    @endif

    // Reinicializar DataTables cuando se cambia de tab
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Pequeño delay para asegurar que el contenido esté visible
        setTimeout(function() {
            $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
        }, 100);
    });
});

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

function eliminarContenidoCE(id) {
    // Obtener información de la fila para confirmación más específica
    const fila = event.target.closest('tr');
    const titulo = fila.cells[1].querySelector('strong').textContent;
    const dimensiones = fila.cells[2].querySelector('.badge').textContent;
    
    if (confirm(`¿Estás seguro de eliminar la tabla "${titulo}" (${dimensiones})?\n\n⚠️ Esta acción no se puede deshacer.`)) {
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

// ============ NUEVAS FUNCIONES PARA TABLAS ============

function generarTabla() {
    const filas = parseInt(document.getElementById('tabla_filas').value);
    const columnas = parseInt(document.getElementById('tabla_columnas').value);
    
    if (filas < 1 || filas > 50 || columnas < 1 || columnas > 20) {
        alert('Las dimensiones deben estar entre 1-50 filas y 1-20 columnas');
        return;
    }
    
    // Actualizar preview
    const totalCeldas = filas * columnas;
    document.getElementById('preview-dimensiones').textContent = `Tabla de ${filas}x${columnas} (${totalCeldas} celdas total)`;
    
    // Generar campos de entrada
    let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
    
    for (let fila = 0; fila < filas; fila++) {
        html += '<tr>';
        for (let col = 0; col < columnas; col++) {
            const nombre = `celda_${fila}_${col}`;
            const placeholder = fila === 0 ? `Encabezado ${col + 1}` : `Fila ${fila + 1}, Col ${col + 1}`;
            html += `<td>
                <input type="text" class="form-control form-control-sm" 
                       name="${nombre}" placeholder="${placeholder}"
                       onchange="validarTabla()">
            </td>`;
        }
        html += '</tr>';
    }
    
    html += '</table></div>';
    
    document.getElementById('tabla-inputs').innerHTML = html;
    document.getElementById('tabla-editor').style.display = 'block';
    document.getElementById('btn-guardar-tabla').disabled = false;
}

function validarTabla() {
    // Contar celdas llenas
    const inputs = document.querySelectorAll('#tabla-inputs input[type="text"]');
    let celdas_llenas = 0;
    
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            celdas_llenas++;
        }
    });
    
    const total_celdas = inputs.length;
    const porcentaje_lleno = (celdas_llenas / total_celdas) * 100;
    
    // Cambiar color del botón según el completado
    const btnGuardar = document.getElementById('btn-guardar-tabla');
    if (porcentaje_lleno < 20) {
        btnGuardar.className = 'btn btn-outline-warning';
        btnGuardar.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Guardar Tabla (Incompleta)';
    } else {
        btnGuardar.className = 'btn btn-warning';
        btnGuardar.innerHTML = '<i class="bi bi-save"></i> Guardar Tabla';
    }
}

function verTablaContenidoCE(id) {
    // Mostrar loading con mejor diseño
    document.getElementById('tabla-vista-completa').innerHTML = `
        <div class="text-center py-5 tabla-loading">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h5 class="text-primary">Cargando tabla...</h5>
            <p class="text-muted">Obteniendo contenido de Consulta Express</p>
        </div>
    `;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalVerTabla'));
    modal.show();
    
    // Cargar datos via AJAX
    fetch(`{{ url('/sigem/admin/consultas/contenido') }}/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data); // Debug
            
            if (data.error) {
                document.getElementById('tabla-vista-completa').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Error:</strong> ${data.error}
                    </div>
                `;
                return;
            }
            
            if (!data.success || !data.contenido) {
                document.getElementById('tabla-vista-completa').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Advertencia:</strong> No se encontraron datos válidos.
                    </div>
                `;
                return;
            }
            
            const contenido = data.contenido;
            const tablaHtml = data.tabla_html;
            
            // Verificar que tenemos los datos necesarios
            if (!contenido.subtema || !contenido.subtema.tema) {
                console.warn('Datos incompletos de relaciones:', contenido);
            }
            
            // Construir HTML de respuesta
            let htmlRespuesta = `
                <div class="mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-primary mb-2">
                                <i class="bi bi-table me-2"></i>${contenido.titulo_tabla || 'Tabla sin título'}
                            </h4>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-primary fs-6">
                                    <i class="bi bi-bookmark-fill me-1"></i>
                                    ${contenido.subtema?.tema?.tema || 'Sin tema'}
                                </span>
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-bookmarks-fill me-1"></i>
                                    ${contenido.subtema?.ce_subtema || 'Sin subtema'}
                                </span>
                                <span class="badge bg-info fs-6">
                                    <i class="bi bi-grid-3x3 me-1"></i>
                                    ${contenido.tabla_filas}×${contenido.tabla_columnas}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                ${contenido.created_at ? new Date(contenido.created_at).toLocaleDateString('es-MX', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) : 'Fecha no disponible'}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="table-container mb-3">
                    ${tablaHtml || '<div class="alert alert-warning">No se pudo renderizar la tabla</div>'}
                </div>
            `;
            
            // Agregar pie de tabla si existe
            if (contenido.pie_tabla) {
                htmlRespuesta += `
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            <em>${contenido.pie_tabla}</em>
                        </small>
                    </div>
                `;
            }
            
            document.getElementById('tabla-vista-completa').innerHTML = htmlRespuesta;
        })
        .catch(error => {
            console.error('Error detallado:', error);
            document.getElementById('tabla-vista-completa').innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle"></i> Error al cargar la tabla</h5>
                    <p class="mb-2"><strong>Detalles:</strong> ${error.message}</p>
                    <small class="text-muted">
                        Verifica la consola del navegador para más información o contacta al administrador del sistema.
                    </small>
                    <hr>
                    <button type="button" class="btn btn-outline-primary btn-sm" 
                            onclick="verTablaContenidoCE(${id})">
                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                    </button>
                </div>
            `;
        });
}

function editarContenidoCE(id) {
    // Mostrar loading en el modal de edición
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarContenido'));
    modalEditar.show();
    
    // Cargar datos existentes
    fetch(`{{ url('/sigem/admin/consultas/contenido') }}/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                modalEditar.hide();
                return;
            }
            
            const contenido = data.contenido;
            
            // Llenar formulario de edición
            document.getElementById('edit_contenido_id').value = contenido.ce_contenido_id;
            document.getElementById('edit_titulo_tabla').value = contenido.titulo_tabla || '';
            document.getElementById('edit_pie_tabla').value = contenido.pie_tabla || '';
            document.getElementById('edit_tabla_filas').value = contenido.tabla_filas;
            document.getElementById('edit_tabla_columnas').value = contenido.tabla_columnas;
            
            // Seleccionar tema y cargar subtemas
            const temaSelect = document.getElementById('edit_ce_tema_select');
            const subtemaSelect = document.getElementById('edit_ce_subtema_id');
            
            if (contenido.subtema && contenido.subtema.tema) {
                // Primero seleccionar el tema
                temaSelect.value = contenido.subtema.tema.ce_tema_id;
                
                // Cargar subtemas del tema seleccionado
                filtrarSubtemasCE(temaSelect, subtemaSelect);
                
                // Esperar un poco más para asegurar que los subtemas se carguen
                setTimeout(() => {
                    // Verificar si el subtema existe en las opciones
                    const subtemaOption = subtemaSelect.querySelector(`option[value="${contenido.ce_subtema_id}"]`);
                    if (subtemaOption) {
                        subtemaSelect.value = contenido.ce_subtema_id;
                    } else {
                        console.warn('Subtema no encontrado en las opciones:', contenido.ce_subtema_id);
                        // Agregar la opción manualmente si no existe
                        const option = document.createElement('option');
                        option.value = contenido.ce_subtema_id;
                        option.textContent = contenido.subtema.ce_subtema;
                        option.selected = true;
                        subtemaSelect.appendChild(option);
                    }
                }, 200); // Aumentado el tiempo de espera
            }
            
            // Generar tabla con datos existentes
            generarTablaEdicion(contenido.tabla_filas, contenido.tabla_columnas, contenido.tabla_datos);
            
            // Actualizar la acción del formulario
            const form = document.getElementById('formEditarContenido');
            form.action = routesConsultas.contenidoUpdate.replace(':id', id);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del contenido');
            modalEditar.hide();
        });
}

function generarTablaEdicion(filas, columnas, datosExistentes = null) {
    // Actualizar preview
    const totalCeldas = filas * columnas;
    document.getElementById('edit_preview-dimensiones').textContent = `Tabla de ${filas}x${columnas} (${totalCeldas} celdas total)`;
    
    // Generar campos de entrada con datos existentes
    let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
    
    for (let fila = 0; fila < filas; fila++) {
        html += '<tr>';
        for (let col = 0; col < columnas; col++) {
            const nombre = `celda_${fila}_${col}`;
            const placeholder = fila === 0 ? `Encabezado ${col + 1}` : `Fila ${fila + 1}, Col ${col + 1}`;
            
            // Obtener valor existente si hay datos
            let valor = '';
            if (datosExistentes && datosExistentes[fila] && datosExistentes[fila][col] !== undefined) {
                valor = datosExistentes[fila][col];
            }
            
            html += `<td>
                <input type="text" class="form-control form-control-sm" 
                       name="${nombre}" placeholder="${placeholder}"
                       value="${escapeHtml(valor)}"
                       onchange="validarTablaEdicion()">
            </td>`;
        }
        html += '</tr>';
    }
    
    html += '</table></div>';
    
    document.getElementById('edit_tabla-inputs').innerHTML = html;
    document.getElementById('edit_tabla-editor').style.display = 'block';
    document.getElementById('edit_btn-guardar-tabla').disabled = false;
}

function validarTablaEdicion() {
    // Contar celdas llenas en edición
    const inputs = document.querySelectorAll('#edit_tabla-inputs input[type="text"]');
    let celdas_llenas = 0;
    
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            celdas_llenas++;
        }
    });
    
    const total_celdas = inputs.length;
    const porcentaje_lleno = (celdas_llenas / total_celdas) * 100;
    
    // Cambiar color del botón según el completado
    const btnGuardar = document.getElementById('edit_btn-guardar-tabla');
    if (porcentaje_lleno < 20) {
        btnGuardar.className = 'btn btn-outline-warning';
        btnGuardar.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Actualizar Tabla (Incompleta)';
    } else {
        btnGuardar.className = 'btn btn-warning';
        btnGuardar.innerHTML = '<i class="bi bi-save"></i> Actualizar Tabla';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
    document.getElementById('tabla-editor').style.display = 'none';
    document.getElementById('btn-guardar-tabla').disabled = true;
    document.getElementById('preview-dimensiones').textContent = 'Tabla de 3x3 (9 celdas total)';
});

// Event listeners para dimensiones de edición
document.getElementById('edit_tabla_filas')?.addEventListener('input', function() {
    actualizarPreviewDimensionesEdicion();
});

document.getElementById('edit_tabla_columnas')?.addEventListener('input', function() {
    actualizarPreviewDimensionesEdicion();
});

function actualizarPreviewDimensionesEdicion() {
    const filas = parseInt(document.getElementById('edit_tabla_filas').value) || 0;
    const columnas = parseInt(document.getElementById('edit_tabla_columnas').value) || 0;
    const total = filas * columnas;
    
    document.getElementById('edit_preview-dimensiones').textContent = `Tabla de ${filas}x${columnas} (${total} celdas total)`;
}

function regenerarTablaEdicion() {
    const filas = parseInt(document.getElementById('edit_tabla_filas').value);
    const columnas = parseInt(document.getElementById('edit_tabla_columnas').value);
    
    if (filas < 1 || filas > 50 || columnas < 1 || columnas > 20) {
        alert('Las dimensiones deben estar entre 1-50 filas y 1-20 columnas');
        return;
    }
    
    if (confirm('¿Estás seguro? Esto borrará el contenido actual de la tabla.')) {
        generarTablaEdicion(filas, columnas);
    }
}

// Limpiar modal de edición al cerrar
document.getElementById('modalEditarContenido')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('edit_ce_subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
    document.getElementById('edit_tabla-editor').style.display = 'none';
    document.getElementById('edit_btn-guardar-tabla').disabled = true;
    document.getElementById('edit_preview-dimensiones').textContent = 'Tabla cargada';
});

// Versión simplificada que solo usa localStorage
document.addEventListener('DOMContentLoaded', function() {
    const tabActivo = localStorage.getItem('consultas_tab_activo') || 'nav-contenidos';
    
    // Restaurar tab
    const tabBtn = document.querySelector(`#${tabActivo}-tab`);
    if (tabBtn) {
        const tab = new bootstrap.Tab(tabBtn);
        tab.show();
    }
    
    // Guardar cuando cambie
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const tabId = e.target.getAttribute('data-bs-target').replace('#', '');
            localStorage.setItem('consultas_tab_activo', tabId);
        });
    });
});
</script>

<style>
/* Estilos específicos para las tablas de Consulta Express */
.consulta-express-tabla {
    margin: 0;
}

.consulta-express-tabla .table {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.consulta-express-tabla .table th {
    background-color: var(--bs-primary) !important;
    color: white;
    font-weight: 600;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.2);
}

.consulta-express-tabla .table td {
    border: 1px solid #dee2e6;
    vertical-align: middle;
}

.consulta-express-tabla .table-responsive {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* ===== NUEVO: Anchos consistentes para todas las tablas ===== */

/* Tabla de Temas CE */
#tablaTemasCE {
    table-layout: fixed;
    width: 100%;
}

#tablaTemasCE th:nth-child(1), 
#tablaTemasCE td:nth-child(1) { 
    width: 80px; /* ID */
    min-width: 80px;
}

#tablaTemasCE th:nth-child(2), 
#tablaTemasCE td:nth-child(2) { 
    width: calc(100% - 280px); /* Tema - flexible */
    min-width: 200px;
}

#tablaTemasCE th:nth-child(3), 
#tablaTemasCE td:nth-child(3) { 
    width: 120px; /* Subtemas */
    min-width: 120px;
}

#tablaTemasCE th:nth-child(4), 
#tablaTemasCE td:nth-child(4) { 
    width: 120px; /* Acciones */
    min-width: 120px;
}

/* Tabla de Subtemas CE */
#tablaSubtemasCE {
    table-layout: fixed;
    width: 100%;
}

#tablaSubtemasCE th:nth-child(1), 
#tablaSubtemasCE td:nth-child(1) { 
    width: 80px; /* ID */
    min-width: 80px;
}

#tablaSubtemasCE th:nth-child(2), 
#tablaSubtemasCE td:nth-child(2) { 
    width: calc(35% - 40px); /* Subtema */
    min-width: 150px;
}

#tablaSubtemasCE th:nth-child(3), 
#tablaSubtemasCE td:nth-child(3) { 
    width: calc(35% - 40px); /* Tema Padre */
    min-width: 150px;
}

#tablaSubtemasCE th:nth-child(4), 
#tablaSubtemasCE td:nth-child(4) { 
    width: 120px; /* Contenidos */
    min-width: 120px;
}

#tablaSubtemasCE th:nth-child(5), 
#tablaSubtemasCE td:nth-child(5) { 
    width: 120px; /* Acciones */
    min-width: 120px;
}

/* Tabla de Contenidos CE */
#tablaContenidosCE {
    table-layout: fixed;
    width: 100%;
}

#tablaContenidosCE th:nth-child(1), 
#tablaContenidosCE td:nth-child(1) { 
    width: 80px; /* ID */
    min-width: 80px;
}

#tablaContenidosCE th:nth-child(2), 
#tablaContenidosCE td:nth-child(2) { 
    width: calc(30% - 27px); /* Tabla */
    min-width: 200px;
}

#tablaContenidosCE th:nth-child(3), 
#tablaContenidosCE td:nth-child(3) { 
    width: 100px; /* Dimensiones */
    min-width: 100px;
}

#tablaContenidosCE th:nth-child(4), 
#tablaContenidosCE td:nth-child(4) { 
    width: calc(20% - 27px); /* Tema */
    min-width: 120px;
}

#tablaContenidosCE th:nth-child(5), 
#tablaContenidosCE td:nth-child(5) { 
    width: calc(20% - 27px); /* Subtema */
    min-width: 120px;
}

#tablaContenidosCE th:nth-child(6), 
#tablaContenidosCE td:nth-child(6) { 
    width: 110px; /* Fecha */
    min-width: 110px;
}

#tablaContenidosCE th:nth-child(7), 
#tablaContenidosCE td:nth-child(7) { 
    
 
    width: 140px; /* Acciones */
    min-width: 140px;
}

/* Prevenir overflow de texto largo */
.table td {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Permitir wrap solo en columnas de contenido principal */
#tablaTemasCE td:nth-child(2),
#tablaSubtemasCE td:nth-child(2),
#tablaSubtemasCE td:nth-child(3),
#tablaContenidosCE td:nth-child(2) {
    white-space: normal;
    word-wrap: break-word;
}

/* Responsive: En pantallas pequeñas, permitir scroll horizontal */
@media (max-width: 992px) {
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Mantener anchos mínimos en móvil */
    .table {
        min-width: 800px;
    }
}

/* Animación suave para el loading */
.tabla-loading {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Mejorar el spacing del modal */
#modalVerTabla .modal-body {
    padding: 1.5rem;
}

#modalVerTabla .modal-header {
    background-color: #23753eff;
    border-bottom: 2px solid #dee2e6;
}

/* Responsive para tablas en modales */
@media (max-width: 768px) {
    .consulta-express-tabla .table {
        font-size: 0.8rem;
    }
    
    .consulta-express-tabla .table th,
    .consulta-express-tabla .table td {
        padding: 0.5rem 0.25rem;
    }
}
</style>

</body>
</html>
