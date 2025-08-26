<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Panel CRUD de Cuadros Estadísticos</h5>
            </div>
            <div class="card-body">
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarCuadro">
                    <i class="bi bi-plus-circle"></i> Nuevo Cuadro
                </button>
                <small class="text-muted d-block mt-2">Total de registros: <strong>{{ count($cuadros ?? []) }}</strong></small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos -->
<div class="row pb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-table"></i> Listado de Cuadros Estadísticos</h6>
            </div>
            <div class="card-body">
                @if(isset($cuadros) && count($cuadros) > 0)
                    <div class="table-responsive">
                        <table id="tablaCuadros" class="table table-striped table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Tema</th>
                                    <th>Subtema</th>
                                    <th>Archivos</th>
                                    <th>Gráfica</th>
                                    <th width="160">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cuadros as $cuadro)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $cuadro->cuadro_estadistico_id }}</span></td>
                                    <?php
                                        $codigoParts = explode('.', $cuadro->codigo_cuadro);
                                        $nTema = intval($codigoParts[0] ?? 0);
                                        $TSubtema = $codigoParts[1] ?? '';
                                        $nCuadro = intval($codigoParts[2] ?? 0);
                                        // Para el subtema, rellena a 10 caracteres para que ordene bien (alfanumérico)
                                        $subtemaOrden = str_pad($TSubtema, 10, ' ', STR_PAD_RIGHT);
                                    ?>
                                    <td data-order="{{ sprintf('%03d.%s.%03d', $nTema, $subtemaOrden, $nCuadro) }}">
                                        <code class="text-primary">{{ $cuadro->codigo_cuadro }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $cuadro->cuadro_estadistico_titulo }}</strong>
                                    </td>
                                    <td>
                                        @if($cuadro->subtema && $cuadro->subtema->tema)
                                            <span class="badge bg-success">{{ $cuadro->subtema->tema->tema_titulo }}</span>
                                        @else
                                            <span class="text-danger">Sin tema</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cuadro->subtema)
                                            <span class="badge bg-warning text-dark">{{ $cuadro->subtema->subtema_titulo }}</span>
                                        @else
                                            <span class="text-danger">Sin subtema</span>
                                        @endif
                                    </td>
                                    <td data-order="{{ ($cuadro->excel_file ? 1 : 0) + ($cuadro->pdf_file ? 1 : 0) }}">
                                        <div class="d-flex gap-1">
                                            @if($cuadro->excel_file)
                                                <span class="badge bg-primary" title="Dataset disponible">
                                                    <i class="bi bi-table"></i>
                                                </span>
                                            @endif
                                            @if($cuadro->pdf_file)
                                                <span class="badge bg-danger" title="PDF disponible">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </span>
                                            @endif
                                            @if($cuadro->excel_formated_file)
                                                <span class="badge bg-success" title="Excel formateado disponible">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-order="{{ $cuadro->permite_grafica ? 1 : 0 }}">
                                        @if($cuadro->permite_grafica)
                                            <span class="badge bg-info" title="Permite gráfica">
                                                <i class="bi bi-graph-up"></i>
                                            </span>
                                        @else
                                            <span class="text-muted">Sin gráfica</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-lg" role="group">
                                            <button type="button" class="btn btn-outline-warning" 
                                                    title="Editar cuadro" 
                                                    onclick="editarCuadro({{ $cuadro->cuadro_estadistico_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Eliminar cuadro" 
                                                    onclick="eliminarCuadro({{ $cuadro->cuadro_estadistico_id }}, '{{ addslashes($cuadro->cuadro_estadistico_titulo) }}')">
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
                        <i class="bi bi-table text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay cuadros estadísticos registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer cuadro haciendo clic en el botón "Nuevo Cuadro".</p>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalAgregarCuadro">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Cuadro
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar nuevo cuadro -->
<div class="modal fade" id="modalAgregarCuadro" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Asignar Nuevo Cuadro Estadístico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarCuadro" method="POST" action="{{ route('sigem.admin.cuadros.crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body bg-fonde">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_id_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select @error('tema_id') is-invalid @enderror" 
                                        id="tema_id_select" name="tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($temas))
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}" 
                                                    {{ old('tema_id') == $tema->tema_id ? 'selected' : '' }}>
                                                {{$tema -> orden_indice}}. {{ $tema->tema_titulo }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('tema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select @error('subtema_id') is-invalid @enderror" 
                                        id="subtema_id" name="subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                                @error('subtema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigo_cuadro" class="form-label">Código del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo_cuadro') is-invalid @enderror" 
                                       id="codigo_cuadro" name="codigo_cuadro" 
                                       placeholder="#NTema.CodigoSubtemaAsignado.#NCuadro" value="{{ old('codigo_cuadro') }}" required>
                                @error('codigo_cuadro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="cuadro_estadistico_titulo" class="form-label">Título del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cuadro_estadistico_titulo') is-invalid @enderror" 
                                       id="cuadro_estadistico_titulo" name="cuadro_estadistico_titulo" 
                                       placeholder="Ej: Población por Edad y Sexo 2024" 
                                       value="{{ old('cuadro_estadistico_titulo') }}" required>
                                @error('cuadro_estadistico_titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    

                    <div class="row">

                        <div class="col-8 mb-3">
                            <label for="cuadro_estadistico_subtitulo" class="form-label">Subtítulo del Cuadro</label>
                            <input type="text" class="form-control @error('cuadro_estadistico_subtitulo') is-invalid @enderror" 
                                id="cuadro_estadistico_subtitulo" name="cuadro_estadistico_subtitulo" 
                                placeholder="Subtitulo adicional del cuadro (opcional)"
                                value="{{ old('cuadro_estadistico_subtitulo') }}">
                            @error('cuadro_estadistico_subtitulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 d-flex justify-content-center align-items-center" style="min-height: 56px;">
                            <div class="form-check mb-0 me-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="permite_grafica" name="permite_grafica" value="1"
                                       {{ old('permite_grafica') ? 'checked' : '' }}>
                                <label class="form-check-label" for="permite_grafica">
                                   <i class="bi bi-graph-up"></i> Permite gráficas
                                </label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" 
                                       id="tipo_mapa_pdf" name="tipo_mapa_pdf" value="1"
                                       {{ old('tipo_mapa_pdf') ? 'checked' : '' }}>
                                <label class="form-check-label" for="tipo_mapa_pdf">
                                   <i class="bi bi-file-earmark-pdf"></i> Es tipo Mapa PDF
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- Archivos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-files"></i> Archivos Asociados</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="excel_file" class="form-label">Archivo Dataset</label>
                                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                               id="excel_file" name="excel_file" accept=".xlsx,.xls">
                                        <small class="form-text text-muted">Formato: .xlsx o .xls (Max: 5MB)</small>
                                        @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="pdf_file" class="form-label">Archivo PDF</label>
                                        <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" 
                                               id="pdf_file" name="pdf_file" accept=".pdf">
                                        <small class="form-text text-muted">Formato: .pdf (Max: 5MB)</small>
                                        @error('pdf_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="excel_formated_file" class="form-label">Excel Formateado</label>
                                        <input type="file" class="form-control @error('excel_formated_file') is-invalid @enderror"
                                            id="excel_formated_file" name="excel_formated_file" accept=".xlsx">
                                        <small class="form-text text-muted">Formato: .xlsx o .xls (Max: 5MB)</small>
                                        @error('excel_formated_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Editor:</h6>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="pie_pagina" class="form-label">Pie de Página:</label>
                                        <div id="pie_pagina_toolbar" class="border rounded-top p-2 bg-light">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')">
                                                <i class="bi bi-type-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')">
                                                <i class="bi bi-type-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')">
                                                <i class="bi bi-type-underline"></i>
                                            </button>
                                        </div>

                                        <div class="form-control" id="pie_pagina" contenteditable="true" 
                                             style="min-height: 100px; max-height: 200px; overflow-y: auto;"
                                             placeholder="Escriba aquí las notas o fuente del cuadro. Puede usar formato HTML básico.">{{ old('pie_pagina') }}</div>
                                        <input type="hidden" id="pie_pagina_hidden" name="pie_pagina" value="{{ old('pie_pagina') }}">
                                        <small class="form-text text-muted">Use los botones de arriba para dar formato al texto.</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Cuadro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar cuadro -->
<div class="modal fade" id="modalEditarCuadro" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Cuadro Estadístico existente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarCuadro" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_cuadro_estadistico_id" name="cuadro_estadistico_id">
                <div class="modal-body bg-fonde">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tema_id_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_tema_id_select" name="tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($temas))
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}">{{ $tema->orden_indice }}. {{ $tema->tema_titulo }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_subtema_id" name="subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_codigo_cuadro" class="form-label">Código del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_codigo_cuadro" name="codigo_cuadro" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_cuadro_estadistico_titulo" class="form-label">Título del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_cuadro_estadistico_titulo" name="cuadro_estadistico_titulo" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8 mb-3">
                            <label for="edit_cuadro_estadistico_subtitulo" class="form-label">Subtítulo del Cuadro</label>
                            <input type="text" class="form-control" id="edit_cuadro_estadistico_subtitulo" name="cuadro_estadistico_subtitulo">
                        </div>
                                <div class="col-md-4 d-flex justify-content-center align-items-center" style="min-height: 56px;">
                            <div class="form-check mb-0 me-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="edit_permite_grafica" name="permite_grafica" value="1">
                                <label class="form-check-label" for="edit_permite_grafica">
                                    <i class="bi bi-graph-up"></i> Permite gráficas
                                </label>
                            </div>
                            <!-- Checkbox para "Es tipo Mapa PDF" -->
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" 
                                       id="edit_tipo_mapa_pdf" name="tipo_mapa_pdf" value="1">
                                <label class="form-check-label" for="edit_tipo_mapa_pdf">
                                    <i class="bi bi-file-earmark-pdf"></i> Es tipo Mapa PDF
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Archivos actuales y nuevos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-files"></i> Gestión de Archivos</h6>
                        </div>
                        <div class="card-body">
                            <!-- Archivos actuales -->
                            <div id="archivos_actuales_section" class="mb-4">
                                <h6 class="text-muted mb-3">Archivos Actuales</h6>
                                <div id="archivo_excel_actual" class="alert alert-primary d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-table text-primary"></i>
                                            <strong>Dataset:</strong> <span id="nombre_excel_actual"></span>
                                            <br><small class="text-muted">Archivo guardado como: <span id="archivo_excel_sistema"></span></small>
                                        </div>
                                        <div>
                                            <input type="hidden" id="remove_excel_hidden" name="remove_excel" value="0">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarArchivoExcel()">
                                                <i class="bi bi-trash"></i> Eliminar Dataset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="archivo_pdf_actual" class="alert alert-warning d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <strong>PDF:</strong> <span id="nombre_pdf_actual"></span>
                                            <br><small class="text-muted">Archivo guardado como: <span id="archivo_pdf_sistema"></span></small>
                                        </div>
                                        <div>
                                            <input type="hidden" id="remove_pdf_hidden" name="remove_pdf" value="0">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarArchivoPdf()">
                                                <i class="bi bi-trash"></i> Eliminar PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="archivo_excel_formated_actual" class="alert alert-success d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-excel text-success"></i>
                                            <strong>Excel Formateado:</strong> <span id="nombre_excel_formated_actual"></span>
                                            <br><small class="text-muted">Archivo guardado como: <span id="archivo_excel_formated_sistema"></span></small>
                                        </div>
                                        <div>
                                            <input type="hidden" id="remove_excel_formated_hidden" name="remove_excel_formated" value="0">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarArchivoExcelFormated()">
                                                <i class="bi bi-trash"></i> Eliminar Excel Formateado
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="sin_archivos_actuales" class="alert alert-light">
                                    <i class="bi bi-info-circle"></i> No hay archivos actuales asociados a este cuadro.
                                </div>
                            </div>
                            <!-- Subir nuevos archivos -->
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_excel_file" class="form-label">
                                            <span id="label_excel_nuevo">Nuevo Archivo Dataset</span>
                                            <span id="label_excel_reemplazar" class="d-none">Reemplazar Archivo Dataset</span>
                                        </label>
                                        <input type="file" class="form-control" id="edit_excel_file" name="excel_file" accept=".xlsx,.xls">
                                        <small class="form-text text-muted">Formato: .xlsx o .xls (Max: 5MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_pdf_file" class="form-label">
                                            <span id="label_pdf_nuevo">Nuevo Archivo PDF</span>
                                            <span id="label_pdf_reemplazar" class="d-none">Reemplazar Archivo PDF</span>
                                        </label>
                                        <input type="file" class="form-control" id="edit_pdf_file" name="pdf_file" accept=".pdf">
                                        <small class="form-text text-muted">Formato: .pdf (Max: 5MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_excel_formated_file" class="form-label">
                                            <span id="label_excel_formated_nuevo">Nuevo Archivo Excel Formateado</span>
                                            <span id="label_excel_formated_reemplazar" class="d-none">Reemplazar Archivo Excel Formateado</span>
                                        </label>
                                        <input type="file" class="form-control" id="edit_excel_formated_file" name="excel_formated_file" accept=".xlsx,.xls">
                                        <small class="form-text text-muted">Formato: .xlsx o .xls (Max: 5MB)</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Editor Pie de Página -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Editor:</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="edit_pie_pagina" class="form-label">Pie de Página:</label>
                                        <div id="edit_pie_pagina_toolbar" class="border rounded-top p-2 bg-light">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatEditText('bold')">
                                                <i class="bi bi-type-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatEditText('italic')">
                                                <i class="bi bi-type-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatEditText('underline')">
                                                <i class="bi bi-type-underline"></i>
                                            </button>
                                        </div>
                                        <div class="form-control" id="edit_pie_pagina" contenteditable="true" 
                                             style="min-height: 100px; max-height: 200px; overflow-y: auto;"
                                             placeholder="Escriba aquí las notas o fuente del cuadro. Puede usar formato HTML básico."></div>
                                        <input type="hidden" id="edit_pie_pagina_hidden" name="pie_pagina">
                                        <small class="form-text text-muted">Use los botones de arriba para dar formato al texto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Cuadro
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
    @if(isset($cuadros) && count($cuadros) > 0)
    $('#tablaCuadros').DataTable({
        responsive: true,
        language: {
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
        },
        columnDefs: [
            {
                targets: 0, // Columna ID
                width: "6%",
                className: "text-center"
            },
            {
                targets: 1, // Columna Código
                width: "10%",
                className: "text-center"
            },
            {
                targets: 2, // Columna Título
                width: "25%"
            },
            {
                targets: 3, // Columna Tema
                width: "8%"
            },
            {
                targets: 4, // Columna Subtema
                width: "8%"
            },
            {
                targets: 5, // Columna Archivos
                width: "10%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 6, // Columna Gráfica
                width: "14%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 7, // Columna Acciones
                width: "160px",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
        }
    });
    @endif
});

// Rutas para JavaScript
const routesCuadros = {
    update: '{{ route("sigem.admin.cuadros.actualizar", ":id") }}',
    delete: '{{ route("sigem.admin.cuadros.eliminar", ":id") }}',
    subtemasPorTema: '{{ url("/sigem/admin/cuadros/subtemas") }}',
    obtenerCuadro: '{{ route("sigem.admin.cuadros.obtener", ":id") }}'
};

// Datos de subtemas para filtrado
const subtemasData = @json($subtemas ?? []);


function verificarArchivosRestantes() {
    const excelVisible = !document.getElementById('archivo_excel_actual').classList.contains('d-none');
    const pdfVisible = !document.getElementById('archivo_pdf_actual').classList.contains('d-none');
    const sinArchivos = document.getElementById('sin_archivos_actuales');
    
    if (!excelVisible && !pdfVisible) {
        sinArchivos.style.display = 'block';
    } else {
        sinArchivos.style.display = 'none';
    }
}

function eliminarArchivoExcel() {
    if (confirm('¿Está seguro de eliminar el archivo Dataset? Esta acción no se puede deshacer.')) {
        document.getElementById('remove_excel_hidden').value = '1';
        document.getElementById('archivo_excel_actual').classList.add('d-none');
        document.getElementById('label_excel_nuevo').classList.remove('d-none');
        document.getElementById('label_excel_reemplazar').classList.add('d-none');
        
        // Verificar si quedan archivos
        verificarArchivosRestantes();
    }
}

function eliminarArchivoPdf() {
    if (confirm('¿Está seguro de eliminar el archivo PDF? Esta acción no se puede deshacer.')) {
        document.getElementById('remove_pdf_hidden').value = '1';
        document.getElementById('archivo_pdf_actual').classList.add('d-none');
        document.getElementById('label_pdf_nuevo').classList.remove('d-none');
        document.getElementById('label_pdf_reemplazar').classList.add('d-none');
        
        // Verificar si quedan archivos
        verificarArchivosRestantes();
    }
}

function eliminarArchivoExcelFormated() {
    if (confirm('¿Está seguro de eliminar el archivo Excel formateado? Esta acción no se puede deshacer.')) {
        document.getElementById('remove_excel_formated_hidden').value = '1';
        document.getElementById('archivo_excel_formated_actual').classList.add('d-none');
        document.getElementById('label_excel_formated_nuevo').classList.remove('d-none');
        document.getElementById('label_excel_formated_reemplazar').classList.add('d-none');
        
        // Verificar si quedan archivos
        verificarArchivosRestantes();
    }
}

// Filtrar subtemas por tema seleccionado
function filtrarSubtemas(temaSelect, subtemaSelect) {
    const temaId = temaSelect.value;
    subtemaSelect.innerHTML = '<option value="">Seleccionar subtema...</option>';
    
    if (temaId) {
        const subtemasDelTema = subtemasData.filter(subtema => 
            subtema.tema && subtema.tema.tema_id == temaId
        );
        
        subtemasDelTema.forEach(subtema => {
            const option = document.createElement('option');
            option.value = subtema.subtema_id;
            if (!subtema.clave_subtema) {
                option.textContent = (subtema.tema?.clave_tema ? subtema.tema.clave_tema + ' - ' : '') + subtema.subtema_titulo;
            } else {
                option.textContent = subtema.clave_subtema + ' - ' + subtema.subtema_titulo;
            }
            subtemaSelect.appendChild(option);
        });
    }
}

// Event listeners para filtrado de subtemas
document.getElementById('tema_id_select')?.addEventListener('change', function() {
    filtrarSubtemas(this, document.getElementById('subtema_id'));
});

document.getElementById('edit_tema_id_select')?.addEventListener('change', function() {
    filtrarSubtemas(this, document.getElementById('edit_subtema_id'));
});

// Funciones para el editor HTML simple
function formatText(command) {
    document.execCommand(command, false, null);
    updateHiddenField();
}

function insertLineBreak() {
    document.execCommand('insertHTML', false, '<br>');
    updateHiddenField();
}

function updateHiddenField() {
    const editor = document.getElementById('pie_pagina');
    const hiddenField = document.getElementById('pie_pagina_hidden');
    hiddenField.value = editor.innerHTML;
}

// Funciones para el editor de edición
function formatEditText(command) {
    document.execCommand(command, false, null);
    updateEditHiddenField();
}


function updateEditHiddenField() {
    const editor = document.getElementById('edit_pie_pagina');
    const hiddenField = document.getElementById('edit_pie_pagina_hidden');
    hiddenField.value = editor.innerHTML;
}   

function editarCuadro(id) {
    fetch(routesCuadros.obtenerCuadro.replace(':id', id))
        .then(response => response.json())
        .then(cuadroData => {
            console.log('Datos del backend:', cuadroData);
            
            // Llenar el modal de edición con datos del backend
            document.getElementById('edit_cuadro_estadistico_id').value = id;
            document.getElementById('edit_codigo_cuadro').value = cuadroData.codigo_cuadro || '';
            document.getElementById('edit_cuadro_estadistico_titulo').value = cuadroData.cuadro_estadistico_titulo || '';
            document.getElementById('edit_cuadro_estadistico_subtitulo').value = cuadroData.cuadro_estadistico_subtitulo || '';
            
            // Configurar gráficas CON DATOS REALES
            const permiteGraficaCheck = document.getElementById('edit_permite_grafica');
            // NUEVO CAMPO: tipo_mapa_pdf
            const esTipoMapaPDF = document.getElementById('edit_tipo_mapa_pdf');

            permiteGraficaCheck.checked = cuadroData.permite_grafica == 1;
            esTipoMapaPDF.checked = (cuadroData.tipo_mapa_pdf == 1 || cuadroData.tipo_mapa_pdf === true);
            // Trigger change to apply UI toggles
            esTipoMapaPDF.dispatchEvent(new Event('change'));

            // Configurar pie de página CON DATOS REALES
            const piePaginaEditor = document.getElementById('edit_pie_pagina');
            const piePaginaHidden = document.getElementById('edit_pie_pagina_hidden');
            
            piePaginaEditor.innerHTML = cuadroData.pie_pagina || '';
            piePaginaHidden.value = cuadroData.pie_pagina || '';
            
            // Seleccionar tema y subtema con IDs del backend
            const temaSelect = document.getElementById('edit_tema_id_select');
            if (cuadroData.subtema && cuadroData.subtema.tema) {
                temaSelect.value = cuadroData.subtema.tema.tema_id;
                filtrarSubtemas(temaSelect, document.getElementById('edit_subtema_id'));
                
                setTimeout(() => {
                    document.getElementById('edit_subtema_id').value = cuadroData.subtema_id;
                }, 100);
            }
            
            // Resetear visibilidad de archivos
            document.getElementById('archivo_excel_actual').classList.add('d-none');
            document.getElementById('archivo_pdf_actual').classList.add('d-none');
            document.getElementById('archivo_excel_formated_actual').classList.add('d-none');
            document.getElementById('sin_archivos_actuales').style.display = 'block';
            
            // Resetear campos de eliminación
            document.getElementById('remove_excel_hidden').value = '0';
            document.getElementById('remove_pdf_hidden').value = '0';
            document.getElementById('remove_excel_formated_hidden').value = '0';
            
            if (cuadroData.excel_file) {
                document.getElementById('archivo_excel_actual').classList.remove('d-none');
                document.getElementById('nombre_excel_actual').textContent = 'Archivo Excel disponible';
                document.getElementById('archivo_excel_sistema').textContent = cuadroData.excel_file;
                document.getElementById('label_excel_reemplazar').classList.remove('d-none');
                document.getElementById('label_excel_nuevo').classList.add('d-none');
            }
            
            if (cuadroData.pdf_file) {
                document.getElementById('archivo_pdf_actual').classList.remove('d-none');
                document.getElementById('nombre_pdf_actual').textContent = 'Archivo PDF disponible';
                document.getElementById('archivo_pdf_sistema').textContent = cuadroData.pdf_file;
                document.getElementById('label_pdf_reemplazar').classList.remove('d-none');
                document.getElementById('label_pdf_nuevo').classList.add('d-none');
            }
            
            if (cuadroData.excel_formated_file) {
                document.getElementById('archivo_excel_formated_actual').classList.remove('d-none');
                document.getElementById('nombre_excel_formated_actual').textContent = 'Archivo Excel Formateado disponible';
                document.getElementById('archivo_excel_formated_sistema').textContent = cuadroData.excel_formated_file;
                document.getElementById('label_excel_formated_reemplazar').classList.remove('d-none');
                document.getElementById('label_excel_formated_nuevo').classList.add('d-none');
            }
            
            verificarArchivosRestantes();
            
            // Actualizar la acción del formulario
            const form = document.getElementById('formEditarCuadro');
            form.action = routesCuadros.update.replace(':id', id);
            
            // Mostrar modal
            new bootstrap.Modal(document.getElementById('modalEditarCuadro')).show();
            
        })
        .catch(error => {
            console.error('Error obteniendo datos del cuadro:', error);
            alert('Error al cargar los datos del cuadro');
        });
}


function eliminarCuadro(id, titulo) {
    // Obtener información adicional de la fila para confirmación más específica
    const fila = event.target.closest('tr');
    const codigo = fila.cells[1].querySelector('code').textContent;
    const archivosCell = fila.cells[6];
    const tieneArchivos = archivosCell.querySelector('.badge') !== null;
    
    let mensaje = `¿Estás seguro de eliminar el cuadro estadístico "${titulo}" (${codigo})?`;
    
    if (tieneArchivos) {
        mensaje += `\n\n⚠️ ADVERTENCIA: Este cuadro tiene archivos asociados que también serán eliminados.`;
    }
    
    mensaje += `\n\n✗ Esta acción NO se puede deshacer.`;
    
    if (confirm(mensaje)) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesCuadros.delete.replace(':id', id);
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

// Actualizar campo oculto cuando se escriba en el editor
document.getElementById('pie_pagina')?.addEventListener('input', updateHiddenField);
document.getElementById('edit_pie_pagina')?.addEventListener('input', updateEditHiddenField);

// Limpiar modales al cerrar
document.getElementById('modalAgregarCuadro')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('tipos_grafica_container').style.display = 'none';
    document.getElementById('subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
    document.getElementById('pie_pagina').innerHTML = '';
    document.getElementById('pie_pagina_hidden').value = '';
});

document.getElementById('modalEditarCuadro')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    const archivosActuales = document.getElementById('archivos_actuales');
    if (archivosActuales) archivosActuales.innerHTML = '';
    document.getElementById('edit_subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
    document.getElementById('edit_pie_pagina').innerHTML = '';
    document.getElementById('edit_pie_pagina_hidden').value = '';
});

// --- JS: controlar inputs cuando 'Es tipo Mapa PDF' está activo ---
(function() {
    function toggleMapaPdfControls(prefix = '') {
        const tipoMapa = document.getElementById(prefix + 'tipo_mapa_pdf');
        const permiteGraf = document.getElementById(prefix + 'permite_grafica');
        const excelInput = document.getElementById(prefix + 'excel_file');
        const excelFormated = document.getElementById(prefix + 'excel_formated_file');

        if (!tipoMapa) return;

        const setDisabled = () => {
            const checked = tipoMapa.checked;
            if (permiteGraf) permiteGraf.disabled = checked;
            if (excelInput) {
                excelInput.disabled = checked;
                if (checked) excelInput.value = null;
            }
            if (excelFormated) {
                excelFormated.disabled = checked;
                if (checked) excelFormated.value = null;
            }
        };

        tipoMapa.addEventListener('change', setDisabled);
        // Inicializar estado
        setDisabled();
    }

    // Crear para formulario de creación
    toggleMapaPdfControls('');
    // Crear para formulario de edición (prefijo 'edit_')
    toggleMapaPdfControls('edit_');
})();
</script>